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
            ['name' => 'Table 1', 'capacity' => 2, 'status' => 'available', 'location' => 'Window Side'],
            ['name' => 'Table 2', 'capacity' => 4, 'status' => 'available', 'location' => 'Window Side'],
            ['name' => 'Table 3', 'capacity' => 6, 'status' => 'available', 'location' => 'Center'],
            ['name' => 'Table 4', 'capacity' => 2, 'status' => 'occupied', 'location' => 'Garden View'],
            ['name' => 'Table 5', 'capacity' => 4, 'status' => 'available', 'location' => 'Garden View'],
            ['name' => 'Table 6', 'capacity' => 8, 'status' => 'reserved', 'location' => 'Private Area'],
            ['name' => 'Table 7', 'capacity' => 2, 'status' => 'available', 'location' => 'Bar Area'],
            ['name' => 'Table 8', 'capacity' => 4, 'status' => 'available', 'location' => 'Bar Area'],
            ['name' => 'VIP Table 1', 'capacity' => 6, 'status' => 'available', 'location' => 'VIP Section'],
            ['name' => 'VIP Table 2', 'capacity' => 8, 'status' => 'available', 'location' => 'VIP Section'],
        ];

        foreach ($tables as $table) {
            Table::create($table);
        }
    }
}
