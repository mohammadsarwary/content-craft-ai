"""
Brand voice training router.
"""

from fastapi import APIRouter, Depends
from app.models.schemas import BrandTrainRequest, BrandTrainResponse, ResponseMetadata
from app.deps.auth import verify_token
from app.services.brand_service import BrandService
import logging
import time

logger = logging.getLogger(__name__)

router = APIRouter()


@router.post("/brand/train", response_model=BrandTrainResponse)
async def train_brand(
    request: BrandTrainRequest,
    token: str = Depends(verify_token)
) -> BrandTrainResponse:
    """
    Train brand voice from content samples.
    
    Args:
        request: Brand training parameters with content samples
        token: Verified authentication token
        
    Returns:
        Brand profile and prompt template
    """
    start_time = time.time()
    
    logger.info(f"Brand training request: {len(request.samples)} samples, language='{request.language}'")
    
    try:
        service = BrandService()
        brand_data = await service.train(request)
        
        latency_ms = int((time.time() - start_time) * 1000)
        metadata = ResponseMetadata(
            tokens_used=service.last_tokens_used,
            latency_ms=latency_ms,
            model=service.model_name,
            cached=False
        )
        
        logger.info(f"Brand training complete: {latency_ms}ms")
        
        return BrandTrainResponse(
            success=True,
            data=brand_data,
            error=None,
            metadata=metadata
        )
        
    except Exception as e:
        logger.error(f"Brand training failed: {str(e)}", exc_info=True)
        
        latency_ms = int((time.time() - start_time) * 1000)
        
        return BrandTrainResponse(
            success=False,
            data=None,
            error={
                "code": "TRAINING_FAILED",
                "message": str(e)
            },
            metadata=ResponseMetadata(
                tokens_used=0,
                latency_ms=latency_ms,
                model="",
                cached=False
            )
        )


