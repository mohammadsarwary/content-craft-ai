# ContentCraft AI - راهنمای راه‌اندازی سریع

<div dir="rtl">

## 🚀 راه‌اندازی در 5 دقیقه

### پیش‌نیازها

✅ **WordPress** 5.8 یا بالاتر  
✅ **PHP** 7.4 یا بالاتر (8.x پیشنهاد می‌شود)  
✅ **Python** 3.9 یا بالاتر  
✅ **Docker** (اختیاری ولی پیشنهاد می‌شود)  
✅ **یک کلید API**: OpenAI / Anthropic / یا Ollama نصب شده

---

## مرحله 1: راه‌اندازی Backend (FastAPI)

### گزینه A: استفاده از Docker (آسان‌ترین روش) 🐳

```bash
# 1. رفتن به پوشه infrastructure
cd infra

# 2. ایجاد فایل environment
cp .env.example .env

# 3. ویرایش .env و اضافه کردن کلید API
nano .env
# یا
notepad .env  # در Windows
```

**در فایل `.env` این موارد را تنظیم کنید:**
```env
PROVIDER=openai
OPENAI_API_KEY=sk-your-actual-api-key-here
APP_SECRET=یک-رشته-تصادفی-قوی-اینجا-بگذارید
MODEL_NAME=gpt-4o-mini
```

```bash
# 4. اجرای Docker Compose
docker-compose up -d

# 5. بررسی وضعیت
docker-compose ps

# 6. تست اتصال
curl http://localhost:8000/api/health
```

✅ اگر پاسخ JSON دریافت کردید، Backend آماده است!

---

### گزینه B: اجرای مستقیم با Python

```bash
# 1. رفتن به پوشه backend
cd backend

# 2. ایجاد فایل environment
cp .env.example .env
nano .env

# 3. نصب Poetry (اگر ندارید)
curl -sSL https://install.python-poetry.org | python3 -

# 4. نصب dependencies
poetry install

# 5. اجرای سرور
poetry run uvicorn app.main:app --reload --port 8000
```

Backend در `http://localhost:8000` در دسترس خواهد بود.

📚 مستندات API: http://localhost:8000/docs

---

## مرحله 2: نصب افزونه WordPress

### 1. کپی فایل‌های پلاگین

```bash
# از ریشه پروژه:
cp -r plugin /path/to/wordpress/wp-content/plugins/contentcraft-ai

# یا در Windows:
xcopy /E /I plugin C:\xampp\htdocs\wordpress\wp-content\plugins\contentcraft-ai
```

### 2. فعال‌سازی پلاگین

1. به پنل مدیریت WordPress بروید
2. **افزونه‌ها → افزونه‌های نصب شده**
3. **ContentCraft AI** را فعال کنید
4. به صفحه تنظیمات هدایت می‌شوید

### 3. پیکربندی تنظیمات

در صفحه **ContentCraft AI → تنظیمات**:

1. **AI Provider**: OpenAI (یا Anthropic/Ollama)
2. **FastAPI Base URL**: `http://localhost:8000`
3. **API Secret**: همان مقدار `APP_SECRET` که در `.env` گذاشتید
4. **Model Name**: `gpt-4o-mini` (یا هر مدل دیگر)
5. روی **Test Connection** کلیک کنید

✅ اگر "Connected successfully!" دیدید، همه چیز آماده است!

---

## مرحله 3: استفاده از افزونه

### تولید محتوای بلاگ

1. **ContentCraft AI → Content Studio**
2. یک موضوع وارد کنید (مثلاً: "بهترین افزونه‌های سئو وردپرس 2024")
3. کلمات کلیدی، لحن، و زبان را انتخاب کنید
4. روی **Generate Content** کلیک کنید
5. پیش‌نمایش، ویرایش، و درج در ادیتور

### تولید محتوای محصول WooCommerce

1. **محصولات → افزودن محصول** (یا ویرایش موجود)
2. در سایدبار راست، **ContentCraft AI** را پیدا کنید
3. مشخصات محصول را وارد کنید
4. **Generate Product Content** را کلیک کنید
5. محتوای تولید شده را بررسی و اعمال کنید

### آموزش صدای برند (Brand Voice)

1. **ContentCraft AI → Brand Voice**
2. تعداد پست‌های تحلیل (20 پیشنهاد می‌شود) را انتخاب کنید
3. **Start Training** را کلیک کنید
4. صبر کنید تا تحلیل تمام شود (30-60 ثانیه)
5. پروفایل برند شما ذخیره و فعال می‌شود

---

## استفاده با Ollama (LLM محلی - رایگان)

اگر می‌خواهید از مدل‌های محلی استفاده کنید:

```bash
# 1. نصب Ollama
curl -fsSL https://ollama.com/install.sh | sh

# 2. دانلود یک مدل
ollama pull llama2
# یا
ollama pull mistral

# 3. تنظیم backend
cd backend
nano .env
```

در `.env`:
```env
PROVIDER=ollama
OLLAMA_HOST=http://localhost:11434
MODEL_NAME=llama2
APP_SECRET=your-secret
```

```bash
# 4. ری‌استارت backend
docker-compose restart fastapi
# یا
poetry run uvicorn app.main:app --reload
```

---

## عیب‌یابی

### ❌ Backend اجرا نمی‌شود

**خطا**: `Port 8000 already in use`
```bash
# پیدا کردن process روی پورت 8000
lsof -ti:8000 | xargs kill -9  # Mac/Linux
netstat -ano | findstr :8000   # Windows

# یا استفاده از پورت دیگر
uvicorn app.main:app --port 8001
```

**خطا**: `OPENAI_API_KEY not set`
- بررسی کنید فایل `.env` در مسیر درست باشد
- مطمئن شوید کلید API معتبر است

### ❌ WordPress به Backend متصل نمی‌شود

**خطا**: `Connection failed`

1. مطمئن شوید Backend در حال اجراست:
   ```bash
   curl http://localhost:8000/api/health
   ```

2. بررسی کنید `APP_SECRET` در هر دو جا یکسان باشد

3. اگر از Docker استفاده می‌کنید و WordPress در لوکال است:
   - به جای `http://localhost:8000` از `http://host.docker.internal:8000` استفاده کنید (Mac/Windows)
   - یا از IP واقعی سیستم استفاده کنید

### ❌ تولید محتوا شکست می‌خورد

**خطا**: `Rate limit exceeded`
- کلید API شما محدودیت دارد
- کمی صبر کنید و دوباره امتحان کنید

**خطا**: `Invalid JSON response`
- مدل دیگری امتحان کنید (مثلاً `gpt-4o-mini` به جای `gpt-3.5-turbo`)
- Temperature را کمتر کنید

---

## دستورات مفید

### Docker

```bash
# مشاهده لاگ‌ها
docker-compose logs -f fastapi

# ری‌استارت سرویس‌ها
docker-compose restart

# خاموش کردن همه
docker-compose down

# پاک کردن و rebuild
docker-compose down -v
docker-compose build --no-cache
docker-compose up -d
```

### Backend (بدون Docker)

```bash
# اجرای تست‌ها
cd backend
poetry run pytest

# فرمت کردن کد
poetry run black .

# بررسی کد
poetry run flake8
```

### WordPress Plugin

```bash
# بررسی استانداردهای کد
cd plugin
composer install
vendor/bin/phpcs --standard=WordPress

# اجرای تست‌ها
vendor/bin/phpunit
```

---

## مستندات کامل

📚 **Architecture**: [docs/architecture.md](docs/architecture.md)  
📝 **API Contracts**: [docs/api-contracts.md](docs/api-contracts.md)  
🎬 **Demo Scripts**: [docs/demo-scripts.md](docs/demo-scripts.md)  
🤖 **AI Instructions**: [AI_AGENT_INSTRUCTIONS.md](AI_AGENT_INSTRUCTIONS.md)

---

## پشتیبانی

🐛 **گزارش باگ**: [GitHub Issues](https://github.com/contentcraft-ai/issues)  
💬 **سوالات**: [Documentation](docs/)  
📧 **ایمیل**: support@contentcraft.ai

---

## نکات امنیتی

⚠️ **هرگز** کلیدهای API را commit نکنید  
⚠️ از `APP_SECRET` قوی استفاده کنید  
⚠️ در production از HTTPS استفاده کنید  
⚠️ `ALLOWED_ORIGINS` را به دامنه خود محدود کنید  
⚠️ Backend را مستقیماً در اینترنت expose نکنید

---

## آماده به کار است! 🎉

حالا می‌توانید:
- ✅ محتوای بلاگ تولید کنید
- ✅ توضیحات محصول WooCommerce بسازید
- ✅ Alt-text برای تصاویر تولید کنید
- ✅ محتوا را سئو کنید
- ✅ صدای برند خود را آموزش دهید
- ✅ محتوای دسته‌ای تولید کنید

**موفق باشید!** 🚀

</div>

