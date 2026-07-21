import numpy as np
import pandas as pd
import joblib
import os
from sklearn.ensemble import RandomForestRegressor
from sklearn.multioutput import MultiOutputRegressor

def generate_synthetic_data(n_samples=5000):
    """
    Membangkitkan dataset fisiologis sintetis berbasis rumus ilmiah Mifflin-St Jeor:
    BMR Pria   = 10*BB + 6.25*TB - 5*Usia + 5
    BMR Wanita = 10*BB + 6.25*TB - 5*Usia - 161
    
    Aktivitas (PAL):
    - Sedentary (Sangat Jarang): 1.2
    - Lightly active (Ringan): 1.375
    - Moderately active (Sedang): 1.55
    - Very active (Berat): 1.725
    
    Goal Adjustment:
    - Weight Loss (Turun BB): -15% kalori, protein 2.0 g/kg BB
    - Maintenance (Jaga BB): 0% kalori, protein 1.4 g/kg BB
    - Weight Gain / Muscle Building (Naik BB): +15% kalori, protein 1.8 g/kg BB
    """
    np.random.seed(42)
    
    # 0 = Female, 1 = Male
    gender = np.random.randint(0, 2, size=n_samples)
    age = np.random.randint(17, 70, size=n_samples)
    weight = np.random.uniform(40, 130, size=n_samples)  # kg
    height = np.random.uniform(145, 200, size=n_samples)  # cm
    
    # Activity level: 0=Sedentary, 1=Light, 2=Moderate, 3=Active
    activity_level = np.random.randint(0, 4, size=n_samples)
    pal_map = {0: 1.2, 1: 1.375, 2: 1.55, 3: 1.725}
    pal = np.vectorize(pal_map.get)(activity_level)
    
    # Goal: 0=Weight Loss, 1=Maintenance, 2=Weight Gain
    goal = np.random.randint(0, 3, size=n_samples)
    
    # Kalkulasi BMR & TDEE
    bmr = np.where(gender == 1,
                   10 * weight + 6.25 * height - 5 * age + 5,
                   10 * weight + 6.25 * height - 5 * age - 161)
    
    tdee = bmr * pal
    
    # Target Kalori
    caloric_adjustment = np.where(goal == 0, 0.85, np.where(goal == 1, 1.0, 1.15))
    target_calories = tdee * caloric_adjustment
    
    # Target Protein (g/kg BB)
    protein_factor = np.where(goal == 0, 2.0, np.where(goal == 1, 1.4, 1.8))
    target_protein = weight * protein_factor
    
    # Noise realistis
    target_calories += np.random.normal(0, 25, size=n_samples)
    target_protein += np.random.normal(0, 2.5, size=n_samples)
    
    X = pd.DataFrame({
        'gender': gender,
        'age': age,
        'weight': weight,
        'height': height,
        'activity_level': activity_level,
        'goal': goal
    })
    
    Y = pd.DataFrame({
        'recommended_calories': np.round(target_calories),
        'recommended_protein': np.round(target_protein)
    })
    
    return X, Y

def train_and_save_model():
    print("Membangkitkan dataset nutrisi medis sintetis...")
    X, Y = generate_synthetic_data()
    
    print("Melatih model Machine Learning Multi-Output Random Forest...")
    rf = RandomForestRegressor(n_estimators=100, random_state=42)
    model = MultiOutputRegressor(rf)
    model.fit(X, Y)
    
    model_path = os.path.join(os.path.dirname(__file__), 'target_recommendation_model.pkl')
    joblib.dump(model, model_path)
    print(f"Model berhasil dilatih dan disimpan di: {model_path}")

if __name__ == '__main__':
    train_and_save_model()
