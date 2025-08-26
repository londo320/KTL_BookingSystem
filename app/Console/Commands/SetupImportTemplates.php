<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\Outbound\Models\ImportTemplate;

class SetupImportTemplates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'outbound:setup-import-templates {--force : Overwrite existing templates}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up sample import templates for WMS systems';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up import templates...');

        $templates = [
            [
                'name' => 'WMS System 1 - Standard CSV',
                'source_system' => 'wms_1',
                'file_type' => 'csv',
                'description' => 'Standard daily export from WMS System 1 - headers in row 1',
                'header_row' => 1,
                'data_start_row' => 2,
                'delimiter' => ',',
                'text_qualifier' => '"',
                'encoding' => 'UTF-8',
                'column_mapping' => [
                    'load_reference' => 'LoadRef',
                    'order_reference' => 'OrderNo',
                    'po_number' => 'PONumber',
                    'customer_code' => 'CustCode',
                    'customer_name' => 'Customer',
                    'collection_depot_code' => 'Depot',
                    'delivery_address_raw' => 'Address',
                    'delivery_postcode' => 'Postcode',
                    'planned_delivery_date' => 'DelDate',
                    'pallets' => 'Pallets',
                    'cases' => 'Cases',
                    'units' => 'Units',
                    'weight_kg' => 'Weight',
                    'special_instructions' => 'Notes',
                ],
                'default_values' => [
                    'temperature_controlled' => false,
                    'fragile' => false,
                    'hazardous' => false,
                ],
                'transformation_rules' => [
                    'planned_delivery_date' => ['format' => 'd/m/Y', 'output_format' => 'Y-m-d'],
                    'pallets' => ['type' => 'integer', 'default' => 0],
                    'cases' => ['type' => 'integer', 'default' => 0],
                    'units' => ['type' => 'integer', 'default' => 0],
                    'weight_kg' => ['type' => 'decimal', 'default' => null],
                ],
                'required_columns' => ['LoadRef', 'OrderNo', 'CustCode', 'Address', 'Postcode'],
                'validation_rules' => [
                    'load_reference' => ['required' => true, 'max_length' => 50],
                    'order_reference' => ['required' => true, 'max_length' => 100],
                    'customer_code' => ['required' => true, 'max_length' => 50],
                ],
            ],
            [
                'name' => 'WMS System 2 - Headers Row 3',
                'source_system' => 'wms_2',
                'file_type' => 'csv',
                'description' => 'WMS System 2 export with headers on row 3, data starts row 4',
                'header_row' => 3,
                'data_start_row' => 4,
                'delimiter' => ',',
                'text_qualifier' => '"',
                'encoding' => 'UTF-8',
                'column_mapping' => [
                    'load_reference' => 'LOAD_NUM',
                    'order_reference' => 'ORDER_ID',
                    'po_number' => 'PO_REF',
                    'customer_code' => 'CUST_NO',
                    'customer_name' => 'CUST_NAME',
                    'collection_depot_code' => 'PICKUP_LOC',
                    'delivery_address_raw' => 'DEL_ADDRESS',
                    'delivery_postcode' => 'POST_CODE',
                    'planned_delivery_date' => 'REQ_DATE',
                    'delivery_time_start' => 'TIME_FROM',
                    'delivery_time_end' => 'TIME_TO',
                    'pallets' => 'PLT_QTY',
                    'cases' => 'CASE_QTY',
                    'units' => 'UNIT_QTY',
                    'weight_kg' => 'WEIGHT_KG',
                    'temperature_controlled' => 'TEMP_CTRL',
                    'fragile' => 'FRAGILE_FLG',
                    'special_instructions' => 'INSTRUCTIONS',
                ],
                'default_values' => [
                    'hazardous' => false,
                ],
                'transformation_rules' => [
                    'planned_delivery_date' => ['format' => 'Y-m-d', 'output_format' => 'Y-m-d'],
                    'pallets' => ['type' => 'integer', 'default' => 0],
                    'cases' => ['type' => 'integer', 'default' => 0],
                    'units' => ['type' => 'integer', 'default' => 0],
                    'weight_kg' => ['type' => 'decimal', 'default' => null],
                    'temperature_controlled' => ['type' => 'boolean'],
                    'fragile' => ['type' => 'boolean'],
                ],
                'required_columns' => ['LOAD_NUM', 'ORDER_ID', 'CUST_NO', 'DEL_ADDRESS'],
                'validation_rules' => [
                    'load_reference' => ['required' => true, 'max_length' => 50],
                    'order_reference' => ['required' => true, 'max_length' => 100],
                ],
            ],
            [
                'name' => 'Legacy System - Tab Delimited',
                'source_system' => 'legacy',
                'file_type' => 'txt',
                'description' => 'Legacy system export - tab delimited, no headers',
                'header_row' => 0, // No headers
                'data_start_row' => 1,
                'delimiter' => "\t",
                'text_qualifier' => '',
                'encoding' => 'UTF-8',
                'column_mapping' => [
                    'load_reference' => '0', // Column index 0
                    'order_reference' => '1',
                    'customer_code' => '2',
                    'customer_name' => '3',
                    'delivery_address_raw' => '4',
                    'delivery_postcode' => '5',
                    'pallets' => '6',
                    'cases' => '7',
                    'planned_delivery_date' => '8',
                ],
                'default_values' => [
                    'collection_depot_code' => 'MAIN',
                    'temperature_controlled' => false,
                    'fragile' => false,
                    'hazardous' => false,
                    'units' => 0,
                ],
                'transformation_rules' => [
                    'planned_delivery_date' => ['format' => 'Ymd', 'output_format' => 'Y-m-d'],
                    'pallets' => ['type' => 'integer', 'default' => 0],
                    'cases' => ['type' => 'integer', 'default' => 0],
                ],
                'required_columns' => ['0', '1', '2'], // First 3 columns required
                'validation_rules' => [
                    'load_reference' => ['required' => true],
                    'order_reference' => ['required' => true],
                    'customer_code' => ['required' => true],
                ],
            ],
            [
                'name' => 'EDI Format - Manual Review',
                'source_system' => 'edi',
                'file_type' => 'csv',
                'description' => 'EDI import format - requires manual review before processing',
                'header_row' => 1,
                'data_start_row' => 2,
                'delimiter' => ',',
                'text_qualifier' => '"',
                'encoding' => 'UTF-8',
                'auto_process' => false, // Requires manual review
                'column_mapping' => [
                    'load_reference' => 'ConsignmentRef',
                    'order_reference' => 'OrderRef',
                    'external_load_id' => 'EDI_LoadID',
                    'customer_code' => 'PartyCode',
                    'customer_name' => 'PartyName',
                    'delivery_address_raw' => 'DeliveryAddr',
                    'delivery_postcode' => 'Postcode',
                    'planned_delivery_date' => 'ReqDeliveryDate',
                    'pallets' => 'PalletCount',
                    'weight_kg' => 'GrossWeight',
                ],
                'transformation_rules' => [
                    'planned_delivery_date' => ['format' => 'Y-m-d H:i:s', 'output_format' => 'Y-m-d'],
                    'pallets' => ['type' => 'integer', 'default' => 0],
                    'weight_kg' => ['type' => 'decimal', 'default' => null],
                ],
                'required_columns' => ['ConsignmentRef', 'OrderRef', 'PartyCode'],
            ],
        ];

        foreach ($templates as $templateData) {
            $existing = ImportTemplate::where('name', $templateData['name'])->first();
            
            if ($existing && !$this->option('force')) {
                $this->warn("Template '{$templateData['name']}' already exists. Use --force to overwrite.");
                continue;
            }

            if ($existing) {
                $existing->delete();
                $this->info("Overwriting existing template: {$templateData['name']}");
            }

            ImportTemplate::create($templateData);
            $this->info("✓ Created template: {$templateData['name']}");
        }

        $this->info("\nImport templates setup completed!");
        $this->line("\nNext steps:");
        $this->line("1. Go to: 🚛 Outbound → 📁 WMS File Imports");
        $this->line("2. Review and modify templates as needed");
        $this->line("3. Upload your WMS files for processing");
        $this->line("4. Templates can be customized for your specific file formats");
    }
}