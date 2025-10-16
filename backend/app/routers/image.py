"""
Image analysis router.
"""

from fastapi import APIRouter, Depends
from app.models.schemas import ImageRequest, ImageResponse, ResponseMetadata
from app.deps.auth import verify_token
from app.services.image_service import ImageService
import logging
import time

logger = logging.getLogger(__name__)

router = APIRouter()


@router.post("/image/analyze", response_model=ImageResponse)
async def analyze_image(
    request: ImageRequest,
    token: str = Depends(verify_token)
) -> ImageResponse:
    """
    Analyze image and generate descriptions/alt-text.
    
    Args:
        request: Image analysis parameters
        token: Verified authentication token
        
    Returns:
        Image analysis data including alt-text
    """
    start_time = time.time()
    
    logger.info(f"Image analysis request: context='{request.context}', language='{request.language}'")
    
    try:
        service = ImageService()
        image_data = await service.analyze(request)
        
        latency_ms = int((time.time() - start_time) * 1000)
        metadata = ResponseMetadata(
            tokens_used=service.last_tokens_used,
            latency_ms=latency_ms,
            model=service.model_name,
            cached=False
        )
        
        logger.info(f"Image analysis complete: {latency_ms}ms")
        
        return ImageResponse(
            success=True,
            data=image_data,
            error=None,
            metadata=metadata
        )
        
    except Exception as e:
        logger.error(f"Image analysis failed: {str(e)}", exc_info=True)
        
        latency_ms = int((time.time() - start_time) * 1000)
        
        return ImageResponse(
            success=False,
            data=None,
            error={
                "code": "ANALYSIS_FAILED",
                "message": str(e)
            },
            metadata=ResponseMetadata(
                tokens_used=0,
                latency_ms=latency_ms,
                model="",
                cached=False
            )
        )


