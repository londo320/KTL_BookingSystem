<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Depot;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'sku' => 'CONC-BLK',
                'description' => 'Standard concrete building blocks',
                'default_case_count' => 50,
                'default_pallets' => 1,
            ],
            [
                'sku' => 'SAND-BLD',
                'description' => 'Fine building sand for construction (bulk)',
                'default_case_count' => 0,
                'default_pallets' => 0,
            ],
            [
                'sku' => 'GRAV-20',
                'description' => '20mm aggregate gravel (bulk)',
                'default_case_count' => 0,
                'default_pallets' => 0,
            ],
            [
                'sku' => 'CEM-PORT',
                'description' => 'Portland cement bags',
                'default_case_count' => 40,
                'default_pallets' => 1,
            ],
            [
                'sku' => 'GEN-PAL',
                'description' => 'General palletised goods',
                'default_case_count' => 20,
                'default_pallets' => 1,
            ],
            [
                'sku' => 'RETAIL-MX',
                'description' => 'Mixed retail goods and packages',
                'default_case_count' => 100,
                'default_pallets' => 4,
            ],
            [
                'sku' => 'ELEC-COMP',
                'description' => 'Electronic goods and components',
                'default_case_count' => 200,
                'default_pallets' => 2,
            ],
            [
                'sku' => 'FOOD-AMB',
                'description' => 'Ambient temperature food products',
                'default_case_count' => 60,
                'default_pallets' => 2,
            ],
            [
                'sku' => 'FOOD-CHILL',
                'description' => 'Chilled food products (2-8°C)',
                'default_case_count' => 48,
                'default_pallets' => 2,
            ],
            [
                'sku' => 'FOOD-FROZ',
                'description' => 'Frozen food products (-18°C)',
                'default_case_count' => 40,
                'default_pallets' => 2,
            ],
            [
                'sku' => 'BEV-SOFT',
                'description' => 'Soft drinks and non-alcoholic beverages',
                'default_case_count' => 80,
                'default_pallets' => 2,
            ],
            [
                'sku' => 'AUTO-ENG',
                'description' => 'Engine components and auto parts',
                'default_case_count' => 30,
                'default_pallets' => 1,
            ],
            [
                'sku' => 'TYRE-VAR',
                'description' => 'Vehicle tyres of various sizes',
                'default_case_count' => 16,
                'default_pallets' => 1,
            ],
            [
                'sku' => 'CHEM-CLEAN',
                'description' => 'Industrial cleaning chemicals (drums)',
                'default_case_count' => 4,
                'default_pallets' => 1,
            ],
            [
                'sku' => 'PAINT-PROD',
                'description' => 'Paint and coating products',
                'default_case_count' => 24,
                'default_pallets' => 1,
            ],
            [
                'sku' => 'CLOTH-GAR',
                'description' => 'Clothing and garment products (bales)',
                'default_case_count' => 12,
                'default_pallets' => 3,
            ],
            [
                'sku' => 'FAB-ROLL',
                'description' => 'Fabric rolls and textile materials',
                'default_case_count' => 8,
                'default_pallets' => 2,
            ],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(
                ['sku' => $product['sku']],
                $product
            );
        }

        // Link products to Main Depot
        $mainDepot = Depot::first(); // Use first available depot
        if ($mainDepot) {
            $allProducts = Product::all();
            foreach ($allProducts as $product) {
                // Use the depot_product pivot table to link products to depot
                if (!$mainDepot->products()->where('product_id', $product->id)->exists()) {
                    $mainDepot->products()->attach($product->id);
                }
            }
        }

        $this->command->info('Products seeded successfully (' . count($products) . ' products created).');
    }
}