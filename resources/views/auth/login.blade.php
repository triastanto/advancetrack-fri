@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[var(--color-bg)]">
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <div class="mb-6 text-center">
            <img src="/build/logo.png" alt="AdvanceTrack FRI Logo" class="h-12 mx-auto" />
            <h2 class="text-2xl font-bold text-[var(--color-text-main)]">Login</h2>
            <p class="mt-2 text-sm text-[var(--color-text-muted)]">Masuk ke akun AdvanceTrack FRI Anda</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-[var(--color-text-main)]">Email</label>
                <div class="mt-1 relative">
                    <x-heroicon-o-envelope-open class="absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-text-muted)] w-5 h-5" />
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                           class="appearance-none block w-full pl-10 pr-3 py-2 border border-[var(--color-border)] rounded-md shadow-sm placeholder-[var(--color-text-muted)] focus:outline-none focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] sm:text-sm">
                </div>
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-[var(--color-text-main)]">Password</label>
                <div class="mt-1 relative">
                    <x-heroicon-o-lock-closed class="absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-text-muted)] w-5 h-5" />
                    <input id="password" name="password" type="password" required autocomplete="current-password"
                           class="appearance-none block w-full pl-10 pr-3 py-2 border border-[var(--color-border)] rounded-md shadow-sm placeholder-[var(--color-text-muted)] focus:outline-none focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] sm:text-sm">
                </div>
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember_me" name="remember" type="checkbox"
                           class="h-4 w-4 text-[var(--color-primary)] focus:ring-[var(--color-primary)] border-[var(--color-border)] rounded">
                    <label for="remember_me" class="ml-2 block text-sm text-[var(--color-text-main)]">Ingat saya</label>
                </div>

                <div class="text-sm">
                    <a href="{{ route('password.request') }}" class="font-medium text-[var(--color-primary)] hover:text-[var(--color-primary-dark)] flex items-center gap-1">
                        <x-heroicon-o-question-mark-circle class="w-5 h-5" />
                        Lupa password?
                    </a>
                </div>
            </div>

            <div>
                <button type="submit"
                        class="w-full flex justify-center items-center gap-2 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[var(--color-primary)] hover:bg-[var(--color-primary-dark)] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--color-primary)]">
                    <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5" />
                    Login
                </button>
            </div>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-[var(--color-text-muted)]">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-medium text-[var(--color-primary)] hover:text-[var(--color-primary-dark)] flex items-center gap-1 justify-center">
                    <x-heroicon-o-user-plus class="w-5 h-5" />
                    Daftar sekarang
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
