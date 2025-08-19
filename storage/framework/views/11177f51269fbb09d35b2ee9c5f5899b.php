<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

  <title><?php echo e(config('app.name', 'Laravel')); ?></title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

  <!-- Scripts & Styles -->
  <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

  <style>
    @keyframes slideBg {
      from { background-position: center 0%; }
      to   { background-position: center 100%; }
    }
    body {
      animation: slideBg 30s ease-in-out infinite alternate;
    }
  </style>
</head>
<body
  class="font-sans antialiased flex items-center justify-center min-h-screen"
  style="background:
    url('/images/ktl_background.svg') center/cover no-repeat,
    linear-gradient(135deg, #fafbfc, #eaeef4)"
>
  <div class="w-full sm:max-w-md px-6 py-8 bg-white/60 backdrop-blur-lg rounded-2xl shadow-2xl animate-[popIn_0.4s_ease-out_both]">
    <?php echo e($slot); ?>

  </div>
</body>
</html>
<?php /**PATH /Users/londo/Herd/test/resources/views/layouts/guest.blade.php ENDPATH**/ ?>