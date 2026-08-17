import os
import json
from fastapi import FastAPI, Depends, HTTPException, Header
from pydantic import BaseModel
from sqlalchemy.orm import Session
from sqlalchemy import text
from parsers import get_parser
from chunker import chunk_text
from embedder import SentenceTransformerEmbedder
from database import get_db, DocumentChunk, engine, Base

# Create tables if they don't exist
# In a real production app, use Alembic for migrations!
# Also, we must create the vector extension first
with engine.connect() as conn:
    conn.execute(text("CREATE EXTENSION IF NOT EXISTS vector"))
    conn.commit()
Base.metadata.create_all(bind=engine)

app = FastAPI(title="AURA AI Service")

# Initialize embedder (loads model)
embedder = SentenceTransformerEmbedder()

# Authentication Dependency for Internal APIs
async def verify_internal_api_key(x_internal_api_key: str = Header(...)):
    expected_key = os.getenv("INTERNAL_API_KEY", "super_secret_internal_key")
    if x_internal_api_key != expected_key:
        raise HTTPException(status_code=401, detail="Unauthorized")
    return x_internal_api_key

class DocumentProcessRequest(BaseModel):
    document_id: int
    file_path: str

@app.post("/internal/documents/process")
async def process_document(
    request: DocumentProcessRequest,
    api_key: str = Depends(verify_internal_api_key),
    db: Session = Depends(get_db)
):
    """
    Day 6-8: Parses document, cleans, chunks, embeds, and saves to pgvector.
    """
    try:
        file_ext = request.file_path.split('.')[-1]
        
        parser = get_parser(file_ext)
        pages = parser.parse(request.file_path)
        
        if not pages:
             raise Exception("No text could be extracted from the document.")
             
        chunks = chunk_text(pages, chunk_size=1000, overlap=200)
        
        processed_chunks = []
        for chunk in chunks:
            embedding = embedder.embed_text(chunk["text"])
            
            # Save to Database
            db_chunk = DocumentChunk(
                document_id=request.document_id,
                page_number=chunk["page_number"],
                chunk_index=chunk["chunk_index"],
                text=chunk["text"],
                embedding=embedding
            )
            db.add(db_chunk)
            
            processed_chunks.append({
                "chunk_index": chunk["chunk_index"],
                "embedding_length": len(embedding)
            })
            
        db.commit()
            
        return {
            "message": "Document processed successfully",
            "document_id": request.document_id,
            "status": "processed",
            "total_chunks": len(processed_chunks)
        }
    except Exception as e:
        db.rollback()
        print(f"Error processing document {request.document_id}: {e}")
        raise HTTPException(status_code=500, detail=str(e))

class ChatRequest(BaseModel):
    query: str
    document_ids: list[int] = []

@app.post("/internal/chat")
async def chat(
    request: ChatRequest,
    api_key: str = Depends(verify_internal_api_key),
    db: Session = Depends(get_db)
):
    """
    Day 9: RAG Chat
    Embeds the query, finds most similar chunks via pgvector, and answers strictly based on context.
    """
    try:
        # Configuration
        SIMILARITY_THRESHOLD = float(os.getenv("SIMILARITY_THRESHOLD", "0.35"))
        RAG_TOP_K = int(os.getenv("RAG_TOP_K", "5"))
        
        query_embedding = embedder.embed_text(request.query)
        embedding_str = "[" + ",".join(map(str, query_embedding)) + "]"
        
        # Real Vector Search with JOIN on documents table
        # We use 1 - (<=>) because <=> is cosine distance, so 1 - distance is cosine similarity.
        # We also fetch c.id (chunk_id) and c.chunk_index
        sql = text("""
            SELECT c.id as chunk_id, c.text, c.page_number, c.chunk_index, c.document_id, d.title as document_title,
                   1 - (c.embedding <=> CAST(:embedding AS vector)) as similarity
            FROM document_chunks c
            JOIN documents d ON c.document_id = d.id
            ORDER BY c.embedding <=> CAST(:embedding AS vector)
            LIMIT :top_k
        """)
        
        results = db.execute(sql, {"embedding": embedding_str, "top_k": RAG_TOP_K}).fetchall()
        
        print(f"--- RAG Retrieval for Query: '{request.query}' ---")
        
        valid_chunks_str = ""
        source_candidates = {}
        
        for idx, row in enumerate(results):
            sim = float(row.similarity)
            title = row.document_title or f"Doc ID {row.document_id}"
            print(f"Result {idx+1}: Similarity = {sim:.4f} | Source = {title} (Page {row.page_number})")
            
            if sim >= SIMILARITY_THRESHOLD:
                source_id = str(row.chunk_id)
                valid_chunks_str += f"[Kaynak ID: {source_id}]\n{row.text}\n\n---\n\n"
                source_candidates[source_id] = {
                    "document_id": row.document_id,
                    "title": title,
                    "page_number": row.page_number,
                    "chunk_index": row.chunk_index,
                    "similarity": round(sim, 4)
                }
        
        # Check if we have any valid context
        if not valid_chunks_str:
            print(f"No chunks passed the threshold {SIMILARITY_THRESHOLD}.")
            return {
                "query": request.query,
                "answer": "Bu bilgi yüklenen dokümanlarda bulunamadı.",
                "sources": []
            }
            
        # Real LLM Call via Groq
        llm_api_key = os.getenv('LLM_API_KEY')
        
        usage_stats = {"prompt_tokens": 0, "completion_tokens": 0}

        if llm_api_key:
            import requests
            
            system_prompt = (
                "Sen AURA (AI Unified Research & Audit) projesinin yapay zeka asistanısın.\n"
                "Sana '[Kaynak ID: X]' formatında numaralandırılmış bağlam metinleri verilmiştir.\n"
                "Kullanıcının sorusunu SADECE bu bağlamdaki bilgileri kullanarak Türkçe cevapla.\n"
                "Kendi eğitim bilgini KESİNLİKLE KULLANMA. Tahmin yapma.\n"
                "Eğer bağlamda sorunun cevabını destekleyen bir bilgi yoksa, cevaba tam olarak 'Bu bilgi yüklenen dokümanlarda bulunamadı.' yaz.\n\n"
                "Çıktını KESİNLİKLE aşağıdaki JSON formatında vermelisin:\n"
                "{\n"
                "  \"answer\": \"Cevap metni veya 'Bu bilgi yüklenen dokümanlarda bulunamadı.'\",\n"
                "  \"used_source_ids\": [kullandığın kaynakların ID'lerini içeren bir liste (örneğin: [\"12\", \"15\"])]\n"
                "}\n"
                "Eğer cevabı bulamazsan, 'used_source_ids' kesinlikle boş liste [] olmalıdır."
            )
            
            headers = {
                "Authorization": f"Bearer {llm_api_key}",
                "Content-Type": "application/json"
            }
            data = {
                "model": "llama-3.1-8b-instant",
                "response_format": {"type": "json_object"},
                "messages": [
                    {"role": "system", "content": system_prompt},
                    {"role": "user", "content": f"Bağlam:\n{valid_chunks_str}\n\nSoru: {request.query}"}
                ]
            }
            response = requests.post("https://api.groq.com/openai/v1/chat/completions", headers=headers, json=data)
            
            if response.status_code == 200:
                resp_data = response.json()
                if "usage" in resp_data:
                    usage_stats["prompt_tokens"] = resp_data["usage"].get("prompt_tokens", 0)
                    usage_stats["completion_tokens"] = resp_data["usage"].get("completion_tokens", 0)

                try:
                    response_json = json.loads(resp_data['choices'][0]['message']['content'])
                    answer_text = response_json.get('answer', 'Format hatası.')
                    used_ids = response_json.get('used_source_ids', [])
                    
                    if "bulunamadı" in answer_text.lower() or not used_ids:
                        final_sources = []
                    else:
                        final_sources = [source_candidates[str(sid)] for sid in used_ids if str(sid) in source_candidates]
                        
                    answer = answer_text
                    sources = final_sources
                except Exception as parse_e:
                    answer = "Çıktı işleme hatası."
                    sources = []
            else:
                answer = f"Groq API Hatası: {response.text}"
                sources = []
        else:
            answer = "LLM API Key bulunamadı."
            sources = []
        
        return {
            "query": request.query,
            "answer": answer,
            "sources": sources,
            "usage": usage_stats
        }
    except Exception as e:
        print(f"Error in chat endpoint: {e}")
        raise HTTPException(status_code=500, detail=str(e))

from crawler import WebsiteCrawler

class AuditRequest(BaseModel):
    audit_id: int
    url: str

@app.post("/internal/audit")
async def audit_website(
    request: AuditRequest,
    api_key: str = Depends(verify_internal_api_key),
    db: Session = Depends(get_db)
):
    """
    Day 11-13 & Day 18: Audits a website and performs Cross-Intelligence RAG analysis.
    """
    try:
        crawler = WebsiteCrawler()
        result = crawler.crawl_and_audit(request.url)
        
        # --- Cross Intelligence (RAG) ---
        llm_api_key = os.getenv('LLM_API_KEY')
        if llm_api_key:
            # 1. Search for web-related rules in documents
            query_text = "web sitesi kuralları iletişim gereksinimler standartlar"
            query_embedding = embedder.embed_text(query_text)
            
            # Convert embedding to Postgres vector format
            embedding_str = "[" + ",".join(map(str, query_embedding)) + "]"
            
            # Find top 3 most relevant chunks
            sql = text("""
                SELECT text, 1 - (embedding <=> CAST(:embedding AS vector)) as similarity
                FROM document_chunks
                ORDER BY embedding <=> CAST(:embedding AS vector)
                LIMIT 3
            """)
            chunks = db.execute(sql, {"embedding": embedding_str}).fetchall()
            
            if chunks and chunks[0].similarity > 0.3:
                context_text = "\n".join([chunk.text for chunk in chunks])
                issues_text = json.dumps(result['report_data']['issues'], ensure_ascii=False)
                
                import requests
                prompt = f"Sen bir kurum içi denetçisin. Kurumun web sitesi standartları şu şekildedir:\n{context_text}\n\n"
                prompt += f"Web sitesi tarandı ve şu sorunlar bulundu:\n{issues_text}\n\n"
                prompt += f"Lütfen sitenin kurum standartlarına uyup uymadığını kısa bir raporla açıkla."
                
                headers = {
                    "Authorization": f"Bearer {llm_api_key}",
                    "Content-Type": "application/json"
                }
                data = {
                    "model": "llama-3.1-8b-instant",
                    "messages": [
                        {"role": "user", "content": prompt}
                    ]
                }
                response = requests.post("https://api.groq.com/openai/v1/chat/completions", headers=headers, json=data)
                
                if response.status_code == 200:
                    result['cross_intelligence_report'] = response.json()['choices'][0]['message']['content']
                else:
                    result['cross_intelligence_report'] = f"Çapraz Zeka Hatası: {response.status_code}"
            else:
                result['cross_intelligence_report'] = "Kurum dokümanlarında web sitesi standartlarına dair kural bulunamadı."
        
        return result
    except Exception as e:
        print(f"Error auditing website {request.url}: {e}")
        raise HTTPException(status_code=500, detail=str(e))

@app.get("/")
def read_root():
    return {"message": "AURA AI Service is running"}
