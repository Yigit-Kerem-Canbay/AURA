import requests
from bs4 import BeautifulSoup
import time
import socket
import ipaddress
from urllib.parse import urlparse, urljoin
import os
import json

class WebsiteCrawler:
    def __init__(self):
        self.visited_urls = set()
        self.issues = {
            "critical": [],
            "high": [],
            "medium": [],
            "low": []
        }
        self.scores = {
            "seo": 100,
            "security": 100,
            "performance": 100,
            "accessibility": 100
        }
        
    def _validate_url_ssrf(self, url: str) -> bool:
        """Protects against SSRF by validating the IP address."""
        parsed = urlparse(url)
        hostname = parsed.hostname
        if not hostname:
            return False
            
        try:
            ip = socket.gethostbyname(hostname)
            ip_obj = ipaddress.ip_address(ip)
            if ip_obj.is_private or ip_obj.is_loopback or ip_obj.is_link_local or ip_obj.is_multicast:
                return False
            return True
        except socket.gaierror:
            return False
            
    def _deduct_score(self, category: str, amount: int, severity: str, message: str):
        self.scores[category] = max(0, self.scores[category] - amount)
        self.issues[severity].append(message)
        
    def crawl_and_audit(self, url: str) -> dict:
        if not self._validate_url_ssrf(url):
            raise Exception("URL failed SSRF validation. Private or internal IPs are not allowed.")
            
        start_time = time.time()
        
        try:
            headers = {'User-Agent': 'AURA_AuditorBot/2.0'}
            # We'll just audit the main page for now to keep it fast
            response = requests.get(url, headers=headers, timeout=10)
            response.raise_for_status()
            
            fetch_time = time.time() - start_time
            soup = BeautifulSoup(response.content, 'html.parser')
            
            self._audit_security(response)
            self._audit_performance(response, fetch_time, soup)
            self._audit_seo(soup)
            self._audit_accessibility(soup)
            
            total_score = int((self.scores["seo"] * 0.25) + 
                              (self.scores["security"] * 0.30) + 
                              (self.scores["performance"] * 0.25) + 
                              (self.scores["accessibility"] * 0.20))
                              
            ai_summary = self._generate_ai_summary(total_score)
            
            return {
                "seo_score": self.scores["seo"],
                "security_score": self.scores["security"],
                "performance_score": self.scores["performance"],
                "accessibility_score": self.scores["accessibility"],
                "total_score": total_score,
                "ai_summary": ai_summary,
                "cross_intelligence_report": None, # Will be filled by main.py
                "report_data": {
                    "load_time_seconds": round(fetch_time, 2),
                    "issues": self.issues
                }
            }
            
        except Exception as e:
            raise Exception(f"Failed to crawl {url}: {str(e)}")

    def _audit_security(self, response):
        headers = response.headers
        
        if response.url.startswith('http://'):
            self._deduct_score("security", 40, "critical", "Site HTTP kullanıyor. Sebep: Veriler şifrelenmediği için ağ üzerindeki herkes parolaları ve verileri okuyabilir.")
            
        if 'Strict-Transport-Security' not in headers:
            self._deduct_score("security", 20, "high", "HSTS başlığı eksik. Sebep: Tarayıcıyı HTTPS kullanmaya zorlamadığı için kullanıcılar Ortadaki Adam (MITM) saldırılarına maruz kalabilir.")
            
        if 'Content-Security-Policy' not in headers:
            self._deduct_score("security", 20, "high", "CSP başlığı eksik. Sebep: Zararlı JavaScript kodlarının (XSS) çalışmasını engellemek için kritik bir güvenlik katmanıdır.")
            
        if 'X-Frame-Options' not in headers:
            self._deduct_score("security", 10, "medium", "X-Frame-Options eksik. Sebep: Başka sitelerin sitenizi kendi sayfalarına gömmesine izin verir, bu da Clickjacking (tıklama gaspı) saldırılarına yol açabilir.")
            
    def _audit_performance(self, response, fetch_time, soup):
        if fetch_time > 5:
            self._deduct_score("performance", 20, "high", f"Sunucu yanıt süresi çok yavaş: {round(fetch_time, 2)}s. Sebep: Ziyaretçilerin siteyi terk etmesine ve SEO sıralamalarının düşmesine neden olur.")
        elif fetch_time > 3:
            self._deduct_score("performance", 10, "medium", f"Sunucu yanıt süresi yavaş: {round(fetch_time, 2)}s. Sebep: Google önerilen yanıt süresi 1-2 saniye aralığındadır.")
            
        content_length = len(response.content) / (1024 * 1024) # MB
        if content_length > 5:
            self._deduct_score("performance", 15, "high", f"HTML boyutu çok büyük: {round(content_length, 2)} MB. Sebep: Fazla büyük sayfalar tarayıcının belleğini doldurur ve render (çizim) süresini ciddi şekilde uzatır.")
            
        images = soup.find_all('img')
        if len(images) > 30:
            self._deduct_score("performance", 5, "low", f"Sayfada çok fazla resim var ({len(images)}). Sebep: Çok fazla istek yapılması bant genişliğini doldurarak sitenin yüklenme hızını düşürür.")
            
    def _audit_seo(self, soup):
        title = soup.title.string if soup.title else None
        if not title:
            self._deduct_score("seo", 20, "critical", "<title> etiketi eksik. Sebep: Arama motorları sayfanızın konusunu anlayamaz, sıralamaya giremezsiniz.")
        elif len(title) > 60:
            self._deduct_score("seo", 5, "low", "Sayfa başlığı 60 karakterden uzun. Sebep: Google arama sonuçlarında başlığınız kesilerek (... şeklinde) gösterilir.")
            
        h1_tags = soup.find_all('h1')
        if not h1_tags:
            self._deduct_score("seo", 15, "high", "<h1> etiketi eksik. Sebep: Sayfanın ana başlığı olmadığı için hem erişilebilirlik hem de SEO açısından zayıf bir yapı oluşur.")
        elif len(h1_tags) > 1:
            self._deduct_score("seo", 5, "low", "Birden fazla <h1> etiketi bulundu. Sebep: Modern SEO'da tek bir h1 kullanımı ana konuyu Google'a daha net vurgular.")
            
        meta_desc = soup.find('meta', attrs={'name': 'description'})
        if not meta_desc:
            self._deduct_score("seo", 20, "high", "Meta açıklama (description) eksik. Sebep: Kullanıcıları arama sonuçlarında tıklamaya ikna edecek özet metniniz yok.")
            
        canonical = soup.find('link', rel='canonical')
        if not canonical:
            self._deduct_score("seo", 5, "medium", "Canonical URL eksik. Sebep: Aynı içeriğe sahip farklı URL'ler varsa (örn. www ve www-olmayan), Google bunu kopya içerik sayabilir.")
            
        og_title = soup.find('meta', property='og:title')
        if not og_title:
            self._deduct_score("seo", 5, "low", "Open Graph (og:title) eksik. Sebep: Siteniz sosyal medyada paylaşıldığında başlık ve resimler düzgün görünmez.")

    def _audit_accessibility(self, soup):
        html_tag = soup.find('html')
        if not html_tag or not html_tag.get('lang'):
            self._deduct_score("accessibility", 15, "medium", "HTML 'lang' niteliği eksik. Sebep: Ekran okuyucular sayfanın hangi dilde okunduğunu anlayamaz, görme engelli kullanıcılar zorlanır.")
            
        images = soup.find_all('img')
        images_without_alt = [img for img in images if not img.get('alt')]
        if images_without_alt:
            percent = (len(images_without_alt) / len(images)) * 100
            if percent > 50:
                self._deduct_score("accessibility", 20, "high", f"Resimlerin %50'sinden fazlasında 'alt' etiketi yok. Sebep: Ekran okuyucular resimlerin ne olduğunu anlatamaz ve SEO görselleri tarayamaz.")
            elif percent > 0:
                self._deduct_score("accessibility", 10, "medium", f"Bazı resimlerde ({len(images_without_alt)} adet) 'alt' etiketi yok. Sebep: Görsel içerik arama motorları için kapalı kutu olarak kalır.")
                
    def _generate_ai_summary(self, total_score):
        llm_api_key = os.getenv('LLM_API_KEY')
        if not llm_api_key:
            return "AI özeti oluşturulamadı (LLM API Key eksik)."
            
        try:
            prompt = f"Sen bir siber güvenlik ve web analiz uzmanısın. Bir web sitesini denetledik ve toplam skoru {total_score}/100. "
            prompt += f"Bulunan sorunlar şunlar:\n"
            prompt += f"Kritik: {', '.join(self.issues['critical'])}\n"
            prompt += f"Yüksek: {', '.join(self.issues['high'])}\n"
            prompt += f"Orta: {', '.join(self.issues['medium'])}\n"
            prompt += f"Lütfen bu sonuçları teknik olmayan bir kullanıcının anlayabileceği şekilde, profesyonel ama dostane bir dille (yaklaşık 2-3 paragraf) özetle. En acil çözülmesi gerekenlere dikkat çek. Eğer hiç sorun yoksa tebrik et."
            
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
                return response.json()['choices'][0]['message']['content']
            else:
                return f"AI Özet Hatası: {response.status_code}"
        except Exception as e:
            return f"AI Özeti oluşturulurken bir hata oluştu: {str(e)}"
