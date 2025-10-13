<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl">Bulk PO Upload</h2>
      <a href="{{ route('app.bookings.index') }}" class="px-3 py-1 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 text-sm">
        ← Back to Bookings
      </a>
    </div>
  </x-slot>

  <div class="bg-white min-h-screen px-6 py-6">
    {{-- Bookings Needing PO Numbers --}}
    @if($bookingsNeedingPOs->count() > 0)
    <div class="bg-red-50 p-4 rounded-lg border border-red-200 mb-4">
      <h3 class="text-base font-semibold text-red-900 mb-2">⚠️ Bookings Needing PO Details ({{ $bookingsNeedingPOs->count() }})</h3>
      <p class="text-sm text-red-800 mb-3">These bookings have upcoming slots but no PO numbers assigned yet:</p>
      <div class="bg-white rounded border border-red-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Booking ID</th>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Slot Time</th>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @foreach($bookingsNeedingPOs as $booking)
            <tr class="hover:bg-gray-50">
              <td class="px-3 py-2 text-sm font-medium text-gray-900">#{{ $booking['id'] }}</td>
              <td class="px-3 py-2 text-sm text-gray-700">{{ $booking['reference'] }}</td>
              <td class="px-3 py-2 text-sm text-gray-700">
                <span class="font-mono text-xs bg-purple-100 text-purple-800 px-1 rounded">ID: {{ $booking['customer_id'] }}</span>
                <span class="ml-1">{{ $booking['customer_name'] }}</span>
              </td>
              <td class="px-3 py-2 text-sm text-gray-700">{{ $booking['slot_time'] }}</td>
              <td class="px-3 py-2 text-sm text-gray-700">{{ $booking['booking_type'] }}</td>
              <td class="px-3 py-2 text-sm text-gray-500">{{ $booking['created_at'] }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    @endif

    {{-- Customer IDs Reference --}}
    @if($accessibleCustomers->count() > 0)
    <div class="bg-purple-50 p-4 rounded-lg border border-purple-200 mb-4">
      <h3 class="text-base font-semibold text-purple-900 mb-2">🏭 Your Customer IDs</h3>
      <p class="text-sm text-purple-800 mb-3">Use these Customer IDs in your CSV file:</p>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
        @foreach($accessibleCustomers as $customer)
          <div class="bg-white p-2 rounded border border-purple-200">
            <span class="font-mono font-semibold text-purple-900">ID: {{ $customer->id }}</span>
            <span class="text-sm text-gray-700 ml-2">- {{ $customer->name }}</span>
          </div>
        @endforeach
      </div>
    </div>
    @endif

    {{-- Download Template --}}
    <div class="bg-green-50 p-4 rounded-lg border border-green-200 mb-4">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-base font-semibold text-green-900 mb-1">📥 Need a Template?</h3>
          <p class="text-sm text-green-800">Download a reusable CSV template to get started with the correct format.</p>
        </div>
        <a href="{{ route('app.bookings.download-generic-csv-template') }}"
           class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-medium whitespace-nowrap">
          Download Template
        </a>
      </div>
    </div>

    {{-- Instructions --}}
    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 mb-6">
      <h3 class="text-base font-semibold text-blue-900 mb-2">📤 How Bulk Upload Works</h3>
      <ol class="list-decimal list-inside text-sm text-blue-800 space-y-1">
        <li>Upload ONE CSV file with all your PO details for multiple bookings</li>
        <li>Use different "Booking Reference" for each delivery (e.g., MON-AM, TUE-PM)</li>
        <li>System will detect all references and show your recent bookings</li>
        <li>Match each reference to the correct booking</li>
        <li>Click "Process All" to import everything at once</li>
      </ol>
    </div>

    {{-- Step 1: Upload CSV --}}
    <div class="bg-white p-6 rounded-lg border border-gray-200 mb-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Step 1: Upload Your CSV File</h3>

      <form id="bulk-upload-form" enctype="multipart/form-data">
        @csrf
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Select CSV File</label>
          <input type="file" name="csv_file" id="bulk-csv-file" accept=".csv" class="block w-full text-sm text-gray-900 border border-gray-300 rounded cursor-pointer bg-gray-50">
          <p class="text-xs text-gray-500 mt-1">Format: Customer ID, Booking Reference, PO Number, SKU, Product Description, Expected Cases, Expected Pallets</p>
        </div>

        <button type="button" id="analyze-btn" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          Analyze CSV
        </button>
      </form>

      <div id="analysis-result" class="mt-4 hidden"></div>
    </div>

    {{-- Step 2: Match References to Bookings --}}
    <div id="mapping-section" class="bg-white p-6 rounded-lg border border-gray-200 mb-6 hidden">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Step 2: Match Booking References to Your Bookings</h3>

      <div id="mapping-container" class="space-y-4">
        <!-- Populated by JavaScript -->
      </div>

      <div class="mt-6 flex gap-3">
        <button type="button" id="process-all-btn" class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700">
          Process All Assignments
        </button>
        <button type="button" id="cancel-btn" class="px-6 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
          Cancel
        </button>
      </div>
    </div>

    {{-- Step 3: Results --}}
    <div id="results-section" class="bg-white p-6 rounded-lg border border-gray-200 hidden">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Results</h3>
      <div id="results-container"></div>
    </div>
  </div>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const bulkCsvFile = document.getElementById('bulk-csv-file');
    const analyzeBtn = document.getElementById('analyze-btn');
    const analysisResult = document.getElementById('analysis-result');
    const mappingSection = document.getElementById('mapping-section');
    const mappingContainer = document.getElementById('mapping-container');
    const processAllBtn = document.getElementById('process-all-btn');
    const resultsSection = document.getElementById('results-section');
    const resultsContainer = document.getElementById('results-container');
    const cancelBtn = document.getElementById('cancel-btn');

    let csvData = null;
    let recentBookings = @json($recentBookings);

    if (cancelBtn) {
      cancelBtn.addEventListener('click', function() {
        window.location.href = '{{ route("app.bookings.index") }}';
      });
    }

    if (analyzeBtn) {
      analyzeBtn.addEventListener('click', function() {
        if (!bulkCsvFile.files.length) {
          analysisResult.className = 'mt-4 p-3 bg-red-100 border border-red-300 rounded text-sm text-red-800';
          analysisResult.textContent = '⚠️ Please select a CSV file first';
          analysisResult.classList.remove('hidden');
          return;
        }

        const formData = new FormData();
        formData.append('csv_file', bulkCsvFile.files[0]);

        analysisResult.className = 'mt-4 p-3 bg-blue-100 border border-blue-300 rounded text-sm text-blue-800';
        analysisResult.textContent = '⏳ Analyzing CSV file...';
        analysisResult.classList.remove('hidden');

        fetch('{{ route("app.bookings.bulk-upload.process") }}?action=analyze', {
          method: 'POST',
          body: formData,
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            csvData = data;
            displayMappingInterface(data);
            analysisResult.className = 'mt-4 p-3 bg-green-100 border border-green-300 rounded text-sm text-green-800';
            analysisResult.innerHTML = `✅ Found ${data.references.length} booking reference(s) with ${data.total_rows} total items`;
          } else {
            analysisResult.className = 'mt-4 p-3 bg-red-100 border border-red-300 rounded text-sm text-red-800';
            analysisResult.textContent = `❌ Error: ${data.message}`;
          }
        })
        .catch(error => {
          console.error('Analysis error:', error);
          analysisResult.className = 'mt-4 p-3 bg-red-100 border border-red-300 rounded text-sm text-red-800';
          analysisResult.textContent = '❌ Failed to analyze CSV';
        });
      });
    }

    function displayMappingInterface(data) {
      mappingContainer.innerHTML = '';

      data.references.forEach((ref, index) => {
        const card = document.createElement('div');
        card.className = 'bg-gray-50 p-4 rounded-lg border border-gray-300';
        card.innerHTML = `
          <div class="flex items-center justify-between mb-3">
            <div>
              <h4 class="font-semibold text-gray-900">📦 ${ref.reference}</h4>
              <p class="text-sm text-gray-600">${ref.row_count} items • POs: ${ref.po_numbers.join(', ')}</p>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Assign to Booking:</label>
            <select class="booking-select block w-full border-gray-300 rounded text-sm py-2" data-reference="${ref.reference}">
              <option value="">-- Select Booking --</option>
              ${recentBookings.map(booking => `
                <option value="${booking.id}">
                  #${booking.id} - ${booking.customer_name} - ${booking.slot_time} (${booking.booking_type})
                </option>
              `).join('')}
            </select>
          </div>
        `;
        mappingContainer.appendChild(card);
      });

      mappingSection.classList.remove('hidden');
    }

    if (processAllBtn) {
      processAllBtn.addEventListener('click', function() {
        const assignments = [];
        const selects = document.querySelectorAll('.booking-select');

        selects.forEach(select => {
          if (select.value) {
            assignments.push({
              reference: select.dataset.reference,
              booking_id: select.value
            });
          }
        });

        if (assignments.length === 0) {
          alert('Please assign at least one booking reference to a booking');
          return;
        }

        // Send assignments to server
        processAllBtn.disabled = true;
        processAllBtn.textContent = 'Processing...';

        const formData = new FormData();
        formData.append('csv_file', bulkCsvFile.files[0]);
        formData.append('assignments', JSON.stringify(assignments));

        fetch('{{ route("app.bookings.bulk-upload.process") }}?action=process', {
          method: 'POST',
          body: formData,
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            displayResults(data.results);
            mappingSection.classList.add('hidden');
            resultsSection.classList.remove('hidden');
          } else {
            alert('Error: ' + data.message);
            processAllBtn.disabled = false;
            processAllBtn.textContent = 'Process All Assignments';
          }
        })
        .catch(error => {
          console.error('Process error:', error);
          alert('Failed to process assignments');
          processAllBtn.disabled = false;
          processAllBtn.textContent = 'Process All Assignments';
        });
      });
    }

    function displayResults(results) {
      resultsContainer.innerHTML = `
        <div class="space-y-3">
          ${results.map(result => `
            <div class="p-3 rounded ${result.success ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'}">
              <div class="flex items-center justify-between">
                <div>
                  <strong>${result.success ? '✅' : '❌'} ${result.reference}</strong> → Booking #${result.booking_id}
                </div>
                <div class="text-sm text-gray-600">
                  ${result.success ? `${result.products_added} products added` : result.error}
                </div>
              </div>
            </div>
          `).join('')}
        </div>
        <div class="mt-6">
          <a href="{{ route('app.bookings.index') }}" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 inline-block">
            View All Bookings
          </a>
        </div>
      `;
    }
  });
  </script>
</x-app-layout>
