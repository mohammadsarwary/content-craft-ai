# ContentCraft AI - Infrastructure

Docker Compose setup for ContentCraft AI backend.

## 🚀 Quick Start

### Prerequisites

- Docker 20.10+
- Docker Compose 2.0+

### Setup

1. **Create environment file**:
```bash
cd infra
cp .env.example .env
nano .env
```

2. **Configure environment variables**:
```env
PROVIDER=openai
OPENAI_API_KEY=sk-your-key-here
APP_SECRET=your-super-secret-token
```

3. **Start services**:
```bash
docker-compose up -d
```

4. **Check status**:
```bash
docker-compose ps
```

5. **View logs**:
```bash
docker-compose logs -f fastapi
```

## 📦 Services

### FastAPI Backend
- **Port**: 8000
- **URL**: http://localhost:8000
- **Docs**: http://localhost:8000/docs
- **Health**: http://localhost:8000/api/health

### Redis (Cache)
- **Port**: 6379
- Optional but recommended for production

### Ollama (Local LLM)
- **Port**: 11434
- Optional - for running local models
- Pull models: `docker exec -it contentcraft-ollama ollama pull llama2`

### Nginx (Reverse Proxy)
- **Port**: 80, 443
- Optional - for production deployments

## 🔧 Configuration

### Minimal Setup (OpenAI)
```yaml
# docker-compose.yml
services:
  fastapi:
    # ... (FastAPI only)
```

```bash
docker-compose up -d fastapi
```

### Full Setup (with Redis & Nginx)
```bash
docker-compose up -d
```

### Local LLM Setup (Ollama)
```bash
# Start Ollama
docker-compose up -d ollama

# Pull a model
docker exec -it contentcraft-ollama ollama pull llama2

# Configure backend to use Ollama
PROVIDER=ollama
OLLAMA_HOST=http://ollama:11434
MODEL_NAME=llama2
```

## 🛠️ Commands

### Start services
```bash
docker-compose up -d
```

### Stop services
```bash
docker-compose down
```

### Restart a service
```bash
docker-compose restart fastapi
```

### View logs
```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f fastapi
```

### Execute command in container
```bash
docker-compose exec fastapi python -c "print('Hello')"
```

### Rebuild image
```bash
docker-compose build --no-cache fastapi
docker-compose up -d fastapi
```

## 🔍 Troubleshooting

### FastAPI won't start
```bash
# Check logs
docker-compose logs fastapi

# Common issues:
# - Missing API key
# - Port already in use
# - Invalid environment variables
```

### Redis connection failed
```bash
# Check Redis is running
docker-compose ps redis

# Test connection
docker-compose exec redis redis-cli ping
```

### Port already in use
```bash
# Change port in docker-compose.yml
ports:
  - "8001:8000"  # Use 8001 instead of 8000
```

## 📊 Monitoring

### Health checks
```bash
# FastAPI
curl http://localhost:8000/api/health

# Via Nginx
curl http://localhost/api/health
```

### Container stats
```bash
docker stats
```

### Logs
```bash
# Live logs
docker-compose logs -f

# Last 100 lines
docker-compose logs --tail=100
```

## 🚀 Production Deployment

### 1. Use environment-specific configs
```bash
# Production
docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d
```

### 2. Enable SSL (Nginx)
```nginx
# Uncomment SSL server block in nginx.conf
# Add SSL certificates to ./ssl/ directory
```

### 3. Set proper environment variables
```env
DEBUG=False
ALLOWED_ORIGINS=https://yoursite.com
APP_SECRET=<strong-random-secret>
```

### 4. Use Docker secrets (recommended)
```yaml
secrets:
  openai_api_key:
    file: ./secrets/openai_api_key.txt
```

### 5. Enable monitoring
- Add Prometheus/Grafana for metrics
- Configure log aggregation (ELK, Loki)

## 🔒 Security

- Never commit `.env` file
- Use Docker secrets for sensitive data
- Set `ALLOWED_ORIGINS` to specific domains
- Enable SSL/TLS in production
- Use firewall rules to restrict access
- Regularly update images

## 🧹 Cleanup

### Remove containers and volumes
```bash
docker-compose down -v
```

### Remove images
```bash
docker-compose down --rmi all
```

### Clean everything
```bash
docker system prune -a --volumes
```

## 📝 Notes

- **Development**: Use `docker-compose up` (foreground) for easier debugging
- **Production**: Use `docker-compose up -d` (background) and configure logging
- **Scaling**: Use `docker-compose up --scale fastapi=3` for multiple instances
- **Updates**: Pull latest images with `docker-compose pull`

