@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[var(--color-bg)]">
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <div class="mb-6 text-center">
            <img src="/logo.png" alt="AdvanceTrack FRI Logo" class="h-12 mx-auto mb-4" />
            <h2 class="text-2xl font-bold text-[var(--color-text-main)]">Verifikasi Email</h2>
            <p class="mt-2 text-sm text-[var(--color-text-muted)]">Langkah terakhir untuk mengaktifkan akun Anda</p>
        </div>

        <div class="mb-6">
            <div class="flex items-center justify-center mb-4">
                <div class="bg-blue-100 p-3 rounded-full">
                    <x-heroicon-o-envelope class="w-8 h-8 text-blue-600" />
                </div>
            </div>
            
            <div class="text-center">
                <h3 class="text-lg font-semibold text-[var(--color-text-main)] mb-2">
                    Terima kasih telah mendaftar!
                </h3>
                <p class="text-sm text-[var(--color-text-muted)] mb-4">
                    Sebelum Anda dapat mengakses sistem, kami perlu memverifikasi alamat email Anda. 
                    Kami telah mengirimkan link verifikasi ke:
                </p>
                <p class="font-medium text-[var(--color-text-main)] mb-4">
                    {{ auth()->user()->email }}
                </p>
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <x-heroicon-o-information-circle class="w-5 h-5 text-blue-600 mt-0.5 mr-2 flex-shrink-0" />
                <div class="text-sm text-blue-800">
                    <p class="font-medium mb-1">Penting:</p>
                    <ul class="list-disc list-inside space-y-1">
                        <li>Periksa folder spam jika email tidak muncul di inbox</li>
                        <li>Link verifikasi akan kadaluarsa dalam 60 menit</li>
                        <li>Pastikan email yang Anda masukkan sudah benar</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                        class="w-full flex justify-center items-center gap-2 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[var(--color-primary)] hover:bg-[var(--color-primary-dark)] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--color-primary)]">
                    <x-heroicon-o-envelope class="w-5 h-5" />
                    Kirim Ulang Email Verifikasi
                </button>
            </form>

            <div class="text-center">
                <p class="text-sm text-[var(--color-text-muted)]">
                    Sudah verifikasi email?
                    <a href="{{ route('dashboard') }}" class="font-medium text-[var(--color-primary)] hover:text-[var(--color-primary-dark)]">
                        Lanjutkan ke Dashboard
                    </a>
                </p>
            </div>
        </div>

        @if (session('success'))
            <div class="mt-4 p-3 bg-green-100 border border-green-200 rounded-md">
                <div class="flex items-center">
                    <x-heroicon-o-check-circle class="w-5 h-5 text-green-600 mr-2" />
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <div class="mt-6 text-center">
            <p class="text-sm text-[var(--color-text-muted)]">
                Masalah dengan verifikasi email?
                <a href="{{ route('help') }}" class="font-medium text-[var(--color-primary)] hover:text-[var(--color-primary-dark)]">
                    Hubungi Admin
                </a>
            </p>
        </div>
    </div>
</div>
@endsection 