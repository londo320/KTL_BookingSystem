<?php

namespace Database\Seeders;

use App\Models\Depot;
use Illuminate\Database\Seeder;

class DepotSeeder extends Seeder
{
    public function run(): void
    {
        Depot::firstOrCreate(['name' => 'Wimblington'], ['location' => 'March']);
        Depot::firstOrCreate(['name' => 'Cromwell Road'], ['location' => 'Wisbech']);
        Depot::firstOrCreate(['name' => 'Salters Yard'], ['location' => 'Wisbech']);
        Depot::firstOrCreate(['name' => 'Lynn Road'], ['location' => 'Wisbech']);
    }
}
