# AI Agent Instructions for ContentCraft AI Project

## 📋 Project Overview

**Project Name**: ContentCraft AI  
**Type**: WordPress Plugin + FastAPI Backend  
**Purpose**: AI-powered content generation for WordPress posts, pages, and WooCommerce products  
**Architecture**: Monorepo with PHP plugin + Python microservice  

---

## 🎯 Your Role as AI Agent

You are working on a **production-grade, enterprise-level WordPress plugin** that integrates with FastAPI for AI content generation. This is NOT a prototype — every file must be complete, secure, and follow WordPress and Python best practices.

### Key Responsibilities:

1. **Write production-ready code** (not skeleton/placeholder code)
2. **Follow WordPress Coding Standards** (PHPCS compatible)
3. **Implement proper security** (nonces, sanitization, escaping, capabilities)
4. **Create complete functionality** (all hooks, filters, UI, API routes)
5. **Maintain consistency** across the monorepository

---

## 📁 Project Structure Explained

### `/plugin` — WordPress Plugin (PHP)

This is the main WordPress plugin that users install.

#### Core Files:

- **`contentcraft-ai.php`**: Main plugin file with WordPress header, bootstrap, activation/deactivation hooks
- **`includes/class-autoloader.php`**: PSR-4 autoloader for plugin classes
- **`includes/class-admin.php`**: Admin menu, settings pages, UI rendering
- **`includes/class-rest.php`**: WordPress REST API endpoints registration
- **`includes/class-api-client.php`**: HTTP client for communicating with FastAPI (server-to-server)
- **`includes/class-content.php`**: Content generation logic for posts/pages
- **`includes/class-woocommerce.php`**: WooCommerce integration (product content)
- **`includes/class-bulk.php`**: Bulk generation processor with chunked processing
- **`includes/class-seo.php`**: SEO tools (meta tags, schema, internal links)
- **`includes/class-brand-voice.php`**: Brand voice training and application
- **`includes/class-alttext.php`**: Image-to-text and alt-text generation
- **`includes/class-logger.php`**: Logging and analytics
- **`includes/helpers.php`**: Utility functions (sanitize, validate, format)
- **`includes/hooks.php`**: WordPress action/filter hooks registry

#### Admin UI (`admin/`):

- **`views/`**: HTML templates for admin pages (use WordPress admin styles)
- **`assets/js/`**: Vanilla JavaScript for admin interactions (NO React/Vue)
- **`assets/css/`**: Admin styles (minimal, use WordPress admin classes)

#### Languages (`languages/`):

- **`.pot` file**: Translation template (textdomain: `contentcraft-ai`)

### `/backend` — FastAPI Service (Python)

This is the AI engine that processes requests from WordPress.

#### Structure:

```
backend/
├── app/
│   ├── main.py                    # FastAPI app initialization, middleware, CORS
│   ├── deps/
│   │   └── auth.py                # JWT/Bearer token authentication
│   ├── routers/
│   │   ├── content.py             # /api/content/* endpoints
│   │   ├── product.py             # /api/product/* endpoints
│   │   ├── seo.py                 # /api/seo/* endpoints
│   │   ├── brand.py               # /api/brand/* endpoints
│   │   └── image.py               # /api/image/* endpoints
│   ├── services/
│   │   ├── llm_provider.py        # Abstraction for OpenAI/Anthropic/Ollama
│   │   ├── prompts.py             # Prompt templates for different content types
│   │   ├── seo_optimizer.py       # SEO analysis and optimization
│   │   ├── brand_voice.py         # Brand voice extraction and application
│   │   └── image_analyzer.py      # Image analysis (vision models)
│   ├── models/
│   │   └── schemas.py             # Pydantic models for request/response
│   └── utils/
│       ├── cache.py               # Redis/in-memory caching
│       └── logger.py              # Structured logging
├── tests/
│   ├── test_routers.py
│   └── test_services.py
├── pyproject.toml                 # Poetry/uv dependencies
├── .env.example                   # Environment variables template
└── README.md
```

### `/infra` — Infrastructure

- **`docker-compose.yml`**: FastAPI service + Redis + Nginx
- **`nginx.conf`**: Reverse proxy configuration
- **Dockerfile**: For building FastAPI image

### `/docs` — Documentation

- **`architecture.md`**: System architecture, data flow diagrams
- **`api-contracts.md`**: Detailed API request/response schemas
- **`demo-scripts.md`**: Step-by-step demo scenarios
- **`security.md`**: Security best practices and threat model

### `/todo-management` — Project Management

- **`tasks/`**: TODO files organized by feature/milestone
- **`progress.md`**: Development progress tracking

---

## 🔑 Key Technical Requirements

### WordPress Plugin Requirements:

1. **Security First**:
   - All user inputs MUST be sanitized: `sanitize_text_field()`, `sanitize_textarea_field()`, `absint()`
   - All outputs MUST be escaped: `esc_html()`, `esc_attr()`, `wp_kses_post()`
   - All admin actions MUST verify nonces: `check_ajax_referer()`, `wp_verify_nonce()`
   - All operations MUST check capabilities: `current_user_can('manage_options')`

2. **WordPress Standards**:
   - Use WordPress APIs: `wp_remote_post()`, `wp_options`, `add_action()`, `add_filter()`
   - Follow naming conventions: `contentcraft_` prefix for functions, `contentcraft-ai` for textdomain
   - Use WordPress coding style: tabs (not spaces), Yoda conditions where applicable

3. **REST API**:
   - Namespace: `contentcraft/v1`
   - Every endpoint MUST have `permission_callback`
   - Use `WP_REST_Request` and `WP_REST_Response`
   - Return proper HTTP status codes

4. **Internationalization**:
   - Wrap all user-facing strings: `__('Text', 'contentcraft-ai')`, `_e('Text', 'contentcraft-ai')`
   - Use `wp_localize_script()` for passing translatable strings to JS

5. **WooCommerce Integration**:
   - Check if WooCommerce is active: `class_exists('WooCommerce')`
   - Use WooCommerce hooks: `woocommerce_product_options_general_product_data`, `woocommerce_process_product_meta`
   - Update product meta properly: `update_post_meta($product_id, '_key', $value)`

### FastAPI Backend Requirements:

1. **API Design**:
   - Use Pydantic models for all request/response validation
   - Return consistent JSON structure: `{"success": bool, "data": {}, "error": null}`
   - Include proper HTTP status codes
   - Add request/response examples in docstrings

2. **Security**:
   - Validate Bearer token from WordPress: `verify_jwt_token()`
   - Set CORS properly (only allow WordPress origin)
   - Rate limiting on all endpoints
   - Never log sensitive data (API keys, tokens)

3. **LLM Provider Abstraction**:
   ```python
   class LLMProvider(ABC):
       @abstractmethod
       async def generate(self, prompt: str, **kwargs) -> str:
           pass
   
   class OpenAIProvider(LLMProvider):
       # Implementation
   
   class AnthropicProvider(LLMProvider):
       # Implementation
   
   class OllamaProvider(LLMProvider):
       # Implementation
   ```

4. **Prompt Engineering**:
   - All prompts MUST return valid JSON only
   - Include system message: "You are an expert SEO content writer. Return ONLY valid JSON. No markdown, no explanations."
   - Use structured output when possible (OpenAI's `response_format`)
   - Validate JSON before returning to WordPress

5. **Error Handling**:
   - Catch all exceptions and return user-friendly messages
   - Log detailed errors server-side
   - Implement retry logic with exponential backoff for API calls
   - Handle timeouts gracefully

---

## 🔄 Data Flow

### Typical Request Flow:

1. **User** interacts with WordPress admin UI
2. **JavaScript** makes AJAX request to WordPress REST API with nonce
3. **WordPress REST endpoint** validates nonce, checks capabilities
4. **WordPress** proxies request to **FastAPI** (server-to-server with Bearer token)
5. **FastAPI** validates token, calls appropriate service
6. **Service** calls LLM provider (OpenAI/Anthropic/Ollama)
7. **LLM** returns JSON response
8. **FastAPI** validates JSON, returns to WordPress
9. **WordPress** sanitizes response, returns to JavaScript
10. **JavaScript** updates UI with generated content

### Security Chain:

```
User Browser → (AJAX + Nonce) → WP REST API → (Check Nonce + Capability) → 
WP API Client → (Bearer Token) → FastAPI → (Verify Token) → LLM Provider
```

**CRITICAL**: API keys (OpenAI, Anthropic) are ONLY stored in FastAPI backend, NEVER exposed to WordPress or browser.

---

## 🛠️ Development Workflow

### When Adding a New Feature:

1. **Define** the feature in a TODO task
2. **Update** API contracts in `docs/api-contracts.md`
3. **Implement** FastAPI endpoint first (with tests)
4. **Implement** WordPress REST endpoint
5. **Create** admin UI if needed
6. **Add** security checks (nonces, capabilities, sanitization)
7. **Test** end-to-end flow
8. **Document** in relevant docs
9. **Update** README if needed

### Code Style:

**PHP**:
```php
<?php
// Use WordPress coding standards
function contentcraft_my_function( $param ) {
    if ( ! current_user_can( 'manage_options' ) ) {
        return new WP_Error( 'forbidden', __( 'Permission denied', 'contentcraft-ai' ) );
    }
    
    $sanitized = sanitize_text_field( $param );
    return esc_html( $sanitized );
}
```

**Python**:
```python
from fastapi import APIRouter, Depends, HTTPException
from app.models.schemas import ContentRequest, ContentResponse

router = APIRouter(prefix="/api/content", tags=["content"])

@router.post("/generate", response_model=ContentResponse)
async def generate_content(
    request: ContentRequest,
    token: str = Depends(verify_token)
) -> ContentResponse:
    """Generate SEO-optimized content."""
    try:
        # Implementation
        return ContentResponse(success=True, data=result)
    except Exception as e:
        logger.error(f"Content generation failed: {e}")
        raise HTTPException(status_code=500, detail="Generation failed")
```

---

## 🧪 Testing Requirements

### PHP Tests (PHPUnit):
- Test all sanitization functions
- Test permission checks
- Test REST endpoint responses
- Mock external API calls

### Python Tests (pytest):
- Test all routers with mock LLM responses
- Test Pydantic model validation
- Test authentication middleware
- Test prompt template outputs

---

## 📊 API Contracts

### Example: Content Generation

**WordPress calls**:
```
POST /wp-json/contentcraft/v1/content/generate
Headers: X-WP-Nonce: abc123
Body: {
  "topic": "Best WordPress Plugins 2024",
  "keywords": ["wordpress", "plugins", "2024"],
  "tone": "professional",
  "language": "en"
}
```

**FastAPI receives** (proxied from WP):
```
POST /api/content/generate
Headers: Authorization: Bearer xyz789
Body: {
  "topic": "Best WordPress Plugins 2024",
  "keywords": ["wordpress", "plugins", "2024"],
  "tone": "professional",
  "length": "medium",
  "language": "en",
  "brand_profile": {...}
}
```

**FastAPI returns**:
```json
{
  "success": true,
  "data": {
    "title": "Top 10 WordPress Plugins to Boost Your Site in 2024",
    "excerpt": "Discover the most powerful WordPress plugins...",
    "body_html": "<h2>Introduction</h2><p>...</p>",
    "meta": {
      "seo_title": "Best WordPress Plugins 2024 | Complete Guide",
      "meta_desc": "Explore the top WordPress plugins for 2024...",
      "slug": "best-wordpress-plugins-2024"
    },
    "headings": ["Introduction", "Security Plugins", "SEO Plugins"],
    "schema_ld_json": "{...}"
  },
  "error": null
}
```

---

## 🚨 Common Pitfalls to Avoid

1. **Never** expose API keys to frontend JavaScript
2. **Never** skip nonce verification in AJAX handlers
3. **Never** trust user input — always sanitize
4. **Never** output raw data — always escape
5. **Never** assume WooCommerce is active — always check
6. **Always** return valid JSON from FastAPI (no markdown wrappers)
7. **Always** handle timeouts and network errors
8. **Always** validate LLM responses before sending to WordPress
9. **Always** use textdomain in translation functions
10. **Always** check capabilities before sensitive operations

---

## 📚 Reference Documentation

- [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/)
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/)
- [WooCommerce Developer Docs](https://woocommerce.com/document/create-a-plugin/)
- [FastAPI Documentation](https://fastapi.tiangolo.com/)
- [Pydantic Documentation](https://docs.pydantic.dev/)

---

## 🎯 Current Development Status

Check `/todo-management/progress.md` for current status and next steps.

When working on tasks:
1. Read the relevant sections above
2. Check existing code for patterns
3. Follow the established conventions
4. Test thoroughly
5. Update documentation
6. Mark TODO as complete

---

## 🤝 Communication Protocol

When asking for clarification:
- Reference specific file paths
- Include relevant code snippets
- Explain what you're trying to achieve
- Suggest your proposed solution

When reporting progress:
- Mention which TODO item you completed
- List files created/modified
- Note any deviations from the plan
- Highlight any blockers

---

**Remember**: This is a production-grade plugin that will be used by real WordPress users. Quality, security, and user experience are paramount.

Good luck! 🚀

