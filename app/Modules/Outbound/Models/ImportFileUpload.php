<?php

namespace App\Modules\Outbound\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class ImportFileUpload extends Model
{
    protected $fillable = [
        'original_filename',
        'stored_filename',
        'file_path',
        'file_size',
        'mime_type',
        'file_hash',
        'import_template_id',
        'uploaded_by',
        'uploaded_at',
        'status',
        'total_rows',
        'processed_rows',
        'successful_rows',
        'failed_rows',
        'duplicate_rows',
        'processing_summary',
        'error_log',
        'processing_started_at',
        'processing_completed_at',
        'sample_data',
        'detected_columns',
        'requires_review',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'processing_started_at' => 'datetime',
        'processing_completed_at' => 'datetime',
        'processing_summary' => 'array',
        'sample_data' => 'array',
        'detected_columns' => 'array',
        'requires_review' => 'boolean',
        'file_size' => 'integer',
        'total_rows' => 'integer',
        'processed_rows' => 'integer',
        'successful_rows' => 'integer',
        'failed_rows' => 'integer',
        'duplicate_rows' => 'integer',
    ];

    // Relationships
    public function importTemplate(): BelongsTo
    {
        return $this->belongsTo(ImportTemplate::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function rowResults(): HasMany
    {
        return $this->hasMany(ImportRowResult::class);
    }

    public function successfulRows(): HasMany
    {
        return $this->hasMany(ImportRowResult::class)->where('status', 'success');
    }

    public function failedRows(): HasMany
    {
        return $this->hasMany(ImportRowResult::class)->where('status', 'failed');
    }

    // Scopes
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeRequiringReview($query)
    {
        return $query->where('requires_review', true);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('uploaded_at', today());
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('uploaded_at', '>=', now()->subDays($days));
    }

    // Helper methods
    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'uploaded' => 'bg-blue-100 text-blue-800',
            'processing' => 'bg-yellow-100 text-yellow-800',
            'completed' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
        ];
        
        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getStatusDisplayAttribute(): string
    {
        $statuses = [
            'uploaded' => 'Uploaded',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled',
        ];
        
        return $statuses[$this->status] ?? ucfirst($this->status);
    }

    public function getProgressPercentageAttribute(): float
    {
        if (!$this->total_rows || $this->total_rows === 0) {
            return 0;
        }

        return ($this->processed_rows / $this->total_rows) * 100;
    }

    public function getSuccessRateAttribute(): float
    {
        if (!$this->processed_rows || $this->processed_rows === 0) {
            return 0;
        }

        return ($this->successful_rows / $this->processed_rows) * 100;
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        
        if ($bytes < 1024) {
            return $bytes . ' B';
        } elseif ($bytes < 1024 * 1024) {
            return round($bytes / 1024, 2) . ' KB';
        } elseif ($bytes < 1024 * 1024 * 1024) {
            return round($bytes / (1024 * 1024), 2) . ' MB';
        } else {
            return round($bytes / (1024 * 1024 * 1024), 2) . ' GB';
        }
    }

    public function getProcessingDurationAttribute(): ?int
    {
        if (!$this->processing_started_at || !$this->processing_completed_at) {
            return null;
        }

        return $this->processing_started_at->diffInSeconds($this->processing_completed_at);
    }

    public function getProcessingDurationFormattedAttribute(): ?string
    {
        $duration = $this->processing_duration;
        
        if ($duration === null) {
            return null;
        }

        if ($duration < 60) {
            return $duration . ' seconds';
        } elseif ($duration < 3600) {
            return round($duration / 60, 1) . ' minutes';
        } else {
            return round($duration / 3600, 1) . ' hours';
        }
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function requiresReview(): bool
    {
        return $this->requires_review === true;
    }

    public function canBeReprocessed(): bool
    {
        return in_array($this->status, ['failed', 'completed']);
    }

    public function canBeDeleted(): bool
    {
        return $this->status !== 'processing';
    }

    public function getErrorSummary(): array
    {
        if (!$this->processing_summary || !isset($this->processing_summary['errors'])) {
            return [];
        }

        return $this->processing_summary['errors'];
    }

    public function hasErrors(): bool
    {
        return !empty($this->getErrorSummary()) || !empty($this->error_log);
    }

    public function getSampleRowsForPreview(int $limit = 5): array
    {
        if ($this->sample_data) {
            return array_slice($this->sample_data, 0, $limit);
        }

        // If no sample data, get from row results
        return $this->successfulRows()
            ->limit($limit)
            ->get()
            ->pluck('transformed_data')
            ->toArray();
    }

    public function getDetectedColumnsForTemplate(): array
    {
        return $this->detected_columns ?? [];
    }

    /**
     * Mark file as requiring review
     */
    public function markForReview(string $reason = null): void
    {
        $this->update([
            'requires_review' => true,
            'status' => 'uploaded',
            'error_log' => $reason ? "Marked for review: {$reason}" : null,
        ]);
    }

    /**
     * Clear review requirement
     */
    public function clearReviewRequirement(): void
    {
        $this->update(['requires_review' => false]);
    }

    /**
     * Get route key name for route model binding
     */
    public function getRouteKeyName()
    {
        return 'id';
    }
}