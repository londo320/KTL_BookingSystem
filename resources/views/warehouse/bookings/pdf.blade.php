<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking Details #{{ $booking->id }}</title>
    <style>
        body { 
            font-family: 'DejaVu Sans', 'Arial Unicode MS', Arial, sans-serif; 
            margin: 0; 
            padding: 20px; 
            font-size: 12px;
            line-height: 1.4;
        }
        .header { 
            text-align: center; 
            margin-bottom: 20px; 
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .section { 
            margin-bottom: 15px; 
            page-break-inside: avoid;
        }
        .section-title { 
            font-size: 14px; 
            font-weight: bold; 
            margin-bottom: 8px; 
            color: #333;
            border-bottom: 1px solid #ccc;
            padding-bottom: 2px;
        }
        .info-grid { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 10px; 
        }
        .info-item { 
            margin-bottom: 5px; 
        }
        .label { 
            font-weight: bold; 
            color: #555; 
        }
        .value { 
            margin-left: 10px; 
        }
        .status-banner {
            background: #f0f9ff;
            border: 1px solid #0ea5e9;
            padding: 10px;
            margin-bottom: 15px;
            text-align: center;
        }
        .arrived .status-banner {
            background: #f0fdf4;
            border-color: #22c55e;
        }
        .locked .status-banner {
            background: #fffbeb;
            border-color: #f59e0b;
        }
        .load-comparison {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }
        .load-comparison .arrow {
            color: #666;
            font-weight: bold;
        }
        .variance {
            font-size: 11px;
            font-weight: bold;
        }
        .variance.positive { color: #059669; }
        .variance.negative { color: #dc2626; }
        
        /* Emoji styling for PDFs */
        .emoji {
            font-family: 'Apple Color Emoji', 'Segoe UI Emoji', 'Noto Color Emoji', 'Segoe UI Symbol', sans-serif;
            font-size: 14px;
            margin-right: 5px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Booking Details #{{ $booking->id }}</h1>
        <p>Generated on {{ now()->format('d M Y, H:i') }}</p>
    </div>

    @php
        $isLocked = $booking->slot->locked_at && $booking->slot->locked_at->isPast();
        $hasArrived = $booking->arrived_at;
    @endphp

    @if($hasArrived)
        <div class="status-banner arrived">
            <strong><span class="emoji">✅</span>Vehicle Arrived</strong><br>
            Arrived: {{ $booking->arrived_at->format('d M Y, H:i') }}
            @if($booking->departed_at)
                | Departed: {{ $booking->departed_at->format('d M Y, H:i') }}
            @else
                | Currently on-site
            @endif
        </div>
    @elseif($isLocked)
        <div class="status-banner locked">
            <strong><span class="emoji">🔒</span>Booking Locked</strong><br>
            Cut-off time: {{ $booking->slot->locked_at->format('d M Y, H:i') }}
        </div>
    @else
        <div class="status-banner">
            <strong><span class="emoji">📅</span>Booking Active</strong><br>
            This booking is active and can be edited.
        </div>
    @endif

    <div class="info-grid">
        <div class="section">
            <div class="section-title"><span class="emoji">📋</span>Booking Information</div>
            <div class="info-item">
                <span class="label">Booking ID:</span>
                <span class="value">#{{ $booking->id }}</span>
            </div>
            <div class="info-item">
                <span class="label">Customer:</span>
                <span class="value">{{ $booking->customer->name ?? 'Not assigned' }}</span>
            </div>
            <div class="info-item">
                <span class="label">Created By:</span>
                <span class="value">{{ $booking->user->name ?? 'Unknown' }}</span>
            </div>
            <div class="info-item">
                <span class="label">Created At:</span>
                <span class="value">{{ $booking->created_at->format('d M Y, H:i') }}</span>
            </div>
            @if($booking->reference)
                <div class="info-item">
                    <span class="label">Reference:</span>
                    <span class="value">{{ $booking->reference }}</span>
                </div>
            @endif
        </div>

        <div class="section">
            <div class="section-title"><span class="emoji">📍</span>Slot & Location</div>
            <div class="info-item">
                <span class="label">Depot:</span>
                <span class="value">{{ $booking->slot->depot->name }}</span>
            </div>
            @if($booking->slot->depot->location)
                <div class="info-item">
                    <span class="label">Location:</span>
                    <span class="value">{{ $booking->slot->depot->location }}</span>
                </div>
            @endif
            <div class="info-item">
                <span class="label">Date:</span>
                <span class="value">{{ $booking->slot->start_at->format('l, d F Y') }}</span>
            </div>
            <div class="info-item">
                <span class="label">Time:</span>
                <span class="value">{{ $booking->slot->start_at->format('H:i') }} - {{ $booking->slot->end_at->format('H:i') }}</span>
            </div>
            <div class="info-item">
                <span class="label">Booking Type:</span>
                <span class="value">{{ $booking->bookingType->name ?? 'Not specified' }}</span>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title"><span class="emoji">📦</span>PO Numbers & Load Details</div>
        
        @if($booking->poNumbers && $booking->poNumbers->count() > 0)
            @foreach($booking->poNumbers as $index => $poNumber)
                <div style="border: 1px solid #ddd; margin-bottom: 10px; padding: 8px;">
                    <div style="font-weight: bold; margin-bottom: 5px; font-size: 13px;">
                        PO #{{ $index + 1 }}: {{ $poNumber->po_number }}
                        @if($poNumber->hasVariance())
                            <span style="background: #fef2f2; color: #dc2626; padding: 2px 6px; border-radius: 3px; font-size: 10px;">Has Variance</span>
                        @elseif($poNumber->isComplete())
                            <span style="background: #f0fdf4; color: #059669; padding: 2px 6px; border-radius: 3px; font-size: 10px;">Complete</span>
                        @endif
                    </div>
                    
                    {{-- PO Lines --}}
                    @if($poNumber->lines->count() > 0)
                        <div style="margin-bottom: 10px;">
                            <div style="font-weight: bold; margin-bottom: 5px; font-size: 12px; color: #666;">
                                Lines ({{ $poNumber->lines->count() }})
                            </div>
                            @foreach($poNumber->lines as $line)
                                <div style="border: 1px solid #e5e7eb; margin-bottom: 8px; padding: 6px; background: #fafafa;">
                                    <div style="font-weight: bold; margin-bottom: 3px; font-size: 11px;">
                                        Line {{ $line->line_number }}
                                        @if($line->hasVariance())
                                            <span style="background: #fef2f2; color: #dc2626; padding: 1px 4px; border-radius: 2px; font-size: 9px;">Variance</span>
                                        @endif
                                    </div>
                                    
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 10px;">
                                        <div>
                                            <div style="margin-bottom: 2px;">
                                                <span style="font-weight: bold; color: #555;">Cases:</span>
                                                @if($line->expected_cases || $line->actual_cases)
                                                    <div>
                                                        @if($line->expected_cases)
                                                            Expected: {{ number_format($line->expected_cases) }}
                                                        @endif
                                                        @if($line->actual_cases)
                                                            @if($line->expected_cases) → @endif
                                                            Actual: {{ number_format($line->actual_cases) }}
                                                            @if($line->expected_cases && $line->case_variance != 0)
                                                                <span style="font-weight: bold; color: {{ $line->case_variance > 0 ? '#059669' : '#dc2626' }};">
                                                                    ({{ $line->case_variance > 0 ? '+' : '' }}{{ $line->case_variance }})
                                                                </span>
                                                            @endif
                                                        @endif
                                                    </div>
                                                @else
                                                    Not specified
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <div style="margin-bottom: 2px;">
                                                <span style="font-weight: bold; color: #555;">Pallets:</span>
                                                @if($line->expected_pallets || $line->actual_pallets)
                                                    <div>
                                                        @if($line->expected_pallets)
                                                            Expected: {{ number_format($line->expected_pallets) }}
                                                            @if($line->expectedPalletType) ({{ $line->expectedPalletType->name }})@endif
                                                        @endif
                                                        @if($line->actual_pallets)
                                                            @if($line->expected_pallets) → @endif
                                                            Actual: {{ number_format($line->actual_pallets) }}
                                                            @if($line->actualPalletType) ({{ $line->actualPalletType->name }})@endif
                                                            @if($line->expected_pallets && $line->pallet_variance != 0)
                                                                <span style="font-weight: bold; color: {{ $line->pallet_variance > 0 ? '#059669' : '#dc2626' }};">
                                                                    ({{ $line->pallet_variance > 0 ? '+' : '' }}{{ $line->pallet_variance }})
                                                                </span>
                                                            @endif
                                                        @endif
                                                        @if($line->pallet_type_variance)
                                                            <div style="color: #dc2626; font-size: 9px; margin-top: 1px;">
                                                                Type Variance: {{ $line->pallet_type_variance }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    Not specified
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="text-align: center; padding: 10px; color: #666; font-style: italic; font-size: 11px;">
                            No lines defined for this PO
                        </div>
                    @endif
                </div>
            @endforeach
            
            @if($booking->poNumbers->count() > 1)
                <div style="border-top: 2px solid #333; padding-top: 8px; margin-top: 15px;">
                    <div style="font-weight: bold; margin-bottom: 5px;">Summary Totals</div>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="label">Total Cases:</span>
                            <div class="load-comparison" style="margin-left: 0;">
                                @if($booking->total_expected_cases > 0)
                                    <span>Expected: {{ number_format($booking->total_expected_cases) }}</span>
                                @endif
                                @if($booking->total_actual_cases > 0)
                                    @if($booking->total_expected_cases > 0) → @endif
                                    <span>Actual: {{ number_format($booking->total_actual_cases) }}</span>
                                    @if($booking->total_expected_cases > 0 && $booking->total_case_variance != 0)
                                        <span class="variance {{ $booking->total_case_variance > 0 ? 'positive' : 'negative' }}">
                                            ({{ $booking->total_case_variance > 0 ? '+' : '' }}{{ $booking->total_case_variance }})
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <span class="label">Total Pallets:</span>
                            <div class="load-comparison" style="margin-left: 0;">
                                @if($booking->total_expected_pallets > 0)
                                    <span>Expected: {{ number_format($booking->total_expected_pallets) }}</span>
                                @endif
                                @if($booking->total_actual_pallets > 0)
                                    @if($booking->total_expected_pallets > 0) → @endif
                                    <span>Actual: {{ number_format($booking->total_actual_pallets) }}</span>
                                    @if($booking->total_expected_pallets > 0 && $booking->total_pallet_variance != 0)
                                        <span class="variance {{ $booking->total_pallet_variance > 0 ? 'positive' : 'negative' }}">
                                            ({{ $booking->total_pallet_variance > 0 ? '+' : '' }}{{ $booking->total_pallet_variance }})
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div style="text-align: center; padding: 20px; color: #666;">
                No PO numbers recorded for this booking
            </div>
        @endif
        
        @if($booking->container_size)
            <div class="info-item">
                <span class="label">Container Size:</span>
                <span class="value">{{ number_format($booking->container_size) }} kg</span>
            </div>
        @endif
        
        @if($booking->load_type)
            <div class="info-item">
                <span class="label">Load Type:</span>
                <span class="value">{{ $booking->load_type }}</span>
            </div>
        @endif
        
        @if($booking->hazmat)
            <div class="info-item">
                <span class="label">Special Requirements:</span>
                <span class="value" style="color: #dc2626; font-weight: bold;"><span class="emoji">⚠️</span>Hazardous Materials (HAZMAT)</span>
            </div>
        @endif
        
        @if($booking->temperature_requirements)
            <div class="info-item">
                <span class="label">Temperature:</span>
                <span class="value">{{ $booking->temperature_requirements }}</span>
            </div>
        @endif
    </div>

    @if($booking->vehicle_registration || $booking->container_number || $booking->carrier_company || $booking->trailerType)
        <div class="section">
            <div class="section-title"><span class="emoji">🚛</span>Transportation & Vehicle Details</div>
            
            <div class="info-grid">
                <div>
                    <div style="font-weight: bold; margin-bottom: 5px; color: #333;">Vehicle Information</div>
                    
                    @if($booking->vehicle_registration)
                        <div class="info-item">
                            <span class="label">Vehicle Registration:</span>
                            <span class="value" style="font-family: monospace; background: #f3f4f6; padding: 1px 4px;">{{ $booking->vehicle_registration }}</span>
                        </div>
                    @endif
                    
                    @if($booking->carrier_company)
                        <div class="info-item">
                            <span class="label">Carrier Company:</span>
                            <span class="value">{{ $booking->carrier_company }}</span>
                        </div>
                    @endif
                    
                    @if($booking->carrier_contact)
                        <div class="info-item">
                            <span class="label">Carrier Contact:</span>
                            <span class="value">{{ $booking->carrier_contact }}</span>
                        </div>
                    @endif
                </div>
                
                <div>
                    <div style="font-weight: bold; margin-bottom: 5px; color: #333;">Container/Trailer Details</div>
                    
                    @if($booking->container_number)
                        <div class="info-item">
                            <span class="label">Container/Trailer Number:</span>
                            <span class="value" style="font-family: monospace; background: #f3f4f6; padding: 1px 4px;">{{ $booking->container_number }}</span>
                        </div>
                    @endif
                    
                    @if($booking->trailerType)
                        <div class="info-item">
                            <span class="label">Trailer Type:</span>
                            <span class="value">{{ $booking->trailerType->name }}</span>
                        </div>
                    @endif
                    
                    @if($booking->container_size)
                        <div class="info-item">
                            <span class="label">Container Size:</span>
                            <span class="value">{{ $booking->container_size }}ft</span>
                        </div>
                    @endif
                </div>
            </div>
            
            @if($booking->gate_number || $booking->manifest_number || $booking->estimated_arrival)
                <div style="border-top: 1px solid #ccc; margin-top: 10px; padding-top: 10px;">
                    <div style="font-weight: bold; margin-bottom: 5px; color: #333;">Additional Transportation Info</div>
                    
                    @if($booking->gate_number)
                        <div class="info-item">
                            <span class="label">Gate Number:</span>
                            <span class="value">{{ $booking->gate_number }}</span>
                        </div>
                    @endif
                    
                    @if($booking->manifest_number)
                        <div class="info-item">
                            <span class="label">Manifest Number:</span>
                            <span class="value" style="font-family: monospace;">{{ $booking->manifest_number }}</span>
                        </div>
                    @endif
                    
                    @if($booking->estimated_arrival)
                        <div class="info-item">
                            <span class="label">Estimated Arrival:</span>
                            <span class="value">{{ $booking->estimated_arrival->format('d M Y, H:i') }}</span>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @endif

    @if($booking->special_instructions || $booking->notes)
        <div class="section">
            <div class="section-title"><span class="emoji">📝</span>Additional Information</div>
            
            @if($booking->special_instructions)
                <div class="info-item">
                    <span class="label">Special Instructions:</span>
                    <div class="value">{{ $booking->special_instructions }}</div>
                </div>
            @endif
            
            @if($booking->notes)
                <div class="info-item">
                    <span class="label">Notes:</span>
                    <div class="value">{{ $booking->notes }}</div>
                </div>
            @endif
        </div>
    @endif

    @if($hasArrived)
        <div class="section">
            <div class="section-title"><span class="emoji">✅</span>Arrival Information</div>
            
            <div class="info-item">
                <span class="label">Arrived At:</span>
                <span class="value">{{ $booking->arrived_at->format('l, d F Y - H:i') }}</span>
            </div>
            
            @if($booking->departed_at)
                <div class="info-item">
                    <span class="label">Departed At:</span>
                    <span class="value">{{ $booking->departed_at->format('l, d F Y - H:i') }}</span>
                </div>
                
                <div class="info-item">
                    <span class="label">Time On-Site:</span>
                    <span class="value">{{ $booking->arrived_at->diffForHumans($booking->departed_at, true) }}</span>
                </div>
            @else
                <div class="info-item">
                    <span class="label">Status:</span>
                    <span class="value" style="color: #2563eb; font-weight: bold;"><span class="emoji">🚛</span>Currently on-site</span>
                </div>
            @endif
        </div>
    @endif
</body>
</html>