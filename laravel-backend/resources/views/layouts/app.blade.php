<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title', $title ?? config('app.name', 'Meditrust Nepal'))</title>
    <meta name="description" content="{{ $description ?? 'Meditrust Nepal is your trusted medical equipment provider in Nepal.' }}">
    @yield('meta')
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
    @include('partials.navbar')
    <main class="page-main">
        @yield('content')
    </main>
    @include('partials.footer')
    <script src="/js/app.js" defer></script>
</body>
</html>
