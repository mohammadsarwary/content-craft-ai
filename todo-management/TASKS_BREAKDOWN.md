# Detailed Tasks Breakdown — ContentCraft AI

This file contains granular, actionable tasks for each component.

---

## 📦 WordPress Plugin Tasks

### 1. Main Plugin File (`contentcraft-ai.php`)

- [ ] Add WordPress plugin header with metadata
- [ ] Define constants (VERSION, PLUGIN_DIR, PLUGIN_URL)
- [ ] Check PHP version requirement (>= 7.4)
- [ ] Check WordPress version requirement (>= 5.8)
- [ ] Register activation hook
- [ ] Register deactivation hook
- [ ] Load autoloader
- [ ] Initialize main plugin class
- [ ] Add text domain loading for i18n

**Acceptance Criteria**:
- Plugin activates without errors
- Admin notice if requirements not met
- Constants accessible throughout plugin

---

### 2. Autoloader (`includes/class-autoloader.php`)

- [ ] Implement PSR-4 autoloader
- [ ] Map `ContentCraft\` namespace to `includes/`
- [ ] Handle class name to file path conversion
- [ ] Register with `spl_autoload_register()`

**Acceptance Criteria**:
- Classes auto-loaded without manual includes
- Follows WordPress naming conventions

---

### 3. Admin Class (`includes/class-admin.php`)

- [ ] Register admin menu with `add_menu_page()`
- [ ] Add submenu pages (Settings, Content Studio, Bulk, Brand Voice, Dashboard)
- [ ] Enqueue admin CSS/JS with proper dependencies
- [ ] Add admin notices for errors/success
- [ ] Implement Settings API
  - [ ] Register settings with `register_setting()`
  - [ ] Add settings sections
  - [ ] Add settings fields (provider, API URL, secret, model, tone, language)
  - [ ] Sanitize callbacks for each field
  - [ ] Render settings page template
- [ ] Add capability checks (`manage_options`)
- [ ] Implement "Test Connection" AJAX handler

**Acceptance Criteria**:
- Menu appears in WordPress admin
- Settings save and retrieve correctly
- Test connection button validates FastAPI connectivity
- Secrets partially masked in UI

---

### 4. REST API (`includes/class-rest.php`)

- [ ] Register namespace `contentcraft/v1`
- [ ] Register routes:
  - [ ] `POST /content/generate`
  - [ ] `POST /product/generate`
  - [ ] `POST /image/analyze`
  - [ ] `POST /seo/optimize`
  - [ ] `POST /brand/train`
  - [ ] `POST /bulk/process`
  - [ ] `GET /settings/test`
- [ ] Implement permission callbacks for each route
  - Content: `edit_posts`
  - Settings: `manage_options`
  - Product: `edit_products` (WooCommerce)
- [ ] Validate and sanitize request parameters
- [ ] Call API client for each endpoint
- [ ] Return `WP_REST_Response` with proper status codes
- [ ] Handle errors gracefully
- [ ] Add rate limiting per user

**Acceptance Criteria**:
- Routes accessible via `/wp-json/contentcraft/v1/*`
- Nonce validation works
- Permission checks prevent unauthorized access
- Error responses are user-friendly

---

### 5. API Client (`includes/class-api-client.php`)

- [ ] Create method `post($endpoint, $body)`
- [ ] Retrieve FastAPI URL from settings
- [ ] Retrieve Bearer token from settings
- [ ] Use `wp_remote_post()` with:
  - [ ] Timeout (30 seconds)
  - [ ] Headers (Authorization, Content-Type)
  - [ ] Body (JSON encoded)
- [ ] Handle `WP_Error` from `wp_remote_post()`
- [ ] Parse JSON response
- [ ] Validate response structure
- [ ] Log requests to custom table
- [ ] Implement retry logic (max 2 retries with exponential backoff)
- [ ] Cache responses (transients with hash key)

**Acceptance Criteria**:
- Successful requests to FastAPI
- Errors logged and returned to caller
- Cache reduces duplicate requests
- Timeouts handled gracefully

---

### 6. Content Generation (`includes/class-content.php`)

- [ ] Method `generate($params)`:
  - [ ] Validate required params (topic, keywords)
  - [ ] Get brand profile from settings if enabled
  - [ ] Construct request payload
  - [ ] Call API client `post('/api/content/generate', $payload)`
  - [ ] Validate response JSON structure
  - [ ] Sanitize HTML output with `wp_kses_post()`
  - [ ] Return structured data
- [ ] Method `insert_to_editor($content, $post_id)`:
  - [ ] Update post content
  - [ ] Update post meta (SEO title, description, slug)
  - [ ] Insert schema to post meta
- [ ] Add filter `contentcraft_generated_content`
- [ ] Add action `contentcraft_content_generated`

**Acceptance Criteria**:
- Valid content generated from API
- Content insertable into Gutenberg/Classic editor
- Filters/actions allow extensions

---

### 7. WooCommerce Integration (`includes/class-woocommerce.php`)

- [ ] Check if WooCommerce active
- [ ] Add meta box to product editor
- [ ] Render form in meta box:
  - [ ] Category dropdown
  - [ ] Attributes input
  - [ ] Features textarea
  - [ ] USP textarea
  - [ ] Keywords input
  - [ ] Tone select
  - [ ] Language select
  - [ ] Sections checkboxes
- [ ] Handle AJAX submission
- [ ] Method `generate_product_content($product_id, $params)`:
  - [ ] Get product object
  - [ ] Extract existing data if available
  - [ ] Call API client
  - [ ] Apply generated content to product:
    - [ ] Short description
    - [ ] Long description
    - [ ] Meta (`_yoast_wpseo_title`, etc.)
    - [ ] Tags
  - [ ] Log generation
- [ ] Add action on product save to optionally auto-generate

**Acceptance Criteria**:
- Meta box appears on product edit page
- Generated content applies to correct WooCommerce fields
- Works with simple and variable products
- Doesn't overwrite without confirmation

---

### 8. Bulk Generation (`includes/class-bulk.php`)

- [ ] Create admin page with product selection
- [ ] Add filters (category, date, missing content)
- [ ] AJAX handler for chunked processing:
  - [ ] Accept array of product IDs
  - [ ] Process in batches of 10
  - [ ] Return progress after each batch
  - [ ] Log each product (success/failure)
- [ ] Store batch state in transient (for resume)
- [ ] Generate CSV report
- [ ] Add ability to cancel processing

**Acceptance Criteria**:
- UI shows progress bar
- Processing doesn't timeout
- Failed products clearly indicated
- Report downloadable

---

### 9. SEO Tools (`includes/class-seo.php`)

- [ ] Method `optimize($content, $keywords)`:
  - [ ] Call FastAPI `/api/seo/optimize`
  - [ ] Parse suggestions
  - [ ] Return structured data
- [ ] Method `apply_meta($post_id, $meta)`:
  - [ ] Update post meta for SEO plugins (Yoast, RankMath)
  - [ ] Update post slug if needed
- [ ] Method `insert_schema($post_id, $schema_json)`:
  - [ ] Store in post meta
  - [ ] Add filter to output in `<head>`
- [ ] Method `suggest_internal_links($content)`:
  - [ ] Query similar posts by keywords
  - [ ] Return anchor suggestions

**Acceptance Criteria**:
- SEO meta applied correctly
- Schema outputs valid JSON-LD
- Internal links contextually relevant

---

### 10. Brand Voice (`includes/class-brand-voice.php`)

- [ ] Method `train($num_samples, $post_types, $date_range)`:
  - [ ] Query recent posts
  - [ ] Extract title, excerpt, content
  - [ ] Send to FastAPI `/api/brand/train`
  - [ ] Store profile in options
- [ ] Method `get_profile()`:
  - [ ] Retrieve from options
  - [ ] Return null if not trained
- [ ] Method `apply_to_prompt($base_prompt)`:
  - [ ] Merge brand profile into prompt
  - [ ] Return enhanced prompt

**Acceptance Criteria**:
- Training analyzes minimum 10 posts
- Profile stored as JSON
- Can be toggled on/off
- Applied automatically to generations

---

### 11. Alt-Text (`includes/class-alttext.php`)

- [ ] Add "Generate Alt-Text" button to media editor
- [ ] AJAX handler `generate_alttext($attachment_id)`:
  - [ ] Get image URL
  - [ ] Call FastAPI `/api/image/analyze`
  - [ ] Update attachment meta `_wp_attachment_image_alt`
- [ ] Bulk alt-text generation:
  - [ ] Query attachments without alt-text
  - [ ] Process in batches

**Acceptance Criteria**:
- Alt-text under 125 characters
- Descriptive and SEO-friendly
- Bulk processing doesn't timeout

---

### 12. Logger (`includes/class-logger.php`)

- [ ] Create custom table on activation:
  ```sql
  CREATE TABLE wp_contentcraft_logs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    type VARCHAR(50),
    target_id BIGINT,
    tokens_used INT,
    latency_ms INT,
    status VARCHAR(20),
    error_message TEXT,
    created_at DATETIME
  )
  ```
- [ ] Method `log($type, $target_id, $tokens, $latency, $status, $error)`:
  - [ ] Insert into table
- [ ] Method `get_stats($user_id, $date_range)`:
  - [ ] Query logs
  - [ ] Return aggregated data
- [ ] Dashboard widget with stats

**Acceptance Criteria**:
- Table created without errors
- Logs queryable for analytics
- Dashboard shows usage stats

---

### 13. Helpers (`includes/helpers.php`)

- [ ] `contentcraft_sanitize_text($text)` — wrapper for `sanitize_text_field`
- [ ] `contentcraft_sanitize_array($array)` — recursive sanitize
- [ ] `contentcraft_get_option($key, $default)` — retrieve plugin option
- [ ] `contentcraft_update_option($key, $value)` — update plugin option
- [ ] `contentcraft_is_woocommerce_active()` — check WC
- [ ] `contentcraft_user_can_generate()` — capability check
- [ ] `contentcraft_format_tokens($tokens)` — format number

**Acceptance Criteria**:
- Functions reusable across plugin
- Follow WordPress naming conventions

---

### 14. Hooks (`includes/hooks.php`)

- [ ] Register all actions and filters in one place:
  - [ ] `add_action('admin_menu', [Admin, 'register_menu'])`
  - [ ] `add_action('rest_api_init', [REST, 'register_routes'])`
  - [ ] `add_action('admin_enqueue_scripts', [Admin, 'enqueue_assets'])`
  - [ ] `add_filter('contentcraft_generated_content', ...)`
  
**Acceptance Criteria**:
- Centralized hook registration
- Easy to see all registered hooks

---

## 🐍 FastAPI Backend Tasks

### 1. Main App (`app/main.py`)

- [ ] Initialize FastAPI app
- [ ] Add CORS middleware (restrict to WordPress domain)
- [ ] Add authentication middleware
- [ ] Add rate limiting middleware
- [ ] Include routers (content, product, seo, brand, image)
- [ ] Add global exception handler
- [ ] Add health check endpoint `/api/health`
- [ ] Configure logging

**Acceptance Criteria**:
- App starts with `uvicorn app.main:app`
- Health endpoint returns 200
- CORS allows WordPress domain only

---

### 2. Authentication (`app/deps/auth.py`)

- [ ] Function `verify_token(token: str)`:
  - [ ] Compare with `APP_SECRET` env var
  - [ ] OR decode JWT token
  - [ ] Raise HTTPException if invalid
- [ ] Dependency `get_current_token`:
  - [ ] Extract from `Authorization` header
  - [ ] Call `verify_token()`
  - [ ] Return token or raise 401

**Acceptance Criteria**:
- Invalid tokens rejected with 401
- Valid tokens allow access
- Works as FastAPI dependency

---

### 3. Content Router (`app/routers/content.py`)

- [ ] Route `POST /api/content/generate`:
  - [ ] Pydantic model `ContentRequest`
  - [ ] Validate required fields
  - [ ] Call `ContentService.generate()`
  - [ ] Return `ContentResponse`
- [ ] Handle LLM errors gracefully
- [ ] Log request/response metadata

**Acceptance Criteria**:
- Returns valid JSON
- Handles timeouts
- Response matches schema

---

### 4. Product Router (`app/routers/product.py`)

- [ ] Route `POST /api/product/generate`:
  - [ ] Pydantic model `ProductRequest`
  - [ ] Call `ProductService.generate()`
  - [ ] Return `ProductResponse`

**Acceptance Criteria**:
- Generates all product sections
- FAQs are realistic
- Cross-sell suggestions relevant

---

### 5. Image Router (`app/routers/image.py`)

- [ ] Route `POST /api/image/analyze`:
  - [ ] Pydantic model `ImageRequest` (image_url, language)
  - [ ] Download image or pass URL to vision model
  - [ ] Call OpenAI GPT-4 Vision or equivalent
  - [ ] Extract description, features, alt-text
  - [ ] Return `ImageResponse`

**Acceptance Criteria**:
- Alt-text under 125 chars
- Description accurate
- Handles invalid images

---

### 6. SEO Router (`app/routers/seo.py`)

- [ ] Route `POST /api/seo/optimize`:
  - [ ] Pydantic model `SEORequest`
  - [ ] Analyze content for keyword density
  - [ ] Generate SEO title (< 60 chars)
  - [ ] Generate meta description (140-160 chars)
  - [ ] Suggest slug
  - [ ] Suggest headings
  - [ ] Generate schema JSON-LD
  - [ ] Return `SEOResponse`

**Acceptance Criteria**:
- Titles within length limits
- Schema validates at schema.org
- Suggestions actionable

---

### 7. Brand Router (`app/routers/brand.py`)

- [ ] Route `POST /api/brand/train`:
  - [ ] Pydantic model `BrandTrainRequest` (samples list)
  - [ ] Call `BrandVoiceService.analyze()`
  - [ ] Extract tone, sentence length, vocabulary, phrases
  - [ ] Generate prompt template
  - [ ] Return `BrandProfileResponse`

**Acceptance Criteria**:
- Profile captures writing style
- Minimum 10 samples required
- Prompt template reusable

---

### 8. LLM Provider (`app/services/llm_provider.py`)

- [ ] Abstract class `LLMProvider`:
  - [ ] Method `generate(prompt, **kwargs) -> str`
- [ ] Class `OpenAIProvider`:
  - [ ] Use `openai` library
  - [ ] Set API key from env
  - [ ] Call `chat.completions.create()`
  - [ ] Parse response
  - [ ] Handle rate limits
- [ ] Class `AnthropicProvider`:
  - [ ] Use `anthropic` library
  - [ ] Similar implementation
- [ ] Class `OllamaProvider`:
  - [ ] HTTP requests to Ollama API
  - [ ] Handle local model
- [ ] Factory function `get_provider(name: str) -> LLMProvider`

**Acceptance Criteria**:
- Providers interchangeable
- Errors handled per provider
- Supports structured output (JSON mode)

---

### 9. Prompts (`app/services/prompts.py`)

- [ ] Function `get_content_prompt(params) -> str`:
  - [ ] Template with placeholders
  - [ ] System message: "Return only JSON..."
  - [ ] Include brand profile if provided
  - [ ] Specify output structure
- [ ] Function `get_product_prompt(params) -> str`:
  - [ ] Template for products
  - [ ] Include attributes, features, USP
- [ ] Function `get_seo_prompt(content, keywords) -> str`
- [ ] Function `get_brand_analysis_prompt(samples) -> str`
- [ ] Function `get_image_analysis_prompt(image_context) -> str`

**Acceptance Criteria**:
- Prompts enforce JSON output
- Clear instructions to LLM
- Brand voice integrated when available

---

### 10. Services (Content, Product, SEO, Brand, Image)

Each service follows pattern:
- [ ] Method `generate(request_data) -> response_data`:
  - [ ] Construct prompt using templates
  - [ ] Get LLM provider
  - [ ] Call provider.generate()
  - [ ] Parse and validate JSON response
  - [ ] Handle JSON parsing errors
  - [ ] Apply post-processing (e.g., trim lengths)
  - [ ] Return structured data

**Acceptance Criteria**:
- Each service independently testable
- Errors propagate correctly
- Responses validated before returning

---

### 11. Schemas (`app/models/schemas.py`)

Define Pydantic models for:
- [ ] `ContentRequest` / `ContentResponse`
- [ ] `ProductRequest` / `ProductResponse`
- [ ] `ImageRequest` / `ImageResponse`
- [ ] `SEORequest` / `SEOResponse`
- [ ] `BrandTrainRequest` / `BrandProfileResponse`
- [ ] `ErrorResponse`
- [ ] Common sub-models (MetaData, FAQ, InternalLink)

**Acceptance Criteria**:
- All fields typed
- Validation rules applied (min/max length)
- Examples in docstrings

---

### 12. Cache (`app/utils/cache.py`)

- [ ] Class `Cache`:
  - [ ] Method `get(key) -> Optional[str]`
  - [ ] Method `set(key, value, ttl)`
  - [ ] Redis implementation if available
  - [ ] In-memory dict fallback
- [ ] Function `cache_key(params) -> str`:
  - [ ] Hash of request params
  - [ ] Consistent for same input

**Acceptance Criteria**:
- Cache hits reduce LLM calls
- TTL respected
- Redis optional (graceful fallback)

---

### 13. Environment Config (`.env.example`)

```env
# Provider: openai | anthropic | ollama | custom
PROVIDER=openai

# API Keys
OPENAI_API_KEY=sk-...
ANTHROPIC_API_KEY=sk-ant-...

# Ollama (if using local)
OLLAMA_HOST=http://localhost:11434

# Model
MODEL_NAME=gpt-4o-mini

# Auth
APP_SECRET=shared-secret-with-wordpress

# Cache
REDIS_URL=redis://localhost:6379
CACHE_TTL=600

# Rate Limiting
RATE_LIMIT_PER_MINUTE=60
```

---

### 14. Tests (`tests/`)

- [ ] `test_routers.py`:
  - [ ] Test each endpoint with valid input
  - [ ] Test with invalid input
  - [ ] Test authentication
- [ ] `test_services.py`:
  - [ ] Mock LLM responses
  - [ ] Test prompt construction
  - [ ] Test JSON parsing
- [ ] `test_prompts.py`:
  - [ ] Validate prompt templates
- [ ] `conftest.py`:
  - [ ] Fixtures for test client, mock provider

**Acceptance Criteria**:
- `pytest` runs without failures
- Coverage > 80%
- All critical paths tested

---

## 🐳 Infrastructure Tasks

### Docker Compose (`infra/docker-compose.yml`)

```yaml
version: '3.8'
services:
  fastapi:
    build: ../backend
    ports:
      - "8000:8000"
    environment:
      - OPENAI_API_KEY=${OPENAI_API_KEY}
      - APP_SECRET=${APP_SECRET}
    depends_on:
      - redis
  
  redis:
    image: redis:alpine
    ports:
      - "6379:6379"
  
  nginx:
    image: nginx:alpine
    ports:
      - "80:80"
    volumes:
      - ./nginx.conf:/etc/nginx/nginx.conf
    depends_on:
      - fastapi
```

**Acceptance Criteria**:
- `docker-compose up` starts all services
- FastAPI accessible at http://localhost:8000
- Redis connectable

---

### Dockerfile (`backend/Dockerfile`)

```dockerfile
FROM python:3.11-slim
WORKDIR /app
COPY pyproject.toml poetry.lock ./
RUN pip install poetry && poetry install --no-dev
COPY app ./app
CMD ["poetry", "run", "uvicorn", "app.main:app", "--host", "0.0.0.0", "--port", "8000"]
```

---

### Nginx Config (`infra/nginx.conf`)

```nginx
events {
    worker_connections 1024;
}

http {
    upstream fastapi {
        server fastapi:8000;
    }

    server {
        listen 80;

        location /api/ {
            proxy_pass http://fastapi;
            proxy_set_header Host $host;
            proxy_set_header X-Real-IP $remote_addr;
        }
    }
}
```

---

## 🧪 Testing Tasks

### PHP Tests

- [ ] Setup PHPUnit configuration
- [ ] Test sanitization functions
- [ ] Test capability checks
- [ ] Test REST endpoint permissions
- [ ] Mock API client responses
- [ ] Test WooCommerce integration (if WC active)

---

### Python Tests

- [ ] Setup pytest configuration
- [ ] Test authentication middleware
- [ ] Test router endpoints
- [ ] Test LLM provider abstraction
- [ ] Test prompt templates (validate structure)
- [ ] Test Pydantic model validation

---

## 🚀 CI/CD Tasks

### GitHub Actions (`.github/workflows/ci.yml`)

```yaml
name: CI

on: [push, pull_request]

jobs:
  php:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.0'
      - run: composer install
      - run: vendor/bin/phpcs --standard=WordPress plugin/
      - run: vendor/bin/phpunit

  python:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - uses: actions/setup-python@v4
        with:
          python-version: '3.11'
      - run: pip install poetry
      - run: cd backend && poetry install
      - run: cd backend && poetry run pytest
```

---

## 📝 Documentation Tasks

- [x] README.md
- [x] AI_AGENT_INSTRUCTIONS.md
- [x] Architecture documentation
- [x] API contracts
- [x] Demo scripts
- [ ] Contributing guidelines
- [ ] Changelog
- [ ] WordPress.org readme.txt
- [ ] Video demo scripts
- [ ] User manual (PDF)

---

## 🎨 UI/UX Tasks

- [ ] Design admin menu icon
- [ ] Create consistent color scheme (use WordPress admin colors)
- [ ] Add loading spinners for async operations
- [ ] Add success/error notifications (use WordPress admin notices)
- [ ] Progress bars for bulk operations
- [ ] Tooltips for settings
- [ ] Preview modals for generated content
- [ ] Responsive design for mobile

---

**This breakdown will be updated as tasks are completed.**

**Last Updated**: October 16, 2024


