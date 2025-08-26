<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Depot;
use App\Models\TippingLocation;
use App\Models\TippingBay;
use App\Models\Booking;
use App\Models\Movement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepotMapController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin|depot-admin|site-admin|warehouse']);
    }
    
    public function index(Request $request)
    {
        // Get user's depot or specific depot if provided
        $depot = null;
        
        try {
            $depot = null;
            
            if ($request->depot_id) {
                $depot = Depot::findOrFail($request->depot_id);
                \Log::info('DepotMap: Using requested depot', ['depot_id' => $request->depot_id, 'name' => $depot->name]);
            } elseif (Auth::check() && Auth::user()->depot_id) {
                $depot = Auth::user()->depot;
                \Log::info('DepotMap: Using user default depot', [
                    'user_id' => Auth::user()->id,
                    'user_depot_id' => Auth::user()->depot_id, 
                    'depot_name' => $depot->name ?? 'null',
                    'user_name' => Auth::user()->name ?? 'unknown'
                ]);
            } 
            
            // Always fallback to first depot if none found
            if (!$depot) {
                $depot = Depot::first();
                \Log::info('DepotMap: Using fallback depot (first)', ['depot_id' => $depot->id ?? 'none', 'name' => $depot->name ?? 'none']);
            }

            if (!$depot) {
                return redirect()->route('admin.bookings.index')
                    ->with('error', 'No depots exist in the system');
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.bookings.index')
                ->with('error', 'Error loading depot: ' . $e->getMessage());
        }

        try {
            // Get all tipping bays for this depot that should show on map
            $bays = TippingBay::where('depot_id', $depot->id)
                ->active()
                ->where('show_on_map', true)
                ->whereNotNull('map_x')
                ->whereNotNull('map_y')
                ->orderBy('name')
                ->get();
                
            // Get all tipping locations for this depot that should show on map (exclude soft-deleted)
            $locations = TippingLocation::where('depot_id', $depot->id)
                ->where('is_active', true)
                ->where('show_on_map', true)
                ->whereNotNull('map_x')
                ->whereNotNull('map_y')
                ->orderBy('name')
                ->get();

            // Get real-time status for each bay
            try {
                $bayStatuses = $this->getBayStatuses($bays);
            } catch (\Exception $e) {
                \Log::error('Bay status error: ' . $e->getMessage());
                // Fallback with empty statuses
                $bayStatuses = [];
                foreach ($bays as $bay) {
                    $bayStatuses[$bay->id] = [
                        'status' => 'available',
                        'occupancy' => 0,
                        'capacity' => 1,
                        'bookings' => 0,
                        'available' => true,
                        'booking' => null
                    ];
                }
            }
            
            // Get real-time status for each location
            try {
                $locationStatuses = $this->getLocationStatuses($locations);
            } catch (\Exception $e) {
                \Log::error('Location status error: ' . $e->getMessage());
                // Fallback with empty statuses
                $locationStatuses = [];
                foreach ($locations as $location) {
                    $locationStatuses[$location->id] = [
                        'status' => 'available',
                        'occupancy' => 0,
                        'capacity' => $location->capacity,
                        'bookings' => 0,
                        'available' => true
                    ];
                }
            }

            // Get current activity summary
            try {
                $activitySummary = $this->getActivitySummary($depot->id);
            } catch (\Exception $e) {
                \Log::error('Activity summary error: ' . $e->getMessage());
                $activitySummary = [
                    'total_locations' => $bays->count(),
                    'available_locations' => $bays->count(),
                    'active_bookings' => 0,
                    'awaiting_collection' => 0,
                    'todays_arrivals' => 0,
                    'pending_arrivals' => 0,
                ];
            }

            // Get recent activity
            try {
                $recentActivity = $this->getRecentActivity($depot->id);
            } catch (\Exception $e) {
                \Log::error('Recent activity error: ' . $e->getMessage());
                $recentActivity = collect();
            }
            
            // Get user's accessible depots for filter dropdown
            $userDepots = [];
            try {
                if (Auth::check() && Auth::user()->depots) {
                    // User has specific depot access
                    $userDepots = Auth::user()->depots()->orderBy('name')->get();
                } else {
                    // Admin access to all depots
                    $userDepots = Depot::orderBy('name')->get();
                }
            } catch (\Exception $e) {
                \Log::error('User depots error: ' . $e->getMessage());
                $userDepots = Depot::orderBy('name')->get();
            }

            return view('warehouse.depot-map.index', compact(
                'depot',
                'bays', 
                'bayStatuses',
                'locations',
                'locationStatuses',
                'activitySummary',
                'recentActivity',
                'userDepots'
            ));
        } catch (\Exception $e) {
            \Log::error('Depot map error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'depot_id' => $depot->id ?? 'null'
            ]);
            
            return redirect()->route('admin.bookings.index')
                ->with('error', 'Error loading depot map data: ' . $e->getMessage() . ' (Check logs for details)');
        }
    }
    
    public function manageBayPositions(Request $request)
    {
        try {
            // Get user's depot or specific depot if provided
            $depot = null;
            
            if ($request->depot_id) {
                $depot = Depot::findOrFail($request->depot_id);
            } elseif (Auth::check() && Auth::user()->depot_id) {
                $depot = Auth::user()->depot;
            } 
            
            // Always fallback to first depot if none found
            if (!$depot) {
                $depot = Depot::first();
            }

            if (!$depot) {
                return redirect()->route('admin.bookings.index')
                    ->with('error', 'No depots exist in the system');
            }

            // Get all bays for this depot
            $bays = TippingBay::where('depot_id', $depot->id)
                ->active()
                ->orderBy('name')
                ->get();
                
            // Get all tipping locations for this depot
            $locations = TippingLocation::where('depot_id', $depot->id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            return view('warehouse.depot-map.manage-positions', compact('depot', 'bays', 'locations'));
        } catch (\Exception $e) {
            \Log::error('Bay positioning error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('admin.bookings.index')
                ->with('error', 'Error loading bay positioning: ' . $e->getMessage());
        }
    }
    
    public function managePositions(Request $request, $depot = null)
    {
        try {
            // Get the specific depot from parameter
            if ($depot) {
                $depot = Depot::findOrFail($depot);
            } elseif ($request->depot_id) {
                $depot = Depot::findOrFail($request->depot_id);
            } elseif (Auth::check() && Auth::user()->depot_id) {
                $depot = Auth::user()->depot;
            } else {
                // Always fallback to first depot if none found
                $depot = Depot::first();
            }

            if (!$depot) {
                return redirect()->route('app.depot-map.index')
                    ->with('error', 'No depots exist in the system');
            }

            // Get all bays for this depot
            $bays = TippingBay::where('depot_id', $depot->id)
                ->active()
                ->orderBy('name')
                ->get();
                
            // Get all tipping locations for this depot
            $locations = TippingLocation::where('depot_id', $depot->id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            return view('warehouse.depot-map.manage-positions', compact('depot', 'bays', 'locations'));
        } catch (\Exception $e) {
            \Log::error('Bay positioning error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('app.depot-map.index')
                ->with('error', 'Error loading bay positioning: ' . $e->getMessage());
        }
    }
    
    public function updateBayPosition(Request $request)
    {
        $request->validate([
            'bay_id' => 'required|exists:tipping_bays,id',
            'map_x' => 'nullable|numeric|min:0|max:100',
            'map_y' => 'nullable|numeric|min:0|max:100',
            'show_on_map' => 'boolean',
            'map_width' => 'nullable|integer|min:20|max:300',
            'map_height' => 'nullable|integer|min:15|max:200',
            'map_rotation' => 'nullable|numeric|min:0|max:360',
            'text_size' => 'nullable|in:xs,sm,md,lg',
            'text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $bay = TippingBay::findOrFail($request->bay_id);
        
        $updateData = [];
        
        // Only update provided fields
        if ($request->has('map_x')) $updateData['map_x'] = $request->map_x;
        if ($request->has('map_y')) $updateData['map_y'] = $request->map_y;
        if ($request->has('show_on_map')) $updateData['show_on_map'] = $request->boolean('show_on_map', true);
        if ($request->has('map_width')) $updateData['map_width'] = $request->map_width;
        if ($request->has('map_height')) $updateData['map_height'] = $request->map_height;
        if ($request->has('map_rotation')) $updateData['map_rotation'] = $request->map_rotation;
        if ($request->has('text_size')) $updateData['text_size'] = $request->text_size;
        if ($request->has('text_color')) $updateData['text_color'] = $request->text_color;
        
        $bay->update($updateData);

        return response()->json([
            'success' => true,
            'message' => "Settings updated for {$bay->name}",
        ]);
    }
    
    public function updateLocationPosition(Request $request)
    {
        $request->validate([
            'location_id' => 'required|exists:tipping_locations,id',
            'map_x' => 'nullable|numeric|min:0|max:100',
            'map_y' => 'nullable|numeric|min:0|max:100',
            'show_on_map' => 'boolean',
            'map_width' => 'nullable|integer|min:20|max:300',
            'map_height' => 'nullable|integer|min:15|max:200',
            'map_rotation' => 'nullable|numeric|min:0|max:360',
            'text_size' => 'nullable|in:xs,sm,md,lg',
            'text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $location = TippingLocation::findOrFail($request->location_id);
        
        $updateData = [];
        
        // Only update provided fields
        if ($request->has('map_x')) $updateData['map_x'] = $request->map_x;
        if ($request->has('map_y')) $updateData['map_y'] = $request->map_y;
        if ($request->has('show_on_map')) $updateData['show_on_map'] = $request->boolean('show_on_map', true);
        if ($request->has('map_width')) $updateData['map_width'] = $request->map_width;
        if ($request->has('map_height')) $updateData['map_height'] = $request->map_height;
        if ($request->has('map_rotation')) $updateData['map_rotation'] = $request->map_rotation;
        if ($request->has('text_size')) $updateData['text_size'] = $request->text_size;
        if ($request->has('text_color')) $updateData['text_color'] = $request->text_color;
        
        $location->update($updateData);

        return response()->json([
            'success' => true,
            'message' => "Settings updated for {$location->name}",
        ]);
    }
    
    public function selectMapFile(Request $request, $depotId = null)
    {
        if ($depotId) {
            $depot = Depot::findOrFail($depotId);
        } else {
            // Get user's depot or first depot
            $depot = null;
            if (Auth::check() && Auth::user()->depot_id) {
                $depot = Auth::user()->depot;
            } else {
                $depot = Depot::first();
            }
        }
        
        if (!$depot) {
            return redirect()->route('admin.depots.index')
                ->with('error', 'No depot found for map file selection');
        }
        
        // Get available map files
        $mapPath = storage_path('app/public/depot-maps');
        $availableFiles = [];
        
        if (is_dir($mapPath)) {
            $files = scandir($mapPath);
            foreach ($files as $file) {
                if (in_array(pathinfo($file, PATHINFO_EXTENSION), ['svg', 'png', 'jpg', 'jpeg', 'gif'])) {
                    $availableFiles[] = $file;
                }
            }
        }
        
        
        // Get all depots for the dropdown
        $allDepots = Depot::orderBy('name')->get();
        
        return view('warehouse.depot-map.select-map-file', compact('depot', 'availableFiles', 'allDepots'));
    }
    
    public function updateMapFile(Request $request)
    {
        $request->validate([
            'depot_id' => 'required|exists:depots,id',
            'map_file' => 'nullable|string',
        ]);
        
        $depot = Depot::findOrFail($request->depot_id);
        
        // If map_file is provided, verify it exists
        if ($request->map_file) {
            $mapPath = storage_path('app/public/depot-maps/' . $request->map_file);
            if (!file_exists($mapPath)) {
                return back()->with('error', 'Selected map file does not exist.');
            }
        }
        
        $depot->update([
            'map_file' => $request->map_file,
            'map_notes' => $request->map_notes
        ]);
        
        $message = $request->map_file 
            ? "Map file updated for {$depot->name}!" 
            : "Map file cleared for {$depot->name}!";
            
        return back()->with('success', $message);
    }
    
    public function uploadMapFile(Request $request)
    {
        $request->validate([
            'depot_id' => 'required|exists:depots,id',
            'map_file_upload' => 'required|file|mimes:svg,png,jpg,jpeg,gif|max:10240', // 10MB max
            'map_notes' => 'nullable|string'
        ]);
        
        $depot = Depot::findOrFail($request->depot_id);
        
        try {
            // Handle file upload
            $file = $request->file('map_file_upload');
            
            // Generate a clean filename
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $cleanName = preg_replace('/[^A-Za-z0-9_-]/', '_', $originalName);
            $filename = $cleanName . '_' . time() . '.' . $extension;
            
            // Create directory if it doesn't exist
            $uploadPath = storage_path('app/public/depot-maps');
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            // Move the uploaded file
            $file->move($uploadPath, $filename);
            
            // Update depot with new map file
            $depot->update([
                'map_file' => $filename,
                'map_notes' => $request->map_notes
            ]);
            
            return back()->with('success', "Map file '{$filename}' uploaded and set for {$depot->name}!");
            
        } catch (\Exception $e) {
            \Log::error('Map file upload error: ' . $e->getMessage());
            return back()->with('error', 'Error uploading map file: ' . $e->getMessage());
        }
    }
    
    public function deleteMapFile(Request $request)
    {
        $request->validate([
            'filename' => 'required|string'
        ]);
        
        try {
            $filename = $request->filename;
            $filePath = storage_path('app/public/depot-maps/' . $filename);
            
            // Check if file exists
            if (!file_exists($filePath)) {
                return back()->with('error', 'File not found: ' . $filename);
            }
            
            // Check if any depot is currently using this file
            $depotsUsingFile = \App\Models\Depot::where('map_file', $filename)->get();
            if ($depotsUsingFile->count() > 0) {
                $depotNames = $depotsUsingFile->pluck('name')->join(', ');
                return back()->with('error', "Cannot delete '{$filename}' - it is currently in use by: {$depotNames}. Please change their map files first.");
            }
            
            // Delete the file
            unlink($filePath);
            
            return back()->with('success', "Map file '{$filename}' has been deleted successfully.");
            
        } catch (\Exception $e) {
            \Log::error('Map file deletion error: ' . $e->getMessage());
            return back()->with('error', 'Error deleting map file: ' . $e->getMessage());
        }
    }

    public function getBayStatus(Request $request, $bayId)
    {
        $bay = TippingBay::findOrFail($bayId);
        
        $currentBooking = $bay->currentBooking();
        $status = $this->determineBayStatus($bay, $currentBooking);
        
        // Get available alternative bays for this depot
        $alternativeBays = TippingBay::where('depot_id', $bay->depot_id)
            ->where('id', '!=', $bay->id)
            ->active()
            ->where('is_occupied', false)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        $bookingData = null;
        if ($currentBooking) {
            $movement = $currentBooking->movements?->first();
            $bookingData = [
                'id' => $currentBooking->id,
                'booking_reference' => $currentBooking->booking_reference ?? $currentBooking->reference_number,
                'customer_name' => $currentBooking->customer->name ?? 'Unknown',
                'customer_phone' => $currentBooking->customer->phone ?? null,
                'vehicle_registration' => $currentBooking->vehicle_registration,
                'driver_name' => $currentBooking->driver_name ?? null,
                'container_number' => $currentBooking->container_number ?? null,
                'waste_type' => $currentBooking->waste_type ?? null,
                'status' => $movement?->current_status ?? 'unknown',
                'arrived_at' => $currentBooking->arrived_at?->format('d/m/Y H:i'),
                'scheduled_at' => $currentBooking->slot?->start_at?->format('d/m/Y H:i'),
                'unit_departed' => $movement?->unit_departed_at?->format('d/m/Y H:i'),
                'unloading_started' => $movement?->unloading_started_at?->format('d/m/Y H:i'),
                'unloading_completed' => $movement?->unloading_completed_at?->format('d/m/Y H:i'),
                'workflow_url' => route('admin.bookings.show', $currentBooking->id),
                'tipping_workflow_url' => $movement ? route('admin.tipping-workflow.show', $movement->id) : null,
            ];
        }

        return response()->json([
            'bay_id' => $bay->id,
            'bay_name' => $bay->name,
            'bay_code' => $bay->code,
            'status' => $status,
            'is_active' => $bay->is_active,
            'is_occupied' => $bay->is_occupied,
            'current_booking' => $bookingData,
            'alternative_bays' => $alternativeBays,
            'can_change_bay' => $currentBooking && in_array($status, ['arrived', 'in_location', 'at_bay'])
        ]);
    }
    
    public function changeBay(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'current_bay_id' => 'required|exists:tipping_bays,id',
            'new_bay_id' => 'required|exists:tipping_bays,id',
        ]);
        
        try {
            $booking = \App\Models\Booking::findOrFail($request->booking_id);
            $currentBay = TippingBay::findOrFail($request->current_bay_id);
            $newBay = TippingBay::findOrFail($request->new_bay_id);
            
            // Check if new bay is available
            if ($newBay->is_occupied) {
                return response()->json([
                    'success' => false,
                    'message' => 'Target bay is already occupied'
                ]);
            }
            
            // Check if both bays are in the same depot
            if ($currentBay->depot_id !== $newBay->depot_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot move between different depots'
                ]);
            }
            
            // Update the movement record
            $movement = $booking->movements()->first();
            if ($movement) {
                $movement->update([
                    'tipping_bay_id' => $newBay->id
                ]);
                
                // Update bay occupancy
                $currentBay->update(['is_occupied' => false]);
                $newBay->update(['is_occupied' => true]);
                
                // Log the change
                \Log::info("Bay changed for booking {$booking->booking_reference}: from {$currentBay->name} to {$newBay->name}");
                
                return response()->json([
                    'success' => true,
                    'message' => "Vehicle moved from {$currentBay->name} to {$newBay->name}"
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No movement record found for this booking'
                ]);
            }
            
        } catch (\Exception $e) {
            \Log::error('Bay change error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error changing bay: ' . $e->getMessage()
            ]);
        }
    }

    public function getLocationStatus(Request $request, $locationId)
    {
        $location = TippingLocation::findOrFail($locationId);
        
        // Get current bookings at this location
        $currentBookings = Booking::whereHas('movements', function($query) use ($locationId) {
            $query->where('tipping_location_id', $locationId)
                  ->whereIn('current_status', ['arrived', 'in_location', 'at_bay', 'unloading', 'trailer_dropped']);
        })->with(['movements' => function($query) use ($locationId) {
            $query->where('tipping_location_id', $locationId);
        }, 'customer'])->get();

        $status = $this->determineLocationStatus($location, $currentBookings);

        return response()->json([
            'location_id' => $location->id,
            'location_name' => $location->name,
            'status' => $status,
            'capacity' => $location->capacity,
            'current_occupancy' => $currentBookings->count(),
            'available_capacity' => max(0, $location->capacity - $currentBookings->count()),
            'current_bookings' => $currentBookings->map(function($booking) {
                $movement = $booking->movements->first();
                return [
                    'id' => $booking->id,
                    'booking_reference' => $booking->booking_reference,
                    'customer_name' => $booking->customer->name ?? 'Unknown',
                    'vehicle_registration' => $booking->vehicle_registration,
                    'container_number' => $booking->container_number,
                    'status' => $movement->current_status ?? 'unknown',
                    'arrived_at' => $booking->arrived_at?->format('H:i'),
                    'unit_departed' => $movement->unit_departed_at?->format('H:i'),
                ];
            })
        ]);
    }

    private function getBayStatuses($bays)
    {
        $statuses = [];
        
        foreach ($bays as $bay) {
            // Get current booking at this bay
            $currentBooking = $bay->currentBooking();
            
            $status = $this->determineBayStatus($bay, $currentBooking);
            
            $statuses[$bay->id] = [
                'status' => $status,
                'occupancy' => $currentBooking ? 1 : 0,
                'capacity' => 1, // Bays typically have capacity of 1
                'bookings' => $currentBooking ? 1 : 0,
                'available' => $bay->isAvailable(),
                'booking' => $currentBooking
            ];
        }

        return $statuses;
    }
    
    private function determineBayStatus($bay, $currentBooking)
    {
        // Bay is inactive/locked - show as disabled
        if (!$bay->is_active) {
            return 'disabled';
        }

        if (!$currentBooking) {
            return 'available';
        }

        // Check booking status through movement
        if ($currentBooking->movements) {
            $movement = $currentBooking->movements->first();
            if ($movement) {
                if (in_array($movement->current_status, ['at_bay', 'unloading'])) {
                    return 'active';
                }
                if ($movement->current_status === 'trailer_dropped' && $movement->unit_departed_at) {
                    return 'waiting_collection';
                }
                if (in_array($movement->current_status, ['arrived', 'in_location'])) {
                    return 'occupied';
                }
            }
        }

        return $bay->is_occupied ? 'occupied' : 'available';
    }

    private function getLocationStatuses($locations)
    {
        $statuses = [];
        
        foreach ($locations as $location) {
            // Get current bookings at this location
            $currentBookings = Booking::whereHas('movements', function($query) use ($location) {
                $query->where('tipping_location_id', $location->id)
                      ->whereIn('current_status', ['arrived', 'in_location', 'at_bay', 'unloading', 'trailer_dropped']);
            })->with('movements')->get();

            $status = $this->determineLocationStatus($location, $currentBookings);
            
            $statuses[$location->id] = [
                'status' => $status,
                'occupancy' => $currentBookings->count(),
                'capacity' => $location->capacity,
                'bookings' => $currentBookings->count(),
                'available' => max(0, $location->capacity - $currentBookings->count()) > 0
            ];
        }

        return $statuses;
    }

    private function determineLocationStatus($location, $currentBookings)
    {
        if (!$location->is_active) {
            return 'offline';
        }

        if ($currentBookings->isEmpty()) {
            return 'available';
        }

        // Check for active tipping
        $activeTipping = $currentBookings->filter(function($booking) {
            $movement = $booking->movements->first();
            return $movement && in_array($movement->current_status, ['at_bay', 'unloading']);
        });

        if ($activeTipping->count() > 0) {
            return 'active';
        }

        // Check for trailers awaiting collection
        $awaitingCollection = $currentBookings->filter(function($booking) {
            $movement = $booking->movements->first();
            return $movement && $movement->current_status === 'trailer_dropped' && $movement->unit_departed_at;
        });

        if ($awaitingCollection->count() > 0) {
            return 'waiting_collection';
        }

        // Check for scheduled/occupied
        $occupied = $currentBookings->filter(function($booking) {
            $movement = $booking->movements->first();
            return $movement && in_array($movement->current_status, ['arrived', 'in_location']);
        });

        if ($occupied->count() > 0) {
            return 'occupied';
        }

        // Check if at capacity
        if ($currentBookings->count() >= $location->capacity) {
            return 'full';
        }

        return 'available';
    }

    private function getActivitySummary($depotId)
    {
        $now = now();
        $today = $now->copy()->startOfDay();

        return [
            'total_locations' => TippingLocation::where('depot_id', $depotId)->active()->count(),
            'available_locations' => TippingLocation::where('depot_id', $depotId)
                ->active()
                ->available()
                ->count(),
            'active_bookings' => Booking::whereHas('movements', function($query) use ($depotId) {
                $query->whereHas('tippingLocation', function($subQuery) use ($depotId) {
                    $subQuery->where('depot_id', $depotId);
                })->whereIn('current_status', ['at_bay', 'unloading']);
            })->count(),
            'awaiting_collection' => Booking::whereHas('movements', function($query) use ($depotId) {
                $query->whereHas('tippingLocation', function($subQuery) use ($depotId) {
                    $subQuery->where('depot_id', $depotId);
                })->where('current_status', 'trailer_dropped')
                  ->whereNotNull('unit_departed_at');
            })->count(),
            'todays_arrivals' => Booking::where('arrived_at', '>=', $today)
                ->whereHas('slot', function($query) use ($depotId) {
                    $query->where('depot_id', $depotId);
                })->count(),
            'pending_arrivals' => Booking::whereNull('arrived_at')
                ->whereHas('slot', function($query) use ($today, $depotId) {
                    $query->where('start_at', '>=', $today)
                          ->where('start_at', '<=', $today->copy()->endOfDay())
                          ->where('depot_id', $depotId);
                })->count(),
        ];
    }

    private function getRecentActivity($depotId, $limit = 10)
    {
        return Booking::whereHas('slot', function($query) use ($depotId) {
            $query->where('depot_id', $depotId);
        })->with(['movements.tippingLocation', 'customer'])
        ->where(function($query) {
            $query->whereNotNull('arrived_at')
                  ->orWhereHas('movements', function($subQuery) {
                      $subQuery->whereNotNull('unloading_started_at')
                               ->orWhereNotNull('unloading_completed_at')
                               ->orWhereNotNull('unit_departed_at')
                               ->orWhereNotNull('collection_unit_departed_at');
                  });
        })
        ->orderByDesc('updated_at')
        ->limit($limit)
        ->get()
        ->map(function($booking) {
            $movement = $booking->movements->first();
            $latestTime = $booking->updated_at;
            $action = 'Updated';

            if ($movement) {
                if ($movement->collection_unit_departed_at && $movement->collection_unit_departed_at > $latestTime) {
                    $latestTime = $movement->collection_unit_departed_at;
                    $action = 'Collection completed';
                } elseif ($movement->unit_departed_at && $movement->unit_departed_at > $latestTime) {
                    $latestTime = $movement->unit_departed_at;
                    $action = 'Unit departed';
                } elseif ($movement->unloading_completed_at && $movement->unloading_completed_at > $latestTime) {
                    $latestTime = $movement->unloading_completed_at;
                    $action = 'Tipping completed';
                } elseif ($movement->unloading_started_at && $movement->unloading_started_at > $latestTime) {
                    $latestTime = $movement->unloading_started_at;
                    $action = 'Tipping started';
                } elseif ($booking->arrived_at && $booking->arrived_at > $latestTime) {
                    $latestTime = $booking->arrived_at;
                    $action = 'Vehicle arrived';
                }
            }

            return [
                'booking_id' => $booking->id,
                'booking_reference' => $booking->booking_reference,
                'customer_name' => $booking->customer->name ?? 'Unknown',
                'location_name' => $movement->tippingLocation->name ?? 'Unknown',
                'vehicle_registration' => $booking->vehicle_registration,
                'action' => $action,
                'time' => $latestTime,
                'time_formatted' => $latestTime->format('H:i'),
            ];
        });
    }

    public function refreshStatus(Request $request)
    {
        $depotId = $request->depot_id ?? Auth::user()->depot_id ?? Depot::first()->id;
        
        $locations = TippingLocation::where('depot_id', $depotId)->active()->get();
        $locationStatuses = $this->getLocationStatuses($locations);
        $activitySummary = $this->getActivitySummary($depotId);

        return response()->json([
            'location_statuses' => $locationStatuses,
            'activity_summary' => $activitySummary,
            'timestamp' => now()->format('H:i:s')
        ]);
    }
}