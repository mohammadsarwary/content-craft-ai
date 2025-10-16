"""
Pydantic schemas for request/response models.
"""

from pydantic import BaseModel, Field
from typing import Optional, List, Dict, Any


# Common Models
class MetaData(BaseModel):
    """SEO metadata."""
    seo_title: str = Field(..., max_length=60, description="SEO title (max 60 chars)")
    meta_desc: str = Field(..., min_length=140, max_length=160, description="Meta description (140-160 chars)")
    slug: str = Field(..., description="URL slug")


class InternalLink(BaseModel):
    """Internal link suggestion."""
    anchor: str = Field(..., description="Anchor text")
    suggested_url: Optional[str] = Field(None, description="Suggested URL")
    rationale: str = Field(..., description="Why this link is relevant")


class FAQ(BaseModel):
    """FAQ item."""
    question: str = Field(..., description="Question")
    answer: str = Field(..., description="Answer")


class ResponseMetadata(BaseModel):
    """Response metadata."""
    tokens_used: int = Field(default=0, description="Tokens consumed")
    latency_ms: int = Field(default=0, description="Latency in milliseconds")
    model: str = Field(default="", description="Model used")
    cached: bool = Field(default=False, description="Whether result was cached")


# Content Generation
class ContentRequest(BaseModel):
    """Content generation request."""
    topic: str = Field(..., min_length=5, max_length=200, description="Content topic")
    keywords: List[str] = Field(default_factory=list, max_items=10, description="Target keywords")
    tone: str = Field(default="professional", description="Content tone")
    length: str = Field(default="medium", description="Content length (short/medium/long)")
    language: str = Field(default="en", description="Target language")
    audience: Optional[str] = Field(None, description="Target audience")
    brand_profile: Optional[Dict[str, Any]] = Field(None, description="Brand voice profile")
    sections: List[str] = Field(
        default_factory=lambda: ["title", "excerpt", "body", "meta"],
        description="Sections to generate"
    )


class ContentData(BaseModel):
    """Generated content data."""
    title: str = Field(..., description="Content title")
    excerpt: str = Field(..., description="Content excerpt")
    outline: List[str] = Field(default_factory=list, description="Content outline")
    body_html: str = Field(..., description="Content body (HTML)")
    meta: MetaData = Field(..., description="SEO metadata")
    headings: List[str] = Field(default_factory=list, description="Headings in content")
    internal_links: List[InternalLink] = Field(default_factory=list, description="Suggested internal links")
    schema_ld_json: Optional[str] = Field(None, description="Schema.org JSON-LD")


class ContentResponse(BaseModel):
    """Content generation response."""
    success: bool = Field(..., description="Whether request was successful")
    data: Optional[ContentData] = Field(None, description="Generated content")
    error: Optional[Dict[str, Any]] = Field(None, description="Error details")
    metadata: Optional[ResponseMetadata] = Field(None, description="Response metadata")


# Product Generation
class ProductRequest(BaseModel):
    """Product content generation request."""
    product_id: Optional[int] = Field(None, description="Product ID (optional)")
    name: str = Field(..., min_length=3, max_length=200, description="Product name")
    category: str = Field(..., description="Product category")
    attributes: Dict[str, Any] = Field(default_factory=dict, description="Product attributes/specs")
    features: List[str] = Field(default_factory=list, description="Product features")
    usp: List[str] = Field(default_factory=list, description="Unique selling points")
    price: Optional[float] = Field(None, description="Product price")
    keywords: List[str] = Field(default_factory=list, description="Target keywords")
    tone: str = Field(default="professional", description="Content tone")
    language: str = Field(default="en", description="Target language")
    brand_profile: Optional[Dict[str, Any]] = Field(None, description="Brand voice profile")


class CrossSellSuggestion(BaseModel):
    """Cross-sell product suggestion."""
    product_type: str = Field(..., description="Suggested product type")
    rationale: str = Field(..., description="Why this product complements")


class ProductData(BaseModel):
    """Generated product content."""
    seo_title: str = Field(..., description="SEO title")
    short_desc_html: str = Field(..., description="Short description (HTML)")
    long_desc_html: str = Field(..., description="Long description (HTML)")
    bullets: List[str] = Field(default_factory=list, description="Bullet features")
    faqs: List[FAQ] = Field(default_factory=list, description="FAQs")
    meta_desc: str = Field(..., description="Meta description")
    tags: List[str] = Field(default_factory=list, description="Product tags")
    cross_sell_suggestions: List[CrossSellSuggestion] = Field(default_factory=list, description="Cross-sell suggestions")


class ProductResponse(BaseModel):
    """Product generation response."""
    success: bool = Field(..., description="Whether request was successful")
    data: Optional[ProductData] = Field(None, description="Generated product content")
    error: Optional[Dict[str, Any]] = Field(None, description="Error details")
    metadata: Optional[ResponseMetadata] = Field(None, description="Response metadata")


# Image Analysis
class ImageRequest(BaseModel):
    """Image analysis request."""
    image_url: str = Field(..., description="Image URL")
    attachment_id: Optional[int] = Field(None, description="WordPress attachment ID")
    language: str = Field(default="en", description="Target language")
    context: str = Field(default="product", description="Image context (product/blog/etc)")


class ImageData(BaseModel):
    """Image analysis data."""
    description: str = Field(..., description="Image description")
    features: List[str] = Field(default_factory=list, description="Visual features detected")
    audience: str = Field(..., description="Target audience")
    selling_points: List[str] = Field(default_factory=list, description="Selling points from image")
    alt_text: str = Field(..., max_length=125, description="Alt-text (<125 chars)")
    suggested_category: Optional[str] = Field(None, description="Suggested product category")
    confidence: float = Field(default=0.0, description="Analysis confidence (0-1)")


class ImageResponse(BaseModel):
    """Image analysis response."""
    success: bool = Field(..., description="Whether request was successful")
    data: Optional[ImageData] = Field(None, description="Image analysis data")
    error: Optional[Dict[str, Any]] = Field(None, description="Error details")
    metadata: Optional[ResponseMetadata] = Field(None, description="Response metadata")


# SEO Optimization
class SEORequest(BaseModel):
    """SEO optimization request."""
    content_html: str = Field(..., description="Content HTML to optimize")
    current_title: Optional[str] = Field(None, description="Current title")
    keywords: List[str] = Field(default_factory=list, description="Target keywords")
    language: str = Field(default="en", description="Content language")
    post_type: str = Field(default="post", description="Post type (post/product)")


class SEOHeading(BaseModel):
    """SEO heading suggestion."""
    level: str = Field(..., description="Heading level (h2/h3)")
    text: str = Field(..., description="Heading text")
    rationale: str = Field(..., description="Why this heading")


class SEOData(BaseModel):
    """SEO optimization data."""
    seo_title: str = Field(..., max_length=60, description="Optimized SEO title")
    meta_desc: str = Field(..., min_length=140, max_length=160, description="Optimized meta description")
    slug: str = Field(..., description="Optimized slug")
    suggested_headings: List[SEOHeading] = Field(default_factory=list, description="Heading suggestions")
    internal_links: List[InternalLink] = Field(default_factory=list, description="Internal link suggestions")
    schema_ld_json: Optional[Dict[str, Any]] = Field(None, description="Schema.org JSON-LD")
    readability_score: Optional[int] = Field(None, description="Readability score (0-100)")
    keyword_density: Optional[Dict[str, float]] = Field(None, description="Keyword density percentages")
    suggestions: List[str] = Field(default_factory=list, description="General SEO suggestions")


class SEOResponse(BaseModel):
    """SEO optimization response."""
    success: bool = Field(..., description="Whether request was successful")
    data: Optional[SEOData] = Field(None, description="SEO optimization data")
    error: Optional[Dict[str, Any]] = Field(None, description="Error details")
    metadata: Optional[ResponseMetadata] = Field(None, description="Response metadata")


# Brand Voice Training
class BrandSample(BaseModel):
    """Brand voice training sample."""
    title: str = Field(..., description="Content title")
    excerpt: str = Field(default="", description="Content excerpt")
    body: str = Field(..., description="Content body")


class BrandTrainRequest(BaseModel):
    """Brand voice training request."""
    samples: List[BrandSample] = Field(..., min_items=10, description="Content samples for training")
    language: str = Field(default="en", description="Content language")


class BrandProfile(BaseModel):
    """Brand voice profile."""
    tone: str = Field(..., description="Brand tone")
    sentence_length: str = Field(..., description="Average sentence length")
    vocabulary_level: str = Field(..., description="Vocabulary level")
    paragraph_structure: Optional[str] = Field(None, description="Paragraph structure")
    common_phrases: List[str] = Field(default_factory=list, description="Common phrases")
    writing_style: str = Field(..., description="Overall writing style")
    punctuation_patterns: Optional[str] = Field(None, description="Punctuation patterns")
    content_structure: Optional[str] = Field(None, description="Content structure")


class BrandAnalysis(BaseModel):
    """Brand analysis metrics."""
    avg_sentence_length: float = Field(..., description="Average sentence length")
    avg_paragraph_length: float = Field(..., description="Average paragraph length")
    flesch_reading_ease: Optional[float] = Field(None, description="Flesch reading ease score")
    common_words: List[str] = Field(default_factory=list, description="Most common words")


class BrandTrainData(BaseModel):
    """Brand training result data."""
    brand_profile: BrandProfile = Field(..., description="Extracted brand profile")
    prompt_template: str = Field(..., description="Prompt template for applying brand voice")
    analysis: Optional[BrandAnalysis] = Field(None, description="Analysis metrics")


class BrandTrainResponse(BaseModel):
    """Brand training response."""
    success: bool = Field(..., description="Whether request was successful")
    data: Optional[BrandTrainData] = Field(None, description="Brand training data")
    error: Optional[Dict[str, Any]] = Field(None, description="Error details")
    metadata: Optional[ResponseMetadata] = Field(None, description="Response metadata")


# Error Response
class ErrorResponse(BaseModel):
    """Standard error response."""
    success: bool = Field(default=False, description="Always false for errors")
    data: Optional[Any] = Field(default=None, description="Always null for errors")
    error: Dict[str, Any] = Field(..., description="Error details")


