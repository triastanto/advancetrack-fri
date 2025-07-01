@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[var(--color-bg)]">
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <div class="mb-6 text-center">
            <img src="/build/logo.png" alt="AdvanceTrack FRI Logo" class="h-12 mx-auto mb-4" />
            <h2 class="text-2xl font-bold text-[var(--color-text-main)]">Buat Akun Baru</h2>
            <p class="mt-2 text-sm text-[var(--color-text-muted)]">Bergabung dengan AdvanceTrack FRI</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-[var(--color-text-main)]">Nama</label>
                <div class="mt-1 relative">
                    <x-heroicon-o-user class="absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-text-muted)] w-5 h-5" />
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name"
                           class="appearance-none block w-full pl-10 pr-3 py-2 border border-[var(--color-border)] rounded-md shadow-sm placeholder-[var(--color-text-muted)] focus:outline-none focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] sm:text-sm">
                </div>
                @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-[var(--color-text-main)]">Email</label>
                <div class="mt-1 relative">
                    <x-heroicon-o-envelope-open class="absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-text-muted)] w-5 h-5" />
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username"
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
                    <input id="password" name="password" type="password" required autocomplete="new-password"
                           class="appearance-none block w-full pl-10 pr-3 py-2 border border-[var(--color-border)] rounded-md shadow-sm placeholder-[var(--color-text-muted)] focus:outline-none focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] sm:text-sm">
                </div>
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-[var(--color-text-main)]">Konfirmasi Password</label>
                <div class="mt-1 relative">
                    <x-heroicon-o-lock-closed class="absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-text-muted)] w-5 h-5" />
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                           class="appearance-none block w-full pl-10 pr-3 py-2 border border-[var(--color-border)] rounded-md shadow-sm placeholder-[var(--color-text-muted)] focus:outline-none focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] sm:text-sm">
                </div>
            </div>

            <div>
                <button type="submit"
                        class="w-full flex justify-center items-center gap-2 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[var(--color-primary)] hover:bg-[var(--color-primary-dark)] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--color-primary)]">
                    <x-heroicon-o-user-plus class="w-5 h-5" />
                    Daftar
                </button>
            </div>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-[var(--color-text-muted)]">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-medium text-[var(--color-primary)] hover:text-[var(--color-primary-dark)] flex items-center gap-1 justify-center">
                    <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5" />
                    Masuk di sini
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
