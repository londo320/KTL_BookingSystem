<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = [
            [
                'registration' => 'AB12 CDE',
                'vehicle_type' => 'tractor',
                'carrier_company' => 'Swift Transport Ltd',
                'default_driver_name' => 'John Smith',
                'default_driver_phone' => '07123456789',
            ],
            [
                'registration' => 'FG34 HIJ',
                'vehicle_type' => 'tractor',
                'carrier_company' => 'Express Logistics',
                'default_driver_name' => 'Mike Johnson',
                'default_driver_phone' => '07987654321',
            ],
            [
                'registration' => 'KL56 MNO',
                'vehicle_type' => 'rigid',
                'carrier_company' => 'Local Delivery Co',
                'default_driver_name' => 'Sarah Brown',
                'default_driver_phone' => '07555123456',
            ],
            [
                'registration' => 'PQ78 RST',
                'vehicle_type' => 'tractor',
                'carrier_company' => 'Swift Transport Ltd',
                'default_driver_name' => 'David Wilson',
                'default_driver_phone' => '07444567890',
            ],
            [
                'registration' => 'UV90 WXY',
                'vehicle_type' => 'van',
                'carrier_company' => 'Quick Couriers',
                'default_driver_name' => 'Emma Davis',
                'default_driver_phone' => '07333789012',
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::create($vehicle);
        }
    }
}
