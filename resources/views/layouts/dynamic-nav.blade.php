<nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 mb-6">
  <div class="flex justify-between items-center px-6 py-3">
    <ul class="flex space-x-4">
      @foreach(\App\Helpers\NavigationHelper::getNavigationItems() as $item)
        @if(isset($item['dropdown']))
          {{-- Dropdown Item --}}
          <li class="relative">
            <button onclick="toggleDropdown('{{ Str::slug($item['name']) }}')" 
                    class="px-3 py-1 rounded flex items-center {{ $item['active'] ? 'bg-' . ($item['color'] ?? 'blue') . '-500 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100' }}">
              {{ $item['icon'] }} {{ $item['name'] }} 
              <svg id="{{ Str::slug($item['name']) }}-arrow" class="ml-1 w-4 h-4 transform transition-transform" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </button>
            <div id="{{ Str::slug($item['name']) }}-dropdown" class="absolute left-0 mt-2 w-56 bg-white border border-gray-200 rounded-md shadow-lg z-20 hidden">
              @foreach($item['dropdown'] as $dropdownItem)
                @if(isset($dropdownItem['divider']))
                  <div class="border-t border-gray-100 my-1"></div>
                @else
                  <a href="{{ route($dropdownItem['route']) }}" 
                     class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ $dropdownItem['active'] ? 'bg-blue-50 text-blue-600' : '' }}">
                    @if(isset($dropdownItem['icon'])){{ $dropdownItem['icon'] }} @endif{{ $dropdownItem['name'] }}
                    @if(isset($dropdownItem['description']))
                      <div class="text-xs text-gray-500 mt-1">{{ $dropdownItem['description'] }}</div>
                    @endif
                  </a>
                @endif
              @endforeach
            </div>
          </li>
        @else
          {{-- Simple Item --}}
          <li>
            <a href="{{ route($item['route']) }}"
               class="px-3 py-1 rounded {{ $item['active'] ? 'bg-' . ($item['color'] ?? 'blue') . '-500 text-white' : 'text-gray-700 dark:text-gray-300' }}">
              {{ $item['icon'] }} {{ $item['name'] }}
            </a>
          </li>
        @endif
      @endforeach
    </ul>

    {{-- User Switching (Testing Only) --}}
    @if(!app()->isProduction() && session('original_admin_id'))
      <div class="flex items-center space-x-2">
        <span class="text-sm text-orange-600 font-medium">🔄 Testing as: {{ auth()->user()->name }}</span>
        <form action="{{ route('switch-back') }}" method="POST" class="inline">
          @csrf
          <button type="submit" class="px-2 py-1 bg-orange-500 text-white rounded text-xs hover:bg-orange-600">
            Switch Back
          </button>
        </form>
      </div>
    @elseif(!app()->isProduction())
      <div class="relative">
        <select onchange="switchUser(this.value)" class="text-xs border border-gray-300 rounded px-2 py-1 bg-white">
          <option value="">🔄 Switch User (Testing)</option>
          @foreach(\App\Models\User::with('roles')->get() as $user)
            <option value="{{ $user->id }}">
              {{ $user->name }} ({{ $user->roles->pluck('name')->join(', ') ?: 'No Role' }})
            </option>
          @endforeach
        </select>
      </div>
    @endif
  </div>

  <script>
  function switchUser(userId) {
    if (userId) {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = `/admin/switch-user/${userId}`;
      
      const token = document.createElement('input');
      token.type = 'hidden';
      token.name = '_token';
      token.value = '{{ csrf_token() }}';
      
      form.appendChild(token);
      document.body.appendChild(form);
      form.submit();
    }
  }

  function toggleDropdown(name) {
    const dropdown = document.getElementById(name + '-dropdown');
    const arrow = document.getElementById(name + '-arrow');
    
    // Close other dropdowns
    document.querySelectorAll('[id$="-dropdown"]').forEach(d => {
      if (d.id !== name + '-dropdown') {
        d.classList.add('hidden');
      }
    });
    document.querySelectorAll('[id$="-arrow"]').forEach(a => {
      if (a.id !== name + '-arrow') {
        a.classList.remove('rotate-180');
      }
    });
    
    if (dropdown.classList.contains('hidden')) {
      dropdown.classList.remove('hidden');
      arrow.classList.add('rotate-180');
    } else {
      dropdown.classList.add('hidden');
      arrow.classList.remove('rotate-180');
    }
  }

  // Close dropdowns when clicking outside
  document.addEventListener('click', function(event) {
    if (!event.target.closest('button[onclick*="toggleDropdown"]') && 
        !event.target.closest('[id$="-dropdown"]')) {
      document.querySelectorAll('[id$="-dropdown"]').forEach(dropdown => {
        dropdown.classList.add('hidden');
      });
      document.querySelectorAll('[id$="-arrow"]').forEach(arrow => {
        arrow.classList.remove('rotate-180');
      });
    }
  });
  </script>
</nav>