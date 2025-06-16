{{-- Sidebar Toggle Button (Mobile Only) --}}
<div x-data="{ sidebarOpen: false }" class="bg-[var(--color-bg)]">
    <!-- Mobile Sidebar Toggle Button -->
    <button id="sidebar-toggle" @click="sidebarOpen = !sidebarOpen" class="lg:hidden fixed top-8 right-10 z-50 p-2 rounded bg-[var(--color-bg-alt,rgba(0,0,0,0.04))] text-[var(--color-primary)] focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>

    <!-- Sidebar Container -->
    <aside id="sidebar" x-data="{ userMenuOpen: false }" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="flex flex-col h-screen w-72 bg-[var(--color-bg)] border-r border-[var(--color-border)] fixed top-0 left-0 z-40 transform lg:translate-x-0 transition-transform duration-200 ease-in-out lg:static lg:flex">
        <!-- Sidebar Header / Logo -->
        <div class="flex items-center gap-3 px-10 py-7">
            <img src="/build/logo.png" alt="AdvanceTrack FRI Logo" class="h-10" />
            <span class="text-[var(--color-primary)] font-bold text-lg">AdvanceTrack FRI</span>
        </div>

        <!-- User Menu Section -->
        <div class="px-6 pb-4">
            <div class="flex items-center gap-3 relative w-full">
                <!-- User Menu Toggle Button -->
                <button id="user-menu-toggle" @click="userMenuOpen = !userMenuOpen" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-[var(--color-primary-bg)] focus:outline-none">
                    @auth
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-[var(--color-primary)] flex items-center justify-center text-white font-medium text-sm">
                            {{ Auth::user()->initials() ?? substr(Auth::user()->name ?? 'U', 0, 1) }}
                        </div>
                    </div>
                    <div class="flex-1 min-w-0 text-left">
                        <div class="font-semibold text-[var(--color-text-main)] truncate">{{ Auth::user()->name ?? 'User' }}</div>
                        <div class="text-xs text-[var(--color-text-secondary)]">{{ Auth::user()->email ?? '' }}</div>
                    </div>
                    @else
                    <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="User" class="h-10 w-10 rounded-lg border border-[var(--color-border)]" />
                    <div class="flex-1 min-w-0 text-left">
                        <div class="font-semibold text-[var(--color-text-main)] truncate">Nama Pengguna</div>
                        <div class="text-xs text-[var(--color-text-secondary)]">Jabatan</div>
                    </div>
                    @endauth
                    <svg class="w-4 h-4" :class="{'rotate-180': userMenuOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                    </svg>
                </button>
                <!-- Dropdown User Menu -->
                <div id="user-menu" x-show="userMenuOpen" @click.away="userMenuOpen = false" x-transition class="absolute left-0 top-16 w-full bg-white border border-[var(--color-border)] rounded-lg shadow-lg z-50 overflow-hidden">
                    <!-- Settings Link -->
                    <a href="{{ route('settings.index') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Pengaturan
                    </a>
                    <!-- Account Management -->
                    <a href="{{ route('settings.account') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Manajemen Akun
                    </a>
                    <!-- Help Link -->
                    <a href="/help" class="flex items-center gap-2 px-3 py-2 text-sm text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Bantuan
                    </a>
                    <hr class="my-1 border-t border-[var(--color-border)]">
                    <!-- Logout Form -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 w-full text-left px-3 py-2 text-sm text-[var(--color-primary)] hover:bg-[var(--color-primary-bg)]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-4">
            <ul class="mt-4">
                <!-- Dashboard Link -->
                <li>
                    <a href="/dashboard" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('dashboard') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)]' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <!-- Notifikasi Link with Badge -->
                <li>
                    <a href="/notifications" class="flex items-center gap-3 px-3 py-2 rounded-lg relative text-sm {{ request()->is('notifications*') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5-5V9.09a6 6 0 10-12 0V12l-5 5h5m7 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        Notifikasi
                        <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold bg-[var(--color-primary)] text-white absolute right-3 top-1/2 -translate-y-1/2">8</span>
                    </a>
                </li>
                <!-- Data Pribadi Link -->
                <li>
                    <a href="{{ route('profile.index') ?? '/profile' }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->is('profile*') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Data Pribadi
                    </a>
                </li>
                <!-- Dokumen Saya Group -->
                <li>
                    <div class="py-1">
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Dokumen Saya</span>
                    </div>
                    <a href="{{ route('documents.study-requirements') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('documents.study-requirements') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Kelengkapan Studi Lanjut
                    </a>
                    <a href="{{ route('documents.semester-reports') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('documents.semester-reports') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        Laporan Per Semester
                    </a>
                    <a href="{{ route('documents.final-reports') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('documents.final-reports') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Laporan Akhir & Kelulusan
                    </a>
                    <a href="{{ route('documents.fsdp-documents') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('documents.fsdp-documents') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 1v6m8-6v6"></path>
                        </svg>
                        Dokumen FSDP
                    </a>
                </li>
                <!-- Administrasi Dokumen Group -->
                <li>
                    <div class="py-1 mt-3">
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Administrasi Dokumen</span>
                    </div>
                    <a href="{{ route('administrations.lecturers') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.documents.lecturers') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"></path>
                        </svg>
                        Cari & Pilih Dosen
                    </a>
                    <a href="{{ route('administrations.upload') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.documents.upload') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Unggah Dokumen FSDP
                    </a>
                    <a href="{{ route('administrations.verification') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('verification') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Verifikasi Dokumen
                    </a>
                </li>
                <!-- Monitoring & Laporan Group -->
                <li>
                    <div class="py-1 mt-3">
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Monitoring & Laporan</span>
                    </div>
                    <a href="/monitoring/analytics" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Dashboard Analytics
                    </a>
                    <a href="/reports/lecturers" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-5a4 4 0 11-8 0 4 4 0 018 0zm6 6v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-2a6 6 0 0112 0z"></path>
                        </svg>
                        Laporan Dosen
                    </a>
                    <a href="/monitoring/documents" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Status Dokumen
                    </a>
                    <a href="/audit" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Audit & Log
                    </a>
                </li>
            </ul>
        </nav>
    </aside>
</div>

<!-- Responsive Sidebar Toggle Button Style -->
<style>
    @media (min-width: 768px) {
        #sidebar-toggle { display: none; }
    }
</style>
