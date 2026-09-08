<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?php echo $__env->yieldContent('title', $title ?? config('app.name', 'Meditrust Nepal')); ?></title>
    <meta name="description" content="<?php echo e($description ?? 'Meditrust Nepal is your trusted medical equipment provider in Nepal.'); ?>">
    <link rel="stylesheet" href="/css/app.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        darkMode: 'class',
        theme: {
          extend: {
            colors: {
              primary: { 50: '#f0f9ff', 100: '#e0f2fe', 300: '#7dd3fc', 600: '#0284c7', 700: '#0369a1', 900: '#0c4a6e' },
              brand: { cyan: '#06B6D4', red: '#EF4444' },
              dark: { surface: '#0f172a', card: '#1e293b', border: '#334155', muted: '#94a3b8' }
            }
          }
        }
      }
    </script>
</head>
<body class="page-body">
    <?php echo $__env->make('partials.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <main class="page-main">
        <?php echo $__env->yieldContent('content'); ?>
    </main>
    <?php echo $__env->make('partials.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <script src="/js/app.js" defer></script>
</body>
</html>
<?php /**PATH /Users/dmk/Desktop/medi-main/laravel-backend/resources/views/layouts/app.blade.php ENDPATH**/ ?>