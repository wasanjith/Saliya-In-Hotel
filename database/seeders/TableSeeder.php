<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Table;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tables = [
            [
                'number' => 1,
                'capacity' => 4,
                'status' => 'available',
                'description' => 'Window table with nice view',
                'location' => 'window',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'number' => 2,
                'capacity' => 6,
                'status' => 'available',
                'description' => 'Center table for larger groups',
                'location' => 'center',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'number' => 3,
                'capacity' => 4,
                'status' => 'available',
                'description' => 'Corner table for intimate dining',
                'location' => 'corner',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'number' => 4,
                'capacity' => 8,
                'status' => 'available',
                'description' => 'Large table for big groups',
                'location' => 'center',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'number' => 5,
                'capacity' => 4,
                'status' => 'available',
                'description' => 'Indoor table near the entrance',
                'location' => 'indoor',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($tables as $tableData) {
            Table::create($tableData);
        }

        $this->command->info('5 tables seeded successfully!');
    }
}
