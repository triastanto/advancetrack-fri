@if ($role === 'lecturer')
    <div class="px-4 py-8 md:px-12 bg-gray-50 min-h-screen">
        <!-- Welcome Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-blue-900 flex items-center gap-2">
                    <x-heroicon-o-academic-cap class="w-8 h-8 text-blue-700" />
                    Selamat Datang, Dosen!
                </h1>
            </div>
        </div>

        <!-- Document Status Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-document-text class="w-8 h-8 text-blue-500" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pending</p>
                        <p class="text-2xl font-semibold text-gray-900">0</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-check-circle class="w-8 h-8 text-green-500" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Terverifikasi</p>
                        <p class="text-2xl font-semibold text-gray-900">0</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-x-circle class="w-8 h-8 text-red-500" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Ditolak</p>
                        <p class="text-2xl font-semibold text-gray-900">0</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info & Actions Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-bell class="w-8 h-8 text-yellow-500" />
                    </div>
                    <div class="ml-4">
                        <h2 class="text-lg font-semibold text-gray-900 mb-1">Notifikasi Terbaru</h2>
                        <p class="text-gray-700 text-sm">Anda tidak memiliki notifikasi baru saat ini.</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-clock class="w-8 h-8 text-indigo-500" />
                    </div>
                    <div class="ml-4">
                        <h2 class="text-lg font-semibold text-gray-900 mb-1">Reminder Laporan Semester</h2>
                        <p class="text-gray-700 text-sm">Tidak ada laporan semester yang perlu segera diisi.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kalender Studi Lanjut (Timeline & Status) -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Kalender Studi Lanjut</h3>
                    <a href="{{ route('study-calendar.manage') }}" class="text-blue-600 hover:underline text-sm">Kelola Kalender</a>
                </div>
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="flex items-center space-x-4">
                        <span class="inline-block w-6 h-6 text-blue-500 mr-2 align-middle">📅</span>
                        <span class="text-gray-700">Belum ada kalender studi. <a href="{{ route('study-calendar.create') }}" class="text-blue-600 hover:underline">Buat sekarang</a></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status & Riwayat Verifikasi Dokumen -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Status & Riwayat Verifikasi Dokumen</h3>
                    <a href="{{ route('documents.study-requirements') }}" class="text-blue-600 hover:underline text-sm">Lihat Semua</a>
                </div>
                <ul class="divide-y divide-gray-100 text-sm">
                    <li class="py-2 text-gray-700">Belum ada riwayat verifikasi dokumen.</li>
                </ul>
            </div>
        </div>
    </div>
@else
    <livewire:monitoring.analytics />
@endif