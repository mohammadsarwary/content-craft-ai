"""
SEO optimization router.
"""

from fastapi import APIRouter, Depends
from app.models.schemas import SEORequest, SEOResponse, ResponseMetadata
from app.deps.auth import verify_token
from app.services.seo_service import SEOService
import logging
import time

logger = logging.getLogger(__name__)

router = APIRouter()


@router.post("/seo/optimize", response_model=SEOResponse)
async def optimize_seo(
    request: SEORequest,
    token: str = Depends(verify_token)
) -> SEOResponse:
    """
    Optimize content for SEO.
    
    Args:
        request: SEO optimization parameters
        token: Verified authentication token
        
    Returns:
        SEO optimization suggestions
    """
    start_time = time.time()
    
    logger.info(f"SEO optimization request: post_type='{request.post_type}', language='{request.language}'")
    
    try:
        service = SEOService()
        seo_data = await service.optimize(request)
        
        latency_ms = int((time.time() - start_time) * 1000)
        metadata = ResponseMetadata(
            tokens_used=service.last_tokens_used,
            latency_ms=latency_ms,
            model=service.model_name,
            cached=False
        )
        
        logger.info(f"SEO optimization complete: {latency_ms}ms")
        
        return SEOResponse(
            success=True,
            data=seo_data,
            error=None,
            metadata=metadata
        )
        
    except Exception as e:
        logger.error(f"SEO optimization failed: {str(e)}", exc_info=True)
        
        latency_ms = int((time.time() - start_time) * 1000)
        
        return SEOResponse(
            success=False,
            data=None,
            error={
                "code": "OPTIMIZATION_FAILED",
                "message": str(e)
            },
            metadata=ResponseMetadata(
                tokens_used=0,
                latency_ms=latency_ms,
                model="",
                cached=False
            )
        )


