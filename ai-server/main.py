"""
Smart Calorie Tracker — AI Server
FastAPI application entry point.

Architecture:
  main.py → app/api/routes/predict.py → app/services/prediction_service.py → app/models/model_loader.py

To run:
  uvicorn main:app --host 0.0.0.0 --port 8001 --reload
"""

import logging

from contextlib import asynccontextmanager
from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware

from app.api.routes.predict import router as predict_router
from app.core.config import get_settings
from app.models.model_loader import model_loader
from app.schemas.response import HealthResponse

# Configure logging
logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s | %(levelname)s | %(name)s — %(message)s",
    datefmt="%Y-%m-%d %H:%M:%S",
)
logger = logging.getLogger(__name__)
settings = get_settings()


@asynccontextmanager
async def lifespan(app: FastAPI):
    """
    Startup/shutdown lifecycle manager.
    Model is loaded once on startup and cached for all requests.
    """
    # Startup
    logger.info("🚀 Starting Smart Calorie Tracker AI Server...")
    logger.info(f"   Model type: {settings.AI_MODEL_TYPE}")
    logger.info(f"   Model path: {settings.AI_MODEL_PATH}")

    loaded = model_loader.load(
        model_type=settings.AI_MODEL_TYPE,
        model_path=settings.AI_MODEL_PATH,
    )

    if loaded:
        logger.info("✅ AI Server ready. Accepting requests.")
    else:
        logger.warning("⚠️ Model failed to load. /predict endpoint will return 503.")

    yield

    # Shutdown
    logger.info("👋 Shutting down AI Server.")


# Create FastAPI app
app = FastAPI(
    title=settings.APP_TITLE,
    version=settings.APP_VERSION,
    description="""
## Smart Calorie Tracker — AI Server

This server provides AI-powered food recognition from images.
It is consumed by the Laravel web application via REST API.

### Endpoints
- **GET /** — Root/welcome
- **GET /health** — Model health check
- **POST /predict** — Food prediction from image
    """,
    docs_url="/docs",
    redoc_url="/redoc",
    lifespan=lifespan,
)

# CORS — allow Laravel app to call this API
app.add_middleware(
    CORSMiddleware,
    allow_origins=["http://localhost:8000", "http://127.0.0.1:8000", "*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Register routers
app.include_router(predict_router, tags=["Prediction"])


@app.get("/", tags=["Health"])
async def root():
    """Welcome endpoint."""
    return {
        "message": "Smart Calorie Tracker — AI Server is running 🚀",
        "version": settings.APP_VERSION,
        "docs": "/docs",
        "model_loaded": model_loader.is_loaded,
    }


@app.get("/health", response_model=HealthResponse, tags=["Health"])
async def health_check():
    """Health check endpoint for Laravel to verify AI server status."""
    return HealthResponse(
        status="ok" if model_loader.is_loaded else "degraded",
        message="AI Server is operational" if model_loader.is_loaded else "Model not loaded",
        model_type=model_loader.model_type,
        model_loaded=model_loader.is_loaded,
    )


if __name__ == "__main__":
    import uvicorn
    uvicorn.run(
        "main:app",
        host=settings.HOST,
        port=settings.PORT,
        reload=settings.DEBUG,
        log_level="info",
    )
