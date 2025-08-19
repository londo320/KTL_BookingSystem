<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArrivalTimeSetting;
use App\Models\Customer;
use App\Models\Depot;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ArrivalTimeSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin|depot-admin']);
    }

    public function index(Request $request)
    {
        $level = $request->get('level', 'all');
        $depotId = $request->get('depot_id');
        $customerId = $request->get('customer_id');

        $query = ArrivalTimeSetting::with(['depot', 'customer'])
            ->active()
            ->orderByRaw("
                CASE level 
                    WHEN 'global' THEN 1 
                    WHEN 'depot' THEN 2 
                    WHEN 'customer' THEN 3 
                END
            ")
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($level !== 'all') {
            $query->where('level', $level);
        }

        if ($depotId) {
            $query->where('depot_id', $depotId);
        }

        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        $settings = $query->paginate(20);

        // Get filter options
        $depots = Depot::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $levels = ArrivalTimeSetting::getAvailableLevels();

        return view('admin.arrival-time-settings.index', compact(
            'settings',
            'depots',
            'customers',
            'levels',
            'level',
            'depotId',
            'customerId'
        ));
    }

    public function create(Request $request)
    {
        $level = $request->get('level', ArrivalTimeSetting::LEVEL_GLOBAL);
        $depotId = $request->get('depot_id');
        $customerId = $request->get('customer_id');

        $depots = Depot::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $levels = ArrivalTimeSetting::getAvailableLevels();

        return view('admin.arrival-time-settings.create', compact(
            'depots',
            'customers',
            'levels',
            'level',
            'depotId',
            'customerId'
        ));
    }

    public function store(Request $request)
    {
        $rules = [
            'level' => ['required', Rule::in(array_keys(ArrivalTimeSetting::getAvailableLevels()))],
            'early_threshold_minutes' => 'required|integer|min:0|max:1440',
            'late_threshold_minutes' => 'required|integer|min:0|max:1440',
            'description' => 'nullable|string|max:500',
            'depot_id' => 'nullable|exists:depots,id',
            'customer_id' => 'nullable|exists:customers,id',
        ];

        // Level-specific validation
        if ($request->level === ArrivalTimeSetting::LEVEL_GLOBAL) {
            $rules['depot_id'] = 'required|null';
            $rules['customer_id'] = 'required|null';
        } elseif ($request->level === ArrivalTimeSetting::LEVEL_DEPOT) {
            $rules['depot_id'] = 'required|exists:depots,id';
            $rules['customer_id'] = 'required|null';
        } elseif ($request->level === ArrivalTimeSetting::LEVEL_CUSTOMER) {
            $rules['customer_id'] = 'required|exists:customers,id';
        }

        $validated = $request->validate($rules);

        // Check for existing setting at this level
        $existing = ArrivalTimeSetting::where('level', $validated['level'])
            ->where('depot_id', $validated['depot_id'] ?? null)
            ->where('customer_id', $validated['customer_id'] ?? null)
            ->where('is_active', true)
            ->first();

        if ($existing) {
            return back()->withErrors([
                'level' => 'An active setting already exists for this level/entity combination.'
            ])->withInput();
        }

        $setting = ArrivalTimeSetting::create([
            'level' => $validated['level'],
            'depot_id' => $validated['depot_id'] ?? null,
            'customer_id' => $validated['customer_id'] ?? null,
            'early_threshold_minutes' => $validated['early_threshold_minutes'],
            'late_threshold_minutes' => $validated['late_threshold_minutes'],
            'description' => $validated['description'],
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.arrival-time-settings.index')
            ->with('success', 'Arrival time setting created successfully.');
    }

    public function show(ArrivalTimeSetting $arrivalTimeSetting)
    {
        $arrivalTimeSetting->load(['depot', 'customer']);
        
        // Get some example scenarios
        $examples = $this->getExampleScenarios($arrivalTimeSetting);

        return view('admin.arrival-time-settings.show', compact('arrivalTimeSetting', 'examples'));
    }

    public function edit(ArrivalTimeSetting $arrivalTimeSetting)
    {
        $arrivalTimeSetting->load(['depot', 'customer']);
        
        $depots = Depot::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $levels = ArrivalTimeSetting::getAvailableLevels();

        return view('admin.arrival-time-settings.edit', compact(
            'arrivalTimeSetting',
            'depots',
            'customers',
            'levels'
        ));
    }

    public function update(Request $request, ArrivalTimeSetting $arrivalTimeSetting)
    {
        $rules = [
            'early_threshold_minutes' => 'required|integer|min:0|max:1440',
            'late_threshold_minutes' => 'required|integer|min:0|max:1440',
            'description' => 'nullable|string|max:500',
        ];

        $validated = $request->validate($rules);

        $arrivalTimeSetting->update($validated);

        return redirect()
            ->route('admin.arrival-time-settings.index')
            ->with('success', 'Arrival time setting updated successfully.');
    }

    public function destroy(ArrivalTimeSetting $arrivalTimeSetting)
    {
        // Don't allow deletion of global setting if it's the only one
        if ($arrivalTimeSetting->level === ArrivalTimeSetting::LEVEL_GLOBAL) {
            $globalCount = ArrivalTimeSetting::where('level', ArrivalTimeSetting::LEVEL_GLOBAL)
                ->where('is_active', true)
                ->count();

            if ($globalCount <= 1) {
                return back()->withErrors([
                    'error' => 'Cannot delete the last global arrival time setting.'
                ]);
            }
        }

        $arrivalTimeSetting->update(['is_active' => false]);

        return redirect()
            ->route('admin.arrival-time-settings.index')
            ->with('success', 'Arrival time setting deactivated successfully.');
    }

    public function preview(Request $request)
    {
        $customerId = $request->get('customer_id');
        $depotId = $request->get('depot_id');
        
        $settings = ArrivalTimeSetting::getEffectiveSettings($customerId, $depotId);
        
        return response()->json([
            'effective_settings' => $settings,
            'examples' => $this->getExampleTimings($settings)
        ]);
    }

    private function getExampleScenarios(ArrivalTimeSetting $setting): array
    {
        $scheduledTime = now()->setHour(10)->setMinute(0)->setSecond(0);
        
        $scenarios = [
            [
                'name' => 'Very Early',
                'actual_time' => $scheduledTime->copy()->subMinutes($setting->early_threshold_minutes + 10),
                'expected_status' => ArrivalTimeSetting::STATUS_EARLY,
            ],
            [
                'name' => 'Just Early (within tolerance)',
                'actual_time' => $scheduledTime->copy()->subMinutes($setting->early_threshold_minutes - 5),
                'expected_status' => ArrivalTimeSetting::STATUS_ON_TIME,
            ],
            [
                'name' => 'On Time',
                'actual_time' => $scheduledTime->copy(),
                'expected_status' => ArrivalTimeSetting::STATUS_ON_TIME,
            ],
            [
                'name' => 'Just Late (within tolerance)',
                'actual_time' => $scheduledTime->copy()->addMinutes($setting->late_threshold_minutes - 5),
                'expected_status' => ArrivalTimeSetting::STATUS_ON_TIME,
            ],
            [
                'name' => 'Very Late',
                'actual_time' => $scheduledTime->copy()->addMinutes($setting->late_threshold_minutes + 10),
                'expected_status' => ArrivalTimeSetting::STATUS_LATE,
            ],
        ];

        foreach ($scenarios as &$scenario) {
            $details = ArrivalTimeSetting::getArrivalStatusDetails(
                $scheduledTime,
                $scenario['actual_time'],
                $setting->customer_id,
                $setting->depot_id
            );
            $scenario['details'] = $details;
        }

        return $scenarios;
    }

    private function getExampleTimings(array $settings): array
    {
        $early = $settings['early_threshold_minutes'];
        $late = $settings['late_threshold_minutes'];

        return [
            'on_time_window' => "±{$early}/{$late} minutes",
            'early_cutoff' => "More than {$early} minutes before",
            'late_cutoff' => "More than {$late} minutes after",
            'example' => "For 10:00 booking: Early < 9:" . str_pad(60 - $early, 2, '0', STR_PAD_LEFT) . 
                        ", On-time 9:" . str_pad(60 - $early, 2, '0', STR_PAD_LEFT) . 
                        " - 10:" . str_pad($late, 2, '0', STR_PAD_LEFT) . 
                        ", Late > 10:" . str_pad($late, 2, '0', STR_PAD_LEFT)
        ];
    }
}