"""
Product content generation router.
"""

from fastapi import APIRouter, Depends
from app.models.schemas import ProductRequest, ProductResponse, ResponseMetadata
from app.deps.auth import verify_token
from app.services.product_service import ProductService
import logging
import time

logger = logging.getLogger(__name__)

router = APIRouter()


@router.post("/product/generate", response_model=ProductResponse)
async def generate_product(
    request: ProductRequest,
    token: str = Depends(verify_token)
) -> ProductResponse:
    """
    Generate product content (descriptions, features, FAQs).
    
    Args:
        request: Product generation parameters
        token: Verified authentication token
        
    Returns:
        Generated product content
    """
    start_time = time.time()
    
    logger.info(f"Product generation request: name='{request.name}', category='{request.category}'")
    
    try:
        service = ProductService()
        product_data = await service.generate(request)
        
        latency_ms = int((time.time() - start_time) * 1000)
        metadata = ResponseMetadata(
            tokens_used=service.last_tokens_used,
            latency_ms=latency_ms,
            model=service.model_name,
            cached=False
        )
        
        logger.info(f"Product content generated: {metadata.tokens_used} tokens, {latency_ms}ms")
        
        return ProductResponse(
            success=True,
            data=product_data,
            error=None,
            metadata=metadata
        )
        
    except Exception as e:
        logger.error(f"Product generation failed: {str(e)}", exc_info=True)
        
        latency_ms = int((time.time() - start_time) * 1000)
        
        return ProductResponse(
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


