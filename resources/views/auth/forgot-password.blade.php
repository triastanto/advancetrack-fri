@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[var(--color-bg)]">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <div class="mb-6 text-center">
            <img src="/build/logo.png" alt="AdvanceTrack FRI Logo" class="h-12 mx-auto mb-4" />
            <h2 class="text-2xl font-bold text-[var(--color-text-main)]">Lupa Password</h2>
            <p class="mt-2 text-sm text-[var(--color-text-muted)]">Masukkan email Anda untuk menerima link reset password</p>
        </div>

        @if (session('status'))
            <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                <div class="flex items-center gap-2">
                    <span class="material-icons-outlined text-sm">check_circle</span>
                    <p>{{ session('status') }}</p>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-[var(--color-text-main)]">Email</label>
                <div class="mt-1 relative">
                    <span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-text-muted)]">email</span>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                           class="appearance-none block w-full pl-10 pr-3 py-2 border border-[var(--color-border)] rounded-md shadow-sm placeholder-[var(--color-text-muted)] focus:outline-none focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] sm:text-sm">
                </div>
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <button type="submit"
                        class="w-full flex justify-center items-center gap-2 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[var(--color-primary)] hover:bg-[var(--color-primary-dark)] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--color-primary)]">
                    <span class="material-icons-outlined text-sm">send</span>
                    Kirim Link Reset Password
                </button>
            </div>
        </form>

            <div class="mt-6 text-center">
            <p class="text-sm text-[var(--color-text-muted)]">
                Ingat password Anda?
                <a href="{{ route('login') }}" class="font-medium text-[var(--color-primary)] hover:text-[var(--color-primary-dark)] flex items-center gap-1 justify-center">
                    <span class="material-icons-outlined text-sm">arrow_back</span>
                    Kembali ke halaman login
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
