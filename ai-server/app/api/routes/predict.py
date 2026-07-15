import io
import logging

from fastapi import APIRouter, File, HTTPException, UploadFile
from PIL import Image

from app.models.model_loader import model_loader
from app.schemas.response import PredictionResponse
from app.services.prediction_service import prediction_service

logger = logging.getLogger(__name__)

router = APIRouter()


@router.post(
    "/predict",
    response_model=PredictionResponse,
    summary="Predict food from image",
    description="""
    Upload a food image and receive AI prediction with:
    - Detected food name (in Indonesian)
    - Estimated calories per 100g (1 serving)
    - Estimated protein per 100g (1 serving)
    - Model confidence score

    **Request**: multipart/form-data with 'image' field (JPEG/PNG/WebP)
    **Response**: JSON matching PredictionResponse schema
    """,
)
async def predict_food(
    image: UploadFile = File(..., description="Food image (JPEG, PNG, or WebP)"),
):
    """Main prediction endpoint called by Laravel."""

    # Validate model is loaded
    if not model_loader.is_loaded:
        raise HTTPException(
            status_code=503,
            detail="Model is not loaded. Server may be starting up."
        )

    # Validate file type
    content_type = image.content_type or ""
    if not content_type.startswith("image/"):
        raise HTTPException(
            status_code=400,
            detail=f"File must be an image. Received: {content_type}"
        )

    # Read and open image
    try:
        image_bytes = await image.read()
        pil_image = Image.open(io.BytesIO(image_bytes))
    except Exception as e:
        logger.error(f"Failed to read image: {e}")
        raise HTTPException(
            status_code=400,
            detail="Cannot open the uploaded image. Ensure it is a valid image file."
        )

    # Run prediction pipeline
    logger.info(f"Running prediction on image: {image.filename}")
    result = prediction_service.run_full_pipeline(
        model=model_loader.model,
        image=pil_image,
        model_type=model_loader.model_type,
    )

    return PredictionResponse(**result)
