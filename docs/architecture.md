# ContentCraft AI — Architecture Documentation

## 🏗️ System Architecture

### High-Level Overview

```
┌─────────────────────────────────────────────────────────────┐
│                    WordPress Admin UI                        │
│  ┌──────────────┐  ┌─────────────┐  ┌──────────────┐       │
│  │Content Studio│  │Product Writer│  │Bulk Generator│       │
│  └──────┬───────┘  └──────┬──────┘  └──────┬───────┘       │
│         │                  │                 │                │
│         └──────────────────┴─────────────────┘                │
│                            │                                   │
│                    ┌───────▼────────┐                         │
│                    │  WP REST API   │                         │
│                    │ (contentcraft) │                         │
│                    └───────┬────────┘                         │
└────────────────────────────┼──────────────────────────────────┘
                             │ (Nonce + Capability Check)
                             │
                             ▼
                    ┌────────────────┐
                    │  API Client    │ (Server-side PHP)
                    │ (Bearer Token) │
                    └────────┬───────┘
                             │ HTTPS
                             ▼
┌─────────────────────────────────────────────────────────────┐
│                    FastAPI Backend                           │
│                                                               │
│  ┌───────────────┐  ┌────────────────┐  ┌─────────────┐    │
│  │ Authentication│  │    Routers     │  │   Services  │    │
│  │  Middleware   │  │ (Endpoints)    │  │   (Logic)   │    │
│  └───────┬───────┘  └────────┬───────┘  └──────┬──────┘    │
│          │                   │                   │            │
│          └───────────────────┴───────────────────┘            │
│                              │                                │
│                    ┌─────────▼──────────┐                    │
│                    │  LLM Provider      │                    │
│                    │   Abstraction      │                    │
│                    └─────────┬──────────┘                    │
│                              │                                │
│          ┌───────────────────┼───────────────────┐           │
│          │                   │                   │           │
│    ┌─────▼─────┐      ┌─────▼──────┐     ┌─────▼─────┐    │
│    │  OpenAI   │      │ Anthropic  │     │  Ollama   │    │
│    │ Provider  │      │  Provider  │     │ (Local)   │    │
│    └───────────┘      └────────────┘     └───────────┘    │
└─────────────────────────────────────────────────────────────┘
                             │
                    ┌────────▼─────────┐
                    │   Redis Cache    │
                    │   (Optional)     │
                    └──────────────────┘
```

---

## 🔄 Request Flow

### Scenario 1: Generate Blog Post Content

1. **User** opens Content Studio in WordPress admin
2. **User** fills form: topic, keywords, tone, length
3. **JavaScript** submits AJAX request to `/wp-json/contentcraft/v1/content/generate` with nonce
4. **WP REST Controller** (`class-rest.php`):
   - Validates nonce: `check_ajax_referer('wp_rest')`
   - Checks capability: `current_user_can('edit_posts')`
   - Sanitizes input: `sanitize_text_field()` on all inputs
5. **WP API Client** (`class-api-client.php`):
   - Constructs request payload with brand profile (if enabled)
   - Adds Bearer token from plugin settings
   - Calls FastAPI: `wp_remote_post('http://backend:8000/api/content/generate')`
6. **FastAPI Auth Middleware** (`deps/auth.py`):
   - Validates Bearer token
   - Checks rate limits
7. **FastAPI Router** (`routers/content.py`):
   - Validates request with Pydantic schema
   - Calls `ContentService.generate()`
8. **Content Service** (`services/llm_provider.py`):
   - Loads appropriate LLM provider (OpenAI/Anthropic/Ollama)
   - Constructs prompt from template with brand voice
   - Calls LLM API
   - Validates JSON response
   - Applies SEO optimization
9. **Response** flows back through layers:
   - FastAPI returns structured JSON
   - WordPress validates and sanitizes response
   - JavaScript receives data and updates UI
   - User previews content and inserts to editor

**Total Time**: ~2-5 seconds (depending on LLM provider and content length)

---

### Scenario 2: Bulk Product Generation

1. **User** selects 50 products in Bulk Generator
2. **JavaScript** splits into chunks of 10 products
3. **For each chunk**:
   - AJAX request to `/wp-json/contentcraft/v1/bulk/process`
   - Progress bar updates (10%, 20%, ...)
4. **WP Bulk Processor** (`class-bulk.php`):
   - Validates products exist
   - For each product: calls WooCommerce service
   - Logs success/failure for each
   - Returns chunk status
5. **FastAPI** processes each product sequentially (to respect rate limits)
6. **UI** displays final report: 45 success, 5 failed

---

## 🗄️ Data Models

### WordPress Side

#### Plugin Options
```php
[
    'provider' => 'openai',          // openai|anthropic|ollama|custom
    'api_base_url' => 'http://localhost:8000',
    'api_secret' => 'encrypted_hash',
    'model_name' => 'gpt-4o-mini',
    'default_tone' => 'professional',
    'default_language' => 'en',
    'seo_options' => [
        'enable_schema' => true,
        'suggest_internal_links' => true,
        'auto_alt_text' => false
    ],
    'rate_limit' => 60,              // requests per minute
    'cache_ttl' => 600,              // seconds
    'brand_profile' => [
        'enabled' => true,
        'tone' => 'conversational',
        'vocabulary_level' => 'intermediate',
        'sentence_length' => 'medium',
        'prompt_template' => '...'
    ]
]
```

#### Content Log Table
```sql
CREATE TABLE wp_contentcraft_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    type VARCHAR(50) NOT NULL, -- 'content', 'product', 'image', 'seo'
    target_id BIGINT UNSIGNED, -- post_id or product_id
    tokens_used INT,
    latency_ms INT,
    status VARCHAR(20), -- 'success', 'failed'
    error_message TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_id),
    INDEX idx_type (type),
    INDEX idx_created (created_at)
)
```

### FastAPI Side

#### Pydantic Schemas

**ContentRequest**:
```python
class ContentRequest(BaseModel):
    topic: str = Field(..., min_length=5, max_length=200)
    keywords: List[str] = Field(default_factory=list, max_items=10)
    tone: str = Field(default="professional")
    length: str = Field(default="medium")  # short|medium|long
    language: str = Field(default="en")
    brand_profile: Optional[Dict] = None
    sections: List[str] = Field(default_factory=lambda: ["title", "body", "meta"])
```

**ContentResponse**:
```python
class ContentResponse(BaseModel):
    success: bool
    data: Optional[ContentData] = None
    error: Optional[str] = None
    
class ContentData(BaseModel):
    title: str
    excerpt: str
    outline: List[str]
    body_html: str
    meta: MetaData
    headings: List[str]
    internal_links: List[InternalLink]
    schema_ld_json: Optional[str]
    
class MetaData(BaseModel):
    seo_title: str = Field(..., max_length=60)
    meta_desc: str = Field(..., min_length=140, max_length=160)
    slug: str
```

---

## 🔒 Security Architecture

### Authentication Chain

1. **WordPress Admin → WordPress REST API**:
   - Uses WordPress nonce mechanism
   - Validates user capabilities
   - Session-based authentication

2. **WordPress → FastAPI**:
   - Bearer token (JWT or shared secret)
   - Stored in WordPress options (encrypted)
   - Verified on FastAPI side

3. **FastAPI → LLM Providers**:
   - API keys stored in environment variables
   - Never exposed to WordPress or client
   - Rotated regularly

### Security Measures

#### WordPress Plugin:
- ✅ All inputs sanitized with WordPress functions
- ✅ All outputs escaped before rendering
- ✅ Nonce verification on all AJAX requests
- ✅ Capability checks on all operations
- ✅ No direct database queries (use WordPress APIs)
- ✅ Prepared statements when custom queries needed
- ✅ Rate limiting per user
- ✅ Audit logging of all generations

#### FastAPI:
- ✅ Token validation middleware
- ✅ CORS restricted to WordPress domain
- ✅ Rate limiting with sliding window
- ✅ Input validation with Pydantic
- ✅ No sensitive data in logs
- ✅ Timeout on all LLM requests
- ✅ Content length limits
- ✅ Structured error messages (no stack traces to client)

---

## 🚀 Performance Optimization

### Caching Strategy

#### Layer 1: Browser Cache
- Static assets (CSS/JS) with versioning
- No caching of API responses

#### Layer 2: WordPress Transients
- Cache LLM responses for identical requests
- Key: `hash(request_params)`
- TTL: 10 minutes (configurable)

#### Layer 3: Redis (Optional)
- Shared cache across multiple WordPress instances
- Store brand profiles, common prompts
- Session storage for bulk operations

### Load Handling

#### WordPress Side:
- Async processing for bulk operations
- WP-Cron for scheduled tasks
- Queue system for large batches

#### FastAPI Side:
- Async/await for all I/O operations
- Connection pooling for LLM APIs
- Background tasks for non-critical operations
- Graceful degradation on rate limit

---

## 🔌 Plugin Extension Points

### Filters

```php
// Modify content before generation
apply_filters('contentcraft_before_generate', $params);

// Modify generated content before saving
apply_filters('contentcraft_generated_content', $content, $params);

// Modify API request to FastAPI
apply_filters('contentcraft_api_request_args', $args);

// Modify API response from FastAPI
apply_filters('contentcraft_api_response', $response);

// Add custom prompt instructions
apply_filters('contentcraft_prompt_instructions', $instructions, $type);
```

### Actions

```php
// Before API call
do_action('contentcraft_before_api_request', $endpoint, $body);

// After successful generation
do_action('contentcraft_content_generated', $post_id, $content_data);

// On generation error
do_action('contentcraft_generation_failed', $error, $params);

// After brand training
do_action('contentcraft_brand_trained', $profile);
```

---

## 🌍 Multi-language Architecture

### Content Generation:
- Language parameter sent to FastAPI
- LLM generates content in target language
- Prompts include language-specific instructions

### Plugin UI:
- WordPress i18n system
- `.pot` template file
- Community translations via translate.wordpress.org

### Supported Languages:
- English (en)
- فارسی (fa)
- العربية (ar)
- Deutsch (de)
- Español (es)
- Français (fr)
- Italiano (it)
- 日本語 (ja)
- 한국어 (ko)
- 中文 (zh)

---

## 📊 Monitoring & Logging

### WordPress Logs:
```php
[2024-10-16 10:30:45] INFO User 1 generated content for post 123
[2024-10-16 10:30:47] SUCCESS Post 123 content generated (1250 tokens, 2.3s)
[2024-10-16 10:31:02] ERROR Failed to generate product 456: API timeout
```

### FastAPI Logs:
```json
{
  "timestamp": "2024-10-16T10:30:46Z",
  "level": "INFO",
  "endpoint": "/api/content/generate",
  "user_token": "hash",
  "latency_ms": 2300,
  "tokens": 1250,
  "provider": "openai",
  "model": "gpt-4o-mini"
}
```

### Metrics Tracked:
- Total generations per day/week/month
- Average latency by type
- Token consumption by user
- Error rates by endpoint
- Most used tones/languages
- Cache hit rate

---

## 🔄 Deployment Topology

### Development:
```
Localhost WordPress → http://localhost:8000 → OpenAI API
```

### Production Option 1 (Single Server):
```
WordPress (Apache/Nginx) → FastAPI (Gunicorn) → LLM APIs
         ↓                         ↓
    MySQL DB                  Redis Cache
```

### Production Option 2 (Distributed):
```
WordPress Cluster → Load Balancer → FastAPI Cluster → LLM APIs
       ↓                                    ↓
   MySQL RDS                          Redis Cluster
```

### Docker Compose (Recommended for Testing):
```yaml
services:
  wordpress:
    image: wordpress:latest
    volumes:
      - ./plugin:/var/www/html/wp-content/plugins/contentcraft-ai
  
  fastapi:
    build: ./backend
    environment:
      - OPENAI_API_KEY=${OPENAI_API_KEY}
  
  redis:
    image: redis:alpine
```

---

## 📈 Scalability Considerations

### Bottlenecks:
1. **LLM API Rate Limits**: Use multiple API keys, implement queuing
2. **WordPress Database**: Use proper indexing, cache frequently accessed data
3. **Network Latency**: Deploy FastAPI close to WordPress server

### Horizontal Scaling:
- WordPress: Standard WP scaling (load balancer, object cache)
- FastAPI: Stateless, can add workers easily
- Redis: Cluster mode for distributed cache

---

## 🛡️ Disaster Recovery

### Backup Strategy:
- WordPress database includes all settings and logs
- FastAPI is stateless (no data to backup)
- Redis cache can be rebuilt

### Failover:
- If FastAPI is down: Show user-friendly error, queue requests
- If LLM API is down: Fall back to alternative provider (if configured)
- If Redis is down: Fall back to WordPress transients

---

## 🔮 Future Enhancements

- [ ] GraphQL API alongside REST
- [ ] Real-time WebSocket updates for bulk operations
- [ ] Advanced analytics dashboard
- [ ] Plugin marketplace for custom prompts
- [ ] Multi-site network support
- [ ] Integration with popular page builders (Elementor, Divi)
- [ ] Mobile app for content approval workflow

---

**Last Updated**: October 2024  
**Version**: 1.0.0  
**Maintainer**: ContentCraft AI Team

