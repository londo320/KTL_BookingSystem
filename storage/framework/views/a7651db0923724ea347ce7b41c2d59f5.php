<!-- Quick Trailer Collection Modal -->
<div id="quickTrailerCollectionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
  <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
    <div class="mt-3">
      <h3 class="text-lg font-bold text-gray-900 mb-4">🚛 Quick Trailer Collection</h3>
      <p class="text-sm text-gray-600 mb-4">Record a vehicle arriving to collect a trailer</p>
      
      <?php
        $collectionRoutePrefix = 'app.';
      ?>
      <form action="<?php echo e(route($collectionRoutePrefix . 'empty-unit-collection.process')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        
        <div class="space-y-3">
          <!-- Vehicle Registration -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Collection Vehicle <span class="text-red-500">*</span>
            </label>
            <input type="text" name="vehicle_registration" required
                   placeholder="e.g., AB12 CDE"
                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 font-mono">
          </div>
          
          <!-- Available Trailers Dropdown -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Select Trailer <span class="text-red-500">*</span>
            </label>
            <select name="collected_from_booking_id" id="availableTrailerSelect" required
                    onchange="updateTrailerDetails(this)"
                    class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
              <option value="">– Loading trailers... –</option>
            </select>
          </div>
          
          <!-- Trailer Location Display -->
          <div id="trailerLocationDisplay" class="hidden p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <h4 class="font-medium text-blue-800 mb-2">📍 Collection Point</h4>
            <div class="text-sm">
              <div><strong>Trailer:</strong> <span id="displayTrailerNumber"></span></div>
              <div><strong>Location:</strong> <span id="displayTrailerLocation"></span></div>
            </div>
          </div>
          
          <!-- Company -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Company</label>
            <input type="text" name="carrier_company"
                   placeholder="e.g., ABC Transport"
                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
          </div>
        </div>
        
        <div class="mt-6 flex justify-end space-x-3">
          <button type="button" onclick="closeQuickTrailerCollectionModal()" 
                  class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
            Cancel
          </button>
          <button type="submit" 
                  class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            🚛 Record Collection
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function openQuickTrailerCollectionModal() {
  document.getElementById('quickTrailerCollectionModal').classList.remove('hidden');
  loadAvailableTrailers();
}

function closeQuickTrailerCollectionModal() {
  document.getElementById('quickTrailerCollectionModal').classList.add('hidden');
  document.getElementById('availableTrailerSelect').value = '';
  document.getElementById('trailerLocationDisplay').classList.add('hidden');
}

// Load available trailers from server
async function loadAvailableTrailers() {
  try {
    const routePrefix = window.location.pathname.includes('/depot-admin/') ? '/depot-admin' : '/admin';
    const response = await fetch(`${routePrefix}/api/available-trailers`);
    const trailers = await response.json();
    
    const select = document.getElementById('availableTrailerSelect');
    select.innerHTML = '<option value="">– Choose Trailer to Collect –</option>';
    
    trailers.forEach(trailer => {
      const option = document.createElement('option');
      option.value = trailer.id;
      option.textContent = `${trailer.container_number || 'Container#' + trailer.id} @ ${trailer.dropped_trailer_location || 'Unknown'}`;
      option.dataset.containerNumber = trailer.container_number || 'No container number';
      option.dataset.location = trailer.dropped_trailer_location || 'Unknown location';
      select.appendChild(option);
    });
    
    if (trailers.length === 0) {
      const option = document.createElement('option');
      option.value = '';
      option.textContent = '– No trailers available for collection –';
      option.disabled = true;
      select.appendChild(option);
    }
  } catch (error) {
    console.error('Failed to load available trailers:', error);
    const select = document.getElementById('availableTrailerSelect');
    select.innerHTML = '<option value="">– Error loading trailers –</option>';
  }
}

// Update trailer details when selection changes
function updateTrailerDetails(selectElement) {
  const selectedOption = selectElement.options[selectElement.selectedIndex];
  const locationDisplay = document.getElementById('trailerLocationDisplay');
  
  if (selectedOption.value) {
    locationDisplay.classList.remove('hidden');
    document.getElementById('displayTrailerNumber').textContent = selectedOption.dataset.containerNumber;
    document.getElementById('displayTrailerLocation').textContent = selectedOption.dataset.location;
  } else {
    locationDisplay.classList.add('hidden');
  }
}

// Close modal when clicking outside
document.getElementById('quickTrailerCollectionModal').addEventListener('click', function(e) {
  if (e.target === this) {
    closeQuickTrailerCollectionModal();
  }
});
</script><?php /**PATH /Users/londo/Herd/test/resources/views/admin/bookings/partials/collection-modal.blade.php ENDPATH**/ ?>