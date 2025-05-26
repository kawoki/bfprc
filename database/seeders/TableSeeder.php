<?php

namespace Database\Seeders;

use App\Models\Table;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        $tables = [
            // 2-person tables
            ['name' => 'Table 1 (2p)', 'status' => 'available', 'capacity' => 2],
            ['name' => 'Table 2 (2p)', 'status' => 'available', 'capacity' => 2],
            ['name' => 'Table 3 (2p)', 'status' => 'available', 'capacity' => 2],

            // 4-person tables
            ['name' => 'Table 4 (4p)', 'status' => 'available', 'capacity' => 4],
            ['name' => 'Table 5 (4p)', 'status' => 'available', 'capacity' => 4],

            // 8-person table
            ['name' => 'Table 6 (8p)', 'status' => 'available', 'capacity' => 8],
        ];

        foreach ($tables as $table) {
            Table::create($table);
        }
    }
}
