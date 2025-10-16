"""
Authentication dependencies.

Handles authentication for API requests from WordPress.
"""

from fastapi import Header, HTTPException, status
import os
import logging

logger = logging.getLogger(__name__)

# Get APP_SECRET from environment
APP_SECRET = os.getenv("APP_SECRET", "")


async def verify_token(authorization: str = Header(None)) -> str:
    """
    Verify Bearer token from WordPress.
    
    Args:
        authorization: Authorization header value
        
    Returns:
        Verified token
        
    Raises:
        HTTPException: If token is invalid
    """
    if not authorization:
        logger.warning("Missing Authorization header")
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail={
                "code": "UNAUTHORIZED",
                "message": "Missing Authorization header",
            },
        )
    
    # Check Bearer token format
    if not authorization.startswith("Bearer "):
        logger.warning("Invalid Authorization header format")
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail={
                "code": "UNAUTHORIZED",
                "message": "Invalid Authorization header format",
            },
        )
    
    # Extract token
    token = authorization.replace("Bearer ", "")
    
    # Verify token against APP_SECRET
    if not APP_SECRET:
        logger.error("APP_SECRET not configured")
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail={
                "code": "CONFIGURATION_ERROR",
                "message": "Server authentication not configured",
            },
        )
    
    if token != APP_SECRET:
        logger.warning("Invalid token provided")
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail={
                "code": "UNAUTHORIZED",
                "message": "Invalid authentication token",
            },
        )
    
    logger.debug("Token verified successfully")
    return token


# Optional: Rate limiting dependency
# Can be implemented with Redis or in-memory store
async def rate_limit(token: str = Header(None)):
    """
    Rate limiting for API requests.
    
    TODO: Implement actual rate limiting logic.
    For now, this is a placeholder.
    """
    # In production, implement rate limiting with Redis
    # Example: Check if token has exceeded rate limit
    # If exceeded, raise HTTPException with 429 status
    pass


