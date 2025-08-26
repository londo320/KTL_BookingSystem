<?php

namespace App\Modules\Outbound\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportRowResult extends Model
{
    protected $fillable = [
        'import_file_upload_id',
        'row_number',
        'status',
        'error_message',
        'raw_data',
        'transformed_data',
        'wms_staging_order_id',
    ];

    protected $casts = [
        'raw_data' => 'array',
        'transformed_data' => 'array',
        'row_number' => 'integer',
    ];

    // Relationships
    public function importFileUpload(): BelongsTo
    {
        return $this->belongsTo(ImportFileUpload::class);
    }

    public function wmsStagingOrder(): BelongsTo
    {
        return $this->belongsTo(WmsStagingOrder::class);
    }

    // Scopes
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeDuplicate($query)
    {
        return $query->where('status', 'duplicate');
    }

    public function scopeSkipped($query)
    {
        return $query->where('status', 'skipped');
    }

    public function scopeForFile($query, int $fileUploadId)
    {
        return $query->where('import_file_upload_id', $fileUploadId);
    }

    // Helper methods
    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'success' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
            'duplicate' => 'bg-yellow-100 text-yellow-800',
            'skipped' => 'bg-gray-100 text-gray-800',
        ];
        
        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getStatusDisplayAttribute(): string
    {
        $statuses = [
            'success' => 'Success',
            'failed' => 'Failed',
            'duplicate' => 'Duplicate',
            'skipped' => 'Skipped',
        ];
        
        return $statuses[$this->status] ?? ucfirst($this->status);
    }

    public function isSuccessful(): bool
    {
        return $this->status === 'success';
    }

    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isDuplicate(): bool
    {
        return $this->status === 'duplicate';
    }

    public function wasSkipped(): bool
    {
        return $this->status === 'skipped';
    }

    public function hasError(): bool
    {
        return !empty($this->error_message);
    }

    public function getRawValue(string $key)
    {
        return $this->raw_data[$key] ?? null;
    }

    public function getTransformedValue(string $key)
    {
        return $this->transformed_data[$key] ?? null;
    }

    public function getErrorSummary(): string
    {
        if (empty($this->error_message)) {
            return '';
        }

        return "Row {$this->row_number}: {$this->error_message}";
    }

    /**
     * Get the primary identifier from the raw data (usually order reference or similar)
     */
    public function getPrimaryIdentifier(): ?string
    {
        // Try common identifier fields
        $identifierFields = ['order_reference', 'OrderNo', 'ORDER_ID', 'order_number'];
        
        foreach ($identifierFields as $field) {
            if (isset($this->raw_data[$field]) && !empty($this->raw_data[$field])) {
                return $this->raw_data[$field];
            }
            if (isset($this->transformed_data[$field]) && !empty($this->transformed_data[$field])) {
                return $this->transformed_data[$field];
            }
        }

        return null;
    }

    /**
     * Get a summary of the row data for display
     */
    public function getDataSummary(): string
    {
        $identifier = $this->getPrimaryIdentifier();
        if ($identifier) {
            return "Order: {$identifier}";
        }

        // Fallback to showing first few values
        $rawValues = array_values($this->raw_data ?? []);
        $summary = implode(', ', array_slice($rawValues, 0, 3));
        
        return strlen($summary) > 50 ? substr($summary, 0, 47) . '...' : $summary;
    }
}