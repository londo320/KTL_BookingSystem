<?php

namespace App\Modules\Outbound\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImportTemplate extends Model
{
    protected $fillable = [
        'name',
        'source_system',
        'file_type',
        'description',
        'header_row',
        'data_start_row',
        'delimiter',
        'text_qualifier',
        'encoding',
        'column_mapping',
        'default_values',
        'transformation_rules',
        'required_columns',
        'validation_rules',
        'auto_process',
        'duplicate_handling',
        'is_active',
        'files_processed',
        'last_used_at',
    ];

    protected $casts = [
        'column_mapping' => 'array',
        'default_values' => 'array',
        'transformation_rules' => 'array',
        'required_columns' => 'array',
        'validation_rules' => 'array',
        'auto_process' => 'boolean',
        'is_active' => 'boolean',
        'files_processed' => 'integer',
        'last_used_at' => 'datetime',
    ];

    // Relationships
    public function fileUploads(): HasMany
    {
        return $this->hasMany(ImportFileUpload::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForSystem($query, string $system)
    {
        return $query->where('source_system', $system);
    }

    public function scopeForFileType($query, string $fileType)
    {
        return $query->where('file_type', $fileType);
    }

    // Helper methods
    public function getStandardFieldMapping(): array
    {
        return [
            'load_reference' => 'Load/Consignment Reference',
            'order_reference' => 'Order Number',
            'po_number' => 'PO Number',
            'customer_code' => 'Customer Code',
            'customer_name' => 'Customer Name',
            'collection_depot_code' => 'Collection Depot',
            'delivery_address_raw' => 'Delivery Address',
            'delivery_postcode' => 'Postcode',
            'planned_delivery_date' => 'Delivery Date',
            'delivery_time_start' => 'Delivery Time From',
            'delivery_time_end' => 'Delivery Time To',
            'pallets' => 'Pallets',
            'cases' => 'Cases',
            'units' => 'Units',
            'weight_kg' => 'Weight (KG)',
            'temperature_controlled' => 'Temperature Controlled',
            'fragile' => 'Fragile',
            'hazardous' => 'Hazardous',
            'special_instructions' => 'Special Instructions',
        ];
    }

    public function getMappedColumn(string $standardField): ?string
    {
        $mapping = $this->column_mapping ?? [];
        return $mapping[$standardField] ?? null;
    }

    public function getColumnTransformation(string $standardField): ?array
    {
        $rules = $this->transformation_rules ?? [];
        return $rules[$standardField] ?? null;
    }

    public function getDefaultValue(string $standardField)
    {
        $defaults = $this->default_values ?? [];
        return $defaults[$standardField] ?? null;
    }

    public function hasRequiredColumn(string $columnName): bool
    {
        $required = $this->required_columns ?? [];
        return in_array($columnName, $required);
    }

    public function validateRow(array $rowData): array
    {
        $errors = [];
        $rules = $this->validation_rules ?? [];

        foreach ($rules as $field => $rule) {
            $value = $rowData[$field] ?? null;
            
            if ($rule['required'] ?? false) {
                if (empty($value)) {
                    $errors[] = "Field '{$field}' is required";
                    continue;
                }
            }

            if (!empty($value) && isset($rule['type'])) {
                switch ($rule['type']) {
                    case 'numeric':
                        if (!is_numeric($value)) {
                            $errors[] = "Field '{$field}' must be numeric";
                        }
                        break;
                    case 'date':
                        if (!strtotime($value)) {
                            $errors[] = "Field '{$field}' must be a valid date";
                        }
                        break;
                    case 'email':
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[] = "Field '{$field}' must be a valid email";
                        }
                        break;
                }
            }

            if (!empty($value) && isset($rule['max_length'])) {
                if (strlen($value) > $rule['max_length']) {
                    $errors[] = "Field '{$field}' exceeds maximum length of {$rule['max_length']}";
                }
            }
        }

        return $errors;
    }

    public function incrementUsage(): void
    {
        $this->increment('files_processed');
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Create a sample template for testing
     */
    public static function createSampleWms1Template(): self
    {
        return static::create([
            'name' => 'WMS 1 Standard Export',
            'source_system' => 'wms_1',
            'file_type' => 'csv',
            'description' => 'Standard daily export from WMS System 1',
            'header_row' => 1,
            'data_start_row' => 2,
            'delimiter' => ',',
            'text_qualifier' => '"',
            'encoding' => 'UTF-8',
            'column_mapping' => [
                'load_reference' => 'LoadRef',
                'order_reference' => 'OrderNumber',
                'po_number' => 'PONumber',
                'customer_code' => 'CustCode',
                'customer_name' => 'CustomerName',
                'collection_depot_code' => 'DepotCode',
                'delivery_address_raw' => 'DeliveryAddress',
                'delivery_postcode' => 'Postcode',
                'planned_delivery_date' => 'DeliveryDate',
                'pallets' => 'Pallets',
                'cases' => 'Cases',
                'units' => 'Units',
                'weight_kg' => 'Weight',
                'special_instructions' => 'Notes',
            ],
            'default_values' => [
                'source_system' => 'wms_1',
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
            'required_columns' => [
                'LoadRef', 'OrderNumber', 'CustCode', 'DeliveryAddress', 'Postcode'
            ],
            'validation_rules' => [
                'load_reference' => ['required' => true, 'max_length' => 50],
                'order_reference' => ['required' => true, 'max_length' => 100],
                'customer_code' => ['required' => true, 'max_length' => 50],
                'delivery_postcode' => ['required' => true, 'max_length' => 20],
                'pallets' => ['type' => 'numeric'],
                'cases' => ['type' => 'numeric'],
                'units' => ['type' => 'numeric'],
                'weight_kg' => ['type' => 'numeric'],
            ],
            'auto_process' => true,
            'duplicate_handling' => 'skip',
            'is_active' => true,
        ]);
    }
}