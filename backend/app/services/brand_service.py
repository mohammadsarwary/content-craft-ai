"""
Brand voice training service.
"""

from app.models.schemas import BrandTrainRequest, BrandTrainData, BrandProfile, BrandAnalysis
from app.services.llm_provider import get_provider
from app.services import prompts
import logging

logger = logging.getLogger(__name__)


class BrandService:
    """Service for brand voice training."""
    
    def __init__(self):
        """Initialize brand service."""
        self.provider = get_provider()
        self.model_name = self.provider.model_name
        self.last_tokens_used = 0
    
    async def train(self, request: BrandTrainRequest) -> BrandTrainData:
        """
        Train brand voice from content samples.
        
        Args:
            request: Brand training parameters
            
        Returns:
            Brand profile and prompt template
            
        Raises:
            Exception: If training fails
        """
        logger.info(f"Training brand voice: {len(request.samples)} samples, language='{request.language}'")
        
        try:
            # Concatenate samples
            samples_text = self._concatenate_samples(request.samples)
            
            # Build prompt
            prompt = prompts.build_brand_training_prompt(
                samples_text=samples_text,
                language=request.language,
                num_samples=len(request.samples)
            )
            
            system_message = prompts.get_system_message("brand")
            
            # Generate with LLM
            response_json = await self.provider.generate_json(
                prompt=prompt,
                system_message=system_message,
                temperature=0.3,  # Low temperature for consistent analysis
                max_tokens=2000
            )
            
            # Track tokens
            self.last_tokens_used = self.provider.last_tokens_used
            
            # Parse response
            brand_data = self._parse_brand_response(response_json)
            
            logger.info(f"Brand voice training complete")
            
            return brand_data
            
        except Exception as e:
            logger.error(f"Brand training failed: {str(e)}")
            raise Exception(f"Failed to train brand voice: {str(e)}")
    
    def _concatenate_samples(self, samples: list) -> str:
        """
        Concatenate content samples for analysis.
        
        Args:
            samples: List of BrandSample objects
            
        Returns:
            Concatenated text
        """
        texts = []
        for sample in samples:
            text = f"TITLE: {sample.title}\n"
            if sample.excerpt:
                text += f"EXCERPT: {sample.excerpt}\n"
            text += f"BODY: {sample.body[:500]}...\n\n"
            texts.append(text)
        
        return "\n---\n".join(texts)
    
    def _parse_brand_response(self, response: dict) -> BrandTrainData:
        """
        Parse LLM response into BrandTrainData.
        
        Args:
            response: LLM JSON response
            
        Returns:
            Parsed BrandTrainData
        """
        try:
            # Extract brand profile
            profile_dict = response.get("brand_profile", {})
            brand_profile = BrandProfile(
                tone=profile_dict.get("tone", ""),
                sentence_length=profile_dict.get("sentence_length", ""),
                vocabulary_level=profile_dict.get("vocabulary_level", ""),
                paragraph_structure=profile_dict.get("paragraph_structure"),
                common_phrases=profile_dict.get("common_phrases", []),
                writing_style=profile_dict.get("writing_style", ""),
                punctuation_patterns=profile_dict.get("punctuation_patterns"),
                content_structure=profile_dict.get("content_structure")
            )
            
            # Extract analysis (optional)
            analysis = None
            if "analysis" in response:
                analysis_dict = response["analysis"]
                analysis = BrandAnalysis(
                    avg_sentence_length=analysis_dict.get("avg_sentence_length", 0.0),
                    avg_paragraph_length=analysis_dict.get("avg_paragraph_length", 0.0),
                    flesch_reading_ease=analysis_dict.get("flesch_reading_ease"),
                    common_words=analysis_dict.get("common_words", [])
                )
            
            # Create BrandTrainData
            brand_data = BrandTrainData(
                brand_profile=brand_profile,
                prompt_template=response.get("prompt_template", ""),
                analysis=analysis
            )
            
            return brand_data
            
        except Exception as e:
            logger.error(f"Failed to parse brand response: {str(e)}")
            raise ValueError(f"Invalid response structure: {str(e)}")

