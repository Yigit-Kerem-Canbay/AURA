import re

def clean_text(text: str) -> str:
    """
    Cleans the extracted text by removing unnecessary whitespaces, 
    repeating characters, and broken line breaks.
    """
    if not text:
        return ""
    
    # Replace multiple spaces with a single space
    text = re.sub(r'\s+', ' ', text)
    # Replace repeating characters (more than 4) with just 4 (e.g. ------ -> ----)
    text = re.sub(r'(.)\1{4,}', r'\1\1\1\1', text)
    
    return text.strip()

def chunk_text(pages: list, chunk_size: int = 1000, overlap: int = 200) -> list:
    """
    Splits text into chunks of specified size with overlap.
    pages: list of dicts {"page_number": int, "text": str}
    Returns: list of dicts with chunk metadata
    """
    chunks = []
    chunk_index = 0
    
    for page in pages:
        text = clean_text(page.get("text", ""))
        page_num = page.get("page_number", 1)
        
        if not text:
            continue
            
        start = 0
        text_length = len(text)
        
        while start < text_length:
            end = start + chunk_size
            
            # If we're not at the end of the text, try to find a natural break (period or newline)
            if end < text_length:
                # Look back for a period or newline to avoid cutting words
                lookback_idx = text.rfind('. ', start, end)
                if lookback_idx == -1:
                    lookback_idx = text.rfind('\n', start, end)
                if lookback_idx == -1:
                    lookback_idx = text.rfind(' ', start, end)
                
                if lookback_idx != -1 and lookback_idx > start + (chunk_size // 2):
                    end = lookback_idx + 1
                    
            chunk_text_str = text[start:end].strip()
            
            if chunk_text_str:
                chunks.append({
                    "chunk_index": chunk_index,
                    "page_number": page_num,
                    "text": chunk_text_str
                })
                chunk_index += 1
                
            start = end - overlap
            
    return chunks
