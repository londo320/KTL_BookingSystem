<?php

namespace App\Modules\Outbound\Services;

use App\Modules\Outbound\Models\ImportTemplate;
use App\Modules\Outbound\Models\ImportFileUpload;
use App\Modules\Outbound\Models\ImportRowResult;
use App\Modules\Outbound\Models\WmsStagingOrder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class FileImportService
{
    /**
     * Process an uploaded file using specified template
     */
    public function processUploadedFile(UploadedFile $file, ImportTemplate $template, int $userId): ImportFileUpload
    {
        // Store the file
        $storedFile = $this->storeUploadedFile($file);
        
        // Create file upload record
        $fileUpload = ImportFileUpload::create([
            'original_filename' => $file->getClientOriginalName(),
            'stored_filename' => $storedFile['filename'],
            'file_path' => $storedFile['path'],
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'file_hash' => hash_file('sha256', $file->getPathname()),
            'import_template_id' => $template->id,
            'uploaded_by' => $userId,
            'uploaded_at' => now(),
            'status' => 'uploaded',
        ]);

        // Process the file
        if ($template->auto_process) {
            $this->processFileUpload($fileUpload);
        } else {
            // Generate preview for manual review
            $this->generateFilePreview($fileUpload);
        }

        return $fileUpload;
    }

    /**
     * Process file upload and convert to staging orders
     */
    public function processFileUpload(ImportFileUpload $fileUpload): void
    {
        $template = $fileUpload->importTemplate;
        
        try {
            $fileUpload->update([
                'status' => 'processing',
                'processing_started_at' => now(),
            ]);

            $filePath = Storage::path($fileUpload->file_path);
            $rows = $this->readFileData($filePath, $template);
            
            $fileUpload->update(['total_rows' => count($rows)]);

            $results = [
                'processed' => 0,
                'successful' => 0,
                'failed' => 0,
                'duplicates' => 0,
                'errors' => []
            ];

            foreach ($rows as $index => $rowData) {
                $rowNumber = $index + $template->data_start_row;
                $result = $this->processRow($fileUpload, $rowNumber, $rowData, $template);
                
                $results['processed']++;
                $results[$result['status']]++;
                
                if (!empty($result['errors'])) {
                    $results['errors'][] = "Row {$rowNumber}: " . implode(', ', $result['errors']);
                }
            }

            $fileUpload->update([
                'status' => 'completed',
                'processed_rows' => $results['processed'],
                'successful_rows' => $results['successful'],
                'failed_rows' => $results['failed'],
                'duplicate_rows' => $results['duplicates'],
                'processing_summary' => $results,
                'processing_completed_at' => now(),
            ]);

            $template->incrementUsage();

            Log::info("File import completed", [
                'file_upload_id' => $fileUpload->id,
                'results' => $results
            ]);

        } catch (\Exception $e) {
            $fileUpload->update([
                'status' => 'failed',
                'error_log' => $e->getMessage(),
                'processing_completed_at' => now(),
            ]);

            Log::error("File import failed", [
                'file_upload_id' => $fileUpload->id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Read file data based on template configuration
     */
    private function readFileData(string $filePath, ImportTemplate $template): array
    {
        switch ($template->file_type) {
            case 'csv':
                return $this->readCsvFile($filePath, $template);
            case 'xlsx':
                return $this->readExcelFile($filePath, $template);
            case 'txt':
                return $this->readTextFile($filePath, $template);
            default:
                throw new \Exception("Unsupported file type: {$template->file_type}");
        }
    }

    /**
     * Read CSV file
     */
    private function readCsvFile(string $filePath, ImportTemplate $template): array
    {
        $rows = [];
        $headers = [];
        
        if (($handle = fopen($filePath, 'r')) !== false) {
            $rowNumber = 1;
            
            while (($data = fgetcsv($handle, 0, $template->delimiter, $template->text_qualifier)) !== false) {
                if ($rowNumber == $template->header_row && $template->header_row > 0) {
                    $headers = $data;
                } elseif ($rowNumber >= $template->data_start_row) {
                    if (empty($headers)) {
                        // No headers, use column indexes
                        $rows[] = $data;
                    } else {
                        // Combine headers with data
                        $rows[] = array_combine($headers, array_pad($data, count($headers), ''));
                    }
                }
                $rowNumber++;
            }
            fclose($handle);
        }

        return $rows;
    }

    /**
     * Read Excel file (requires PhpSpreadsheet)
     */
    private function readExcelFile(string $filePath, ImportTemplate $template): array
    {
        // This would require PhpSpreadsheet package
        // For now, return empty array with note
        Log::warning("Excel file reading not implemented yet", ['file' => $filePath]);
        return [];
    }

    /**
     * Read fixed-width text file
     */
    private function readTextFile(string $filePath, ImportTemplate $template): array
    {
        // This would handle fixed-width formats
        // For now, treat as CSV with different delimiter
        return $this->readCsvFile($filePath, $template);
    }

    /**
     * Process individual row
     */
    private function processRow(ImportFileUpload $fileUpload, int $rowNumber, array $rowData, ImportTemplate $template): array
    {
        try {
            // Transform row data to standard format
            $standardData = $this->transformRowData($rowData, $template);
            
            // Validate data
            $validationErrors = $template->validateRow($standardData);
            
            if (!empty($validationErrors)) {
                ImportRowResult::create([
                    'import_file_upload_id' => $fileUpload->id,
                    'row_number' => $rowNumber,
                    'status' => 'failed',
                    'error_message' => implode('; ', $validationErrors),
                    'raw_data' => $rowData,
                    'transformed_data' => $standardData,
                ]);
                
                return ['status' => 'failed', 'errors' => $validationErrors];
            }

            // Check for duplicates
            if ($template->duplicate_handling !== 'create_new') {
                $existing = WmsStagingOrder::where('order_reference', $standardData['order_reference'])
                    ->where('load_reference', $standardData['load_reference'])
                    ->first();

                if ($existing) {
                    if ($template->duplicate_handling === 'skip') {
                        ImportRowResult::create([
                            'import_file_upload_id' => $fileUpload->id,
                            'row_number' => $rowNumber,
                            'status' => 'duplicate',
                            'error_message' => 'Order already exists',
                            'raw_data' => $rowData,
                            'transformed_data' => $standardData,
                            'wms_staging_order_id' => $existing->id,
                        ]);
                        
                        return ['status' => 'duplicates', 'errors' => []];
                    }
                    // For 'overwrite', we'll update the existing record
                }
            }

            // Create WMS staging order
            $stagingOrder = WmsStagingOrder::create(array_merge($standardData, [
                'source_system' => $template->source_system,
                'source_file_name' => $fileUpload->original_filename,
                'uploaded_at' => $fileUpload->uploaded_at,
            ]));

            ImportRowResult::create([
                'import_file_upload_id' => $fileUpload->id,
                'row_number' => $rowNumber,
                'status' => 'success',
                'raw_data' => $rowData,
                'transformed_data' => $standardData,
                'wms_staging_order_id' => $stagingOrder->id,
            ]);

            return ['status' => 'successful', 'errors' => []];

        } catch (\Exception $e) {
            ImportRowResult::create([
                'import_file_upload_id' => $fileUpload->id,
                'row_number' => $rowNumber,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'raw_data' => $rowData,
            ]);

            return ['status' => 'failed', 'errors' => [$e->getMessage()]];
        }
    }

    /**
     * Transform raw row data to standard format using template mapping
     */
    private function transformRowData(array $rowData, ImportTemplate $template): array
    {
        $standardData = [];
        $mapping = $template->column_mapping ?? [];
        $defaults = $template->default_values ?? [];
        $transformations = $template->transformation_rules ?? [];

        // Apply column mapping
        foreach ($mapping as $standardField => $sourceColumn) {
            $value = $rowData[$sourceColumn] ?? null;
            
            // Apply transformations
            if (isset($transformations[$standardField])) {
                $value = $this->applyTransformation($value, $transformations[$standardField]);
            }
            
            $standardData[$standardField] = $value;
        }

        // Apply default values for missing fields
        foreach ($defaults as $field => $defaultValue) {
            if (!isset($standardData[$field]) || $standardData[$field] === null || $standardData[$field] === '') {
                $standardData[$field] = $defaultValue;
            }
        }

        return $standardData;
    }

    /**
     * Apply transformation rules to field value
     */
    private function applyTransformation($value, array $rules)
    {
        if ($value === null || $value === '') {
            return $rules['default'] ?? null;
        }

        // Type conversion
        if (isset($rules['type'])) {
            switch ($rules['type']) {
                case 'integer':
                    $value = (int) $value;
                    break;
                case 'decimal':
                    $value = (float) $value;
                    break;
                case 'boolean':
                    $value = in_array(strtolower($value), ['true', '1', 'yes', 'y']);
                    break;
            }
        }

        // Date format conversion
        if (isset($rules['format']) && isset($rules['output_format'])) {
            try {
                $date = Carbon::createFromFormat($rules['format'], $value);
                $value = $date->format($rules['output_format']);
            } catch (\Exception $e) {
                // Keep original value if date parsing fails
            }
        }

        // String transformations
        if (isset($rules['trim']) && $rules['trim']) {
            $value = trim($value);
        }

        if (isset($rules['uppercase']) && $rules['uppercase']) {
            $value = strtoupper($value);
        }

        if (isset($rules['lowercase']) && $rules['lowercase']) {
            $value = strtolower($value);
        }

        return $value;
    }

    /**
     * Store uploaded file
     */
    private function storeUploadedFile(UploadedFile $file): array
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('imports', $filename);

        return [
            'filename' => $filename,
            'path' => $path
        ];
    }

    /**
     * Generate preview data for manual review
     */
    private function generateFilePreview(ImportFileUpload $fileUpload): void
    {
        $template = $fileUpload->importTemplate;
        $filePath = Storage::path($fileUpload->file_path);
        
        try {
            $rows = $this->readFileData($filePath, $template);
            $sampleData = array_slice($rows, 0, 5); // First 5 rows
            
            $detectedColumns = [];
            if (!empty($rows)) {
                $detectedColumns = array_keys($rows[0]);
            }
            
            $fileUpload->update([
                'sample_data' => $sampleData,
                'detected_columns' => $detectedColumns,
                'total_rows' => count($rows),
                'requires_review' => true,
            ]);
            
        } catch (\Exception $e) {
            $fileUpload->update([
                'status' => 'failed',
                'error_log' => 'Preview generation failed: ' . $e->getMessage(),
            ]);
        }
    }
}