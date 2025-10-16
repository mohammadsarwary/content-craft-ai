"""
ContentCraft AI - FastAPI Backend

Main application entry point.
"""

from fastapi import FastAPI, Request, status
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import JSONResponse
from fastapi.exceptions import RequestValidationError
import logging
import time
import os

from app.routers import content, product, seo, brand, image
from app.utils.logger import setup_logging

# Setup logging
setup_logging()
logger = logging.getLogger(__name__)

# Application version
VERSION = "1.0.0"

# Create FastAPI app
app = FastAPI(
    title="ContentCraft AI API",
    description="AI-powered content generation API for WordPress",
    version=VERSION,
    docs_url="/docs",
    redoc_url="/redoc",
)

# CORS Configuration
# In production, set this to your WordPress domain
ALLOWED_ORIGINS = os.getenv("ALLOWED_ORIGINS", "*").split(",")

app.add_middleware(
    CORSMiddleware,
    allow_origins=ALLOWED_ORIGINS,
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)


# Middleware for logging and timing
@app.middleware("http")
async def log_requests(request: Request, call_next):
    """Log all requests with timing information."""
    start_time = time.time()
    
    # Log request
    logger.info(f"Request: {request.method} {request.url.path}")
    
    # Process request
    response = await call_next(request)
    
    # Calculate latency
    latency_ms = int((time.time() - start_time) * 1000)
    
    # Log response
    logger.info(
        f"Response: {request.method} {request.url.path} "
        f"Status: {response.status_code} Latency: {latency_ms}ms"
    )
    
    # Add custom headers
    response.headers["X-Process-Time"] = str(latency_ms)
    response.headers["X-API-Version"] = VERSION
    
    return response


# Exception handlers
@app.exception_handler(RequestValidationError)
async def validation_exception_handler(request: Request, exc: RequestValidationError):
    """Handle validation errors."""
    logger.error(f"Validation error: {exc.errors()}")
    return JSONResponse(
        status_code=status.HTTP_422_UNPROCESSABLE_ENTITY,
        content={
            "success": False,
            "data": None,
            "error": {
                "code": "VALIDATION_ERROR",
                "message": "Invalid request parameters",
                "details": exc.errors(),
            },
        },
    )


@app.exception_handler(Exception)
async def general_exception_handler(request: Request, exc: Exception):
    """Handle general exceptions."""
    logger.error(f"Unhandled exception: {str(exc)}", exc_info=True)
    return JSONResponse(
        status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
        content={
            "success": False,
            "data": None,
            "error": {
                "code": "INTERNAL_ERROR",
                "message": "An internal error occurred",
            },
        },
    )


# Include routers
app.include_router(content.router, prefix="/api", tags=["Content"])
app.include_router(product.router, prefix="/api", tags=["Product"])
app.include_router(seo.router, prefix="/api", tags=["SEO"])
app.include_router(brand.router, prefix="/api", tags=["Brand"])
app.include_router(image.router, prefix="/api", tags=["Image"])


# Health check endpoint
@app.get("/api/health", tags=["System"])
async def health_check():
    """
    Health check endpoint.
    
    Returns system status and configuration.
    """
    provider = os.getenv("PROVIDER", "openai")
    
    # Check provider availability
    provider_status = "unknown"
    try:
        # TODO: Add actual provider health check
        provider_status = "connected"
    except Exception:
        provider_status = "unavailable"
    
    return {
        "status": "healthy",
        "version": VERSION,
        "provider": provider,
        "provider_status": provider_status,
        "uptime_seconds": int(time.time() - app.state.start_time) if hasattr(app.state, "start_time") else 0,
    }


# Root endpoint
@app.get("/", tags=["System"])
async def root():
    """Root endpoint with API information."""
    return {
        "name": "ContentCraft AI API",
        "version": VERSION,
        "docs": "/docs",
        "health": "/api/health",
    }


# Startup event
@app.on_event("startup")
async def startup_event():
    """Run on application startup."""
    app.state.start_time = time.time()
    logger.info(f"ContentCraft AI API v{VERSION} starting...")
    logger.info(f"Provider: {os.getenv('PROVIDER', 'openai')}")
    logger.info(f"Model: {os.getenv('MODEL_NAME', 'gpt-4o-mini')}")


# Shutdown event
@app.on_event("shutdown")
async def shutdown_event():
    """Run on application shutdown."""
    logger.info("ContentCraft AI API shutting down...")


if __name__ == "__main__":
    import uvicorn
    
    uvicorn.run(
        "main:app",
        host="0.0.0.0",
        port=8000,
        reload=True,
        log_level="info",
    )


