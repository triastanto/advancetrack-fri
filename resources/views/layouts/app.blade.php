<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    @livewireStyles
</head>

<body class="flex min-h-screen">
    @auth
    @include('components.layouts.sidebar')
    @endauth

    <main class="flex-1 bg-[var(--color-bg)]">
    @yield('content')
    </main>

    @livewireScripts
    <script>
        document.getElementById('mobile-menu-button')?.addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
    </script>
</body>
</html>
