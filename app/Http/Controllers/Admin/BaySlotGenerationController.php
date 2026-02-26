<?php

namespace App\Http\Controllers\Admin;

use App\Console\Commands\GenerateBaySlots;
use App\Http\Controllers\Controller;
use App\Jobs\GenerateBaySlotsJob;
use App\Models\Depot;
use App\Models\TippingBay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class BaySlotGenerationController extends Controller
{
    /**
     * Show the bay slot generation form
     */
    public function index()
    {
        $depots = Depot::with(['tippingBays' => function ($query) {
            $query->where('is_active', true);
        }])->orderBy('name')->get();

        return view('admin.bay-slot-generation.index', compact('depots'));
    }

    /**
     * Generate bay slots
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'depot_id' => 'required|exists:depots,id',
            'days' => 'required|integer|min:1|max:90',
            'bay_configs' => 'required|array',
            'bay_configs.*.bay_id' => 'required|exists:tipping_bays,id',
            'bay_configs.*.is_24_hour' => 'boolean',
            'bay_configs.*.operational_start' => 'nullable|date_format:H:i',
            'bay_configs.*.operational_end' => 'nullable|date_format:H:i|after:bay_configs.*.operational_start',
            'bay_configs.*.operational_days' => 'nullable|array',
            'bay_configs.*.operational_days.*' => 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
        ]);

        // Update bay operational hours
        foreach ($validated['bay_configs'] as $config) {
            $bay = TippingBay::find($config['bay_id']);

            $is24Hour = isset($config['is_24_hour']) && $config['is_24_hour'] == '1';

            $bay->update([
                'is_24_hour' => $is24Hour,
                'operational_start' => $is24Hour ? null : ($config['operational_start'] ?? null),
                'operational_end' => $is24Hour ? null : ($config['operational_end'] ?? null),
                'operational_days' => $is24Hour ? null : ($config['operational_days'] ?? null),
            ]);
        }

        // Dispatch slot generation as a background job to prevent timeout
        GenerateBaySlotsJob::dispatch($validated['depot_id'], $validated['days']);

        return redirect()
            ->route('app.bay-slot-generation.index')
            ->with('success', 'Bay slot generation started! This will run in the background and may take a few minutes. Check the slot list to see progress.')
            ->with('info', 'The job is running in the background. Refresh the slot list in a few minutes to see the new slots.');
    }

    /**
     * Get bay details for a depot (AJAX)
     */
    public function getBaysForDepot(Request $request)
    {
        $depotId = $request->input('depot_id');

        $bays = TippingBay::where('depot_id', $depotId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'is_24_hour', 'operational_start', 'operational_end', 'operational_days']);

        return response()->json($bays);
    }
}
