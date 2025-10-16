"""
SEO optimization service.
"""

from app.models.schemas import SEORequest, SEOData, SEOHeading, InternalLink
from app.services.llm_provider import get_provider
from app.services import prompts
import logging

logger = logging.getLogger(__name__)


class SEOService:
    """Service for SEO optimization."""
    
    def __init__(self):
        """Initialize SEO service."""
        self.provider = get_provider()
        self.model_name = self.provider.model_name
        self.last_tokens_used = 0
    
    async def optimize(self, request: SEORequest) -> SEOData:
        """
        Optimize content for SEO.
        
        Args:
            request: SEO optimization parameters
            
        Returns:
            SEO optimization data
            
        Raises:
            Exception: If optimization fails
        """
        logger.info(f"Optimizing SEO: post_type='{request.post_type}', language='{request.language}'")
        
        try:
            # Build prompt
            prompt = prompts.build_seo_optimization_prompt(
                content_html=request.content_html,
                current_title=request.current_title,
                keywords=request.keywords,
                language=request.language,
                post_type=request.post_type
            )
            
            system_message = prompts.get_system_message("seo")
            
            # Generate with LLM
            response_json = await self.provider.generate_json(
                prompt=prompt,
                system_message=system_message,
                temperature=0.5,  # Lower temperature for more consistent SEO
                max_tokens=1500
            )
            
            # Track tokens
            self.last_tokens_used = self.provider.last_tokens_used
            
            # Parse response
            seo_data = self._parse_seo_response(response_json)
            
            logger.info(f"SEO optimization complete")
            
            return seo_data
            
        except Exception as e:
            logger.error(f"SEO optimization failed: {str(e)}")
            raise Exception(f"Failed to optimize SEO: {str(e)}")
    
    def _parse_seo_response(self, response: dict) -> SEOData:
        """
        Parse LLM response into SEOData.
        
        Args:
            response: LLM JSON response
            
        Returns:
            Parsed SEOData
        """
        try:
            # Extract suggested headings
            headings = []
            for heading in response.get("suggested_headings", []):
                headings.append(
                    SEOHeading(
                        level=heading.get("level", "h2"),
                        text=heading.get("text", ""),
                        rationale=heading.get("rationale", "")
                    )
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
            
            # Create SEOData
            seo_data = SEOData(
                seo_title=response.get("seo_title", ""),
                meta_desc=response.get("meta_desc", ""),
                slug=response.get("slug", ""),
                suggested_headings=headings,
                internal_links=internal_links,
                schema_ld_json=response.get("schema_ld_json"),
                readability_score=response.get("readability_score"),
                keyword_density=response.get("keyword_density"),
                suggestions=response.get("suggestions", [])
            )
            
            return seo_data
            
        except Exception as e:
            logger.error(f"Failed to parse SEO response: {str(e)}")
            raise ValueError(f"Invalid response structure: {str(e)}")

