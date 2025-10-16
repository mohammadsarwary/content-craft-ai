"""
LLM Provider Abstraction Layer.

Supports multiple AI providers: OpenAI, Anthropic, Ollama.
"""

from abc import ABC, abstractmethod
from typing import Optional, Dict, Any
import os
import logging
import json
import httpx

logger = logging.getLogger(__name__)


class LLMProvider(ABC):
    """Abstract base class for LLM providers."""
    
    def __init__(self, model_name: Optional[str] = None):
        """
        Initialize provider.
        
        Args:
            model_name: Override default model name
        """
        self.model_name = model_name or self.get_default_model()
        self.last_tokens_used = 0
    
    @abstractmethod
    def get_default_model(self) -> str:
        """Get default model name for this provider."""
        pass
    
    @abstractmethod
    async def generate(
        self,
        prompt: str,
        system_message: Optional[str] = None,
        temperature: float = 0.7,
        max_tokens: int = 2000,
        json_mode: bool = True,
        **kwargs
    ) -> str:
        """
        Generate text from prompt.
        
        Args:
            prompt: User prompt
            system_message: System message (optional)
            temperature: Sampling temperature
            max_tokens: Maximum tokens to generate
            json_mode: Whether to enforce JSON output
            **kwargs: Provider-specific parameters
            
        Returns:
            Generated text
        """
        pass
    
    async def generate_json(
        self,
        prompt: str,
        system_message: Optional[str] = None,
        **kwargs
    ) -> Dict[str, Any]:
        """
        Generate JSON response.
        
        Args:
            prompt: User prompt
            system_message: System message
            **kwargs: Additional parameters
            
        Returns:
            Parsed JSON dict
            
        Raises:
            ValueError: If response is not valid JSON
        """
        response = await self.generate(
            prompt=prompt,
            system_message=system_message,
            json_mode=True,
            **kwargs
        )
        
        try:
            return json.loads(response)
        except json.JSONDecodeError as e:
            logger.error(f"Failed to parse JSON response: {response}")
            raise ValueError(f"Invalid JSON response from LLM: {str(e)}")


class OpenAIProvider(LLMProvider):
    """OpenAI provider (GPT-4, GPT-3.5)."""
    
    def __init__(self, model_name: Optional[str] = None):
        """Initialize OpenAI provider."""
        self.api_key = os.getenv("OPENAI_API_KEY")
        if not self.api_key:
            raise ValueError("OPENAI_API_KEY environment variable not set")
        
        super().__init__(model_name)
        
        try:
            from openai import AsyncOpenAI
            self.client = AsyncOpenAI(api_key=self.api_key)
        except ImportError:
            raise ImportError("openai package not installed. Run: pip install openai")
    
    def get_default_model(self) -> str:
        """Get default OpenAI model."""
        return os.getenv("MODEL_NAME", "gpt-4o-mini")
    
    async def generate(
        self,
        prompt: str,
        system_message: Optional[str] = None,
        temperature: float = 0.7,
        max_tokens: int = 2000,
        json_mode: bool = True,
        **kwargs
    ) -> str:
        """Generate text using OpenAI API."""
        messages = []
        
        if system_message:
            messages.append({"role": "system", "content": system_message})
        
        messages.append({"role": "user", "content": prompt})
        
        # Build request parameters
        request_params = {
            "model": self.model_name,
            "messages": messages,
            "temperature": temperature,
            "max_tokens": max_tokens,
        }
        
        # Enable JSON mode if supported and requested
        if json_mode and self.model_name.startswith("gpt-4"):
            request_params["response_format"] = {"type": "json_object"}
        
        # Add any extra kwargs
        request_params.update(kwargs)
        
        logger.debug(f"OpenAI request: model={self.model_name}, tokens={max_tokens}")
        
        try:
            response = await self.client.chat.completions.create(**request_params)
            
            # Extract content
            content = response.choices[0].message.content
            
            # Track token usage
            if response.usage:
                self.last_tokens_used = response.usage.total_tokens
                logger.debug(f"OpenAI tokens used: {self.last_tokens_used}")
            
            return content
            
        except Exception as e:
            logger.error(f"OpenAI API error: {str(e)}")
            raise Exception(f"OpenAI generation failed: {str(e)}")


class AnthropicProvider(LLMProvider):
    """Anthropic provider (Claude)."""
    
    def __init__(self, model_name: Optional[str] = None):
        """Initialize Anthropic provider."""
        self.api_key = os.getenv("ANTHROPIC_API_KEY")
        if not self.api_key:
            raise ValueError("ANTHROPIC_API_KEY environment variable not set")
        
        super().__init__(model_name)
        
        try:
            from anthropic import AsyncAnthropic
            self.client = AsyncAnthropic(api_key=self.api_key)
        except ImportError:
            raise ImportError("anthropic package not installed. Run: pip install anthropic")
    
    def get_default_model(self) -> str:
        """Get default Anthropic model."""
        return os.getenv("MODEL_NAME", "claude-3-5-sonnet-20241022")
    
    async def generate(
        self,
        prompt: str,
        system_message: Optional[str] = None,
        temperature: float = 0.7,
        max_tokens: int = 2000,
        json_mode: bool = True,
        **kwargs
    ) -> str:
        """Generate text using Anthropic API."""
        # Anthropic requires system message separately
        request_params = {
            "model": self.model_name,
            "max_tokens": max_tokens,
            "temperature": temperature,
            "messages": [{"role": "user", "content": prompt}],
        }
        
        if system_message:
            request_params["system"] = system_message
        
        # Add any extra kwargs
        request_params.update(kwargs)
        
        logger.debug(f"Anthropic request: model={self.model_name}, tokens={max_tokens}")
        
        try:
            response = await self.client.messages.create(**request_params)
            
            # Extract content
            content = response.content[0].text
            
            # Track token usage
            if response.usage:
                self.last_tokens_used = response.usage.input_tokens + response.usage.output_tokens
                logger.debug(f"Anthropic tokens used: {self.last_tokens_used}")
            
            return content
            
        except Exception as e:
            logger.error(f"Anthropic API error: {str(e)}")
            raise Exception(f"Anthropic generation failed: {str(e)}")


class OllamaProvider(LLMProvider):
    """Ollama provider (local models)."""
    
    def __init__(self, model_name: Optional[str] = None):
        """Initialize Ollama provider."""
        self.base_url = os.getenv("OLLAMA_HOST", "http://localhost:11434")
        super().__init__(model_name)
    
    def get_default_model(self) -> str:
        """Get default Ollama model."""
        return os.getenv("MODEL_NAME", "llama2")
    
    async def generate(
        self,
        prompt: str,
        system_message: Optional[str] = None,
        temperature: float = 0.7,
        max_tokens: int = 2000,
        json_mode: bool = True,
        **kwargs
    ) -> str:
        """Generate text using Ollama API."""
        # Build full prompt
        full_prompt = prompt
        if system_message:
            full_prompt = f"{system_message}\n\n{prompt}"
        
        if json_mode:
            full_prompt += "\n\nRespond ONLY with valid JSON. No markdown, no explanations."
        
        request_data = {
            "model": self.model_name,
            "prompt": full_prompt,
            "temperature": temperature,
            "stream": False,
            "options": {
                "num_predict": max_tokens,
            }
        }
        
        logger.debug(f"Ollama request: model={self.model_name}, url={self.base_url}")
        
        try:
            async with httpx.AsyncClient(timeout=120.0) as client:
                response = await client.post(
                    f"{self.base_url}/api/generate",
                    json=request_data
                )
                response.raise_for_status()
                
                result = response.json()
                content = result.get("response", "")
                
                # Estimate token usage (Ollama doesn't provide exact counts)
                self.last_tokens_used = len(content.split()) * 1.3  # Rough estimate
                
                return content
                
        except Exception as e:
            logger.error(f"Ollama API error: {str(e)}")
            raise Exception(f"Ollama generation failed: {str(e)}")


class CustomProvider(LLMProvider):
    """Custom API provider."""
    
    def __init__(self, model_name: Optional[str] = None):
        """Initialize custom provider."""
        self.api_url = os.getenv("CUSTOM_API_URL")
        self.api_key = os.getenv("CUSTOM_API_KEY")
        
        if not self.api_url:
            raise ValueError("CUSTOM_API_URL environment variable not set")
        
        super().__init__(model_name)
    
    def get_default_model(self) -> str:
        """Get default custom model."""
        return os.getenv("MODEL_NAME", "custom-model")
    
    async def generate(
        self,
        prompt: str,
        system_message: Optional[str] = None,
        temperature: float = 0.7,
        max_tokens: int = 2000,
        json_mode: bool = True,
        **kwargs
    ) -> str:
        """Generate text using custom API."""
        # Implement custom API logic
        # This is a placeholder - customize based on your API
        raise NotImplementedError("Custom provider needs to be implemented for your specific API")


def get_provider(provider_name: Optional[str] = None) -> LLMProvider:
    """
    Get LLM provider instance.
    
    Args:
        provider_name: Provider name (openai/anthropic/ollama/custom)
                      If None, uses PROVIDER env var
                      
    Returns:
        LLM provider instance
        
    Raises:
        ValueError: If provider is unknown or not configured
    """
    if not provider_name:
        provider_name = os.getenv("PROVIDER", "openai")
    
    provider_name = provider_name.lower()
    
    logger.info(f"Initializing LLM provider: {provider_name}")
    
    providers = {
        "openai": OpenAIProvider,
        "anthropic": AnthropicProvider,
        "ollama": OllamaProvider,
        "custom": CustomProvider,
    }
    
    if provider_name not in providers:
        raise ValueError(
            f"Unknown provider: {provider_name}. "
            f"Supported providers: {', '.join(providers.keys())}"
        )
    
    try:
        return providers[provider_name]()
    except Exception as e:
        logger.error(f"Failed to initialize provider {provider_name}: {str(e)}")
        raise

