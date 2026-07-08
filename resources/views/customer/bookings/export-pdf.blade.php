<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>My Bookings Export</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Arial Unicode MS', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 10px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0 0 4px 0;
        }
        .meta {
            color: #666;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 4px 6px;
            text-align: left;
        }
        th {
            background: #f3f4f6;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background: #fafafa;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>My Bookings</h1>
        <div class="meta">
            {{ $customerName }} &middot; Generated {{ $generatedAt->format('d M Y H:i') }} &middot; {{ $totalBookings }} booking{{ $totalBookings === 1 ? '' : 's' }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Booking Ref</th>
                <th>Customer</th>
                <th>Depot</th>
                <th>Start</th>
                <th>End</th>
                <th>Type</th>
                <th>Vehicle Reg</th>
                <th>Container</th>
                <th>Exp. Cases</th>
                <th>Act. Cases</th>
                <th>Exp. Pallets</th>
                <th>Act. Pallets</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
                @php
                    $isFactory = isset($booking->type) && $booking->type === 'factory';

                    $expectedCases = 0;
                    $expectedPallets = 0;
                    $actualCases = 0;
                    $actualPallets = 0;
                    foreach ($booking->poNumbers as $po) {
                        foreach ($po->lines as $line) {
                            $expectedCases += $line->expected_cases ?? 0;
                            $expectedPallets += $line->expected_pallets ?? 0;
                            $actualCases += $line->actual_cases ?? 0;
                            $actualPallets += $line->actual_pallets ?? 0;
                        }
                    }

                    $status = 'Scheduled';
                    if ($booking->cancelled_at) {
                        $status = 'Cancelled';
                    } elseif ($booking->arrived_at) {
                        $status = $booking->departed_at ? 'Completed' : 'On-site';
                    }
                @endphp
                <tr>
                    <td>{{ $booking->booking_reference ?? 'N/A' }}</td>
                    <td>{{ optional($booking->customer)->name ?? '-' }}</td>
                    <td>{{ $booking->slot->depot->name }}</td>
                    <td>{{ $booking->slot->start_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $booking->slot->end_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $isFactory ? 'Factory Delivery' : (optional($booking->bookingType)->name ?? 'N/A') }}</td>
                    <td>{{ $booking->vehicle_registration ?? '-' }}</td>
                    <td>{{ $booking->container_number ?? '-' }}</td>
                    <td>{{ $expectedCases }}</td>
                    <td>{{ $actualCases }}</td>
                    <td>{{ $expectedPallets }}</td>
                    <td>{{ $actualPallets }}</td>
                    <td>{{ $status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" style="text-align: center; color: #999;">No bookings found for the current filters.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
