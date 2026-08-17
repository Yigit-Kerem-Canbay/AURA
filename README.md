# AURA (AI Unified Research & Audit) 🚀

![AURA Banner](https://img.shields.io/badge/AURA-AI%20Unified%20Research%20%26%20Audit-4F46E5?style=for-the-badge&logo=openai)

AURA, yapay zeka destekli kurumsal bir doküman araştırma (RAG) ve akıllı web sitesi denetim (Audit) platformudur. Kurumların devasa bilgi havuzlarını (PDF, DOCX, XLSX) saniyeler içinde tarayarak sorulara yapay zeka ile cevap veren ve aynı zamanda şirketin web standartlarını çapraz zeka analizleriyle (Cross-Intelligence) otomatik olarak denetleyen "Enterprise-Grade" (Kurumsal Seviye) bir yazılımdır.

---

## 🌟 Öne Çıkan Özellikler

### 🧠 1. RAG (Retrieval-Augmented Generation) Sohbet Asistanı
* **Gelişmiş Vektör Arama:** pgvector HNSW indekslemesi sayesinde milyonlarca kelimelik dokümanlar (PDF, DOCX, XLSX) milisaniyeler içinde taranır.
* **Akıllı Parçalama (Chunking):** Dokümanlar, anlam bütünlüğü bozulmadan küçük parçalara (chunks) bölünerek LLM modeline (Groq Llama 3.1) yedirilir.
* **Tesseract OCR:** Sadece metin bazlı dosyaları değil, taranmış görüntü halindeki PDF'leri de okuyarak anlar.
* **Sıfır Halüsinasyon:** Yapay zeka sadece yüklenen dokümanlar ışığında cevap verir. Bağlamda cevap yoksa uydurmaz, dürüstçe söyler.

### 🕸️ 2. Akıllı Website Denetimi (Crawler & Audit)
* **4 Boyutlu Analiz:** Web sitelerini SEO, Performans, Güvenlik (HTTP, SSL, HSTS, CSP) ve Erişilebilirlik (A11y) açısından otomatik denetler.
* **SSRF Korumalı Motor:** Crawler, kötü niyetli (localhost veya private IP) adres taramalarını otomatik reddederek sunucu güvenliğini sağlar.
* **AI Destekli Özet:** Teknik logları okuyup yöneticiler için anlaşılır 1-2 paragraflık "Yönetici Özetleri" çıkarır.

### 🔄 3. Çapraz Zeka (Cross-Intelligence) Analizi
* Sistemdeki kurum dokümanlarını (örn: "Şirket Web Sitesi Standartları") okur ve denetlenen web sitesinin bu standartlara uyup uymadığını otomatik olarak çapraz analiz eder.

### 🛡️ 4. Güvenlik, İzleme ve Veri İzolasyonu (Enterprise Security)
* **Rol Bazlı Erişim (RBAC):** Admin ve Çalışan rolleri ile sıkı veri izolasyonu. Çalışanlar sadece kendi departman verilerini görebilir.
* **Action Logs:** Kimin ne zaman sisteme girdiği, şifresini yanlış girdiği gibi kritik sistem olayları anlık olarak IP adresleriyle loglanır.
* **Token (Maliyet) Takibi:** Yapay zeka ile yapılan her sohbette harcanan `prompt_tokens` ve `completion_tokens` veritabanına loglanır, fatura tahmini sağlar.
* **Otomatik Yazılım Testleri:** Sürekli entegrasyona (CI) hazır PHPUnit test senaryoları ile sistemin sağlığı her an kanıtlanabilir.

---

## 🛠️ Teknoloji Yığınımız (Tech Stack)

### Backend (Laravel 10 - PHP 8.2)
* **Architecture:** Monolith API + Queue Workers (Redis)
* **Auth:** Laravel Sanctum & Role-Based Middleware
* **Frontend UI:** Blade, TailwindCSS v3, Chart.js, HeroIcons
* **Testing:** PHPUnit (Feature Tests)

### AI Servisi (FastAPI - Python 3.11)
* **Framework:** FastAPI & Uvicorn
* **Database:** PostgreSQL 16 + `pgvector` eklentisi
* **Machine Learning:** SentenceTransformers (`all-MiniLM-L6-v2`)
* **LLM Provider:** Groq (Llama-3.1-8b-instant)
* **Parsers:** PyMuPDF, pdfplumber, python-docx, openpyxl, pytesseract

### DevOps & Altyapı
* **Konteynerizasyon:** Docker & Docker Compose (Laravel Sail)
* **Asenkron Kuyruk:** Redis
* **Veritabanı:** PostgreSQL (HNSW Vector Indexing)

---

## 🚀 Kurulum ve Çalıştırma

Proje tamamen Dockerize edilmiştir. Yerel makinenizde sadece **Docker** ve **Docker Compose** kurulu olması yeterlidir.

### 1. Depoyu Klonlayın
```bash
git clone https://github.com/your-username/AURA.git
cd AURA
```

### 2. Ortam Değişkenlerini Ayarlayın (.env)
Hem Laravel hem de Python servisi için `.env` dosyalarını oluşturun.
```bash
# Laravel İçin
cd backend
cp .env.example .env

# Groq API Key ve Python Servis URL'ini .env içine ekleyin
# PYTHON_SERVICE_URL=http://ai_service:8001
# INTERNAL_API_KEY=your_secret_key
```

### 3. Docker Compose ile Başlatın
Tüm sistemi (PostgreSQL, Redis, Laravel, FastAPI) tek bir komutla ayağa kaldırın:
```bash
# Laravel klasöründe (backend)
./vendor/bin/sail up -d
```

### 4. Veritabanını Hazırlayın
Konteynerler ayağa kalktıktan sonra, tabloları ve HNSW vektör indekslerini oluşturun:
```bash
./vendor/bin/sail artisan migrate
```

Sisteminiz kullanıma hazır! 
- **AURA Arayüzü:** `http://localhost:8000`
- **FastAPI Dökümantasyonu:** `http://localhost:8001/docs`

---

## 🔒 Güvenlik Uyarıları (Production)
- Canlı ortama çıkmadan önce `INTERNAL_API_KEY` değişkenini güçlü bir anahtar ile değiştirin.
- Python servisi (`ai_service`) doğrudan dış ağa (Public) **açılmamalıdır**. Sadece Laravel backend'i üzerinden erişilmelidir.
- Veritabanı ve Redis portlarını dışarıya kapatın.

---

<div align="center">
  <b>AURA</b> - Geleceğin kurumları için tasarlandı. 💜
</div>
