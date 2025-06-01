<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="flex min-h-screen">
    @include('components.layouts.sidebar')
    <main class="flex-1 bg-[var(--color-bg)]">
        @yield('content')
    </main>
</body>

</html>