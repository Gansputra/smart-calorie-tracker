<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@smartcalorietracker.com'],
            [
                'name'            => 'Administrator',
                'password'        => Hash::make('admin123'),
                'calorie_target'  => 2000,
                'protein_target'  => 150,
                'email_verified_at' => now(),
            ]
        );

        $admin->assignRole('admin');

        // Create a demo user
        $user = User::firstOrCreate(
            ['email' => 'demo@smartcalorietracker.com'],
            [
                'name'            => 'Demo User',
                'password'        => Hash::make('demo123'),
                'calorie_target'  => 1800,
                'protein_target'  => 120,
                'email_verified_at' => now(),
            ]
        );

        $user->assignRole('user');

        $this->command->info('✅ Admin created: admin@smartcalorietracker.com / admin123');
        $this->command->info('✅ Demo user: demo@smartcalorietracker.com / demo123');
    }
}
