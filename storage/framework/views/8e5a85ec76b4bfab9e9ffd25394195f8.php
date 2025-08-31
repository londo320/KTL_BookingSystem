<?php if(session('success')): ?>
    <div class="alert alert-success">
        <?php echo e(session('success')); ?>

    </div>
<?php endif; ?>

<?php if(session('error')): ?>
    <div class="alert alert-danger">
        <?php echo e(session('error')); ?>

    </div>
<?php endif; ?>

<form action="<?php echo e(route('app.assignRoleAndDepots', $user->id)); ?>" method="POST">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    <h3>Assign Role</h3>
    <div>
        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <label>
                <input type="checkbox" name="role" value="<?php echo e($role->name); ?>"
                    <?php echo e($user->hasRole($role->name) ? 'checked' : ''); ?>>
                <?php echo e($role->name); ?>

            </label><br>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <h3>Assign Depots</h3>
    <div>
        <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <label>
                <input type="checkbox" name="depots[]" value="<?php echo e($depot->id); ?>"
                    <?php echo e($user->depots->contains($depot->id) ? 'checked' : ''); ?>>
                <?php echo e($depot->name); ?>

            </label><br>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <button type="submit">Save</button>
</form><?php /**PATH /Users/londo/Herd/test/resources/views/admin/users/assign-role.blade.php ENDPATH**/ ?>