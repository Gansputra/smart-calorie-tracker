"""
Model Loader — ISOLATION LAYER

This module is the ONLY place where the AI model is loaded and managed.
To replace the placeholder model with your final custom model:

1. Change AI_MODEL_TYPE in .env to "custom"
2. Place your .h5 or .keras model file in the models/ directory
3. Update the load_custom_model() function to match your model's input shape

Zero changes needed in routes, services, or the Laravel application.
"""

import logging
from functools import lru_cache
from typing import Any, Optional

logger = logging.getLogger(__name__)


class ModelLoader:
    """
    Singleton model loader.
    Loads and caches the AI model based on configuration.
    """

    _instance: Optional["ModelLoader"] = None
    _model: Any = None
    _model_type: str = "none"
    _is_loaded: bool = False

    def __new__(cls):
        if cls._instance is None:
            cls._instance = super().__new__(cls)
        return cls._instance

    def load(self, model_type: str = "mobilenet_placeholder", model_path: str = "models/") -> bool:
        """
        Load the AI model based on model_type.

        Args:
            model_type: "mobilenet_placeholder" | "custom"
            model_path: Path to custom model directory

        Returns:
            True if loaded successfully, False otherwise
        """
        self._model_type = model_type

        if model_type == "mobilenet_placeholder":
            return self._load_mobilenet_placeholder()
        elif model_type == "custom":
            return self._load_custom_model(model_path)
        else:
            logger.error(f"Unknown model type: {model_type}")
            return False

    def _load_mobilenet_placeholder(self) -> bool:
        """
        Load MobileNetV2 pre-trained on ImageNet as a placeholder.
        This model is used until the custom food classification model is ready.
        """
        try:
            import tensorflow as tf
            logger.info("Loading MobileNetV2 placeholder model (ImageNet weights)...")
            self._model = tf.keras.applications.MobileNetV2(
                weights="imagenet",
                include_top=True
            )
            self._is_loaded = True
            logger.info("✅ MobileNetV2 placeholder loaded successfully.")
            return True
        except ImportError:
            logger.error("TensorFlow not installed. Run: pip install tensorflow")
            return False
        except Exception as e:
            logger.error(f"Failed to load MobileNetV2: {e}")
            return False

    def _load_custom_model(self, model_path: str) -> bool:
        """
        === SWAP POINT — Replace with your final custom model here ===

        To use your final model:
        1. Export your trained model as .h5 or .keras format
        2. Place it in the models/ directory
        3. Update the filename below
        4. Adjust preprocess() and decode_predictions() in prediction_service.py
        """
        import os
        import tensorflow as tf

        model_file = None
        for ext in [".keras", ".h5"]:
            candidate = os.path.join(model_path, f"food_classifier{ext}")
            if os.path.exists(candidate):
                model_file = candidate
                break

        if model_file is None:
            logger.error(f"No model file found in {model_path}. Expected: food_classifier.keras or food_classifier.h5")
            return False

        try:
            logger.info(f"Loading custom model from {model_file}...")
            self._model = tf.keras.models.load_model(model_file)
            self._is_loaded = True
            logger.info(f"✅ Custom model loaded: {model_file}")
            return True
        except Exception as e:
            logger.error(f"Failed to load custom model: {e}")
            return False

    @property
    def model(self) -> Any:
        return self._model

    @property
    def model_type(self) -> str:
        return self._model_type

    @property
    def is_loaded(self) -> bool:
        return self._is_loaded


# Global singleton instance
model_loader = ModelLoader()
