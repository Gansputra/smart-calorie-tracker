<?php

namespace Database\Seeders;

use App\Models\Food;
use Illuminate\Database\Seeder;

class FoodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $foods = [
            // Nasi & Roti
            ['name' => 'Nasi Putih', 'category' => 'Nasi & Roti', 'calories_per_100g' => 130, 'protein_per_100g' => 2.7, 'carbs_per_100g' => 28.2, 'fat_per_100g' => 0.3],
            ['name' => 'Nasi Goreng', 'category' => 'Nasi & Roti', 'calories_per_100g' => 185, 'protein_per_100g' => 4.2, 'carbs_per_100g' => 30.5, 'fat_per_100g' => 5.2],
            ['name' => 'Nasi Kuning', 'category' => 'Nasi & Roti', 'calories_per_100g' => 160, 'protein_per_100g' => 3.0, 'carbs_per_100g' => 31.0, 'fat_per_100g' => 2.5],
            ['name' => 'Nasi Uduk', 'category' => 'Nasi & Roti', 'calories_per_100g' => 178, 'protein_per_100g' => 3.5, 'carbs_per_100g' => 32.0, 'fat_per_100g' => 4.0],
            ['name' => 'Roti Tawar', 'category' => 'Nasi & Roti', 'calories_per_100g' => 265, 'protein_per_100g' => 9.0, 'carbs_per_100g' => 49.0, 'fat_per_100g' => 3.2],
            ['name' => 'Roti Bakar', 'category' => 'Nasi & Roti', 'calories_per_100g' => 290, 'protein_per_100g' => 8.5, 'carbs_per_100g' => 52.0, 'fat_per_100g' => 5.5],

            // Lauk Pauk
            ['name' => 'Ayam Goreng', 'category' => 'Lauk Pauk', 'calories_per_100g' => 265, 'protein_per_100g' => 27.0, 'carbs_per_100g' => 2.0, 'fat_per_100g' => 16.5],
            ['name' => 'Ayam Bakar', 'category' => 'Lauk Pauk', 'calories_per_100g' => 200, 'protein_per_100g' => 28.0, 'carbs_per_100g' => 1.5, 'fat_per_100g' => 9.0],
            ['name' => 'Ayam Geprek', 'category' => 'Lauk Pauk', 'calories_per_100g' => 280, 'protein_per_100g' => 26.0, 'carbs_per_100g' => 5.0, 'fat_per_100g' => 16.0],
            ['name' => 'Rendang Sapi', 'category' => 'Lauk Pauk', 'calories_per_100g' => 245, 'protein_per_100g' => 22.0, 'carbs_per_100g' => 6.0, 'fat_per_100g' => 14.0],
            ['name' => 'Ikan Goreng', 'category' => 'Lauk Pauk', 'calories_per_100g' => 185, 'protein_per_100g' => 24.0, 'carbs_per_100g' => 3.0, 'fat_per_100g' => 8.5],
            ['name' => 'Ikan Bakar', 'category' => 'Lauk Pauk', 'calories_per_100g' => 150, 'protein_per_100g' => 25.0, 'carbs_per_100g' => 0.0, 'fat_per_100g' => 5.0],
            ['name' => 'Tahu Goreng', 'category' => 'Lauk Pauk', 'calories_per_100g' => 160, 'protein_per_100g' => 11.0, 'carbs_per_100g' => 4.5, 'fat_per_100g' => 10.5],
            ['name' => 'Tempe Goreng', 'category' => 'Lauk Pauk', 'calories_per_100g' => 195, 'protein_per_100g' => 18.0, 'carbs_per_100g' => 9.0, 'fat_per_100g' => 10.0],
            ['name' => 'Telur Goreng', 'category' => 'Lauk Pauk', 'calories_per_100g' => 185, 'protein_per_100g' => 13.0, 'carbs_per_100g' => 0.5, 'fat_per_100g' => 14.5],
            ['name' => 'Telur Rebus', 'category' => 'Lauk Pauk', 'calories_per_100g' => 155, 'protein_per_100g' => 13.0, 'carbs_per_100g' => 1.1, 'fat_per_100g' => 10.6],
            ['name' => 'Sate Ayam', 'category' => 'Lauk Pauk', 'calories_per_100g' => 160, 'protein_per_100g' => 20.0, 'carbs_per_100g' => 3.0, 'fat_per_100g' => 7.0],
            ['name' => 'Sate Kambing', 'category' => 'Lauk Pauk', 'calories_per_100g' => 175, 'protein_per_100g' => 21.0, 'carbs_per_100g' => 2.0, 'fat_per_100g' => 9.0],

            // Mie & Pasta
            ['name' => 'Mie Goreng', 'category' => 'Mie & Pasta', 'calories_per_100g' => 210, 'protein_per_100g' => 5.5, 'carbs_per_100g' => 35.0, 'fat_per_100g' => 6.0],
            ['name' => 'Mie Ayam', 'category' => 'Mie & Pasta', 'calories_per_100g' => 195, 'protein_per_100g' => 7.2, 'carbs_per_100g' => 32.0, 'fat_per_100g' => 5.0],
            ['name' => 'Bakmi Goreng', 'category' => 'Mie & Pasta', 'calories_per_100g' => 205, 'protein_per_100g' => 5.8, 'carbs_per_100g' => 33.5, 'fat_per_100g' => 5.5],
            ['name' => 'Indomie Goreng', 'category' => 'Mie & Pasta', 'calories_per_100g' => 400, 'protein_per_100g' => 9.0, 'carbs_per_100g' => 56.0, 'fat_per_100g' => 15.0],
            ['name' => 'Kwetiau Goreng', 'category' => 'Mie & Pasta', 'calories_per_100g' => 190, 'protein_per_100g' => 5.0, 'carbs_per_100g' => 31.0, 'fat_per_100g' => 5.5],

            // Sup & Soto
            ['name' => 'Bakso', 'category' => 'Sup & Soto', 'calories_per_100g' => 180, 'protein_per_100g' => 12.0, 'carbs_per_100g' => 15.0, 'fat_per_100g' => 7.5],
            ['name' => 'Soto Ayam', 'category' => 'Sup & Soto', 'calories_per_100g' => 145, 'protein_per_100g' => 14.0, 'carbs_per_100g' => 8.0, 'fat_per_100g' => 6.0],
            ['name' => 'Rawon', 'category' => 'Sup & Soto', 'calories_per_100g' => 165, 'protein_per_100g' => 15.0, 'carbs_per_100g' => 6.0, 'fat_per_100g' => 9.0],
            ['name' => 'Opor Ayam', 'category' => 'Sup & Soto', 'calories_per_100g' => 195, 'protein_per_100g' => 16.0, 'carbs_per_100g' => 5.0, 'fat_per_100g' => 13.0],

            // Sayuran
            ['name' => 'Gado-Gado', 'category' => 'Sayuran', 'calories_per_100g' => 175, 'protein_per_100g' => 9.0, 'carbs_per_100g' => 18.0, 'fat_per_100g' => 8.0],
            ['name' => 'Bayam Rebus', 'category' => 'Sayuran', 'calories_per_100g' => 45, 'protein_per_100g' => 3.5, 'carbs_per_100g' => 5.5, 'fat_per_100g' => 0.5],
            ['name' => 'Kangkung Tumis', 'category' => 'Sayuran', 'calories_per_100g' => 65, 'protein_per_100g' => 2.5, 'carbs_per_100g' => 8.0, 'fat_per_100g' => 2.0],
            ['name' => 'Cap Cay', 'category' => 'Sayuran', 'calories_per_100g' => 85, 'protein_per_100g' => 5.0, 'carbs_per_100g' => 10.0, 'fat_per_100g' => 2.5],
            ['name' => 'Sayur Asem', 'category' => 'Sayuran', 'calories_per_100g' => 55, 'protein_per_100g' => 2.8, 'carbs_per_100g' => 9.5, 'fat_per_100g' => 0.5],

            // Buah
            ['name' => 'Pisang', 'category' => 'Buah-Buahan', 'calories_per_100g' => 89, 'protein_per_100g' => 1.1, 'carbs_per_100g' => 23.0, 'fat_per_100g' => 0.3],
            ['name' => 'Pepaya', 'category' => 'Buah-Buahan', 'calories_per_100g' => 43, 'protein_per_100g' => 0.5, 'carbs_per_100g' => 11.0, 'fat_per_100g' => 0.3],
            ['name' => 'Mangga', 'category' => 'Buah-Buahan', 'calories_per_100g' => 60, 'protein_per_100g' => 0.8, 'carbs_per_100g' => 15.0, 'fat_per_100g' => 0.4],
            ['name' => 'Jeruk', 'category' => 'Buah-Buahan', 'calories_per_100g' => 47, 'protein_per_100g' => 0.9, 'carbs_per_100g' => 12.0, 'fat_per_100g' => 0.1],
            ['name' => 'Semangka', 'category' => 'Buah-Buahan', 'calories_per_100g' => 30, 'protein_per_100g' => 0.6, 'carbs_per_100g' => 7.6, 'fat_per_100g' => 0.2],
            ['name' => 'Alpukat', 'category' => 'Buah-Buahan', 'calories_per_100g' => 160, 'protein_per_100g' => 2.0, 'carbs_per_100g' => 9.0, 'fat_per_100g' => 15.0],

            // Cemilan
            ['name' => 'Pisang Goreng', 'category' => 'Cemilan', 'calories_per_100g' => 145, 'protein_per_100g' => 1.5, 'carbs_per_100g' => 28.0, 'fat_per_100g' => 3.5],
            ['name' => 'Martabak Manis', 'category' => 'Cemilan', 'calories_per_100g' => 290, 'protein_per_100g' => 5.5, 'carbs_per_100g' => 48.0, 'fat_per_100g' => 9.0],
            ['name' => 'Siomay', 'category' => 'Cemilan', 'calories_per_100g' => 130, 'protein_per_100g' => 8.0, 'carbs_per_100g' => 12.0, 'fat_per_100g' => 5.5],
            ['name' => 'Batagor', 'category' => 'Cemilan', 'calories_per_100g' => 155, 'protein_per_100g' => 7.5, 'carbs_per_100g' => 15.0, 'fat_per_100g' => 7.0],

            // Makanan Cepat Saji
            ['name' => 'Burger', 'category' => 'Makanan Cepat Saji', 'calories_per_100g' => 295, 'protein_per_100g' => 17.0, 'carbs_per_100g' => 24.0, 'fat_per_100g' => 14.0],
            ['name' => 'Pizza', 'category' => 'Makanan Cepat Saji', 'calories_per_100g' => 266, 'protein_per_100g' => 11.0, 'carbs_per_100g' => 33.0, 'fat_per_100g' => 10.0],
            ['name' => 'Kentang Goreng', 'category' => 'Makanan Cepat Saji', 'calories_per_100g' => 312, 'protein_per_100g' => 3.4, 'carbs_per_100g' => 41.0, 'fat_per_100g' => 15.0],

            // Minuman
            ['name' => 'Susu Sapi', 'category' => 'Minuman', 'calories_per_100g' => 61, 'protein_per_100g' => 3.2, 'carbs_per_100g' => 4.8, 'fat_per_100g' => 3.3],
            ['name' => 'Es Teh Manis', 'category' => 'Minuman', 'calories_per_100g' => 70, 'protein_per_100g' => 0.0, 'carbs_per_100g' => 18.0, 'fat_per_100g' => 0.0],
        ];

        foreach ($foods as $food) {
            Food::firstOrCreate(
                ['name' => $food['name']],
                array_merge($food, ['is_active' => true])
            );
        }

        $this->command->info('✅ ' . count($foods) . ' makanan Indonesia berhasil ditambahkan.');
    }
}
