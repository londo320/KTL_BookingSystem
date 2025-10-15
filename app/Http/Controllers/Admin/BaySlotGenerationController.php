<?php

namespace App\Http\Controllers\Admin;

use App\Console\Commands\GenerateBaySlots;
use App\Http\Controllers\Controller;
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

        // Generate slots using the command
        Artisan::call('slots:generate-bay', [
            '--depot' => $validated['depot_id'],
            '--days' => $validated['days'],
        ]);

        $output = Artisan::output();

        return redirect()
            ->route('app.bay-slot-generation.index')
            ->with('success', 'Bay slots generated successfully!')
            ->with('command_output', $output);
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
