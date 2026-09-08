<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?php echo $__env->yieldContent('title', 'Admin Panel | Meditrust Nepal'); ?></title>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        darkMode: 'class',
        theme: {
          extend: {
            fontFamily: {
              sans: ['"Plus Jakarta Sans"', 'sans-serif'],
            },
            colors: {
              primary: {
                50: '#f0f9ff',
                100: '#e0f2fe',
                200: '#bae6fd',
                300: '#7dd3fc',
                400: '#38bdf8',
                500: '#0ea5e9',
                600: '#0284c7',
                700: '#0369a1',
                800: '#075985',
                900: '#0c4a6e',
                950: '#082f49',
              },
            }
          }
        }
      }
    </script>
    <style>
      [x-cloak] { display: none !important; }
      body {
        font-family: 'Plus Jakarta Sans', sans-serif;
      }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen">
    <?php echo $__env->yieldContent('content'); ?>
</body>
</html>
<?php /**PATH /Users/dmk/Desktop/medi-main/laravel-backend/resources/views/layouts/admin.blade.php ENDPATH**/ ?>