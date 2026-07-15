"""
Prediction Service — Business Logic Layer

Handles the complete prediction pipeline:
  1. Image preprocessing (resize, normalize)
  2. Model inference
  3. Post-processing (map predictions → Indonesian food data)

When the custom model is ready:
- Update preprocess() if input shape differs
- Update decode_prediction() to use the custom model's class labels
- Model loading itself is handled separately in model_loader.py
"""

import json
import logging
import os
import random
from pathlib import Path
from typing import Any, Optional, Tuple

import numpy as np

logger = logging.getLogger(__name__)

# Path to the Indonesian food nutrition map
NUTRITION_MAP_PATH = Path(__file__).parent.parent / "models" / "food_nutrition_map.json"

# ImageNet class index to Indonesian food name mapping
# This is the mapping used for the MobileNetV2 placeholder
IMAGENET_TO_INDONESIAN_FOOD = {
    "hotdog": "Sosis",
    "pizza": "Pizza",
    "hamburger": "Burger",
    "french_loaf": "Roti Tawar",
    "bakery": "Roti Bakar",
    "banana": "Pisang",
    "orange": "Jeruk",
    "mango": "Mangga",
    "pineapple": "Nanas",
    "strawberry": "Strawberry",
    "broccoli": "Brokoli",
    "cauliflower": "Kembang Kol",
    "eggplant": "Terong",
    "corn": "Jagung",
    "mushroom": "Jamur",
    "egg": "Telur Goreng",
    "guacamole": "Alpukat",
    "ice_cream": "Es Krim",
    "chocolate": "Coklat",
    "cup": "Es Teh Manis",
    "soup_bowl": "Soto Ayam",
    "meat": "Ayam Goreng",
    "pork": "Babi (Tidak Halal)",
    "chicken": "Ayam Goreng",
    "fish": "Ikan Goreng",
    "spaghetti": "Mie Goreng",
    "noodle": "Mie Ayam",
    "rice": "Nasi Putih",
    "dumpling": "Siomay",
    "tofu": "Tahu Goreng",
}


class PredictionService:
    """
    Handles image preprocessing, model inference, and result post-processing.
    """

    def __init__(self):
        self._nutrition_map = self._load_nutrition_map()
        self._food_names = list(self._nutrition_map.keys())

    def _load_nutrition_map(self) -> dict:
        """Load the Indonesian food nutrition database."""
        try:
            with open(NUTRITION_MAP_PATH, "r", encoding="utf-8") as f:
                data = json.load(f)
            logger.info(f"✅ Loaded nutrition map with {len(data)} foods.")
            return data
        except FileNotFoundError:
            logger.error(f"Nutrition map not found at {NUTRITION_MAP_PATH}")
            return {}
        except Exception as e:
            logger.error(f"Failed to load nutrition map: {e}")
            return {}

    def preprocess(self, image) -> np.ndarray:
        """
        Preprocess a PIL Image for MobileNetV2 input.

        For custom model: adjust target_size and normalization as needed.
        MobileNetV2 expects:
          - Input shape: (1, 224, 224, 3)
          - Pixel values: [-1.0, 1.0] via preprocess_input

        Args:
            image: PIL.Image.Image

        Returns:
            Preprocessed numpy array ready for model.predict()
        """
        # pyrefly: ignore [missing-import]
        from tensorflow.keras.applications.mobilenet_v2 import preprocess_input

        # Ensure RGB (some images may be RGBA or grayscale)
        image = image.convert("RGB")

        # Resize to model's expected input
        image = image.resize((224, 224))

        # Convert to numpy and preprocess
        img_array = np.array(image, dtype=np.float32)
        img_array = np.expand_dims(img_array, axis=0)
        img_array = preprocess_input(img_array)

        return img_array

    def predict(self, model: Any, image) -> Tuple[str, float]:
        """
        Run inference and return (food_name_indonesian, confidence).

        For MobileNetV2 placeholder: maps ImageNet labels → Indonesian food.
        For custom model: map class index directly to food label.

        Args:
            model: Loaded Keras model
            image: PIL.Image.Image

        Returns:
            Tuple of (indonesian_food_name, confidence_score)
        """
        # pyrefly: ignore [missing-import]
        from tensorflow.keras.applications.mobilenet_v2 import decode_predictions

        img_array = self.preprocess(image)
        predictions = model.predict(img_array, verbose=0)

        # Decode top-5 ImageNet predictions
        decoded = decode_predictions(predictions, top=5)[0]
        top_class_id, top_class_name, top_confidence = decoded[0]

        # Try to map to Indonesian food
        indonesian_name = self._map_to_indonesian(top_class_name, decoded)

        return indonesian_name, float(top_confidence)

    def _map_to_indonesian(self, imagenet_class: str, all_predictions: list) -> str:
        """
        Map ImageNet class name to Indonesian food name.

        Tries multiple fallback strategies:
        1. Direct mapping from known ImageNet → Indonesian foods
        2. Keyword matching in top-5 predictions
        3. Random selection from nutrition map (for demo purposes)
        """
        # Direct mapping
        for class_id, class_name, confidence in all_predictions:
            if class_name.lower() in IMAGENET_TO_INDONESIAN_FOOD:
                return IMAGENET_TO_INDONESIAN_FOOD[class_name.lower()]

        # Keyword matching
        for keyword, indonesian in IMAGENET_TO_INDONESIAN_FOOD.items():
            if keyword in imagenet_class.lower():
                return indonesian

        # Fallback: return a random food from our database
        # In production, this will be replaced by the custom model's actual prediction
        logger.info(f"No mapping found for '{imagenet_class}', using random food for demo.")
        return random.choice(self._food_names)

    def get_nutrition(self, food_name: str) -> dict:
        """
        Get nutritional information for a food from our database.

        Args:
            food_name: Indonesian food name

        Returns:
            Dict with calories, protein, category
        """
        if food_name in self._nutrition_map:
            return self._nutrition_map[food_name]

        # Fuzzy match: find closest food
        for name, data in self._nutrition_map.items():
            if food_name.lower() in name.lower() or name.lower() in food_name.lower():
                return data

        # Default fallback values
        return {"calories": 150.0, "protein": 5.0, "category": "Umum"}

    def run_full_pipeline(self, model: Any, image, model_type: str = "mobilenet_placeholder") -> dict:
        """
        Execute the complete prediction pipeline.

        Args:
            model: Loaded Keras model
            image: PIL.Image.Image
            model_type: Type of model being used

        Returns:
            Dict matching PredictionResponse schema
        """
        try:
            food_name, confidence = self.predict(model, image)
            nutrition = self.get_nutrition(food_name)

            return {
                "success": True,
                "food_name": food_name,
                "calories": float(nutrition["calories"]),
                "protein": float(nutrition["protein"]),
                "confidence": round(confidence, 4),
                "category": nutrition.get("category", "Umum"),
                "error": None,
            }
        except Exception as e:
            logger.error(f"Prediction pipeline error: {e}", exc_info=True)
            return {
                "success": False,
                "food_name": "",
                "calories": 0.0,
                "protein": 0.0,
                "confidence": 0.0,
                "category": None,
                "error": f"Terjadi kesalahan saat memproses gambar: {str(e)}",
            }


# Global singleton instance
prediction_service = PredictionService()
