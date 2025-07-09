<div class="px-4 py-8 md:px-12 bg-gray-50 min-h-screen">
    <!-- Welcome Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-blue-900 flex items-center gap-2">
                <x-heroicon-o-academic-cap class="w-8 h-8 text-blue-700" />
                Selamat Datang, Dosen!
            </h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('study-calendar.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-medium shadow hover:bg-blue-700 transition">
                <x-heroicon-o-plus class="w-5 h-5 mr-2" /> Buat Kalender Studi
            </a>
        </div>
    </div>

    <!-- Alerts for incomplete or pending documents -->
    <div class="mb-8 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded">
        <div class="font-bold mb-1">Dokumen Belum Lengkap</div>
        <div>Beberapa dokumen persyaratan belum diunggah atau masih menunggu verifikasi.</div>
    </div>

    <!-- Document Status Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-blue-50 rounded-lg p-5 flex items-center gap-4">
            <x-heroicon-o-document-text class="w-10 h-10 text-blue-600" />
            <div>
                <p class="text-sm font-medium text-blue-900">Pending</p>
                <p class="text-2xl font-bold text-blue-700">0</p>
            </div>
        </div>
        <div class="bg-green-50 rounded-lg p-5 flex items-center gap-4">
            <x-heroicon-o-check-circle class="w-10 h-10 text-green-600" />
            <div>
                <p class="text-sm font-medium text-green-900">Terverifikasi</p>
                <p class="text-2xl font-bold text-green-700">0</p>
            </div>
        </div>
        <div class="bg-red-50 rounded-lg p-5 flex items-center gap-4">
            <x-heroicon-o-x-circle class="w-10 h-10 text-red-600" />
            <div>
                <p class="text-sm font-medium text-red-900">Ditolak</p>
                <p class="text-2xl font-bold text-red-700">0</p>
            </div>
        </div>
    </div>

    <!-- Info & Actions Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <div class="bg-white rounded-lg shadow p-6 flex items-center gap-4">
            <div class="flex-shrink-0">
                <x-heroicon-o-bell class="w-8 h-8 text-yellow-500" />
            </div>
            <div>
                <h2 class="text-lg font-semibold text-yellow-900 mb-1">Notifikasi Terbaru</h2>
                <p class="text-gray-700 text-sm">Anda tidak memiliki notifikasi baru saat ini.</p>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 flex items-center gap-4">
            <div class="flex-shrink-0">
                <x-heroicon-o-clock class="w-8 h-8 text-indigo-500" />
            </div>
            <div>
                <h2 class="text-lg font-semibold text-indigo-900 mb-1">Reminder Laporan Semester</h2>
                <p class="text-gray-700 text-sm">Tidak ada laporan semester yang perlu segera diisi.</p>
            </div>
        </div>
    </div>

    <!-- Kalender Studi Lanjut (Timeline & Status) -->
    <div class="mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-2">
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
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-lg font-semibold text-gray-900">Status & Riwayat Verifikasi Dokumen</h3>
                <a href="{{ route('documents.study-requirements') }}" class="text-blue-600 hover:underline text-sm">Lihat Semua</a>
            </div>
            <ul class="divide-y divide-gray-100 text-sm">
                <li class="py-2 text-gray-700">Belum ada riwayat verifikasi dokumen.</li>
            </ul>
        </div>
    </div>


</div>