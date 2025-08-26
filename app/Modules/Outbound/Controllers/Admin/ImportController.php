<?php

namespace App\Modules\Outbound\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Outbound\Models\ImportTemplate;
use App\Modules\Outbound\Models\ImportFileUpload;
use App\Modules\Outbound\Services\FileImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImportController extends Controller
{
    /**
     * Show import dashboard
     */
    public function dashboard()
    {
        $recentUploads = ImportFileUpload::with(['importTemplate', 'uploadedBy'])
            ->orderBy('uploaded_at', 'desc')
            ->limit(10)
            ->get();

        $templates = ImportTemplate::active()
            ->orderBy('source_system')
            ->orderBy('name')
            ->get();

        $statistics = [
            'total_uploads' => ImportFileUpload::count(),
            'processing' => ImportFileUpload::where('status', 'processing')->count(),
            'pending_review' => ImportFileUpload::where('requires_review', true)->count(),
            'failed_today' => ImportFileUpload::where('status', 'failed')
                ->whereDate('uploaded_at', today())
                ->count(),
        ];

        return view('outbound::admin.imports.dashboard', compact(
            'recentUploads', 'templates', 'statistics'
        ));
    }

    /**
     * Show file upload form
     */
    public function create()
    {
        $templates = ImportTemplate::active()
            ->orderBy('source_system')
            ->orderBy('name')
            ->get()
            ->groupBy('source_system');

        return view('outbound::admin.imports.create', compact('templates'));
    }

    /**
     * Process file upload
     */
    public function store(Request $request)
    {
        $request->validate([
            'import_template_id' => 'required|exists:import_templates,id',
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        $template = ImportTemplate::findOrFail($request->import_template_id);
        
        // Validate file type matches template
        $fileExtension = $request->file('file')->getClientOriginalExtension();
        if (strtolower($fileExtension) !== strtolower($template->file_type)) {
            return back()->withErrors([
                'file' => "File type must be {$template->file_type}, got {$fileExtension}"
            ]);
        }

        try {
            $importService = new FileImportService();
            $fileUpload = $importService->processUploadedFile(
                $request->file('file'), 
                $template, 
                auth()->id()
            );

            if ($fileUpload->requires_review) {
                return redirect()
                    ->route('outbound.imports.review', $fileUpload)
                    ->with('success', 'File uploaded successfully. Please review before processing.');
            }

            return redirect()
                ->route('outbound.imports.show', $fileUpload)
                ->with('success', 'File uploaded and processed successfully.');

        } catch (\Exception $e) {
            return back()->withErrors([
                'file' => 'Import failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show file upload details
     */
    public function show(ImportFileUpload $fileUpload)
    {
        $fileUpload->load([
            'importTemplate', 
            'uploadedBy', 
            'rowResults.wmsStagingOrder'
        ]);

        return view('outbound::admin.imports.show', compact('fileUpload'));
    }

    /**
     * Show file for review before processing
     */
    public function review(ImportFileUpload $fileUpload)
    {
        if (!$fileUpload->requires_review) {
            return redirect()->route('outbound.imports.show', $fileUpload);
        }

        $fileUpload->load('importTemplate');

        return view('outbound::admin.imports.review', compact('fileUpload'));
    }

    /**
     * Approve and process file after review
     */
    public function approve(ImportFileUpload $fileUpload)
    {
        if (!$fileUpload->requires_review || $fileUpload->status !== 'uploaded') {
            return back()->with('error', 'File cannot be processed at this time.');
        }

        try {
            $importService = new FileImportService();
            $importService->processFileUpload($fileUpload);

            return redirect()
                ->route('outbound.imports.show', $fileUpload)
                ->with('success', 'File processed successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Reject file after review
     */
    public function reject(ImportFileUpload $fileUpload)
    {
        if (!$fileUpload->requires_review || $fileUpload->status !== 'uploaded') {
            return back()->with('error', 'File cannot be rejected at this time.');
        }

        $fileUpload->update([
            'status' => 'rejected',
            'processing_completed_at' => now(),
            'error_log' => 'File rejected during manual review'
        ]);

        return redirect()
            ->route('outbound.imports.dashboard')
            ->with('success', 'File has been rejected.');
    }

    /**
     * Reprocess failed file
     */
    public function reprocess(ImportFileUpload $fileUpload)
    {
        if ($fileUpload->status === 'processing') {
            return back()->with('error', 'File is already being processed.');
        }

        try {
            $importService = new FileImportService();
            $importService->processFileUpload($fileUpload);

            return back()->with('success', 'File reprocessed successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Reprocessing failed: ' . $e->getMessage());
        }
    }

    /**
     * Download original file
     */
    public function download(ImportFileUpload $fileUpload)
    {
        if (!Storage::exists($fileUpload->file_path)) {
            return back()->with('error', 'File not found.');
        }

        return Storage::download(
            $fileUpload->file_path, 
            $fileUpload->original_filename
        );
    }

    /**
     * Delete file upload
     */
    public function destroy(ImportFileUpload $fileUpload)
    {
        if ($fileUpload->status === 'processing') {
            return back()->with('error', 'Cannot delete file while processing.');
        }

        // Delete physical file
        if (Storage::exists($fileUpload->file_path)) {
            Storage::delete($fileUpload->file_path);
        }

        $fileUpload->delete();

        return redirect()
            ->route('outbound.imports.dashboard')
            ->with('success', 'File deleted successfully.');
    }

    /**
     * Show import templates management
     */
    public function templates()
    {
        $templates = ImportTemplate::with('fileUploads')
            ->orderBy('source_system')
            ->orderBy('name')
            ->get();

        return view('outbound::admin.imports.templates.index', compact('templates'));
    }

    /**
     * Show template creation form
     */
    public function createTemplate()
    {
        $template = new ImportTemplate();
        $standardFields = $template->getStandardFieldMapping();

        return view('outbound::admin.imports.templates.create', compact('template', 'standardFields'));
    }

    /**
     * Store new template
     */
    public function storeTemplate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'source_system' => 'required|string|max:50',
            'file_type' => 'required|in:csv,xlsx,txt,xml,json',
            'description' => 'nullable|string',
            'header_row' => 'required|integer|min:0',
            'data_start_row' => 'required|integer|min:1',
            'delimiter' => 'required|string|max:5',
            'text_qualifier' => 'required|string|max:5',
            'encoding' => 'required|string|max:20',
            'column_mapping' => 'required|array',
            'auto_process' => 'boolean',
            'duplicate_handling' => 'required|in:skip,overwrite,create_new',
        ]);

        $validated['auto_process'] = $request->has('auto_process');

        $template = ImportTemplate::create($validated);

        return redirect()
            ->route('outbound.imports.templates')
            ->with('success', 'Import template created successfully.');
    }

    /**
     * Show template edit form
     */
    public function editTemplate(ImportTemplate $template)
    {
        $standardFields = $template->getStandardFieldMapping();
        
        // Get recent file uploads with their headers and preview data
        $recentUploads = ImportFileUpload::where('import_template_id', $template->id)
            ->where(function($query) {
                $query->whereNotNull('sample_data')
                      ->orWhereNotNull('detected_columns');
            })
            ->orderBy('uploaded_at', 'desc')
            ->limit(3)
            ->get();
            
        // Extract unique headers from recent uploads
        $availableHeaders = collect();
        $previewData = null;
        $selectedUpload = null;
        
        if ($recentUploads->isNotEmpty()) {
            foreach ($recentUploads as $upload) {
                // Try to get headers from detected_columns first, then from sample_data
                $headers = [];
                if ($upload->detected_columns && is_array($upload->detected_columns)) {
                    $headers = $upload->detected_columns;
                } elseif ($upload->sample_data && is_array($upload->sample_data)) {
                    $headers = array_keys($upload->sample_data[0] ?? []);
                }
                $availableHeaders = $availableHeaders->merge($headers);
            }
            
            // Use the most recent upload for preview
            $selectedUpload = $recentUploads->first();
            $previewData = null;
            if ($selectedUpload && $selectedUpload->sample_data && is_array($selectedUpload->sample_data)) {
                $previewData = array_slice($selectedUpload->sample_data, 0, 100);
            }
        }
        
        $availableHeaders = $availableHeaders->unique()->sort()->values();

        return view('outbound::admin.imports.templates.edit', compact(
            'template', 
            'standardFields', 
            'recentUploads',
            'availableHeaders',
            'previewData',
            'selectedUpload'
        ));
    }

    /**
     * Update template
     */
    public function updateTemplate(Request $request, ImportTemplate $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'header_row' => 'required|integer|min:0',
            'data_start_row' => 'required|integer|min:1',
            'delimiter' => 'required|string|max:5',
            'text_qualifier' => 'required|string|max:5',
            'encoding' => 'required|string|max:20',
            'column_mapping' => 'required|array',
            'auto_process' => 'boolean',
            'duplicate_handling' => 'required|in:skip,overwrite,create_new',
            'is_active' => 'boolean',
        ]);

        $validated['auto_process'] = $request->has('auto_process');
        $validated['is_active'] = $request->has('is_active');

        $template->update($validated);

        return back()->with('success', 'Template updated successfully.');
    }

    /**
     * Toggle template active status
     */
    public function toggleTemplate(ImportTemplate $template)
    {
        $template->update(['is_active' => !$template->is_active]);
        
        $status = $template->is_active ? 'activated' : 'deactivated';
        
        return back()->with('success', "Template has been {$status}.");
    }
}