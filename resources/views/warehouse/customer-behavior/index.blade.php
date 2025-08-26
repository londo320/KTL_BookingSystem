

<x-warehouse-layout>
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    📊 Customer Behavior Analysis
                </h1>
                <p class="mt-2 text-gray-600">Track customer booking patterns, rebooks, and cancellations</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('app.customer-behavior.flagged') }}" 
                   class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition-colors">
                    🚩 Flagged Customers
                </a>
                <a href="{{ route('app.customer-behavior.export', ['days' => $days]) }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                    📥 Export CSV
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border mb-8">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">📝 Analysis Filters</h3>
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label for="days" class="block text-sm font-medium text-gray-700 mb-2">Time Period</label>
                    <select name="days" id="days" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="7" {{ $days == 7 ? 'selected' : '' }}>📅 Last 7 days</option>
                        <option value="30" {{ $days == 30 ? 'selected' : '' }}>📅 Last 30 days</option>
                        <option value="90" {{ $days == 90 ? 'selected' : '' }}>📅 Last 90 days</option>
                        <option value="365" {{ $days == 365 ? 'selected' : '' }}>📅 Last year</option>
                    </select>
                </div>
                <div>
                    <label for="sort" class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                    <select name="sort" id="sort" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="rebook_count" {{ $sortBy == 'rebook_count' ? 'selected' : '' }}>🔄 Rebooks</option>
                        <option value="cancellation_count" {{ $sortBy == 'cancellation_count' ? 'selected' : '' }}>❌ Cancellations</option>
                        <option value="last_minute_count" {{ $sortBy == 'last_minute_count' ? 'selected' : '' }}>⏰ Last Minute Actions</option>
                        <option value="risk_score" {{ $sortBy == 'risk_score' ? 'selected' : '' }}>⚠️ Risk Score</option>
                        <option value="name" {{ $sortBy == 'name' ? 'selected' : '' }}>🏢 Customer Name</option>
                    </select>
                </div>
                <div>
                    <label for="direction" class="block text-sm font-medium text-gray-700 mb-2">Order</label>
                    <select name="direction" id="direction" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="desc" {{ $direction == 'desc' ? 'selected' : '' }}>📈 High to Low</option>
                        <option value="asc" {{ $direction == 'asc' ? 'selected' : '' }}>📉 Low to High</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium">
                        🔍 Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Overall Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-8 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-blue-600">{{ $overallStats['total_customers'] }}</div>
            <div class="text-sm text-gray-600 mt-1">👥 Customers</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-amber-600">{{ $overallStats['total_rebooks'] }}</div>
            <div class="text-sm text-gray-600 mt-1">🔄 Rebooks</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-cyan-600">{{ $overallStats['total_cancellations'] }}</div>
            <div class="text-sm text-gray-600 mt-1">❌ Cancellations</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-red-600">{{ $overallStats['total_last_minute'] }}</div>
            <div class="text-sm text-gray-600 mt-1">⏰ Last Minute</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-orange-600">{{ $customers->sum('late_arrivals') }}</div>
            <div class="text-sm text-gray-600 mt-1">🕐 Late Arrivals</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-blue-500">{{ $customers->sum('early_arrivals') }}</div>
            <div class="text-sm text-gray-600 mt-1">⏪ Early Arrivals</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-green-600">{{ $overallStats['avg_notice_hours'] }}h</div>
            <div class="text-sm text-gray-600 mt-1">📊 Avg Notice</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-gray-600">{{ $overallStats['rebook_rate'] }}%</div>
            <div class="text-sm text-gray-600 mt-1">📈 Rebook Rate</div>
        </div>
    </div>

    <!-- Customer Analysis Table -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">🏢 Customer Analysis ({{ $days }} days)</h3>
            <p class="text-sm text-gray-600 mt-1">Detailed breakdown of customer booking behavior and risk assessment</p>
        </div>
        <div class="overflow-hidden">
            @if($customers->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Bookings</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Rebooks</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Cancellations</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Last Minute</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Notice</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Risk Level</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($customers as $customer)
                        <tr class="hover:bg-gray-50 {{ $customer->risk_level === 'High Risk' ? 'bg-red-50 border-l-4 border-red-500' : ($customer->risk_level === 'Medium Risk' ? 'bg-amber-50 border-l-4 border-amber-500' : '') }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $customer->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $customer->user_emails ?: '📧 No users assigned' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    📊 {{ $customer->total_bookings }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                    🔄 {{ $customer->rebook_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800">
                                    ❌ {{ $customer->cancellation_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    ⏰ {{ $customer->last_minute_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                📊 {{ $customer->avg_hours_notice }}h
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @php
                                    $riskClasses = [
                                        'High Risk' => 'bg-red-100 text-red-800',
                                        'Medium Risk' => 'bg-amber-100 text-amber-800',
                                        'Low Risk' => 'bg-gray-100 text-gray-800',
                                        'No Risk' => 'bg-green-100 text-green-800'
                                    ];
                                    $riskClass = $riskClasses[$customer->risk_level] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $riskClass }}">
                                    {{ $customer->risk_level === 'High Risk' ? '🚨' : ($customer->risk_level === 'Medium Risk' ? '⚠️' : ($customer->risk_level === 'Low Risk' ? '⚡' : '✅')) }} 
                                    {{ $customer->risk_level }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('app.customer-behavior.show', $customer->id) }}" 
                                       class="inline-flex items-center px-3 py-1 border border-blue-300 text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-md text-sm font-medium transition-colors"
                                       title="View Details">
                                        👁️ View
                                    </a>
                                    @if($customer->risk_level === 'High Risk' || $customer->risk_level === 'Medium Risk')
                                    <button class="inline-flex items-center px-3 py-1 border border-amber-300 text-amber-700 bg-amber-50 hover:bg-amber-100 rounded-md text-sm font-medium transition-colors"
                                            title="Flag Customer" onclick="flagCustomer({{ $customer->id }})">
                                        🚩 Flag
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-12">
                <div class="text-6xl mb-4">📊</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Customer Data Available</h3>
                <p class="text-gray-600">No customer behavior data found for the selected {{ $days }}-day period.</p>
                <p class="text-sm text-gray-500 mt-2">Try selecting a different time period or check that customers have booking activity.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function flagCustomer(customerId) {
    if (confirm('🚩 Flag this customer for review?\n\nThis will mark the customer for management attention and further review.')) {
        // Show loading state
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '⏳ Flagging...';
        button.disabled = true;
        
        // Simulate API call - replace with actual implementation
        setTimeout(() => {
            alert('✅ Customer has been flagged for review and will be reviewed by management.');
            button.innerHTML = '✅ Flagged';
            button.classList.remove('border-amber-300', 'text-amber-700', 'bg-amber-50', 'hover:bg-amber-100');
            button.classList.add('border-green-300', 'text-green-700', 'bg-green-50');
            
            // Reset after 3 seconds
            setTimeout(() => {
                button.innerHTML = originalText;
                button.disabled = false;
                button.classList.remove('border-green-300', 'text-green-700', 'bg-green-50');
                button.classList.add('border-amber-300', 'text-amber-700', 'bg-amber-50', 'hover:bg-amber-100');
            }, 3000);
        }, 1500);
    }
}

// Auto-submit form when filter values change for better UX
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const selects = form.querySelectorAll('select');
    
    selects.forEach(select => {
        select.addEventListener('change', function() {
            // Add a small delay to prevent rapid submissions
            clearTimeout(window.autoSubmitTimeout);
            window.autoSubmitTimeout = setTimeout(() => {
                form.submit();
            }, 500);
        });
    });
});

// Add smooth scrolling to anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});
</script>
</x-warehouse-layout>