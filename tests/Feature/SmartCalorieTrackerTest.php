<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\FoodLog;
use App\Models\WeightLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SmartCalorieTrackerTest extends TestCase
{
    /**
     * Test the full user flow: dashboard, AI scanning (integration), logging food, and recording weight.
     */
    public function test_full_application_flow(): void
    {
        // 1. Retrieve the seeded demo user
        $user = User::where('email', 'demo@smartcalorietracker.com')->first();
        $this->assertNotNull($user, 'Seeded demo user should exist');

        // 2. Act as the user and visit the dashboard
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee('demo@smartcalorietracker.com');

        // 3. Test AI Server connectivity & prediction (Integration Test)
        // We will upload a small dummy JPEG or reuse the downloaded pizza.jpg
        $pizzaPath = base_path('pizza.jpg');
        $this->assertFileExists($pizzaPath, 'Test pizza.jpg must be present in root');

        $uploadedFile = new UploadedFile(
            $pizzaPath,
            'pizza.jpg',
            'image/jpeg',
            null,
            true // test mode
        );

        // Call the predict endpoint
        $predictResponse = $this->actingAs($user)->post('/scanner/predict', [
            'image' => $uploadedFile,
        ]);

        // Assert response structure matches contract
        $predictResponse->assertStatus(200);
        $data = $predictResponse->json();
        $this->assertTrue($data['success'], 'AI Prediction should be successful');
        $this->assertNotEmpty($data['food_name'], 'Predicted food name should not be empty');
        $this->assertGreaterThan(0, $data['calories'], 'Predicted calories should be > 0');
        $this->assertGreaterThanOrEqual(0, $data['protein'], 'Predicted protein should be >= 0');

        $foodName = $data['food_name'];
        $calories = $data['calories'];
        $protein = $data['protein'];
        $confidence = $data['confidence'];

        // 4. Save the prediction to the Jurnal Makanan
        $saveResponse = $this->actingAs($user)->post('/scanner/save', [
            'food_name' => $foodName,
            'calories' => $calories,
            'protein' => $protein,
            'confidence' => $confidence,
            'portion' => 1.5,
            'meal_type' => 'Sarapan',
            'notes' => 'Makan pagi pizza hasil AI',
        ]);

        $saveResponse->assertRedirect(route('food-log.index'));

        // Assert food log was created in DB
        $this->assertDatabaseHas('food_logs', [
            'user_id' => $user->id,
            'food_name' => $foodName,
            'portion' => 1.50,
            'meal_type' => 'Sarapan',
            'notes' => 'Makan pagi pizza hasil AI',
            'ai_detected' => true,
        ]);

        // 5. Test Fat Loss Tracker (Weight Logging)
        $weightResponse = $this->actingAs($user)->post('/weight-log', [
            'weight' => 75.2,
            'date' => today()->format('Y-m-d'),
            'notes' => 'Awal program diet',
        ]);

        $weightResponse->assertRedirect(route('weight-log.index'));

        // Assert weight log was created in DB
        $this->assertDatabaseHas('weight_logs', [
            'user_id' => $user->id,
            'weight' => 75.2,
            'notes' => 'Awal program diet',
            'date' => today()->format('Y-m-d'),
        ]);

        // 6. Test Admin access block for normal user
        $adminResponse = $this->actingAs($user)->get('/admin/dashboard');
        $adminResponse->assertStatus(403); // Forbidden

        // 7. Log in as admin and visit Admin Panel
        $adminUser = User::where('email', 'admin@smartcalorietracker.com')->first();
        $this->assertNotNull($adminUser, 'Seeded admin user should exist');

        $adminDashboardResponse = $this->actingAs($adminUser)->get('/admin/dashboard');
        $adminDashboardResponse->assertStatus(200);
        $adminDashboardResponse->assertSee('Dashboard Admin');
        $adminDashboardResponse->assertSee('Total User');
    }
}
