{{-- Sidebar Toggle Button (Mobile Only) --}}
<div x-data="{ sidebarOpen: false }" class="bg-[var(--color-bg)]">
    <!-- Mobile Sidebar Toggle Button -->
    <button id="sidebar-toggle" @click="sidebarOpen = !sidebarOpen" class="lg:hidden fixed top-8 right-10 z-50 p-2 rounded bg-[var(--color-bg-alt,rgba(0,0,0,0.04))] text-[var(--color-primary)] focus:outline-none">
        <x-heroicon-o-bars-3 class="w-6 h-6" />
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
                    <x-heroicon-o-chevron-up class="w-4 h-4" x-bind:class="{'rotate-180': userMenuOpen}" />
                </button>
                <!-- Dropdown User Menu -->
                <div id="user-menu" x-show="userMenuOpen" @click.away="userMenuOpen = false" x-transition class="absolute left-0 top-16 w-full bg-white border border-[var(--color-border)] rounded-lg shadow-lg z-50 overflow-hidden">
                    <!-- Settings Link -->
                    <a href="{{ route('settings.index') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]">
                        <x-heroicon-o-cog-6-tooth class="w-5 h-5" />
                        Pengaturan
                    </a>
                    <!-- Account Management -->
                    <a href="{{ route('settings.account') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]">
                        <x-heroicon-o-user class="w-5 h-5" />
                        Manajemen Akun
                    </a>
                    <!-- Help Link -->
                    <a href="/help" class="flex items-center gap-2 px-3 py-2 text-sm text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]">
                        <x-heroicon-o-question-mark-circle class="w-5 h-5" />
                        Bantuan
                    </a>
                    <hr class="my-1 border-t border-[var(--color-border)]">
                    <!-- Logout Form -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 w-full text-left px-3 py-2 text-sm text-[var(--color-primary)] hover:bg-[var(--color-primary-bg)]">
                            <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5" />
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
                        <x-heroicon-o-squares-2x2 class="w-5 h-5" />
                        Dashboard
                    </a>
                </li>
                <!-- Notifikasi Link with Badge -->
                <li>
                    <a href="/notifications" class="flex items-center gap-3 px-3 py-2 rounded-lg relative text-sm {{ request()->is('notifications*') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">
                        <x-heroicon-o-bell class="w-5 h-5" />
                        Notifikasi
                        <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold bg-[var(--color-primary)] text-white absolute right-3 top-1/2 -translate-y-1/2">8</span>
                    </a>
                </li>
                <!-- Data Pribadi Link -->
                <li>
                    <a href="{{ route('profile.index') ?? '/profile' }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->is('profile*') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">
                        <x-heroicon-o-user class="w-5 h-5" />
                        Data Pribadi
                    </a>
                </li>
                <!-- Dokumen Saya Group -->
                <li>
                    <div class="py-1">
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Dokumen Saya</span>
                    </div>
                    <a href="{{ route('documents.study-requirements') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('documents.study-requirements') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">
                        <x-heroicon-o-document-text class="w-5 h-5" />
                        Persyaratan Studi Lanjut
                    </a>
                    <a href="{{ route('documents.semester-reports') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('documents.semester-reports') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">
                        <x-heroicon-o-clipboard-document-list class="w-5 h-5" />
                        Laporan Per Semester
                    </a>
                    <a href="{{ route('documents.final-reports') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('documents.final-reports') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">
                        <x-heroicon-o-check-circle class="w-5 h-5" />
                        Laporan Akhir & Kelulusan
                    </a>
                    <a href="{{ route('documents.approval-documents') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('documents.approval-documents') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">
                        <x-heroicon-o-calendar-days class="w-5 h-5" />
                        Persetujuan Studi Lanjut
                    </a>
                </li>
                <!-- Administrasi Dokumen Group -->
                <li>
                    <div class="py-1 mt-3">
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Administrasi Dokumen</span>
                    </div>
                    <a href="{{ route('administrations.lecturers') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.documents.lecturers') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">
                        <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                        Cari & Pilih Dosen
                    </a>
                    <a href="{{ route('administrations.upload') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.documents.upload') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">
                        <x-heroicon-o-cloud-arrow-up class="w-5 h-5" />
                        Unggah Persetujuan Studi Lanjut
                    </a>
                    <a href="{{ route('administrations.verification') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('verification') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">
                        <x-heroicon-o-check-circle class="w-5 h-5" />
                        Verifikasi Dokumen
                    </a>
                </li>
                <!-- Monitoring & Laporan Group -->
                <li>
                    <div class="py-1 mt-3">
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Monitoring & Laporan</span>
                    </div>
                    <a href="/monitoring/analytics" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]">
                        <x-heroicon-o-chart-bar class="w-5 h-5" />
                        Dashboard Analytics
                    </a>
                    <a href="/reports/lecturers" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]">
                        <x-heroicon-o-users class="w-5 h-5" />
                        Laporan Dosen
                    </a>
                    <a href="/monitoring/documents" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]">
                        <x-heroicon-o-document-chart-bar class="w-5 h-5" />
                        Status Dokumen
                    </a>
                    <a href="/audit" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]">
                        <x-heroicon-o-shield-check class="w-5 h-5" />
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
