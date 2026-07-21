import io
import numpy as np
import pandas as pd
import tensorflow as tf
from PIL import Image
from fastapi import FastAPI, UploadFile, File

import joblib
import os
from pydantic import BaseModel, Field

app = FastAPI(title="Smart Calorie Tracker AI Server")

MODEL_PATH = "model_cnn.keras"
CSV_PATH = "nutrition.csv"
RECOMMENDATION_MODEL_PATH = "target_recommendation_model.pkl"

# 1. Load Model Lokal dan CSV Kamus Nutrisi
model = tf.keras.models.load_model(MODEL_PATH, compile=False)
nutrition_df = pd.read_csv(CSV_PATH)
nutrition_df = nutrition_df.rename(columns={'food_item': 'food_name'})

# Load Model Rekomendasi Target
recommendation_model = None
if os.path.exists(RECOMMENDATION_MODEL_PATH):
    recommendation_model = joblib.load(RECOMMENDATION_MODEL_PATH)


# Kamus indeks asli model kelompok bos
idx_to_class = {
    0: 'apple_pie', 1: 'baby_back_ribs', 2: 'baklava', 3: 'beef_carpaccio',
    4: 'beef_tartare', 5: 'beet_salad', 6: 'beignets', 7: 'bibimbap',
    8: 'bread_pudding', 9: 'breakfast_burrito', 10: 'bruschetta', 11: 'caesar_salad',
    12: 'cannoli', 13: 'caprese_salad', 14: 'carrot_cake', 15: 'ceviche',
    16: 'cheese_plate', 17: 'cheesecake', 18: 'chicken_curry', 19: 'chicken_quesadilla',
    20: 'chicken_wings', 21: 'chocolate_cake', 22: 'chocolate_mousse', 23: 'churros',
    24: 'clam_chowder', 25: 'club_sandwich', 26: 'crab_cakes', 27: 'creme_brulee',
    28: 'croque_madame', 29: 'cup_cakes', 30: 'deviled_eggs', 31: 'donuts',
    32: 'dumplings', 33: 'edamame', 34: 'eggs_benedict', 35: 'escargots',
    36: 'falafel', 37: 'filet_mignon', 38: 'fish_and_chips', 39: 'foie_gras',
    40: 'french_fries', 41: 'french_onion_soup', 42: 'french_toast', 43: 'fried_calamari',
    44: 'fried_rice', 45: 'frozen_yogurt', 46: 'garlic_bread', 47: 'gnocchi',
    48: 'greek_salad', 49: 'grilled_cheese_sandwich', 50: 'grilled_salmon', 51: 'guacamole',
    52: 'gyoza', 53: 'hamburger', 54: 'hot_and_sour_soup', 55: 'hot_dog',
    56: 'huevos_rancheros', 57: 'hummus', 58: 'ice_cream', 59: 'lasagna',
    60: 'lobster_bisque', 61: 'lobster_roll_sandwich', 62: 'macaroni_and_cheese', 63: 'macarons',
    64: 'miso_soup', 65: 'mussels', 66: 'nachos', 67: 'omelette',
    68: 'onion_rings', 69: 'oysters', 70: 'pad_thai', 71: 'paella',
    72: 'pancakes', 73: 'panna_cotta', 74: 'peking_duck', 75: 'pho',
    76: 'pizza', 77: 'pork_chop', 78: 'poutine', 79: 'prime_rib',
    80: 'pulled_pork_sandwich', 81: 'ramen', 82: 'ravioli', 83: 'red_velvet_cake',
    84: 'risotto', 85: 'samosa', 86: 'sashimi', 87: 'scallops',
    88: 'seaweed_salad', 89: 'shrimp_and_grits', 90: 'spaghetti_bolognese', 91: 'spaghetti_carbonara',
    92: 'spring_rolls', 93: 'steak', 94: 'strawberry_shortcake', 95: 'sushi',
    96: 'tacos', 97: 'takoyaki', 98: 'tiramisu', 99: 'tuna_tartare', 100: 'waffles'
}


def preprocess_image(image_bytes):
    image = Image.open(io.BytesIO(image_bytes)).convert("RGB")
    image = image.resize((160, 160))
    img_array = tf.keras.preprocessing.image.img_to_array(image) / 255.0
    img_array = np.expand_dims(img_array, axis=0)
    return img_array


@app.post("/predict")
async def predict_food(file: UploadFile = File(...)):
    try:
        contents = await file.read()

        # 1. Jalur Model Lokal
        ready_image = preprocess_image(contents)
        pred = model.predict(ready_image)
        class_idx = np.argmax(pred)
        confidence = float(np.max(pred))

        # 2. SELEKSI KETAT BUKAN MAKANAN (FILTER AKURASI RENDAH)
        # Jika confidence di bawah 30%, langsung ditolak demi keamanan akurasi
        if confidence < 0.30:
            print(
                f"[REJECT] Objek terdeteksi bukan makanan atau akurasi terlalu rendah: {confidence:.2f}")
            return {
                "status": "error",
                "message": "Gagal memprediksi makanan. Silakan coba gunakan gambar makanan dengan pencahayaan yang lebih baik."
            }

        # 3. Jalur Utama Klasifikasi Sukses
        food = idx_to_class[class_idx]
        row = nutrition_df[nutrition_df.food_name.str.lower() == food.lower()]

        food_name = food.replace("_", " ").title()
        calories = 0.0
        protein = 0.0

        if not row.empty:
            food_name = row['food_name'].values[0]
            calories = float(row['calories_kcal'].values[0])
            protein = float(row['protein_g'].values[0])

        return {
            "status": "success",
            "confidence": round(confidence, 2),
            "nama_makanan": food_name,
            "kalori_dasar": calories,
            "protein_dasar": protein
        }

    except Exception as e:
        print(f"[LOG ERROR ASLI]: {str(e)}")
        return {
            "status": "error",
            "message": "Gagal memprediksi makanan. Silakan coba gunakan gambar lain dengan pencahayaan yang lebih baik."
        }


class TargetRecommendationRequest(BaseModel):
    gender: int = Field(..., description="0 = Female, 1 = Male")
    age: int = Field(..., ge=10, le=100, description="Usia dalam tahun")
    weight: float = Field(..., ge=30.0, le=250.0, description="Berat badan dalam kg")
    height: float = Field(..., ge=100.0, le=250.0, description="Tinggi badan dalam cm")
    activity_level: int = Field(..., ge=0, le=3, description="0=Sedentary, 1=Light, 2=Moderate, 3=Active")
    goal: int = Field(..., ge=0, le=2, description="0=Weight Loss, 1=Maintenance, 2=Weight Gain")


@app.get("/")
@app.get("/health")
def health_check():
    return {"status": "ok", "message": "AI Server is running"}


@app.post("/recommend-target")
def recommend_target(req: TargetRecommendationRequest):
    global recommendation_model
    
    # Reload model jika baru saja di-train
    if recommendation_model is None and os.path.exists(RECOMMENDATION_MODEL_PATH):
        recommendation_model = joblib.load(RECOMMENDATION_MODEL_PATH)
        
    if recommendation_model is None:
        # Fallback rumus jika model belum ada
        bmr = (10 * req.weight + 6.25 * req.height - 5 * req.age + 5) if req.gender == 1 else (10 * req.weight + 6.25 * req.height - 5 * req.age - 161)
        pal_map = {0: 1.2, 1: 1.375, 2: 1.55, 3: 1.725}
        tdee = bmr * pal_map.get(req.activity_level, 1.2)
        cal_adj = {0: 0.85, 1: 1.0, 2: 1.15}.get(req.goal, 1.0)
        prot_fact = {0: 2.0, 1: 1.4, 2: 1.8}.get(req.goal, 1.4)
        
        return {
            "status": "success",
            "recommended_calories": round(tdee * cal_adj),
            "recommended_protein": round(req.weight * prot_fact),
            "source": "formula_fallback"
        }

    input_df = pd.DataFrame([{
        'gender': req.gender,
        'age': req.age,
        'weight': req.weight,
        'height': req.height,
        'activity_level': req.activity_level,
        'goal': req.goal
    }])
    
    prediction = recommendation_model.predict(input_df)[0]
    
    return {
        "status": "success",
        "recommended_calories": int(round(prediction[0])),
        "recommended_protein": int(round(prediction[1])),
        "source": "machine_learning"
    }


# Menjalankan server otomatis di port 8080 agar tidak bentrok dengan Laravel
if __name__ == "__main__":
    import uvicorn
    uvicorn.run("main:app", host="127.0.0.1", port=8080, reload=True)

