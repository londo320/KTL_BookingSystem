<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Import templates for different file formats
        Schema::create('import_templates', function (Blueprint $table) {
            $table->id();
            
            // Template identification
            $table->string('name', 100); // "WMS 1 Daily Export", "EDI Format A"
            $table->string('source_system', 50); // wms_1, wms_2, edi, manual
            $table->string('file_type', 20)->default('csv'); // csv, xlsx, txt, xml, json
            $table->text('description')->nullable();
            
            // File structure configuration
            $table->integer('header_row')->default(1); // Which row contains headers (0 = no headers)
            $table->integer('data_start_row')->default(2); // Which row data starts
            $table->string('delimiter', 5)->default(','); // For CSV files
            $table->string('text_qualifier', 5)->default('"'); // For CSV files
            $table->string('encoding', 20)->default('UTF-8'); // File encoding
            
            // Column mapping (JSON configuration)
            $table->json('column_mapping'); // Maps file columns to our standard fields
            $table->json('default_values')->nullable(); // Default values for missing fields
            $table->json('transformation_rules')->nullable(); // Data transformation rules
            
            // Validation rules
            $table->json('required_columns')->nullable(); // Which columns must be present
            $table->json('validation_rules')->nullable(); // Field validation rules
            
            // Processing configuration
            $table->boolean('auto_process')->default(true); // Process automatically or manual review
            $table->string('duplicate_handling', 20)->default('skip'); // skip, overwrite, create_new
            
            // Status and usage tracking
            $table->boolean('is_active')->default(true);
            $table->integer('files_processed')->default(0);
            $table->timestamp('last_used_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['source_system', 'is_active']);
            $table->index('file_type');
        });

        // File upload history and processing log
        Schema::create('import_file_uploads', function (Blueprint $table) {
            $table->id();
            
            // File information
            $table->string('original_filename', 255);
            $table->string('stored_filename', 255);
            $table->string('file_path', 500);
            $table->integer('file_size'); // in bytes
            $table->string('mime_type', 100);
            $table->string('file_hash', 64); // For duplicate detection
            
            // Import configuration
            $table->foreignId('import_template_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamp('uploaded_at');
            
            // Processing status
            $table->enum('status', [
                'uploaded', 'processing', 'completed', 'failed', 'cancelled'
            ])->default('uploaded');
            $table->integer('total_rows')->nullable();
            $table->integer('processed_rows')->default(0);
            $table->integer('successful_rows')->default(0);
            $table->integer('failed_rows')->default(0);
            $table->integer('duplicate_rows')->default(0);
            
            // Processing results
            $table->json('processing_summary')->nullable(); // Detailed results
            $table->text('error_log')->nullable(); // Processing errors
            $table->timestamp('processing_started_at')->nullable();
            $table->timestamp('processing_completed_at')->nullable();
            
            // Preview and validation
            $table->json('sample_data')->nullable(); // First few rows for preview
            $table->json('detected_columns')->nullable(); // Auto-detected column headers
            $table->boolean('requires_review')->default(false);
            
            $table->timestamps();
            
            // Indexes
            $table->index('file_hash'); // For duplicate detection
            $table->index(['status', 'uploaded_at']);
            $table->index('uploaded_by');
        });

        // Individual row processing results for detailed tracking
        Schema::create('import_row_results', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('import_file_upload_id')->constrained()->cascadeOnDelete();
            $table->integer('row_number');
            
            // Processing result
            $table->enum('status', ['success', 'failed', 'duplicate', 'skipped']);
            $table->text('error_message')->nullable();
            $table->json('raw_data'); // Original row data
            $table->json('transformed_data')->nullable(); // After transformation
            
            // Links to created records
            $table->foreignId('wms_staging_order_id')->nullable()->constrained()->nullOnDelete();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['import_file_upload_id', 'status']);
            $table->index('row_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_row_results');
        Schema::dropIfExists('import_file_uploads');
        Schema::dropIfExists('import_templates');
    }
};