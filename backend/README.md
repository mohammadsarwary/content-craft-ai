# ContentCraft AI Backend (FastAPI)

AI-powered content generation API for WordPress.

## 🚀 Quick Start

### Prerequisites

- Python 3.9+
- Poetry (or pip)
- API key for OpenAI/Anthropic/Ollama

### Installation

```bash
# Install dependencies with Poetry
poetry install

# OR with pip
pip install -r requirements.txt

# Copy environment file
cp .env.example .env

# Edit .env with your API keys
nano .env
```

### Configuration

Edit `.env` file:

```env
PROVIDER=openai
OPENAI_API_KEY=sk-your-key-here
MODEL_NAME=gpt-4o-mini
APP_SECRET=your-shared-secret-with-wordpress
```

### Run Development Server

```bash
# With Poetry
poetry run uvicorn app.main:app --reload --port 8000

# OR with Python
python -m uvicorn app.main:app --reload --port 8000
```

API will be available at: `http://localhost:8000`

## 📚 API Documentation

Once running, visit:
- Swagger UI: `http://localhost:8000/docs`
- ReDoc: `http://localhost:8000/redoc`
- Health Check: `http://localhost:8000/api/health`

## 🔑 Authentication

All endpoints require Bearer token authentication:

```bash
curl -X POST http://localhost:8000/api/content/generate \
  -H "Authorization: Bearer your-app-secret" \
  -H "Content-Type: application/json" \
  -d '{"topic": "AI in WordPress", "keywords": ["AI", "WordPress"]}'
```

## 🧪 Testing

```bash
# Run tests
poetry run pytest

# With coverage
poetry run pytest --cov=app --cov-report=html

# View coverage report
open htmlcov/index.html
```

## 🐳 Docker

```bash
# Build image
docker build -t contentcraft-ai-backend .

# Run container
docker run -p 8000:8000 --env-file .env contentcraft-ai-backend
```

## 📁 Project Structure

```
backend/
├── app/
│   ├── main.py                 # FastAPI app
│   ├── deps/                   # Dependencies (auth, etc.)
│   ├── routers/                # API endpoints
│   ├── services/               # Business logic
│   ├── models/                 # Pydantic schemas
│   └── utils/                  # Utilities
├── tests/                      # Tests
├── pyproject.toml             # Poetry config
└── .env.example               # Environment template
```

## 🔧 Providers

### OpenAI (GPT-4)

```env
PROVIDER=openai
OPENAI_API_KEY=sk-...
MODEL_NAME=gpt-4o-mini
```

### Anthropic (Claude)

```env
PROVIDER=anthropic
ANTHROPIC_API_KEY=sk-ant-...
MODEL_NAME=claude-3-5-sonnet-20241022
```

### Ollama (Local)

```bash
# Install Ollama
curl -fsSL https://ollama.com/install.sh | sh

# Pull a model
ollama pull llama2

# Configure
PROVIDER=ollama
OLLAMA_HOST=http://localhost:11434
MODEL_NAME=llama2
```

## 🔒 Security

- Never commit `.env` file
- Use strong `APP_SECRET`
- Set `ALLOWED_ORIGINS` to your WordPress domain in production
- Use HTTPS in production
- Keep API keys secure

## 📊 Monitoring

Check logs:
```bash
tail -f logs/app.log
```

Health check:
```bash
curl http://localhost:8000/api/health
```

## 🐛 Troubleshooting

### Port already in use
```bash
# Kill process on port 8000
lsof -ti:8000 | xargs kill -9

# Or use different port
uvicorn app.main:app --port 8001
```

### Module not found
```bash
# Reinstall dependencies
poetry install

# Or activate virtual environment
poetry shell
```

### API key errors
- Check `.env` file exists
- Verify API key is correct
- Check provider is set correctly

## 📝 License

MIT

