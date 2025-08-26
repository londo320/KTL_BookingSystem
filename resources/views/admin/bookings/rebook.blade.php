@extends('layouts.admin')

@section('title', 'Rebook Booking - ' . $booking->booking_reference)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-exchange-alt"></i>
                        Rebook Booking: {{ $booking->booking_reference }}
                    </h5>
                </div>
                <div class="card-body">
                    @if($restrictions['blocked'])
                        <div class="alert alert-danger">
                            <i class="fas fa-ban"></i>
                            <strong>Rebooking Blocked:</strong> {{ $restrictions['blocked'] }}
                        </div>
                    @else
                        @if($restrictions['warning'])
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ $restrictions['warning'] }}
                            </div>
                        @endif

                        <!-- Current Booking Details -->
                        <div class="mb-4 p-3 bg-light border-left border-primary">
                            <h6>Current Booking Details</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Customer:</strong> {{ $booking->customer->name }}<br>
                                    <strong>Current Slot:</strong> {{ $booking->slot->start_at->format('M j, Y g:i A') }}<br>
                                    <strong>Depot:</strong> {{ $booking->slot->depot->name ?? 'N/A' }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Container:</strong> {{ $booking->container_number ?? 'N/A' }}<br>
                                    <strong>Driver:</strong> {{ $booking->driver_name ?? 'N/A' }}<br>
                                    <strong>Rebook Count:</strong> {{ $booking->rebook_count }}
                                </div>
                            </div>
                        </div>

                        <!-- Rebook Form -->
                        <form action="{{ route('app.bookings.rebook.store', $booking) }}" method="POST">
                            @csrf
                            
                            <div class="form-group">
                                <label for="new_slot_id">New Slot *</label>
                                <select name="new_slot_id" id="new_slot_id" class="form-control @error('new_slot_id') is-invalid @enderror" required>
                                    <option value="">Select New Slot</option>
                                    @forelse($availableSlots as $slot)
                                        <option value="{{ $slot->id }}" {{ old('new_slot_id') == $slot->id ? 'selected' : '' }}>
                                            {{ $slot->start_at->format('M j, Y g:i A') }} 
                                            ({{ $slot->bookings->count() }}/{{ $slot->capacity }} booked)
                                        </option>
                                    @empty
                                        <option value="">No available slots found</option>
                                    @endforelse
                                </select>
                                @error('new_slot_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="reason">Reason for Rebooking *</label>
                                <textarea name="reason" id="reason" rows="3" class="form-control @error('reason') is-invalid @enderror" placeholder="Please provide a reason for rebooking..." required>{{ old('reason') }}</textarea>
                                @error('reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-exchange-alt"></i> Rebook Booking
                                </button>
                                <a href="{{ route('app.bookings.show', $booking) }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Booking History -->
            @if($booking->history->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">Recent History</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($booking->history->take(5) as $history)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-{{ $history->action === 'created' ? 'success' : ($history->action === 'rebooked' ? 'warning' : 'danger') }}"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">{{ ucfirst($history->action) }}</h6>
                                <p class="mb-0 text-muted">
                                    {{ $history->reason ?? 'No reason provided' }}
                                </p>
                                <small class="text-muted">
                                    {{ $history->created_at->format('M j, Y g:i A') }} by {{ $history->user->name ?? 'System' }}
                                </small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <a href="{{ route('app.bookings.history', $booking) }}" class="btn btn-sm btn-outline-primary">
                        View Complete History
                    </a>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <!-- Customer Behavior Stats -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-line"></i>
                        Customer Behavior (30 days)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="stat-box">
                                <h4 class="text-warning">{{ $customerStats['total_rebooks_30days'] }}</h4>
                                <small>Total Rebooks</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-box">
                                <h4 class="text-danger">{{ $customerStats['last_minute_rebooks_30days'] }}</h4>
                                <small>Last Minute</small>
                            </div>
                        </div>
                        <div class="col-6 mt-3">
                            <div class="stat-box">
                                <h4 class="text-info">{{ $customerStats['total_cancellations_30days'] }}</h4>
                                <small>Cancellations</small>
                            </div>
                        </div>
                        <div class="col-6 mt-3">
                            <div class="stat-box">
                                <h4 class="text-success">{{ $customerStats['avg_hours_notice'] }}h</h4>
                                <small>Avg Notice</small>
                            </div>
                        </div>
                    </div>
                    
                    <a href="{{ route('app.customer-behavior.show', $booking->customer) }}" class="btn btn-sm btn-outline-info btn-block mt-3">
                        <i class="fas fa-analytics"></i> View Customer Analysis
                    </a>
                </div>
            </div>

            <!-- Cancel Booking -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0 text-danger">
                        <i class="fas fa-ban"></i>
                        Cancel Booking
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">If you need to cancel this booking instead of rebooking it.</p>
                    
                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#cancelModal">
                        <i class="fas fa-ban"></i> Cancel Booking
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('app.bookings.cancel', $booking) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Cancel Booking</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="cancellation_reason">Reason for Cancellation *</label>
                        <textarea name="cancellation_reason" id="cancellation_reason" rows="3" class="form-control" placeholder="Please provide a reason for cancellation..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Cancel Booking</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.stat-box {
    padding: 15px;
    text-align: center;
}
.stat-box h4 {
    margin-bottom: 5px;
}
.timeline {
    position: relative;
    padding-left: 30px;
}
.timeline-item {
    position: relative;
    margin-bottom: 20px;
}
.timeline-marker {
    position: absolute;
    left: -35px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}
.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    border-left: 3px solid #007bff;
}
</style>
@endsection