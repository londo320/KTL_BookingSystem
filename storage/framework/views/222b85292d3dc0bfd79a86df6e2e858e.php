<nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 mb-6">
  <div class="flex justify-between items-center px-6 py-3">
    <ul class="flex space-x-4">
      <?php $__currentLoopData = \App\Helpers\NavigationHelper::getNavigationItems(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(isset($item['dropdown'])): ?>
          
          <li class="relative">
            <button onclick="toggleDropdown('<?php echo e(Str::slug($item['name'])); ?>')" 
                    class="px-3 py-1 rounded flex items-center <?php echo e($item['active'] ? 'bg-' . ($item['color'] ?? 'blue') . '-500 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100'); ?>">
              <?php echo e($item['icon']); ?> <?php echo e($item['name']); ?> 
              <svg id="<?php echo e(Str::slug($item['name'])); ?>-arrow" class="ml-1 w-4 h-4 transform transition-transform" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </button>
            <div id="<?php echo e(Str::slug($item['name'])); ?>-dropdown" class="absolute left-0 mt-2 w-56 bg-white border border-gray-200 rounded-md shadow-lg z-20 hidden">
              <?php $__currentLoopData = $item['dropdown']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dropdownItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(isset($dropdownItem['divider'])): ?>
                  <div class="border-t border-gray-100 my-1"></div>
                <?php else: ?>
                  <a href="<?php echo e(route($dropdownItem['route'])); ?>" 
                     class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo e($dropdownItem['active'] ? 'bg-blue-50 text-blue-600' : ''); ?>">
                    <?php if(isset($dropdownItem['icon'])): ?><?php echo e($dropdownItem['icon']); ?> <?php endif; ?><?php echo e($dropdownItem['name']); ?>

                    <?php if(isset($dropdownItem['description'])): ?>
                      <div class="text-xs text-gray-500 mt-1"><?php echo e($dropdownItem['description']); ?></div>
                    <?php endif; ?>
                  </a>
                <?php endif; ?>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
          </li>
        <?php else: ?>
          
          <li>
            <a href="<?php echo e(route($item['route'])); ?>"
               class="px-3 py-1 rounded <?php echo e($item['active'] ? 'bg-' . ($item['color'] ?? 'blue') . '-500 text-white' : 'text-gray-700 dark:text-gray-300'); ?>">
              <?php echo e($item['icon']); ?> <?php echo e($item['name']); ?>

            </a>
          </li>
        <?php endif; ?>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>

    
    <?php if(!app()->isProduction() && session('original_admin_id')): ?>
      <div class="flex items-center space-x-2">
        <span class="text-sm text-orange-600 font-medium">🔄 Testing as: <?php echo e(auth()->user()->name); ?></span>
        <form action="<?php echo e(route('switch-back')); ?>" method="POST" class="inline">
          <?php echo csrf_field(); ?>
          <button type="submit" class="px-2 py-1 bg-orange-500 text-white rounded text-xs hover:bg-orange-600">
            Switch Back
          </button>
        </form>
      </div>
    <?php elseif(!app()->isProduction()): ?>
      <div class="relative">
        <select onchange="switchUser(this.value)" class="text-xs border border-gray-300 rounded px-2 py-1 bg-white">
          <option value="">🔄 Switch User (Testing)</option>
          <?php $__currentLoopData = \App\Models\User::with('roles')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($user->id); ?>">
              <?php echo e($user->name); ?> (<?php echo e($user->roles->pluck('name')->join(', ') ?: 'No Role'); ?>)
            </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
    <?php endif; ?>
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
      token.value = '<?php echo e(csrf_token()); ?>';
      
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
</nav><?php /**PATH /Users/londo/Herd/test/resources/views/layouts/dynamic-nav.blade.php ENDPATH**/ ?>