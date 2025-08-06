<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@saliiyahotel.com',
            'password' => Hash::make('password123'),
            'phone' => '+94 11 234 5678',
            'address' => '123 Main Street, Colombo, Sri Lanka',
            'job_role' => 'admin',
        ]);

        // Create a cashier user for testing
        User::create([
            'name' => 'Cashier User',
            'email' => 'cashier@saliiyahotel.com',
            'password' => Hash::make('password123'),
            'phone' => '+94 11 234 5679',
            'address' => '456 Second Street, Colombo, Sri Lanka',
            'job_role' => 'cashier',
        ]);
    }
}
