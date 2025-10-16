"""
Prompt templates for content generation.

These prompts are carefully engineered to produce consistent,
high-quality JSON outputs from LLMs.
"""

from typing import Optional, Dict, Any, List


# System messages (constant across all requests)
SYSTEM_MESSAGE_BASE = """You are an expert SEO content writer and marketing copywriter for WordPress and WooCommerce.

CRITICAL RULES:
1. Respond ONLY with valid JSON. No markdown code blocks, no explanations, no text outside JSON.
2. Follow the exact JSON structure specified in the prompt.
3. Never invent facts, specifications, or claims not provided in the input.
4. Write naturally - avoid keyword stuffing.
5. Be concise and actionable.
6. Use clear, accessible language appropriate for the target audience."""


def build_content_prompt(
    topic: str,
    keywords: List[str],
    tone: str,
    length: str,
    language: str,
    audience: Optional[str] = None,
    brand_profile: Optional[Dict[str, Any]] = None
) -> str:
    """
    Build prompt for blog post/page content generation.
    
    Args:
        topic: Content topic
        keywords: Target keywords
        tone: Writing tone
        length: Content length (short/medium/long)
        language: Target language
        audience: Target audience
        brand_profile: Brand voice profile
        
    Returns:
        Formatted prompt
    """
    # Length guidelines
    length_map = {
        "short": "300-500 words",
        "medium": "800-1200 words",
        "long": "1500-2500 words"
    }
    word_count = length_map.get(length, "800-1200 words")
    
    # Brand voice section
    brand_instructions = ""
    if brand_profile and brand_profile.get("enabled"):
        brand_instructions = f"""
BRAND VOICE GUIDELINES:
- Tone: {brand_profile.get('tone', tone)}
- Sentence length: {brand_profile.get('sentence_length', 'medium')}
- Vocabulary level: {brand_profile.get('vocabulary_level', 'intermediate')}
- Writing style: {brand_profile.get('writing_style', 'clear and engaging')}
{f"- Common phrases to use: {', '.join(brand_profile.get('common_phrases', []))}" if brand_profile.get('common_phrases') else ""}
{f"- Content structure: {brand_profile.get('content_structure')}" if brand_profile.get('content_structure') else ""}
"""
    
    # Audience section
    audience_section = f"Target audience: {audience}" if audience else ""
    
    prompt = f"""Generate a comprehensive blog post/article with the following parameters:

TOPIC: {topic}
TARGET KEYWORDS: {', '.join(keywords)}
TONE: {tone}
LENGTH: {word_count}
LANGUAGE: {language}
{audience_section}
{brand_instructions}

REQUIREMENTS:
- Create an engaging, SEO-optimized article
- Include H2 and H3 subheadings for structure
- Write in {language} language
- Use a {tone} tone
- Target length: approximately {word_count}
- Naturally incorporate keywords: {', '.join(keywords)}
- Include practical examples and actionable tips where appropriate
- Use short paragraphs (3-4 sentences) for readability
- Add bullet lists where helpful

OUTPUT STRUCTURE (strict JSON):
{{
  "title": "Compelling article title (50-60 characters, include primary keyword)",
  "excerpt": "Engaging summary (40-60 words) that hooks the reader",
  "outline": [
    "Introduction",
    "Main Section 1 Title",
    "Main Section 2 Title",
    "Conclusion"
  ],
  "body_html": "<h2>Introduction</h2>\\n<p>Opening paragraph...</p>\\n<h2>Section 1</h2>\\n<p>Content...</p>",
  "meta": {{
    "seo_title": "SEO-optimized title (max 60 chars, include primary keyword)",
    "meta_desc": "Compelling meta description (140-160 chars, include keywords, call-to-action)",
    "slug": "url-friendly-slug-with-primary-keyword"
  }},
  "headings": ["Introduction", "Main Section 1", "Subsection 1.1", "Main Section 2", "Conclusion"],
  "internal_links": [
    {{
      "anchor": "suggested anchor text",
      "suggested_url": "/related-topic/",
      "rationale": "Why this link is relevant"
    }}
  ],
  "schema_ld_json": "{{\\"@context\\":\\"https://schema.org\\",\\"@type\\":\\"Article\\",\\"headline\\":\\"...\\",...}}"
}}

Generate the complete article now in strict JSON format:"""
    
    return prompt


def build_product_prompt(
    name: str,
    category: str,
    attributes: Dict[str, Any],
    features: List[str],
    usp: List[str],
    price: Optional[float],
    keywords: List[str],
    tone: str,
    language: str,
    brand_profile: Optional[Dict[str, Any]] = None
) -> str:
    """
    Build prompt for product content generation.
    
    Args:
        name: Product name
        category: Product category
        attributes: Product attributes/specs
        features: Product features
        usp: Unique selling points
        price: Product price (optional)
        keywords: Target keywords
        tone: Writing tone
        language: Target language
        brand_profile: Brand voice profile
        
    Returns:
        Formatted prompt
    """
    # Format attributes
    attrs_text = "\n".join([f"  - {k}: {v}" for k, v in attributes.items()])
    
    # Format features and USPs
    features_text = "\n".join([f"  - {f}" for f in features])
    usp_text = "\n".join([f"  - {u}" for u in usp])
    
    # Price section
    price_section = f"PRICE: ${price}" if price else ""
    
    # Brand voice
    brand_instructions = ""
    if brand_profile and brand_profile.get("enabled"):
        brand_instructions = f"""
BRAND VOICE:
- Write in a {brand_profile.get('tone', tone)} tone
- {brand_profile.get('writing_style', 'Clear and benefit-focused')}
"""
    
    prompt = f"""Generate compelling WooCommerce product content with the following details:

PRODUCT NAME: {name}
CATEGORY: {category}
{price_section}

ATTRIBUTES/SPECIFICATIONS:
{attrs_text}

KEY FEATURES:
{features_text}

UNIQUE SELLING POINTS:
{usp_text}

TARGET KEYWORDS: {', '.join(keywords)}
TONE: {tone}
LANGUAGE: {language}
{brand_instructions}

REQUIREMENTS:
- Write benefit-focused, persuasive copy
- Emphasize how features solve customer problems
- Use {language} language
- Maintain a {tone} tone
- Create scannable content with clear sections
- Focus on customer benefits, not just features
- Generate realistic, helpful FAQs (5-7 questions)
- Suggest complementary products for cross-selling

OUTPUT STRUCTURE (strict JSON):
{{
  "seo_title": "SEO-optimized product title (max 60 chars, include keyword)",
  "short_desc_html": "<p>Compelling 2-3 sentence benefit-focused summary that hooks customers...</p>",
  "long_desc_html": "<h2>Overview</h2>\\n<p>Engaging product story...</p>\\n<h2>Key Benefits</h2>\\n<ul><li>Benefit 1</li></ul>\\n<h2>Specifications</h2>...",
  "bullets": [
    "Concise benefit-focused feature point",
    "Another key feature with customer value",
    "Feature that solves a problem"
  ],
  "faqs": [
    {{
      "question": "Common customer question?",
      "answer": "Clear, helpful answer addressing customer concern"
    }}
  ],
  "meta_desc": "Compelling meta description (140-160 chars) with benefits and call-to-action",
  "tags": ["keyword1", "keyword2", "category", "feature"],
  "cross_sell_suggestions": [
    {{
      "product_type": "Complementary Product Name",
      "rationale": "Why this pairs well with the main product"
    }}
  ]
}}

Generate the complete product content now in strict JSON format:"""
    
    return prompt


def build_image_analysis_prompt(
    context: str,
    language: str
) -> str:
    """
    Build prompt for image analysis.
    
    Args:
        context: Image context (product/blog/etc)
        language: Target language
        
    Returns:
        Formatted prompt
    """
    prompt = f"""Analyze this image and provide detailed insights for {context} content.

CONTEXT: {context}
LANGUAGE: {language}

REQUIREMENTS:
- Describe what you see in detail
- Identify key visual features and elements
- Determine the target audience based on the image
- Extract selling points or key messages from the visual
- Generate SEO-friendly alt-text (max 125 characters)
- Suggest a product category if applicable
- Rate your confidence in the analysis (0-1)

OUTPUT STRUCTURE (strict JSON):
{{
  "description": "Detailed description of the image in {language}",
  "features": [
    "Visual feature 1 (color, shape, composition)",
    "Visual feature 2",
    "Visual feature 3"
  ],
  "audience": "Target audience based on image style and content",
  "selling_points": [
    "Key selling point visible in image",
    "Another visual selling point"
  ],
  "alt_text": "Concise, descriptive alt-text under 125 characters in {language}",
  "suggested_category": "Product category if applicable",
  "confidence": 0.95
}}

Generate the image analysis now in strict JSON format:"""
    
    return prompt


def build_seo_optimization_prompt(
    content_html: str,
    current_title: Optional[str],
    keywords: List[str],
    language: str,
    post_type: str
) -> str:
    """
    Build prompt for SEO optimization.
    
    Args:
        content_html: Content HTML to optimize
        current_title: Current title
        keywords: Target keywords
        language: Content language
        post_type: Post type (post/product)
        
    Returns:
        Formatted prompt
    """
    current_title_section = f"CURRENT TITLE: {current_title}" if current_title else ""
    
    prompt = f"""Analyze and optimize this content for SEO:

{current_title_section}
TARGET KEYWORDS: {', '.join(keywords)}
LANGUAGE: {language}
CONTENT TYPE: {post_type}

CONTENT TO OPTIMIZE:
{content_html[:3000]}  

REQUIREMENTS:
- Generate SEO-optimized title (max 60 characters, include primary keyword)
- Create compelling meta description (140-160 characters, include keywords + CTA)
- Generate URL-friendly slug from primary keyword
- Suggest improved H2/H3 headings for better structure
- Recommend strategic internal linking opportunities
- Create appropriate schema.org JSON-LD markup
- Calculate readability metrics
- Analyze keyword density
- Provide actionable SEO improvement suggestions

OUTPUT STRUCTURE (strict JSON):
{{
  "seo_title": "Optimized SEO title (max 60 chars)",
  "meta_desc": "Compelling meta description (140-160 chars) with CTA",
  "slug": "url-friendly-slug-with-keyword",
  "suggested_headings": [
    {{
      "level": "h2",
      "text": "Clear, keyword-rich heading",
      "rationale": "Why this heading improves SEO and readability"
    }}
  ],
  "internal_links": [
    {{
      "anchor": "relevant anchor text",
      "suggested_url": "/related-content/",
      "rationale": "Why this link adds value"
    }}
  ],
  "schema_ld_json": {{
    "@context": "https://schema.org",
    "@type": "{{"Article" if post_type == "post" else "Product"}}",
    "headline": "...",
    "author": {{"@type": "Person", "name": "Author"}},
    "datePublished": "2024-10-16"
  }},
  "readability_score": 65,
  "keyword_density": {{
    "keyword1": 2.3,
    "keyword2": 1.8
  }},
  "suggestions": [
    "Add more subheadings to break up long sections",
    "Reduce keyword density for 'keyword' from 3.5% to 2%",
    "Add internal links to related content"
  ]
}}

Generate the SEO optimization analysis now in strict JSON format:"""
    
    return prompt


def build_brand_training_prompt(
    samples_text: str,
    language: str,
    num_samples: int
) -> str:
    """
    Build prompt for brand voice training.
    
    Args:
        samples_text: Concatenated content samples
        language: Content language
        num_samples: Number of samples provided
        
    Returns:
        Formatted prompt
    """
    prompt = f"""Analyze these {num_samples} content samples to extract the brand's unique writing voice and style:

LANGUAGE: {language}
SAMPLES:
{samples_text[:8000]}

ANALYSIS REQUIREMENTS:
1. Overall Tone: (professional, casual, friendly, authoritative, enthusiastic, etc.)
2. Sentence Structure: (average length, complexity, variety)
3. Vocabulary Level: (basic, intermediate, advanced, technical)
4. Paragraph Patterns: (length, structure, flow)
5. Common Phrases: (recurring expressions, transitional phrases, calls-to-action)
6. Writing Style: (storytelling, instructional, conversational, formal, etc.)
7. Punctuation Patterns: (use of dashes, semicolons, exclamation marks, etc.)
8. Content Structure: (how topics are introduced, developed, and concluded)

OUTPUT STRUCTURE (strict JSON):
{{
  "brand_profile": {{
    "tone": "Identified overall tone",
    "sentence_length": "short/medium/long (X-Y words average)",
    "vocabulary_level": "basic/intermediate/advanced",
    "paragraph_structure": "Typical paragraph structure observed",
    "common_phrases": [
      "Recurring phrase 1",
      "Recurring phrase 2",
      "Recurring phrase 3"
    ],
    "writing_style": "Primary writing style description",
    "punctuation_patterns": "Notable punctuation patterns",
    "content_structure": "How content is typically organized"
  }},
  "prompt_template": "When writing content, adopt this voice: [tone] tone with [sentence_length] sentences. Use [vocabulary_level] vocabulary. Structure paragraphs as [paragraph_structure]. Incorporate phrases like: [common_phrases]. Follow a [writing_style] style. [Additional specific instructions based on analysis]",
  "analysis": {{
    "avg_sentence_length": 17.5,
    "avg_paragraph_length": 3.2,
    "flesch_reading_ease": 62.5,
    "common_words": ["word1", "word2", "word3"]
  }}
}}

Generate the brand voice analysis now in strict JSON format:"""
    
    return prompt


def get_system_message(content_type: str = "general") -> str:
    """
    Get system message for specific content type.
    
    Args:
        content_type: Type of content (general/product/seo/image/brand)
        
    Returns:
        System message
    """
    type_specific = {
        "product": "You specialize in e-commerce product descriptions that convert browsers into buyers.",
        "seo": "You specialize in technical SEO optimization and search engine ranking strategies.",
        "image": "You specialize in visual analysis and creating accessible image descriptions.",
        "brand": "You specialize in analyzing writing styles and extracting brand voice patterns.",
    }
    
    specific_instruction = type_specific.get(content_type, "")
    
    return f"{SYSTEM_MESSAGE_BASE}\n\n{specific_instruction}".strip()

