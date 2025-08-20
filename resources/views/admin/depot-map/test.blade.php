<x-app-layout>
    @include('layouts.admin-nav')

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">🗺️ Wimblington Depot Map - Test View</h2>
                <p class="text-sm text-gray-600 mt-1">Interactive site layout with real-time shed status</p>
            </div>
            <div class="flex items-center space-x-3">
                <button onclick="refreshMapStatus()" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                    🔄 Refresh Status
                </button>
                <a href="{{ route('admin.bookings.index') }}" class="px-3 py-1 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 text-sm">
                    ← Back to Bookings
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-full mx-auto px-4">
        
        <!-- Status Legend -->
        <div class="mb-6 bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-semibold mb-3">📊 Current Site Status</h3>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 text-sm">
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 bg-green-500 rounded"></div>
                    <span>Available (<span id="available-count">0</span>)</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 bg-red-500 rounded"></div>
                    <span>Active Tipping (<span id="active-count">0</span>)</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 bg-orange-500 rounded"></div>
                    <span>Awaiting Collection (<span id="waiting-count">0</span>)</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 bg-blue-500 rounded"></div>
                    <span>Scheduled (<span id="scheduled-count">0</span>)</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 bg-gray-500 rounded"></div>
                    <span>Out of Service (<span id="offline-count">0</span>)</span>
                </div>
            </div>
        </div>

        <!-- Main Map Container -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold">🏭 Wimblington Depot Layout</h3>
                <p class="text-sm text-gray-600">Click on any shed or bay for details</p>
            </div>
            
            <div class="p-6">
                <!-- Map Container with Responsive Sizing -->
                <div class="relative bg-gray-100 rounded-lg border-2 border-gray-300" style="min-height: 600px;">
                    
                    <!-- Depot Map Image -->
                    <div class="absolute inset-0 flex items-center justify-center">
                        <img src="{{ asset('images/depot-maps/Wimblington.png') }}" 
                             alt="Wimblington Depot Layout" 
                             class="max-w-full max-h-full object-contain rounded-lg shadow-lg"
                             id="depot-map-image">
                    </div>
                    
                    <!-- Interactive Overlay Areas (positioned over your sheds) -->
                    <!-- These will be positioned based on your actual map layout -->
                    
                    <!-- Sample Shed Overlays - You'll need to adjust positions based on your actual map -->
                    <div class="absolute" style="top: 20%; left: 15%;">
                        <div class="shed-overlay" data-shed="01" onclick="showShedDetails('01')" 
                             style="width: 60px; height: 40px; background: rgba(34, 197, 94, 0.7); border: 2px solid #16a34a; border-radius: 4px; cursor: pointer;"
                             title="Shed 01">
                            <div class="text-xs font-bold text-white text-center pt-2">S01</div>
                        </div>
                    </div>
                    
                    <div class="absolute" style="top: 20%; left: 25%;">
                        <div class="shed-overlay" data-shed="02" onclick="showShedDetails('02')" 
                             style="width: 60px; height: 40px; background: rgba(239, 68, 68, 0.7); border: 2px solid #dc2626; border-radius: 4px; cursor: pointer;"
                             title="Shed 02 - Currently Active">
                            <div class="text-xs font-bold text-white text-center pt-2">S02</div>
                        </div>
                    </div>
                    
                    <div class="absolute" style="top: 20%; left: 35%;">
                        <div class="shed-overlay" data-shed="03" onclick="showShedDetails('03')" 
                             style="width: 60px; height: 40px; background: rgba(249, 115, 22, 0.7); border: 2px solid #ea580c; border-radius: 4px; cursor: pointer;"
                             title="Shed 03 - Awaiting Collection">
                            <div class="text-xs font-bold text-white text-center pt-2">S03</div>
                        </div>
                    </div>
                    
                    <!-- Add more shed overlays as needed based on your map -->
                    
                    <!-- Map Controls -->
                    <div class="absolute top-4 right-4 space-y-2">
                        <button onclick="zoomIn()" class="bg-white shadow-lg rounded p-2 hover:bg-gray-50">
                            🔍+
                        </button>
                        <button onclick="zoomOut()" class="bg-white shadow-lg rounded p-2 hover:bg-gray-50">
                            🔍-
                        </button>
                        <button onclick="resetZoom()" class="bg-white shadow-lg rounded p-2 hover:bg-gray-50">
                            🏠
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Panel -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Current Activity -->
            <div class="bg-white rounded-lg shadow p-4">
                <h4 class="font-semibold text-gray-800 mb-3">🚛 Current Activity</h4>
                <div id="current-activity" class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span>Active Bookings:</span>
                        <span class="font-semibold text-red-600">2</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Available Sheds:</span>
                        <span class="font-semibold text-green-600">12</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Pending Collection:</span>
                        <span class="font-semibold text-orange-600">3</span>
                    </div>
                </div>
            </div>
            
            <!-- Recent Updates -->
            <div class="bg-white rounded-lg shadow p-4">
                <h4 class="font-semibold text-gray-800 mb-3">📝 Recent Updates</h4>
                <div class="space-y-2 text-sm">
                    <div class="text-gray-600">
                        <span class="font-medium">Shed 02:</span> Tipping started - Booking #45
                    </div>
                    <div class="text-gray-600">
                        <span class="font-medium">Shed 07:</span> Collection completed - ABC123
                    </div>
                    <div class="text-gray-600">
                        <span class="font-medium">Shed 03:</span> Unit departed - Awaiting collection
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow p-4">
                <h4 class="font-semibold text-gray-800 mb-3">⚡ Quick Actions</h4>
                <div class="space-y-2">
                    <button onclick="showAllBookings()" class="w-full px-3 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                        📋 View All Bookings
                    </button>
                    <button onclick="openTrailerCollection()" class="w-full px-3 py-2 bg-green-600 text-white rounded text-sm hover:bg-green-700">
                        🚛 Record Collection
                    </button>
                    <button onclick="generateReport()" class="w-full px-3 py-2 bg-gray-600 text-white rounded text-sm hover:bg-gray-700">
                        📊 Generate Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Shed Details Modal -->
    <div id="shed-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 shadow-xl">
            <div class="flex justify-between items-center mb-4">
                <h3 id="modal-title" class="text-lg font-semibold">Shed Details</h3>
                <button onclick="closeShedModal()" class="text-gray-400 hover:text-gray-600">
                    ✕
                </button>
            </div>
            <div id="modal-content" class="space-y-3">
                <!-- Content will be populated by JavaScript -->
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button onclick="closeShedModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                    Close
                </button>
                <button id="modal-action-btn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    View Booking
                </button>
            </div>
        </div>
    </div>

    <script>
        // Sample shed data - replace with real data from your API
        const shedData = {
            '01': { name: 'Shed 01', status: 'available', booking: null, capacity: '500t' },
            '02': { name: 'Shed 02', status: 'active', booking: 'BK-045', vehicle: 'AB12 CDE', capacity: '500t' },
            '03': { name: 'Shed 03', status: 'waiting', booking: 'BK-033', vehicle: 'XY98 ZAB', capacity: '500t' },
            // Add more sheds...
        };

        // Update status counts
        function updateStatusCounts() {
            const counts = { available: 0, active: 0, waiting: 0, scheduled: 0, offline: 0 };
            
            Object.values(shedData).forEach(shed => {
                if (shed.status in counts) {
                    counts[shed.status]++;
                }
            });
            
            document.getElementById('available-count').textContent = counts.available;
            document.getElementById('active-count').textContent = counts.active;
            document.getElementById('waiting-count').textContent = counts.waiting;
            document.getElementById('scheduled-count').textContent = counts.scheduled;
            document.getElementById('offline-count').textContent = counts.offline;
        }

        // Show shed details in modal
        function showShedDetails(shedId) {
            const shed = shedData[shedId];
            if (!shed) return;
            
            document.getElementById('modal-title').textContent = shed.name;
            
            let content = `
                <div class="space-y-2">
                    <div><strong>Status:</strong> ${shed.status.charAt(0).toUpperCase() + shed.status.slice(1)}</div>
                    <div><strong>Capacity:</strong> ${shed.capacity}</div>
            `;
            
            if (shed.booking) {
                content += `
                    <div><strong>Current Booking:</strong> ${shed.booking}</div>
                    <div><strong>Vehicle:</strong> ${shed.vehicle}</div>
                `;
            }
            
            content += '</div>';
            
            document.getElementById('modal-content').innerHTML = content;
            document.getElementById('shed-modal').classList.remove('hidden');
            document.getElementById('shed-modal').classList.add('flex');
        }

        // Close modal
        function closeShedModal() {
            document.getElementById('shed-modal').classList.add('hidden');
            document.getElementById('shed-modal').classList.remove('flex');
        }

        // Map controls
        let currentZoom = 1;
        
        function zoomIn() {
            currentZoom = Math.min(currentZoom * 1.2, 3);
            applyZoom();
        }
        
        function zoomOut() {
            currentZoom = Math.max(currentZoom / 1.2, 0.5);
            applyZoom();
        }
        
        function resetZoom() {
            currentZoom = 1;
            applyZoom();
        }
        
        function applyZoom() {
            const mapImage = document.getElementById('depot-map-image');
            mapImage.style.transform = `scale(${currentZoom})`;
        }

        // Refresh map status
        function refreshMapStatus() {
            // In real implementation, this would fetch from your API
            console.log('Refreshing map status...');
            // updateShedColors();
            updateStatusCounts();
        }

        // Quick actions
        function showAllBookings() {
            window.location.href = '{{ route("admin.bookings.index") }}';
        }
        
        function openTrailerCollection() {
            window.open('{{ route("admin.empty-unit-collection") }}', '_blank', 'width=1200,height=800');
        }
        
        function generateReport() {
            alert('Report generation feature coming soon!');
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateStatusCounts();
        });
    </script>

    <style>
        .shed-overlay:hover {
            transform: scale(1.1);
            transition: transform 0.2s ease;
        }
        
        #depot-map-image {
            transition: transform 0.3s ease;
        }
    </style>
</x-app-layout>