<?php if (isset($component)) { $__componentOriginalc9242005886028143da563f7b99f0c87 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc9242005886028143da563f7b99f0c87 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.warehouse-layout','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('warehouse-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">📋 Tipping System User Guide</h2>
                <p class="text-sm text-gray-600 mt-1">How to use the trailer tipping management system</p>
            </div>
        </div>
     <?php $__env->endSlot(); ?>
    <div class="py-6 max-w-4xl mx-auto space-y-6">
        
        <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
            <h3 class="text-lg font-semibold text-blue-800 mb-3">🚛 System Overview</h3>
            <p class="text-blue-700">
                The tipping system tracks trailers from arrival through departure, handling both drop-and-go scenarios 
                (where the delivery vehicle leaves and a different vehicle collects) and standard operations.
            </p>
        </div>
        
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800">📝 Step-by-Step Process</h3>
            </div>
            <div class="divide-y divide-gray-200">
                
                <div class="p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold text-sm">1</div>
                        <div class="ml-4">
                            <h4 class="font-medium text-gray-800">Vehicle Arrival</h4>
                            <p class="text-gray-600 mt-1">When a vehicle arrives:</p>
                            <ul class="mt-2 text-sm text-gray-700 list-disc list-inside space-y-1">
                                <li>Go to the booking and click "Mark as Arrived"</li>
                                <li>Enter <strong>vehicle registration</strong> (required)</li>
                                <li>Enter <strong>container/trailer number</strong> and <strong>carrier company</strong></li>
                                <li>Enter <strong>gate number</strong> if applicable</li>
                                <li><strong>Choose trailer destination:</strong>
                                    <ul class="ml-4 mt-1 list-disc list-inside">
                                        <li><strong>Drop Location:</strong> For staged tipping (trailer waits in parking area)</li>
                                        <li><strong>Direct to Bay:</strong> Skip parking area, go straight to tipping bay</li>
                                    </ul>
                                </li>
                                <li>Click "Mark Arrived"</li>
                            </ul>
                            <div class="mt-2 p-3 bg-green-50 border border-green-200 rounded text-sm">
                                <strong>✨ Auto-Magic:</strong> System automatically starts tipping workflow based on your choice!
                                <br>• Drop Location → Status: "Trailer Dropped"
                                <br>• Direct to Bay → Status: "In Bay" (ready to start tipping)
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-8 h-8 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center font-bold text-sm">2</div>
                        <div class="ml-4">
                            <h4 class="font-medium text-gray-800">Move to Tipping Bay</h4>
                            <p class="text-gray-600 mt-1">Once trailer is dropped:</p>
                            <ul class="mt-2 text-sm text-gray-700 list-disc list-inside space-y-1">
                                <li>Use the tipping workflow to move trailer from parking area to bay</li>
                                <li>Or assign a bay directly during booking creation/editing</li>
                                <li>System tracks the location and timing automatically</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center font-bold text-sm">3</div>
                        <div class="ml-4">
                            <h4 class="font-medium text-gray-800">Start Tipping</h4>
                            <p class="text-gray-600 mt-1">When ready to begin tipping:</p>
                            <ul class="mt-2 text-sm text-gray-700 list-disc list-inside space-y-1">
                                <li>In the booking view, click the <strong>"▶️ Start Tipping"</strong> button</li>
                                <li>System records start time and operator</li>
                                <li>Status changes to "Tipping in Progress"</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-8 h-8 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center font-bold text-sm">4</div>
                        <div class="ml-4">
                            <h4 class="font-medium text-gray-800">Complete Tipping</h4>
                            <p class="text-gray-600 mt-1">When tipping is finished:</p>
                            <ul class="mt-2 text-sm text-gray-700 list-disc list-inside space-y-1">
                                <li>Click the <strong>"✅ Complete Tipping"</strong> button</li>
                                <li>System calculates duration automatically</li>
                                <li>Status changes to "Tipping Completed"</li>
                                <li>Trailer is now ready for collection</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-8 h-8 bg-gray-100 text-gray-600 rounded-full flex items-center justify-center font-bold text-sm">5</div>
                        <div class="ml-4">
                            <h4 class="font-medium text-gray-800">Trailer Collection</h4>
                            <p class="text-gray-600 mt-1">When collection vehicle arrives:</p>
                            <ul class="mt-2 text-sm text-gray-700 list-disc list-inside space-y-1">
                                <li>Go to <strong>🏗️ Dropped Trailers</strong> menu</li>
                                <li>Find the completed trailer and click <strong>"🔗 Reconnect"</strong></li>
                                <li>Enter collection vehicle registration and driver details</li>
                                <li>Add departure notes if needed</li>
                                <li>Click "Reconnect & Depart"</li>
                            </ul>
                            <div class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded text-sm">
                                <strong>📋 Note:</strong> Collection vehicle can be completely different from delivery vehicle
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800">🌟 Key Features</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0 text-green-500 text-xl mr-3">✅</div>
                    <div>
                        <strong>Separate Vehicle Tracking:</strong> System tracks both delivery and collection vehicles independently
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="flex-shrink-0 text-blue-500 text-xl mr-3">📍</div>
                    <div>
                        <strong>Location Tracking:</strong> Always know where trailers are on-site (parking areas, specific bays)
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="flex-shrink-0 text-orange-500 text-xl mr-3">⏱️</div>
                    <div>
                        <strong>Time Tracking:</strong> Automatic duration calculation for each stage of the process
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="flex-shrink-0 text-purple-500 text-xl mr-3">🔄</div>
                    <div>
                        <strong>Bay Transfers:</strong> Move trailers between bays if needed for operational efficiency
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="flex-shrink-0 text-red-500 text-xl mr-3">📊</div>
                    <div>
                        <strong>Overview Dashboard:</strong> See all dropped trailers at a glance with filter options
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-50 p-6 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">🔍 Quick Reference</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Status Meanings:</h4>
                    <ul class="space-y-1 text-gray-600">
                        <li><strong>Trailer Dropped:</strong> In parking area, awaiting bay</li>
                        <li><strong>In Bay:</strong> Moved to tipping bay, ready to start</li>
                        <li><strong>Tipping in Progress:</strong> Currently being tipped</li>
                        <li><strong>Tipping Complete:</strong> Ready for collection</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Key Menu Items:</h4>
                    <ul class="space-y-1 text-gray-600">
                        <li><strong>📊 Dashboard:</strong> Tipping workflow overview</li>
                        <li><strong>📍 Drop Locations:</strong> Manage parking areas</li>
                        <li><strong>🚛 Tipping Bays:</strong> Manage tipping bays</li>
                        <li><strong>🏗️ Dropped Trailers:</strong> See all on-site trailers</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800">💡 Common Scenarios</h3>
            </div>
            <div class="divide-y divide-gray-200">
                <div class="p-6">
                    <h4 class="font-medium text-gray-800 mb-2">🚛 Drop & Go (Different vehicles)</h4>
                    <p class="text-gray-600 text-sm">
                        Delivery truck drops trailer and leaves. Later, a different vehicle comes to collect:
                        <br><strong>Solution:</strong> Use arrival form to assign drop location, then use "Reconnect" feature for collection vehicle.
                    </p>
                </div>
                <div class="p-6">
                    <h4 class="font-medium text-gray-800 mb-2">🔄 Bay Changes</h4>
                    <p class="text-gray-600 text-sm">
                        Need to move trailer to a different bay due to equipment issues:
                        <br><strong>Solution:</strong> Use "Transfer Bay" button in booking view when trailer is assigned to a bay.
                    </p>
                </div>
                <div class="p-6">
                    <h4 class="font-medium text-gray-800 mb-2">⏰ Long-term Storage</h4>
                    <p class="text-gray-600 text-sm">
                        Trailer dropped but tipping won't happen for hours/days:
                        <br><strong>Solution:</strong> System tracks time on-site automatically. Use "Dropped Trailers" view to monitor.
                    </p>
                </div>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc9242005886028143da563f7b99f0c87)): ?>
<?php $attributes = $__attributesOriginalc9242005886028143da563f7b99f0c87; ?>
<?php unset($__attributesOriginalc9242005886028143da563f7b99f0c87); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc9242005886028143da563f7b99f0c87)): ?>
<?php $component = $__componentOriginalc9242005886028143da563f7b99f0c87; ?>
<?php unset($__componentOriginalc9242005886028143da563f7b99f0c87); ?>
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/warehouse/tipping-guide.blade.php ENDPATH**/ ?>