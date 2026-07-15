"""
Core application settings.
Uses pydantic-settings for type-safe configuration loading from environment variables.
"""

from pydantic_settings import BaseSettings
from functools import lru_cache


class Settings(BaseSettings):
    """Application configuration loaded from environment variables / .env file."""

    # Server settings
    HOST: str = "0.0.0.0"
    PORT: int = 8001
    DEBUG: bool = True

    # Model configuration
    # AI_MODEL_TYPE options:
    #   "mobilenet_placeholder" — Uses pre-trained MobileNetV2 (ImageNet) as placeholder
    #   "custom"                — Loads a custom .h5/.keras model from AI_MODEL_PATH
    AI_MODEL_TYPE: str = "mobilenet_placeholder"
    AI_MODEL_PATH: str = "models/"

    # App metadata
    APP_TITLE: str = "Smart Calorie Tracker — AI Server"
    APP_VERSION: str = "1.0.0"

    class Config:
        env_file = ".env"
        env_file_encoding = "utf-8"


@lru_cache()
def get_settings() -> Settings:
    """Return cached settings instance."""
    return Settings()
