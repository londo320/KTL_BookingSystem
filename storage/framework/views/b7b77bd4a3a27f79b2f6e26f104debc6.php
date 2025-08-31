<?php $__env->startSection('title', 'Customer Addresses'); ?>

<?php $__env->startSection('content'); ?>
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Customer Addresses</h1>
                    <p class="text-gray-600 mt-1">Manage delivery addresses and constraints</p>
                </div>
                <a href="<?php echo e(route('outbound.addresses.create')); ?>" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">
                    New Address
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="p-6">
                <form method="GET" action="<?php echo e(route('outbound.addresses.index')); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                        <select name="customer" class="form-select w-full rounded-md">
                            <option value="">All Customers</option>
                            <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($customer->id); ?>" <?php echo e(request('customer') == $customer->id ? 'selected' : ''); ?>>
                                    <?php echo e($customer->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                        <input type="text" name="city" value="<?php echo e(request('city')); ?>" 
                               class="form-input w-full rounded-md" placeholder="Enter city">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Postcode</label>
                        <input type="text" name="postcode" value="<?php echo e(request('postcode')); ?>" 
                               class="form-input w-full rounded-md" placeholder="Enter postcode">
                    </div>
                    <div class="flex items-end space-x-2">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="form-select w-full rounded-md">
                                <option value="">All</option>
                                <option value="active" <?php echo e(request('status') === 'active' ? 'selected' : ''); ?>>Active</option>
                                <option value="inactive" <?php echo e(request('status') === 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                            </select>
                        </div>
                        <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                            Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Addresses Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Customer & Address
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Contact
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Location
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Delivery Constraints
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__empty_1 = true; $__currentLoopData = $addresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-start">
                                        <div class="flex-1">
                                            <div class="text-sm font-medium text-gray-900 flex items-center">
                                                <?php echo e($address->customer->name); ?>

                                                <?php if($address->is_default): ?>
                                                    <span class="ml-2 px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Default</span>
                                                <?php endif; ?>
                                            </div>
                                            <?php if($address->address_name): ?>
                                                <div class="text-sm text-blue-600"><?php echo e($address->address_name); ?></div>
                                            <?php endif; ?>
                                            <?php if($address->company_name): ?>
                                                <div class="text-sm text-gray-600"><?php echo e($address->company_name); ?></div>
                                            <?php endif; ?>
                                            <div class="text-sm text-gray-900 mt-1">
                                                <?php echo e($address->address_line_1); ?>

                                                <?php if($address->address_line_2): ?>
                                                    <br><?php echo e($address->address_line_2); ?>

                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?php if($address->contact_name): ?>
                                            <div class="font-medium"><?php echo e($address->contact_name); ?></div>
                                        <?php endif; ?>
                                        <?php if($address->contact_phone): ?>
                                            <div><?php echo e($address->contact_phone); ?></div>
                                        <?php endif; ?>
                                        <?php if($address->contact_email): ?>
                                            <div class="text-blue-600"><?php echo e($address->contact_email); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <div class="font-medium"><?php echo e($address->city); ?></div>
                                        <div><?php echo e($address->postcode); ?></div>
                                        <?php if($address->county): ?>
                                            <div class="text-gray-500"><?php echo e($address->county); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        <?php
                                            $constraints = [];
                                            if($address->requires_appointment) $constraints[] = 'Appointment';
                                            if($address->requires_signature) $constraints[] = 'Signature';
                                            if($address->requires_photo_proof) $constraints[] = 'Photo proof';
                                            if($address->latest_delivery_time) $constraints[] = 'Latest: ' . $address->latest_delivery_time->format('H:i');
                                        ?>
                                        <?php if(count($constraints) > 0): ?>
                                            <?php $__currentLoopData = $constraints; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $constraint): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <span class="inline-block px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded mr-1 mb-1">
                                                    <?php echo e($constraint); ?>

                                                </span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php else: ?>
                                            <span class="text-gray-500">No constraints</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full <?php echo e($address->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                                        <?php echo e($address->is_active ? 'Active' : 'Inactive'); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="<?php echo e(route('outbound.addresses.show', $address)); ?>" 
                                           class="text-blue-600 hover:text-blue-900">View</a>
                                        <a href="<?php echo e(route('outbound.addresses.edit', $address)); ?>" 
                                           class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        <?php if(!$address->is_default): ?>
                                            <form method="POST" action="<?php echo e(route('outbound.addresses.destroy', $address)); ?>" 
                                                  class="inline" onsubmit="return confirm('Are you sure?')">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <p>No addresses found</p>
                                        <p class="text-sm">Create your first address to get started</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if($addresses->hasPages()): ?>
                <div class="px-6 py-4 border-t border-gray-200">
                    <?php echo e($addresses->appends(request()->query())->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/outbound/admin/addresses/index.blade.php ENDPATH**/ ?>