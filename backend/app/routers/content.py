"""
Content generation router.
"""

from fastapi import APIRouter, Depends, HTTPException
from app.models.schemas import ContentRequest, ContentResponse, ResponseMetadata
from app.deps.auth import verify_token
from app.services.content_service import ContentService
import logging
import time

logger = logging.getLogger(__name__)

router = APIRouter()


@router.post("/content/generate", response_model=ContentResponse)
async def generate_content(
    request: ContentRequest,
    token: str = Depends(verify_token)
) -> ContentResponse:
    """
    Generate SEO-optimized content for posts/pages.
    
    Args:
        request: Content generation parameters
        token: Verified authentication token
        
    Returns:
        Generated content data
    """
    start_time = time.time()
    
    logger.info(f"Content generation request: topic='{request.topic}', language='{request.language}'")
    
    try:
        # Initialize service
        service = ContentService()
        
        # Generate content
        content_data = await service.generate(request)
        
        # Calculate metadata
        latency_ms = int((time.time() - start_time) * 1000)
        metadata = ResponseMetadata(
            tokens_used=service.last_tokens_used,
            latency_ms=latency_ms,
            model=service.model_name,
            cached=False
        )
        
        logger.info(f"Content generated successfully: {metadata.tokens_used} tokens, {latency_ms}ms")
        
        return ContentResponse(
            success=True,
            data=content_data,
            error=None,
            metadata=metadata
        )
        
    except Exception as e:
        logger.error(f"Content generation failed: {str(e)}", exc_info=True)
        
        latency_ms = int((time.time() - start_time) * 1000)
        
        return ContentResponse(
            success=False,
            data=None,
            error={
                "code": "GENERATION_FAILED",
                "message": str(e)
            },
            metadata=ResponseMetadata(
                tokens_used=0,
                latency_ms=latency_ms,
                model="",
                cached=False
            )
        )


