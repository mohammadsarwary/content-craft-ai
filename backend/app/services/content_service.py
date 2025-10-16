"""
Content generation service.
"""

from app.models.schemas import ContentRequest, ContentData, MetaData, InternalLink
from app.services.llm_provider import get_provider
from app.services import prompts
import logging
import json

logger = logging.getLogger(__name__)


class ContentService:
    """Service for generating blog post/page content."""
    
    def __init__(self):
        """Initialize content service."""
        self.provider = get_provider()
        self.model_name = self.provider.model_name
        self.last_tokens_used = 0
    
    async def generate(self, request: ContentRequest) -> ContentData:
        """
        Generate blog post/page content.
        
        Args:
            request: Content generation parameters
            
        Returns:
            Generated content data
            
        Raises:
            Exception: If generation fails
        """
        logger.info(f"Generating content: topic='{request.topic}', language='{request.language}'")
        
        try:
            # Build prompt
            prompt = prompts.build_content_prompt(
                topic=request.topic,
                keywords=request.keywords,
                tone=request.tone,
                length=request.length,
                language=request.language,
                audience=request.audience,
                brand_profile=request.brand_profile
            )
            
            system_message = prompts.get_system_message("general")
            
            # Generate with LLM
            response_json = await self.provider.generate_json(
                prompt=prompt,
                system_message=system_message,
                temperature=0.7,
                max_tokens=3000
            )
            
            # Track tokens
            self.last_tokens_used = self.provider.last_tokens_used
            
            # Parse and validate response
            content_data = self._parse_content_response(response_json)
            
            logger.info(f"Content generated successfully: {len(content_data.body_html)} chars")
            
            return content_data
            
        except Exception as e:
            logger.error(f"Content generation failed: {str(e)}")
            raise Exception(f"Failed to generate content: {str(e)}")
    
    def _parse_content_response(self, response: dict) -> ContentData:
        """
        Parse LLM response into ContentData.
        
        Args:
            response: LLM JSON response
            
        Returns:
            Parsed ContentData
        """
        try:
            # Extract meta
            meta_dict = response.get("meta", {})
            meta = MetaData(
                seo_title=meta_dict.get("seo_title", ""),
                meta_desc=meta_dict.get("meta_desc", ""),
                slug=meta_dict.get("slug", "")
            )
            
            # Extract internal links
            internal_links = []
            for link in response.get("internal_links", []):
                internal_links.append(
                    InternalLink(
                        anchor=link.get("anchor", ""),
                        suggested_url=link.get("suggested_url"),
                        rationale=link.get("rationale", "")
                    )
                )
            
            # Create ContentData
            content_data = ContentData(
                title=response.get("title", ""),
                excerpt=response.get("excerpt", ""),
                outline=response.get("outline", []),
                body_html=response.get("body_html", ""),
                meta=meta,
                headings=response.get("headings", []),
                internal_links=internal_links,
                schema_ld_json=response.get("schema_ld_json")
            )
            
            return content_data
            
        except Exception as e:
            logger.error(f"Failed to parse content response: {str(e)}")
            raise ValueError(f"Invalid response structure: {str(e)}")

