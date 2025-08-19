<?php echo csrf_field(); ?>
<div class="grid gap-4">
  <div>
    <label>Min Cases</label>
    <input type="number" name="min_cases" value="<?php echo e(old('min_cases', \$caseRange->min_cases ?? '')); ?>" class="input" />
  </div>
  <div>
    <label>Max Cases</label>
    <input type="number" name="max_cases" value="<?php echo e(old('max_cases', \$caseRange->max_cases ?? '')); ?>" class="input" />
  </div>
  <div>
    <label>Duration (minutes)</label>
    <input type="number" name="duration_minutes" required value="<?php echo e(old('duration_minutes', \$caseRange->duration_minutes ?? '')); ?>" class="input" />
  </div>
</div>
<div class="mt-4">
  <button type="submit" class="btn">Save</button>
  <a href="<?php echo e(route('admin.depots.case-ranges.index', \$depot)); ?>" class="btn-secondary">Cancel</a>
</div><?php /**PATH /Users/londo/Herd/test/resources/views/admin/depot_case_ranges/form.blade.php ENDPATH**/ ?>