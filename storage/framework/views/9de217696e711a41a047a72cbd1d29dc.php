<?php $__env->startSection('title', 'Rebook Booking - ' . $booking->booking_reference); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-exchange-alt"></i>
                        Rebook Booking: <?php echo e($booking->booking_reference); ?>

                    </h5>
                </div>
                <div class="card-body">
                    <?php if($restrictions['blocked']): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-ban"></i>
                            <strong>Rebooking Blocked:</strong> <?php echo e($restrictions['blocked']); ?>

                        </div>
                    <?php else: ?>
                        <?php if($restrictions['warning']): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <?php echo e($restrictions['warning']); ?>

                            </div>
                        <?php endif; ?>

                        <!-- Current Booking Details -->
                        <div class="mb-4 p-3 bg-light border-left border-primary">
                            <h6>Current Booking Details</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Customer:</strong> <?php echo e($booking->customer->name); ?><br>
                                    <strong>Current Slot:</strong> <?php echo e($booking->slot->start_at->format('M j, Y g:i A')); ?><br>
                                    <strong>Depot:</strong> <?php echo e($booking->slot->depot->name ?? 'N/A'); ?>

                                </div>
                                <div class="col-md-6">
                                    <strong>Container:</strong> <?php echo e($booking->container_number ?? 'N/A'); ?><br>
                                    <strong>Driver:</strong> <?php echo e($booking->driver_name ?? 'N/A'); ?><br>
                                    <strong>Rebook Count:</strong> <?php echo e($booking->rebook_count); ?>

                                </div>
                            </div>
                        </div>

                        <!-- Rebook Form -->
                        <form action="<?php echo e(route('app.bookings.rebook.store', $booking)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            
                            <div class="form-group">
                                <label for="new_slot_id">New Slot *</label>
                                <select name="new_slot_id" id="new_slot_id" class="form-control <?php $__errorArgs = ['new_slot_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="">Select New Slot</option>
                                    <?php $__empty_1 = true; $__currentLoopData = $availableSlots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <option value="<?php echo e($slot->id); ?>" <?php echo e(old('new_slot_id') == $slot->id ? 'selected' : ''); ?>>
                                            <?php echo e($slot->start_at->format('M j, Y g:i A')); ?> 
                                            (<?php echo e($slot->bookings->count()); ?>/<?php echo e($slot->capacity); ?> booked)
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <option value="">No available slots found</option>
                                    <?php endif; ?>
                                </select>
                                <?php $__errorArgs = ['new_slot_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="form-group">
                                <label for="reason">Reason for Rebooking *</label>
                                <textarea name="reason" id="reason" rows="3" class="form-control <?php $__errorArgs = ['reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Please provide a reason for rebooking..." required><?php echo e(old('reason')); ?></textarea>
                                <?php $__errorArgs = ['reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-exchange-alt"></i> Rebook Booking
                                </button>
                                <a href="<?php echo e(route('app.bookings.show', $booking)); ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Booking History -->
            <?php if($booking->history->count() > 0): ?>
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">Recent History</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <?php $__currentLoopData = $booking->history->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-<?php echo e($history->action === 'created' ? 'success' : ($history->action === 'rebooked' ? 'warning' : 'danger')); ?>"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1"><?php echo e(ucfirst($history->action)); ?></h6>
                                <p class="mb-0 text-muted">
                                    <?php echo e($history->reason ?? 'No reason provided'); ?>

                                </p>
                                <small class="text-muted">
                                    <?php echo e($history->created_at->format('M j, Y g:i A')); ?> by <?php echo e($history->user->name ?? 'System'); ?>

                                </small>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <a href="<?php echo e(route('app.bookings.history', $booking)); ?>" class="btn btn-sm btn-outline-primary">
                        View Complete History
                    </a>
                </div>
            </div>
            <?php endif; ?>
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
                                <h4 class="text-warning"><?php echo e($customerStats['total_rebooks_30days']); ?></h4>
                                <small>Total Rebooks</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-box">
                                <h4 class="text-danger"><?php echo e($customerStats['last_minute_rebooks_30days']); ?></h4>
                                <small>Last Minute</small>
                            </div>
                        </div>
                        <div class="col-6 mt-3">
                            <div class="stat-box">
                                <h4 class="text-info"><?php echo e($customerStats['total_cancellations_30days']); ?></h4>
                                <small>Cancellations</small>
                            </div>
                        </div>
                        <div class="col-6 mt-3">
                            <div class="stat-box">
                                <h4 class="text-success"><?php echo e($customerStats['avg_hours_notice']); ?>h</h4>
                                <small>Avg Notice</small>
                            </div>
                        </div>
                    </div>
                    
                    <a href="<?php echo e(route('app.customer-behavior.show', $booking->customer)); ?>" class="btn btn-sm btn-outline-info btn-block mt-3">
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
            <form action="<?php echo e(route('app.bookings.cancel', $booking)); ?>" method="POST">
                <?php echo csrf_field(); ?>
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/bookings/rebook.blade.php ENDPATH**/ ?>