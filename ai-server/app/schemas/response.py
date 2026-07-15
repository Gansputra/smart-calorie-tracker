"""
Pydantic response schemas for the AI prediction API.
These schemas define the contract between the AI Server and Laravel.
"""

from pydantic import BaseModel, Field
from typing import Optional


class PredictionResponse(BaseModel):
    """
    Standard prediction response returned by POST /predict.

    This schema is the contract with the Laravel application.
    All fields must remain stable even when swapping the underlying AI model.
    """
    success: bool = Field(..., description="Whether the prediction was successful")
    food_name: str = Field(..., description="Detected food name (in Indonesian)")
    calories: float = Field(..., description="Calories per 100g (1 serving)")
    protein: float = Field(..., description="Protein in grams per 100g (1 serving)")
    confidence: float = Field(..., description="Model confidence score (0.0 to 1.0)")
    category: Optional[str] = Field(None, description="Food category")
    error: Optional[str] = Field(None, description="Error message if success=False")

    class Config:
        json_schema_extra = {
            "example": {
                "success": True,
                "food_name": "Nasi Goreng",
                "calories": 185.0,
                "protein": 4.2,
                "confidence": 0.92,
                "category": "Nasi & Roti",
                "error": None
            }
        }


class HealthResponse(BaseModel):
    """Health check response."""
    status: str
    message: str
    model_type: str
    model_loaded: bool
