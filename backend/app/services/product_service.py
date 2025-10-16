"""
Product content generation service.
"""

from app.models.schemas import ProductRequest, ProductData, FAQ, CrossSellSuggestion
from app.services.llm_provider import get_provider
from app.services import prompts
import logging

logger = logging.getLogger(__name__)


class ProductService:
    """Service for generating WooCommerce product content."""
    
    def __init__(self):
        """Initialize product service."""
        self.provider = get_provider()
        self.model_name = self.provider.model_name
        self.last_tokens_used = 0
    
    async def generate(self, request: ProductRequest) -> ProductData:
        """
        Generate product content.
        
        Args:
            request: Product generation parameters
            
        Returns:
            Generated product content
            
        Raises:
            Exception: If generation fails
        """
        logger.info(f"Generating product content: name='{request.name}', category='{request.category}'")
        
        try:
            # Build prompt
            prompt = prompts.build_product_prompt(
                name=request.name,
                category=request.category,
                attributes=request.attributes,
                features=request.features,
                usp=request.usp,
                price=request.price,
                keywords=request.keywords,
                tone=request.tone,
                language=request.language,
                brand_profile=request.brand_profile
            )
            
            system_message = prompts.get_system_message("product")
            
            # Generate with LLM
            response_json = await self.provider.generate_json(
                prompt=prompt,
                system_message=system_message,
                temperature=0.7,
                max_tokens=2500
            )
            
            # Track tokens
            self.last_tokens_used = self.provider.last_tokens_used
            
            # Parse response
            product_data = self._parse_product_response(response_json)
            
            logger.info(f"Product content generated successfully")
            
            return product_data
            
        except Exception as e:
            logger.error(f"Product generation failed: {str(e)}")
            raise Exception(f"Failed to generate product content: {str(e)}")
    
    def _parse_product_response(self, response: dict) -> ProductData:
        """
        Parse LLM response into ProductData.
        
        Args:
            response: LLM JSON response
            
        Returns:
            Parsed ProductData
        """
        try:
            # Extract FAQs
            faqs = []
            for faq in response.get("faqs", []):
                faqs.append(
                    FAQ(
                        question=faq.get("question", ""),
                        answer=faq.get("answer", "")
                    )
                )
            
            # Extract cross-sell suggestions
            cross_sell = []
            for suggestion in response.get("cross_sell_suggestions", []):
                cross_sell.append(
                    CrossSellSuggestion(
                        product_type=suggestion.get("product_type", ""),
                        rationale=suggestion.get("rationale", "")
                    )
                )
            
            # Create ProductData
            product_data = ProductData(
                seo_title=response.get("seo_title", ""),
                short_desc_html=response.get("short_desc_html", ""),
                long_desc_html=response.get("long_desc_html", ""),
                bullets=response.get("bullets", []),
                faqs=faqs,
                meta_desc=response.get("meta_desc", ""),
                tags=response.get("tags", []),
                cross_sell_suggestions=cross_sell
            )
            
            return product_data
            
        except Exception as e:
            logger.error(f"Failed to parse product response: {str(e)}")
            raise ValueError(f"Invalid response structure: {str(e)}")

