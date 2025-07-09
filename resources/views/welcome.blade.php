@extends('layouts.app')

@section('content')
<div class="p-10">
    <div class="flex items-center gap-4 mb-6">
        <img src="/logo.png" alt="AdvanceTrack FRI Logo" class="h-12" />
        <h1 class="text-3xl font-bold text-[#009444]">Selamat Datang di AdvanceTrack FRI</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-md p-6 mb-6">
        <p class="text-[#222222] text-lg mb-6">
            Sistem manajemen studi lanjut untuk Fakultas Rekayasa Industri. Pantau, kelola, dan optimalkan perjalanan akademik Anda dengan mudah.
        </p>
        <a href="{{ route('login') }}" class="inline-block bg-[#009444] text-white px-6 py-3 rounded-lg font-medium hover:bg-[#007a3a] transition-colors">
            Mulai Sekarang
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl shadow-md p-6">
            <h3 class="text-xl font-semibold text-[#222222] mb-3">Manajemen Studi</h3>
            <p class="text-[#222222]">Kelola jadwal kuliah, tugas, dan progress studi Anda dengan lebih efisien.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-md p-6">
            <h3 class="text-xl font-semibold text-[#222222] mb-3">Laporan & Rekapitulasi</h3>
            <p class="text-[#222222]">Akses laporan detail dan rekapitulasi kemajuan studi Anda secara real-time.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-md p-6">
            <h3 class="text-xl font-semibold text-[#222222] mb-3">Kalender Akademik</h3>
            <p class="text-[#222222]">Pantau jadwal penting dan deadline akademik dalam satu kalender terintegrasi.</p>
        </div>
    </div>
</div>
@endsection
