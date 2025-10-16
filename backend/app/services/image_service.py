"""
Image analysis service.
"""

from app.models.schemas import ImageRequest, ImageData
from app.services.llm_provider import get_provider
from app.services import prompts
import logging

logger = logging.getLogger(__name__)


class ImageService:
    """Service for image analysis and alt-text generation."""
    
    def __init__(self):
        """Initialize image service."""
        self.provider = get_provider()
        self.model_name = self.provider.model_name
        self.last_tokens_used = 0
    
    async def analyze(self, request: ImageRequest) -> ImageData:
        """
        Analyze image and generate descriptions/alt-text.
        
        Args:
            request: Image analysis parameters
            
        Returns:
            Image analysis data
            
        Raises:
            Exception: If analysis fails
        """
        logger.info(f"Analyzing image: context='{request.context}', language='{request.language}'")
        
        try:
            # Build prompt
            prompt = prompts.build_image_analysis_prompt(
                context=request.context,
                language=request.language
            )
            
            # Add image URL to prompt
            prompt_with_image = f"{prompt}\n\nIMAGE URL: {request.image_url}"
            
            system_message = prompts.get_system_message("image")
            
            # For vision models (GPT-4 Vision, Claude with vision)
            # Note: This requires special handling for image input
            # For now, we'll use text-based analysis
            # TODO: Implement actual vision model integration
            
            # Generate with LLM
            response_json = await self.provider.generate_json(
                prompt=prompt_with_image,
                system_message=system_message,
                temperature=0.5,
                max_tokens=1000
            )
            
            # Track tokens
            self.last_tokens_used = self.provider.last_tokens_used
            
            # Parse response
            image_data = self._parse_image_response(response_json)
            
            # Ensure alt-text is under 125 characters
            if len(image_data.alt_text) > 125:
                image_data.alt_text = image_data.alt_text[:122] + "..."
            
            logger.info(f"Image analysis complete")
            
            return image_data
            
        except Exception as e:
            logger.error(f"Image analysis failed: {str(e)}")
            raise Exception(f"Failed to analyze image: {str(e)}")
    
    def _parse_image_response(self, response: dict) -> ImageData:
        """
        Parse LLM response into ImageData.
        
        Args:
            response: LLM JSON response
            
        Returns:
            Parsed ImageData
        """
        try:
            image_data = ImageData(
                description=response.get("description", ""),
                features=response.get("features", []),
                audience=response.get("audience", ""),
                selling_points=response.get("selling_points", []),
                alt_text=response.get("alt_text", ""),
                suggested_category=response.get("suggested_category"),
                confidence=response.get("confidence", 0.8)
            )
            
            return image_data
            
        except Exception as e:
            logger.error(f"Failed to parse image response: {str(e)}")
            raise ValueError(f"Invalid response structure: {str(e)}")

