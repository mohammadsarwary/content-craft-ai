# API Contracts — ContentCraft AI

This document defines the complete API contracts between WordPress and FastAPI backend.

---

## 🔗 Base URLs

- **WordPress REST API**: `https://yoursite.com/wp-json/contentcraft/v1`
- **FastAPI Backend**: `http://localhost:8000/api` (configurable)

---

## 🔐 Authentication

### WordPress REST API
- **Method**: WordPress Nonce
- **Header**: `X-WP-Nonce: {nonce}`
- **Capability Required**: `edit_posts` (for content), `manage_options` (for settings)

### FastAPI
- **Method**: Bearer Token (JWT or shared secret)
- **Header**: `Authorization: Bearer {token}`
- **Token** is stored in WordPress options and sent with every request

---

## 📝 Content Generation

### 1. Generate Blog Post/Page Content

#### WordPress REST Endpoint
```http
POST /wp-json/contentcraft/v1/content/generate
X-WP-Nonce: abc123xyz
Content-Type: application/json

{
  "topic": "The Future of AI in Web Development",
  "keywords": ["AI", "web development", "automation", "2024"],
  "tone": "professional",
  "length": "medium",
  "language": "en",
  "audience": "web developers",
  "sections": ["title", "excerpt", "body", "meta"]
}
```

#### WordPress Response
```json
{
  "success": true,
  "data": {
    "title": "The Future of AI in Web Development: Trends to Watch in 2024",
    "excerpt": "Artificial intelligence is revolutionizing web development. From automated code generation to intelligent design systems, discover how AI is shaping the future of building websites.",
    "outline": [
      "Introduction to AI in Web Development",
      "Current AI Tools for Developers",
      "Automation and Productivity Gains",
      "Ethical Considerations",
      "Future Predictions"
    ],
    "body_html": "<h2>Introduction to AI in Web Development</h2>\n<p>The landscape of web development...</p>",
    "meta": {
      "seo_title": "AI in Web Development 2024 | Future Trends & Tools",
      "meta_desc": "Explore how artificial intelligence is transforming web development in 2024. Learn about AI tools, automation, and future trends for developers.",
      "slug": "future-ai-web-development-2024"
    },
    "headings": [
      "Introduction to AI in Web Development",
      "Current AI Tools for Developers",
      "GitHub Copilot",
      "ChatGPT for Code",
      "Automation and Productivity Gains"
    ],
    "internal_links": [
      {
        "anchor": "AI tools we reviewed last year",
        "url": "/best-ai-tools-2023/",
        "rationale": "Related content about AI tools"
      }
    ],
    "schema_ld_json": "{\"@context\":\"https://schema.org\",\"@type\":\"Article\",\"headline\":\"The Future of AI in Web Development\",\"author\":{\"@type\":\"Person\",\"name\":\"Site Author\"}}"
  }
}
```

---

#### FastAPI Endpoint (Called by WordPress)
```http
POST /api/content/generate
Authorization: Bearer xyz789token
Content-Type: application/json

{
  "topic": "The Future of AI in Web Development",
  "keywords": ["AI", "web development", "automation", "2024"],
  "tone": "professional",
  "length": "medium",
  "language": "en",
  "audience": "web developers",
  "brand_profile": {
    "tone": "conversational yet professional",
    "sentence_length": "medium",
    "vocabulary_level": "intermediate",
    "common_phrases": ["let's explore", "consider this", "key takeaway"]
  },
  "sections": ["title", "excerpt", "body", "meta"]
}
```

#### FastAPI Response
```json
{
  "success": true,
  "data": {
    "title": "The Future of AI in Web Development: Trends to Watch in 2024",
    "excerpt": "Artificial intelligence is revolutionizing web development...",
    "outline": ["Introduction to AI in Web Development", "Current AI Tools..."],
    "body_html": "<h2>Introduction...</h2>",
    "meta": {
      "seo_title": "AI in Web Development 2024 | Future Trends & Tools",
      "meta_desc": "Explore how artificial intelligence is transforming...",
      "slug": "future-ai-web-development-2024"
    },
    "headings": ["Introduction to AI in Web Development", "Current AI Tools..."],
    "internal_links": [
      {
        "anchor": "AI tools we reviewed last year",
        "suggested_url": "/best-ai-tools-2023/",
        "rationale": "Related content"
      }
    ],
    "schema_ld_json": "{\"@context\":\"https://schema.org\",\"@type\":\"Article\"...}"
  },
  "error": null,
  "metadata": {
    "tokens_used": 1250,
    "latency_ms": 2300,
    "model": "gpt-4o-mini",
    "cached": false
  }
}
```

---

## 🛒 WooCommerce Product Content

### 2. Generate Product Descriptions

#### WordPress REST Endpoint
```http
POST /wp-json/contentcraft/v1/product/generate
X-WP-Nonce: abc123xyz
Content-Type: application/json

{
  "product_id": 456,
  "name": "Organic Cotton T-Shirt",
  "category": "Apparel",
  "attributes": {
    "Material": "100% Organic Cotton",
    "Color": "Navy Blue",
    "Sizes": ["S", "M", "L", "XL"],
    "Care": "Machine washable"
  },
  "features": [
    "Breathable fabric",
    "Eco-friendly production",
    "Comfortable fit"
  ],
  "usp": [
    "Certified organic materials",
    "Carbon-neutral shipping",
    "30-day returns"
  ],
  "price": 29.99,
  "keywords": ["organic cotton", "sustainable fashion", "eco-friendly"],
  "tone": "friendly",
  "language": "en"
}
```

#### WordPress Response
```json
{
  "success": true,
  "data": {
    "seo_title": "Organic Cotton T-Shirt | Sustainable & Comfortable",
    "short_desc_html": "<p>Discover ultimate comfort with our 100% organic cotton t-shirt. Eco-friendly, breathable, and perfect for everyday wear.</p>",
    "long_desc_html": "<h2>Overview</h2>\n<p>Our Organic Cotton T-Shirt combines sustainability with style...</p>\n<h2>Key Benefits</h2>\n<ul><li>Soft, breathable organic cotton</li><li>Earth-friendly production process</li></ul>",
    "bullets": [
      "Made from 100% certified organic cotton",
      "Breathable fabric keeps you cool all day",
      "Eco-friendly production with zero harmful chemicals",
      "Available in sizes S to XL",
      "Machine washable for easy care"
    ],
    "faqs": [
      {
        "question": "Is this t-shirt really organic?",
        "answer": "Yes! Our t-shirt is made from 100% certified organic cotton, ensuring no pesticides or harmful chemicals were used in production."
      },
      {
        "question": "How should I wash this t-shirt?",
        "answer": "Simply machine wash in cold water with similar colors. Tumble dry on low or hang to dry for best results."
      },
      {
        "question": "What sizes are available?",
        "answer": "We offer sizes Small, Medium, Large, and Extra Large. Check our size guide for detailed measurements."
      }
    ],
    "meta_desc": "Shop our comfortable Organic Cotton T-Shirt made with 100% certified organic materials. Eco-friendly, breathable, and perfect for any occasion.",
    "tags": ["organic", "cotton", "sustainable fashion", "eco-friendly", "casual wear"],
    "cross_sell_suggestions": [
      {
        "product_type": "Organic Cotton Pants",
        "rationale": "Complete the sustainable outfit"
      },
      {
        "product_type": "Bamboo Socks",
        "rationale": "Pair with eco-friendly accessories"
      }
    ]
  }
}
```

---

#### FastAPI Endpoint
```http
POST /api/product/generate
Authorization: Bearer xyz789token
Content-Type: application/json

{
  "name": "Organic Cotton T-Shirt",
  "category": "Apparel",
  "attributes": {
    "Material": "100% Organic Cotton",
    "Color": "Navy Blue",
    "Sizes": ["S", "M", "L", "XL"]
  },
  "features": ["Breathable fabric", "Eco-friendly production"],
  "usp": ["Certified organic materials", "Carbon-neutral shipping"],
  "price": 29.99,
  "keywords": ["organic cotton", "sustainable fashion"],
  "tone": "friendly",
  "language": "en",
  "brand_profile": {...}
}
```

#### FastAPI Response
```json
{
  "success": true,
  "data": {
    "seo_title": "Organic Cotton T-Shirt | Sustainable & Comfortable",
    "short_desc_html": "<p>Discover ultimate comfort...</p>",
    "long_desc_html": "<h2>Overview</h2><p>...</p>",
    "bullets": ["Made from 100% certified organic cotton", "..."],
    "faqs": [{"question": "...", "answer": "..."}],
    "meta_desc": "Shop our comfortable Organic Cotton T-Shirt...",
    "tags": ["organic", "cotton", "sustainable fashion"],
    "cross_sell_suggestions": [...]
  },
  "error": null,
  "metadata": {
    "tokens_used": 980,
    "latency_ms": 1800,
    "model": "gpt-4o-mini"
  }
}
```

---

## 🖼️ Image Analysis

### 3. Image-to-Text & Alt-Text Generation

#### WordPress REST Endpoint
```http
POST /wp-json/contentcraft/v1/image/analyze
X-WP-Nonce: abc123xyz
Content-Type: application/json

{
  "image_url": "https://example.com/wp-content/uploads/product-photo.jpg",
  "attachment_id": 789,
  "language": "en",
  "context": "product"
}
```

#### WordPress Response
```json
{
  "success": true,
  "data": {
    "description": "A navy blue organic cotton t-shirt laid flat on a wooden surface, showing the comfortable crew neck design and relaxed fit.",
    "features": [
      "Crew neck collar",
      "Short sleeves",
      "Relaxed fit",
      "Solid navy blue color",
      "Clean, minimal design"
    ],
    "audience": "Eco-conscious consumers looking for comfortable casual wear",
    "selling_points": [
      "Premium organic cotton visible in fabric texture",
      "Versatile color suitable for any occasion",
      "Quality stitching evident in close-up"
    ],
    "alt_text": "Navy blue organic cotton t-shirt on wooden background",
    "suggested_category": "Apparel > T-Shirts > Men"
  }
}
```

---

#### FastAPI Endpoint
```http
POST /api/image/analyze
Authorization: Bearer xyz789token
Content-Type: application/json

{
  "image_url": "https://example.com/wp-content/uploads/product-photo.jpg",
  "language": "en",
  "context": "product"
}
```

#### FastAPI Response
```json
{
  "success": true,
  "data": {
    "description": "A navy blue organic cotton t-shirt laid flat...",
    "features": ["Crew neck collar", "Short sleeves", "..."],
    "audience": "Eco-conscious consumers...",
    "selling_points": ["Premium organic cotton visible...", "..."],
    "alt_text": "Navy blue organic cotton t-shirt on wooden background",
    "suggested_category": "Apparel > T-Shirts > Men",
    "confidence": 0.95
  },
  "error": null,
  "metadata": {
    "tokens_used": 450,
    "latency_ms": 3200,
    "model": "gpt-4-vision"
  }
}
```

---

## 🎯 SEO Optimization

### 4. SEO Analysis & Optimization

#### WordPress REST Endpoint
```http
POST /wp-json/contentcraft/v1/seo/optimize
X-WP-Nonce: abc123xyz
Content-Type: application/json

{
  "content_html": "<h1>My Article</h1><p>Content here...</p>",
  "current_title": "My Article",
  "keywords": ["wordpress", "seo", "optimization"],
  "language": "en",
  "post_type": "post",
  "existing_posts": [
    {"id": 123, "title": "SEO Best Practices", "slug": "seo-best-practices"},
    {"id": 124, "title": "WordPress Tips", "slug": "wordpress-tips"}
  ]
}
```

#### WordPress Response
```json
{
  "success": true,
  "data": {
    "seo_title": "WordPress SEO Optimization Guide 2024 | Best Practices",
    "meta_desc": "Master WordPress SEO with our comprehensive guide. Learn proven optimization techniques to boost your site's rankings in 2024.",
    "slug": "wordpress-seo-optimization-guide-2024",
    "suggested_headings": [
      {
        "level": "h2",
        "text": "Introduction to WordPress SEO",
        "rationale": "Clear topic introduction improves readability"
      },
      {
        "level": "h2",
        "text": "On-Page SEO Techniques",
        "rationale": "Break content into scannable sections"
      }
    ],
    "internal_links": [
      {
        "anchor": "SEO best practices",
        "target_post_id": 123,
        "target_url": "/seo-best-practices/",
        "context": "When discussing optimization techniques",
        "rationale": "Relevant internal linking to existing content"
      },
      {
        "anchor": "WordPress tips",
        "target_post_id": 124,
        "target_url": "/wordpress-tips/",
        "context": "In the plugins section",
        "rationale": "Related content for better site structure"
      }
    ],
    "schema_ld_json": {
      "@context": "https://schema.org",
      "@type": "Article",
      "headline": "WordPress SEO Optimization Guide 2024",
      "author": {
        "@type": "Person",
        "name": "Site Author"
      },
      "datePublished": "2024-10-16",
      "description": "Master WordPress SEO with our comprehensive guide..."
    },
    "readability_score": 68,
    "keyword_density": {
      "wordpress": 2.3,
      "seo": 1.8,
      "optimization": 1.5
    },
    "suggestions": [
      "Add more subheadings for better structure",
      "Consider adding images with descriptive alt text",
      "Internal link to related articles on SEO"
    ]
  }
}
```

---

#### FastAPI Endpoint
```http
POST /api/seo/optimize
Authorization: Bearer xyz789token
Content-Type: application/json

{
  "content_html": "<h1>My Article</h1><p>Content...</p>",
  "current_title": "My Article",
  "keywords": ["wordpress", "seo", "optimization"],
  "language": "en",
  "post_type": "post"
}
```

---

## 🎨 Brand Voice Training

### 5. Train Brand Voice Profile

#### WordPress REST Endpoint
```http
POST /wp-json/contentcraft/v1/brand/train
X-WP-Nonce: abc123xyz
Content-Type: application/json

{
  "num_samples": 20,
  "post_types": ["post", "page"],
  "date_range": {
    "from": "2023-01-01",
    "to": "2024-10-16"
  },
  "language": "en"
}
```

#### WordPress Response
```json
{
  "success": true,
  "data": {
    "brand_profile": {
      "tone": "conversational yet professional",
      "sentence_length": "medium (15-20 words avg)",
      "vocabulary_level": "intermediate",
      "paragraph_structure": "Short paragraphs (3-4 sentences)",
      "common_phrases": [
        "let's explore",
        "consider this",
        "key takeaway",
        "in other words"
      ],
      "writing_style": "Educational with practical examples",
      "punctuation_patterns": "Moderate use of em dashes and lists",
      "content_structure": "Problem-solution format with clear headings"
    },
    "prompt_template": "Write in a conversational yet professional tone. Use medium-length sentences (15-20 words). Keep paragraphs short (3-4 sentences). Include practical examples. Start sections with phrases like 'let's explore' or 'consider this'. End with clear takeaways.",
    "samples_analyzed": 18,
    "confidence": 0.87
  }
}
```

---

#### FastAPI Endpoint
```http
POST /api/brand/train
Authorization: Bearer xyz789token
Content-Type: application/json

{
  "samples": [
    {
      "title": "How to Optimize WordPress Performance",
      "excerpt": "Performance matters. Let's explore...",
      "body": "Website speed is crucial. Consider this: a one-second delay..."
    },
    {
      "title": "Best WordPress Plugins for 2024",
      "excerpt": "Choosing the right plugins...",
      "body": "In this guide, we'll explore the top plugins..."
    }
  ],
  "language": "en"
}
```

#### FastAPI Response
```json
{
  "success": true,
  "data": {
    "brand_profile": {
      "tone": "conversational yet professional",
      "sentence_length": "medium",
      "vocabulary_level": "intermediate",
      "common_phrases": ["let's explore", "consider this"],
      "writing_style": "Educational with practical examples"
    },
    "prompt_template": "Write in a conversational yet professional tone...",
    "analysis": {
      "avg_sentence_length": 17.3,
      "avg_paragraph_length": 3.2,
      "flesch_reading_ease": 62.5,
      "common_words": ["performance", "optimize", "improve"]
    }
  },
  "error": null,
  "metadata": {
    "samples_processed": 18,
    "confidence": 0.87,
    "latency_ms": 5400
  }
}
```

---

## 🔄 Bulk Operations

### 6. Bulk Product Generation

#### WordPress REST Endpoint
```http
POST /wp-json/contentcraft/v1/bulk/process
X-WP-Nonce: abc123xyz
Content-Type: application/json

{
  "product_ids": [101, 102, 103, 104, 105],
  "sections": ["short_desc", "long_desc", "faqs"],
  "tone": "professional",
  "language": "en",
  "overwrite": false
}
```

#### WordPress Response (Streaming/Chunked)
```json
{
  "success": true,
  "data": {
    "batch_id": "bulk_20241016_103045",
    "total": 5,
    "processed": 3,
    "succeeded": 2,
    "failed": 1,
    "results": [
      {
        "product_id": 101,
        "status": "success",
        "sections_updated": ["short_desc", "long_desc"],
        "tokens_used": 650
      },
      {
        "product_id": 102,
        "status": "success",
        "sections_updated": ["short_desc", "long_desc", "faqs"],
        "tokens_used": 820
      },
      {
        "product_id": 103,
        "status": "failed",
        "error": "Product not found"
      }
    ],
    "continue_url": "/wp-json/contentcraft/v1/bulk/process?batch_id=bulk_20241016_103045&offset=3"
  }
}
```

---

## ⚙️ Settings & Health Check

### 7. Test Connection

#### WordPress REST Endpoint
```http
GET /wp-json/contentcraft/v1/settings/test
X-WP-Nonce: abc123xyz
```

#### WordPress Response
```json
{
  "success": true,
  "data": {
    "fastapi_reachable": true,
    "fastapi_version": "1.0.0",
    "provider": "openai",
    "model": "gpt-4o-mini",
    "provider_status": "connected",
    "latency_ms": 120,
    "rate_limit_remaining": 58
  }
}
```

---

#### FastAPI Health Endpoint
```http
GET /api/health
```

#### FastAPI Response
```json
{
  "status": "healthy",
  "version": "1.0.0",
  "provider": "openai",
  "provider_status": "connected",
  "cache": "redis",
  "cache_status": "connected",
  "uptime_seconds": 86400
}
```

---

## ❌ Error Responses

All endpoints follow this error format:

```json
{
  "success": false,
  "data": null,
  "error": {
    "code": "GENERATION_FAILED",
    "message": "Failed to generate content due to API timeout",
    "details": {
      "provider": "openai",
      "http_status": 504,
      "retry_after": 5
    }
  }
}
```

### Common Error Codes:

- `INVALID_REQUEST`: Missing or invalid parameters
- `UNAUTHORIZED`: Authentication failed
- `RATE_LIMIT_EXCEEDED`: Too many requests
- `GENERATION_FAILED`: LLM generation error
- `PROVIDER_ERROR`: External API error (OpenAI/Anthropic down)
- `TIMEOUT`: Request took too long
- `INSUFFICIENT_TOKENS`: Not enough tokens in account

---

## 📊 Rate Limiting

### Headers (WordPress REST API):
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
X-RateLimit-Reset: 1697456789
```

### Headers (FastAPI):
```
X-RateLimit-Limit: 100
X-RateLimit-Remaining: 95
X-RateLimit-Reset: 1697456789
Retry-After: 60
```

---

## 🔄 Versioning

API versions are specified in the URL:
- WordPress: `/wp-json/contentcraft/v1/...`
- FastAPI: `/api/v1/...` (currently v1 is default)

Breaking changes will increment the version number.

---

**Last Updated**: October 2024  
**API Version**: v1  
**Status**: Stable


