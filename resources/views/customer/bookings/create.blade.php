<x-app-layout>
  @include('layouts.customer-nav')

  <x-slot name="header">
    <h2 class="font-semibold text-xl">Create Booking</h2>
  </x-slot>

  <div class="py-6 max-w-7xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      
      {{-- Main Booking Form --}}
      <div class="lg:col-span-2 bg-white p-6 rounded shadow">
        <form id="booking-form" action="{{ route('customer.bookings.store') }}" method="POST">
          @csrf
          
          {{-- Depot and Date Selection --}}
          <div class="grid grid-cols-2 gap-4 mb-6 pb-4 border-b">
            <div>
              <label class="block text-sm font-medium mb-2">Select Depot</label>
              <select id="depot-select" name="depot_id" required class="w-full border-gray-300 rounded">
                <option value="">– Choose depot –</option>
                @foreach(auth()->user()->depots as $depot)
                  <option value="{{ $depot->id }}" @selected($selectedDepotId == $depot->id)>
                    {{ $depot->name }}
                  </option>
                @endforeach
              </select>
              @error('depot_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div>
              <label class="block text-sm font-medium mb-2">Select Date</label>
              <input type="date" id="date-select" name="date" 
                     value="{{ $selectedDate }}" 
                     min="{{ now()->format('Y-m-d') }}"
                     max="{{ now()->addMonths(3)->format('Y-m-d') }}"
                     class="w-full border-gray-300 rounded">
              @error('date')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
          </div>

          @include('customer.bookings._form')
          
          <!-- Validation Messages -->
          <div id="validation-messages" class="hidden mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <h4 class="text-red-800 font-medium mb-2">❌ Please correct the following errors:</h4>
            <ul id="validation-list" class="list-disc list-inside text-red-700 text-sm space-y-1"></ul>
          </div>

          <div class="mt-6 pt-4 border-t">
            <button type="submit" id="submit-button"
                    class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed">
              <span id="submit-text">Create Booking</span>
              <span id="submit-loading" class="hidden">🔄 Creating...</span>
            </button>
            <a href="{{ route('customer.bookings.index') }}"
               class="ml-3 px-6 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition-colors">
              Cancel
            </a>
          </div>
        </form>
      </div>

      {{-- Availability Preview Sidebar --}}
      <div class="bg-white p-6 rounded shadow">
        <h3 class="text-lg font-semibold mb-4">📅 Available Slots</h3>
        <div id="availability-preview">
          <p class="text-gray-500 text-sm">Select a depot to see available dates</p>
        </div>
      </div>
      
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const depotSelect = document.getElementById('depot-select');
      const dateSelect = document.getElementById('date-select');
      const availabilityPreview = document.getElementById('availability-preview');
      const slotSelect = document.querySelector('select[name="slot_id"]');
      const bookingForm = document.getElementById('booking-form');
      const submitButton = document.getElementById('submit-button');
      const submitText = document.getElementById('submit-text');
      const submitLoading = document.getElementById('submit-loading');
      const validationMessages = document.getElementById('validation-messages');
      const validationList = document.getElementById('validation-list');

      // Load availability when depot changes
      depotSelect.addEventListener('change', function() {
        if (this.value) {
          loadAvailability(this.value);
          loadSlots(this.value, dateSelect.value);
        } else {
          availabilityPreview.innerHTML = '<p class="text-gray-500 text-sm">Select a depot to see available dates</p>';
          clearSlots();
        }
      });

      // Load slots when date changes
      dateSelect.addEventListener('change', function() {
        if (depotSelect.value && this.value) {
          loadSlots(depotSelect.value, this.value);
        }
      });

      // Load availability for initially selected depot
      if (depotSelect.value) {
        loadAvailability(depotSelect.value);
        loadSlots(depotSelect.value, dateSelect.value);
      }

      // Form validation and submission
      bookingForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Check if user is holding Shift key to bypass validation (debug mode)
        if (e.shiftKey) {
          console.log('BYPASSING VALIDATION - Shift key held');
          showLoadingState();
          this.submit();
          return;
        }
        
        // Add a small delay to ensure Alpine.js components are fully loaded
        setTimeout(() => {
          const errors = validateForm();
          
          if (errors.length > 0) {
            showValidationErrors(errors);
            scrollToTop();
            return;
          }
          
          hideValidationErrors();
          showLoadingState();
          
          // Submit the form
          this.submit();
        }, 100);
      });

      function validateForm() {
        const errors = [];
        
        // Debug: Log all form inputs for troubleshooting
        console.log('=== FORM VALIDATION DEBUG ===');
        console.log('Slot ID:', slotSelect?.value);
        console.log('Booking Type:', document.querySelector('select[name="booking_type_id"]')?.value);
        console.log('PO Manager Instance:', !!window.poNumbersManagerInstance);
        
        // Check slot selection
        const slotId = slotSelect.value;
        if (!slotId) {
          errors.push('Please select a time slot for your booking');
        }
        
        // Check booking type
        const bookingTypeId = document.querySelector('select[name="booking_type_id"]').value;
        if (!bookingTypeId) {
          errors.push('Please select a booking type');
        }
        
        // Check PO numbers - use DOM-based validation since Alpine.js instance isn't working correctly
        console.log('Checking PO validation via DOM elements...');
        
        let hasValidPoCases = false;
        
        // Check for cases input fields in the DOM
        const casesInputs = document.querySelectorAll('input[name*="[cases]"]');
        console.log('Found cases inputs:', casesInputs.length);
        
        casesInputs.forEach((input, index) => {
          const value = parseInt(input.value) || 0;
          console.log(`Cases input ${index}: ${input.name} = ${value}`);
          if (value > 0) {
            hasValidPoCases = true;
          }
        });
        
        // If no cases inputs found, check for expected_cases inputs (fallback)
        if (casesInputs.length === 0) {
          const expectedCasesInputs = document.querySelectorAll('input[name*="expected_cases"]');
          console.log('Fallback: Found expected_cases inputs:', expectedCasesInputs.length);
          
          expectedCasesInputs.forEach((input, index) => {
            const value = parseInt(input.value) || 0;
            console.log(`Expected cases input ${index}: ${input.name} = ${value}`);
            if (value > 0) {
              hasValidPoCases = true;
            }
          });
        }
        
        console.log('Has valid PO cases:', hasValidPoCases);
        
        if (!hasValidPoCases) {
          errors.push('At least one Purchase Order with cases greater than 0 is required');
        }
        
        // Check if PO numbers are actually filled (fallback check)
        const poNumbers = document.querySelectorAll('input[name*="[po_number]"]');
        console.log('Found PO Number inputs:', poNumbers.length);
        let hasValidPo = false;
        poNumbers.forEach((input, index) => {
          console.log(`PO Input ${index}:`, input.name, '=', input.value);
          if (input.value && input.value.trim() !== '') {
            hasValidPo = true;
          }
        });
        
        if (!hasValidPo && poNumbers.length > 0) {
          errors.push('Please add at least one Purchase Order number');
        }
        
        console.log('Validation Errors:', errors);
        console.log('=== END DEBUG ===');
        
        return errors;
      }

      function showValidationErrors(errors) {
        validationList.innerHTML = '';
        errors.forEach(error => {
          const li = document.createElement('li');
          li.textContent = error;
          validationList.appendChild(li);
        });
        validationMessages.classList.remove('hidden');
      }

      function hideValidationErrors() {
        validationMessages.classList.add('hidden');
      }

      function showLoadingState() {
        submitButton.disabled = true;
        submitText.classList.add('hidden');
        submitLoading.classList.remove('hidden');
      }

      function scrollToTop() {
        window.scrollTo({
          top: 0,
          behavior: 'smooth'
        });
      }

      function loadAvailability(depotId) {
        availabilityPreview.innerHTML = '<p class="text-gray-500 text-sm">🔄 Loading...</p>';
        
        fetch(`/customer/availability?depot_id=${depotId}`)
          .then(response => response.json())
          .then(data => {
            let html = '';
            
            if (data.dates && data.dates.length > 0) {
              html += '<div class="space-y-2">';
              data.dates.forEach(dateInfo => {
                const date = new Date(dateInfo.date);
                const isSelected = dateInfo.date === dateSelect.value;
                const buttonClass = isSelected 
                  ? 'w-full text-left p-2 rounded bg-blue-100 border border-blue-300 text-blue-800 text-sm'
                  : 'w-full text-left p-2 rounded bg-gray-50 hover:bg-gray-100 border text-sm transition-colors';
                
                html += `
                  <button type="button" onclick="selectDate('${dateInfo.date}')" class="${buttonClass}">
                    <div class="font-medium">${date.toLocaleDateString('en-GB', { 
                      weekday: 'short', 
                      month: 'short', 
                      day: 'numeric' 
                    })}</div>
                    <div class="text-xs text-gray-600">
                      ${dateInfo.available_slots} slot${dateInfo.available_slots !== 1 ? 's' : ''} available
                    </div>
                  </button>
                `;
              });
              html += '</div>';
            } else {
              html = '<p class="text-gray-500 text-sm">📭 No available slots found for this depot</p>';
            }
            
            availabilityPreview.innerHTML = html;
          })
          .catch(error => {
            console.error('Error loading availability:', error);
            availabilityPreview.innerHTML = '<p class="text-red-500 text-sm">❌ Error loading availability</p>';
          });
      }

      function loadSlots(depotId, date) {
        if (!depotId || !date) {
          clearSlots();
          return;
        }

        // Show loading state for slot select
        slotSelect.innerHTML = '<option value="">🔄 Loading slots...</option>';
        slotSelect.disabled = true;

        fetch(`/customer/slots?depot_id=${depotId}&date=${date}`)
          .then(response => response.json())
          .then(data => {
            clearSlots();
            slotSelect.disabled = false;
            
            if (data.slots && data.slots.length > 0) {
              data.slots.forEach(slot => {
                const option = document.createElement('option');
                option.value = slot.id;
                option.textContent = `${slot.time_range} ${slot.is_restricted ? '🔒' : '🌐'} ${slot.customers_info}`;
                slotSelect.appendChild(option);
              });
            } else {
              const option = document.createElement('option');
              option.value = '';
              option.textContent = '📭 No slots available for this date';
              option.disabled = true;
              slotSelect.appendChild(option);
            }
          })
          .catch(error => {
            console.error('Error loading slots:', error);
            clearSlots();
            slotSelect.disabled = false;
            const option = document.createElement('option');
            option.value = '';
            option.textContent = '❌ Error loading slots';
            option.disabled = true;
            slotSelect.appendChild(option);
          });
      }

      function clearSlots() {
        slotSelect.innerHTML = '<option value="">– Choose your time slot –</option>';
      }

      // Global function for date selection buttons
      window.selectDate = function(date) {
        dateSelect.value = date;
        dateSelect.dispatchEvent(new Event('change'));
        loadAvailability(depotSelect.value); // Refresh to update selected state
      };
    });
  </script>
</x-app-layout>
