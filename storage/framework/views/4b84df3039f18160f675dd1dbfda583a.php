<?php $__env->startSection('title', 'Edit Load ' . $load->load_reference); ?>

<?php $__env->startSection('content'); ?>
<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-4">
                <a href="<?php echo e(route('outbound.loads.show', $load)); ?>" 
                   class="text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Load <?php echo e($load->load_reference); ?></h1>
                    <p class="text-gray-600 mt-1">Update load information and assignments</p>
                </div>
            </div>
        </div>

        <form action="<?php echo e(route('outbound.loads.update', $load)); ?>" method="POST" class="space-y-8">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Basic Information</h2>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="load_reference" class="block text-sm font-medium text-gray-700 mb-2">
                                Load Reference
                            </label>
                            <input type="text" name="load_reference" id="load_reference" 
                                   value="<?php echo e(old('load_reference', $load->load_reference)); ?>"
                                   class="form-input w-full rounded-md bg-gray-100 <?php $__errorArgs = ['load_reference'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   readonly>
                            <?php $__errorArgs = ['load_reference'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <p class="text-sm text-gray-500 mt-1">Load reference cannot be changed</p>
                        </div>

                        <div>
                            <label for="load_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Load Name (Optional)
                            </label>
                            <input type="text" name="load_name" id="load_name" 
                                   value="<?php echo e(old('load_name', $load->load_name)); ?>"
                                   class="form-input w-full rounded-md <?php $__errorArgs = ['load_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   placeholder="e.g., North Route - Monday">
                            <?php $__errorArgs = ['load_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Notes
                        </label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="form-textarea w-full rounded-md <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                  placeholder="Any special instructions or notes for this load"><?php echo e(old('notes', $load->notes)); ?></textarea>
                        <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>

            <!-- Vehicle Assignment -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Vehicle Assignment</h2>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="planned_vehicle_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Vehicle
                            </label>
                            <select name="planned_vehicle_id" id="planned_vehicle_id" 
                                    class="form-select w-full rounded-md <?php $__errorArgs = ['planned_vehicle_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="">Select Vehicle (Optional)</option>
                                <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($vehicle->id); ?>" 
                                            <?php echo e(old('planned_vehicle_id', $load->planned_vehicle_id) == $vehicle->id ? 'selected' : ''); ?>>
                                        <?php echo e($vehicle->registration); ?> - <?php echo e($vehicle->make); ?> <?php echo e($vehicle->model); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['planned_vehicle_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label for="assigned_driver_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Driver
                            </label>
                            <select name="assigned_driver_id" id="assigned_driver_id" 
                                    class="form-select w-full rounded-md <?php $__errorArgs = ['assigned_driver_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="">Select Driver (Optional)</option>
                                <?php $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($driver->id); ?>" 
                                            <?php echo e(old('assigned_driver_id', $load->assigned_driver_id) == $driver->id ? 'selected' : ''); ?>>
                                        <?php echo e($driver->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['assigned_driver_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Load Status -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Load Status</h2>
                </div>
                <div class="p-6">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status
                        </label>
                        <select name="status" id="status" 
                                class="form-select w-full rounded-md <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="planning" <?php echo e(old('status', $load->status) === 'planning' ? 'selected' : ''); ?>>
                                Planning
                            </option>
                            <option value="ready_for_collection" <?php echo e(old('status', $load->status) === 'ready_for_collection' ? 'selected' : ''); ?>>
                                Ready for Collection
                            </option>
                            <option value="collecting" <?php echo e(old('status', $load->status) === 'collecting' ? 'selected' : ''); ?>>
                                Collecting
                            </option>
                            <option value="in_transit" <?php echo e(old('status', $load->status) === 'in_transit' ? 'selected' : ''); ?>>
                                In Transit
                            </option>
                            <option value="delivering" <?php echo e(old('status', $load->status) === 'delivering' ? 'selected' : ''); ?>>
                                Delivering
                            </option>
                            <option value="completed" <?php echo e(old('status', $load->status) === 'completed' ? 'selected' : ''); ?>>
                                Completed
                            </option>
                            <option value="cancelled" <?php echo e(old('status', $load->status) === 'cancelled' ? 'selected' : ''); ?>>
                                Cancelled
                            </option>
                        </select>
                        <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <p class="text-sm text-gray-500 mt-1">
                            Current status: <strong><?php echo e(ucfirst(str_replace('_', ' ', $load->status))); ?></strong>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Route Optimization (if applicable) -->
            <?php if($load->optimized_distance_km || $load->estimated_duration_minutes): ?>
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Route Information</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="optimized_distance_km" class="block text-sm font-medium text-gray-700 mb-2">
                                Optimized Distance (km)
                            </label>
                            <input type="number" step="0.01" name="optimized_distance_km" id="optimized_distance_km" 
                                   value="<?php echo e(old('optimized_distance_km', $load->optimized_distance_km)); ?>"
                                   class="form-input w-full rounded-md <?php $__errorArgs = ['optimized_distance_km'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <?php $__errorArgs = ['optimized_distance_km'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label for="estimated_duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                                Estimated Duration (minutes)
                            </label>
                            <input type="number" name="estimated_duration_minutes" id="estimated_duration_minutes" 
                                   value="<?php echo e(old('estimated_duration_minutes', $load->estimated_duration_minutes)); ?>"
                                   class="form-input w-full rounded-md <?php $__errorArgs = ['estimated_duration_minutes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <?php $__errorArgs = ['estimated_duration_minutes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label for="optimization_score" class="block text-sm font-medium text-gray-700 mb-2">
                                Optimization Score
                            </label>
                            <input type="number" step="0.01" name="optimization_score" id="optimization_score" 
                                   value="<?php echo e(old('optimization_score', $load->optimization_score)); ?>"
                                   class="form-input w-full rounded-md <?php $__errorArgs = ['optimization_score'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <?php $__errorArgs = ['optimization_score'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Load Statistics (Read-only) -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Load Statistics</h2>
                    <p class="text-sm text-gray-600">These values are automatically calculated from orders and collections</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Total Orders</label>
                            <div class="text-2xl font-bold text-blue-600"><?php echo e($load->total_orders); ?></div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Total Customers</label>
                            <div class="text-2xl font-bold text-green-600"><?php echo e($load->total_customers); ?></div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Collection Points</label>
                            <div class="text-2xl font-bold text-orange-600"><?php echo e($load->total_collection_points); ?></div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Delivery Points</label>
                            <div class="text-2xl font-bold text-purple-600"><?php echo e($load->total_delivery_points); ?></div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Total Pallets</label>
                            <div class="text-lg font-semibold text-gray-900"><?php echo e($load->total_pallets); ?></div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Total Cases</label>
                            <div class="text-lg font-semibold text-gray-900"><?php echo e($load->total_cases); ?></div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Total Units</label>
                            <div class="text-lg font-semibold text-gray-900"><?php echo e($load->total_units); ?></div>
                        </div>
                        <?php if($load->total_weight_kg): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Total Weight</label>
                            <div class="text-lg font-semibold text-gray-900"><?php echo e($load->total_weight_kg); ?>kg</div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-between">
                <div>
                    <?php if($load->status === 'planning'): ?>
                        <form method="POST" action="<?php echo e(route('outbound.loads.destroy', $load)); ?>" 
                              class="inline" onsubmit="return confirm('Are you sure you want to delete this load? This action cannot be undone.')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" 
                                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-md font-medium">
                                Delete Load
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
                <div class="flex space-x-4">
                    <a href="<?php echo e(route('outbound.loads.show', $load)); ?>" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-md font-medium">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium">
                        Update Load
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/outbound/admin/loads/edit.blade.php ENDPATH**/ ?>