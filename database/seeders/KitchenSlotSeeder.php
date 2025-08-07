<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KitchenSlot;

class KitchenSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 default kitchen slots
        for ($i = 1; $i <= 10; $i++) {
            KitchenSlot::create([
                'slot_number' => 'K' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'status' => 'available',
                'is_active' => true,
            ]);
        }
    }
}
