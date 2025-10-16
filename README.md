# ContentCraft AI — WordPress Smart Content Generator Plugin

<div dir="rtl">

## 🚀 خلاصه پروژه

ContentCraft AI یک پلاگین وردپرس پیشرفته برای تولید محتوای هوشمند است که با بک‌اند FastAPI کار می‌کند. این افزونه قادر است محتوای سئوشده برای پست‌ها، صفحات و محصولات ووکامرس تولید کند.

</div>

## ✨ Key Features

- **Smart Content Generation**: SEO-optimized content for posts, pages, and products
- **WooCommerce Deep Integration**: Complete product descriptions, features, FAQs, tags, cross-sell suggestions
- **Image-to-Text AI**: Generate descriptions and alt-text from product images
- **Brand Voice Training**: Extract and apply your brand's writing style
- **SEO Optimizer**: Meta tags, headings, internal links, schema markup
- **Bulk Generation**: Process multiple products with progress tracking
- **Multi-language Support**: Generate content in multiple languages
- **Social & Email Snippets**: Marketing content generation
- **Automation**: Schedule content generation with WP-Cron
- **A/B Testing**: Generate alternative versions for comparison

## 🏗️ Architecture

```
WordPress Plugin (PHP) ←→ FastAPI Backend (Python) ←→ AI Providers
                                                      (OpenAI/Anthropic/Ollama)
```

## 📁 Project Structure

```
.
├── plugin/                    # WordPress Plugin
│   ├── contentcraft-ai.php    # Main plugin file
│   ├── includes/              # Core classes
│   ├── admin/                 # Admin UI
│   └── languages/             # i18n files
│
├── backend/                   # FastAPI Service
│   ├── app/
│   │   ├── main.py
│   │   ├── routers/           # API endpoints
│   │   ├── services/          # Business logic
│   │   └── models/            # Pydantic schemas
│   └── tests/
│
├── infra/                     # Docker & Infrastructure
│   ├── docker-compose.yml
│   └── nginx.conf
│
├── docs/                      # Documentation
│   ├── architecture.md
│   ├── api-contracts.md
│   └── demo-scripts.md
│
└── todo-management/           # Project Management
    └── tasks/
```

## 🚀 Quick Start

### Prerequisites

- PHP 7.4+ (8.x recommended)
- WordPress 5.8+
- WooCommerce 7.x/8.x (optional, for product features)
- Python 3.9+
- Docker & Docker Compose (recommended)

### Backend Setup

```bash
# Navigate to backend directory
cd backend

# Copy environment file
cp .env.example .env

# Edit .env with your API keys
# PROVIDER=openai
# OPENAI_API_KEY=sk-...
# APP_SECRET=your-secure-secret

# Using Docker (recommended)
cd ../infra
docker-compose up -d

# OR using Python directly
cd ../backend
pip install poetry
poetry install
poetry run uvicorn app.main:app --reload --port 8000
```

### Plugin Installation

1. Copy the `plugin/` directory to `wp-content/plugins/contentcraft-ai/`
2. Activate the plugin in WordPress admin
3. Go to **ContentCraft AI → Settings**
4. Configure:
   - FastAPI URL: `http://localhost:8000`
   - App Secret: (same as backend APP_SECRET)
   - Provider: OpenAI/Anthropic/Ollama
   - Model: gpt-4o-mini (or your choice)
5. Click **Test Connection** to verify

## 🎯 Usage

### Content Studio (Posts/Pages)

1. Go to **ContentCraft AI → Content Studio**
2. Enter topic, keywords, audience, tone
3. Click **Generate**
4. Review and **Insert to Editor**

### Product Writer

1. Edit a WooCommerce product
2. Find **ContentCraft AI** meta box
3. Fill in product details
4. Select sections to generate
5. Click **Generate Content**

### Bulk Generation

1. Go to **ContentCraft AI → Bulk Generator**
2. Select products
3. Choose content elements
4. Start processing with progress tracking

### Brand Voice Training

1. Go to **ContentCraft AI → Brand Voice**
2. Click **Train from Existing Content**
3. System analyzes your recent posts
4. Enable trained profile in settings

## 🔒 Security

- All API keys stored server-side only
- Nonce validation on all requests
- Capability checks (`manage_options`, `edit_posts`)
- Input sanitization with WordPress standards
- Output escaping with `wp_kses_post`
- JWT/Bearer authentication between services

## 🧪 Testing

```bash
# PHP Tests
cd plugin
composer install
vendor/bin/phpunit

# Python Tests
cd backend
poetry run pytest

# Code Standards
vendor/bin/phpcs --standard=WordPress plugin/
```

## 📊 API Endpoints

### FastAPI Backend

- `POST /api/content/generate` - Generate blog content
- `POST /api/product/generate` - Generate product content
- `POST /api/image/analyze` - Analyze product images
- `POST /api/seo/optimize` - SEO optimization
- `POST /api/brand/train` - Train brand voice

### WordPress REST API

- `POST /wp-json/contentcraft/v1/content/generate`
- `POST /wp-json/contentcraft/v1/product/generate`
- `POST /wp-json/contentcraft/v1/bulk/process`
- `GET /wp-json/contentcraft/v1/settings/test`

## 🌍 Multi-language

The plugin UI supports i18n with textdomain `contentcraft-ai`. Content can be generated in multiple languages by selecting the target language in the generation form.

Supported languages: English, فارسی, العربية, Deutsch, Español, Français, and more.

## 📚 Documentation

- [Architecture Overview](docs/architecture.md)
- [API Contracts](docs/api-contracts.md)
- [Demo Scripts](docs/demo-scripts.md)
- [AI Agent Instructions](AI_AGENT_INSTRUCTIONS.md)

## 🤝 Contributing

This is a production-ready monorepository. For contribution guidelines, see `docs/contributing.md`.

## 📝 License

MIT License - See [LICENSE](LICENSE) file

## 🆘 Support

For issues and questions:
- GitHub Issues: [Report here]
- Documentation: Check `/docs` directory
- WordPress.org (coming soon)

## 🎬 Demo

Coming soon: Video walkthrough and live demo site.

---

**Made with ❤️ for WordPress & WooCommerce community**

---

<div dir="rtl">

## نصب سریع فارسی

### نصب بک‌اند:
```bash
cd infra
docker-compose up -d
```

### نصب افزونه:
1. فولدر `plugin` را در `wp-content/plugins/` کپی کنید
2. افزونه را در پنل مدیریت فعال کنید
3. تنظیمات را در **ContentCraft AI → تنظیمات** پیکربندی کنید

</div>

