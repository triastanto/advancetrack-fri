{{-- Sidebar Toggle Button (Mobile Only) --}}
<div x-data="{ sidebarOpen: false }" class="bg-[var(--color-bg)]">
    <!-- Mobile Sidebar Toggle Button -->
    <button id="sidebar-toggle" @click="sidebarOpen = !sidebarOpen" class="md:hidden fixed top-8 right-10 z-50 p-2 rounded bg-[var(--color-bg-alt,rgba(0,0,0,0.04))] text-[var(--color-primary)] focus:outline-none">
        <span class="material-icons-outlined">menu</span>
    </button>

    <!-- Sidebar Container -->
    <aside id="sidebar" x-data="{ userMenuOpen: false }" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="flex flex-col h-screen w-72 bg-[var(--color-bg)] border-r border-[var(--color-border)] fixed top-0 left-0 z-40 transform md:translate-x-0 transition-transform duration-200 ease-in-out md:static md:flex md:translate-x-0">
        <!-- Sidebar Header / Logo -->
        <div class="flex items-center gap-3 px-10 py-6">
            <img src="/build/logo.png" alt="AdvanceTrack FRI Logo" class="h-8" />
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
                    <span class="material-icons-outlined text-sm" :class="{'rotate-180': userMenuOpen}">expand_less</span>
                </button>
                <!-- Dropdown User Menu -->
                <div id="user-menu" x-show="userMenuOpen" @click.away="userMenuOpen = false" x-transition class="absolute left-0 top-16 w-full bg-white border border-[var(--color-border)] rounded-lg shadow-lg z-50 overflow-hidden">
                    <!-- Settings Link -->
                    <a href="{{ route('settings.index') }}" class="flex items-center gap-2 px-3 py-2 text-base text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]">
                        <span class="material-icons-outlined text-xl">settings</span>
                        Pengaturan
                    </a>
                    <!-- Account Management -->
                    <a href="{{ route('settings.account') }}" class="flex items-center gap-2 px-3 py-2 text-base text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]">
                        <span class="material-icons-outlined text-xl">manage_accounts</span>
                        Manajemen Akun
                    </a>
                    <!-- Help Link -->
                    <a href="/help" class="flex items-center gap-2 px-3 py-2 text-base text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]">
                        <span class="material-icons-outlined text-xl">help_outline</span>
                        Bantuan
                    </a>
                    <hr class="my-1 border-t border-[var(--color-border)]">
                    <!-- Logout Form -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 w-full text-left px-3 py-2 text-base text-[var(--color-primary)] hover:bg-[var(--color-primary-bg)]">
                            <span class="material-icons-outlined text-xl">logout</span>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-4">
            <ul class="space-y-2 mt-4">
                <!-- Dashboard Link -->
                <li>
                    <a href="/dashboard" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('dashboard') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)]' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">
                        <span class="material-icons-outlined">dashboard</span>
                        Dashboard
                    </a>
                </li>
                <!-- Data Pribadi Link -->
                <li>
                    <a href="{{ route('lecturer.profile') ?? '/profile' }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->is('profile*') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">
                        <span class="material-icons-outlined">person</span>
                        Data Pribadi
                    </a>
                </li>
                <!-- Dokumen Saya Group -->
                <li x-data="{ submenuOpen: true }" class="relative">
                    <div class="flex items-center justify-between w-full gap-3 px-3 py-2 mb-1 rounded-lg cursor-pointer text-[var(--color-primary)] font-medium" @click="submenuOpen = !submenuOpen">
                        <div class="flex items-center gap-3">
                            <span class="material-icons-outlined">folder</span>
                            <span class="font-semibold">Dokumen Saya</span>
                        </div>
                        <span class="material-icons-outlined text-sm transition-transform" :class="{'rotate-180': submenuOpen}">expand_more</span>
                    </div>
                    <div x-show="submenuOpen" x-transition class="pl-6 mt-1 space-y-1">
                        <a href="{{ route('documents.study-requirements') }}" class="block py-2 px-3 rounded-lg {{ request()->routeIs('documents.study-requirements') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">Kelengkapan Studi Lanjut</a>
                        <a href="{{ route('documents.semester-reports') }}" class="block py-2 px-3 rounded-lg {{ request()->routeIs('documents.semester-reports') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">Laporan Per Semester</a>
                        <a href="{{ route('documents.final-reports') }}" class="block py-2 px-3 rounded-lg {{ request()->routeIs('documents.final-reports') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">Laporan Akhir & Kelulusan</a>
                        <a href="{{ route('documents.additional') }}" class="block py-2 px-3 rounded-lg {{ request()->routeIs('documents.additional') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">Dokumen Tambahan</a>
                        <a href="{{ route('documents.service-bond') }}" class="block py-2 px-3 rounded-lg {{ request()->routeIs('documents.service-bond') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">Perjanjian Ikatan Dinas</a>
                    </div>
                </li>
                <!-- Administrasi Dokumen Group -->
                <li x-data="{ adminSubmenuOpen: true }" class="relative">
                    <div class="flex items-center justify-between w-full gap-3 px-3 py-2 mb-1 rounded-lg cursor-pointer text-[var(--color-primary)] font-medium" @click="adminSubmenuOpen = !adminSubmenuOpen">
                        <div class="flex items-center gap-3">
                            <span class="material-icons-outlined">inventory_2</span>
                            <span class="font-semibold">Administrasi Dokumen</span>
                        </div>
                        <span class="material-icons-outlined text-sm transition-transform" :class="{'rotate-180': adminSubmenuOpen}">expand_more</span>
                    </div>
                    <div x-show="adminSubmenuOpen" x-transition class="pl-6 mt-1 space-y-1">
                        <a href="{{ route('admin.documents.lecturers') }}" class="block py-2 px-3 rounded-lg {{ request()->routeIs('admin.documents.lecturers') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">Cari & Pilih Dosen</a>
                        <a href="{{ route('admin.documents.upload') }}" class="block py-2 px-3 rounded-lg {{ request()->routeIs('admin.documents.upload') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">Unggah Dokumen</a>
                        <a href="{{ route('verification') }}" class="block py-2 px-3 rounded-lg {{ request()->routeIs('verification') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">Verifikasi Dokumen</a>
                    </div>
                </li>
                <!-- Monitoring & Laporan Link -->
                <li>
                    <a href="/reports" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->is('reports*') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">
                        <span class="material-icons-outlined">bar_chart</span>
                        Monitoring & Laporan
                    </a>
                </li>
                <!-- Notifikasi Link with Badge -->
                <li>
                    <a href="/notifications" class="flex items-center gap-3 px-3 py-2 rounded-lg relative {{ request()->is('notifications*') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">
                        <span class="material-icons-outlined">notifications</span>
                        Notifikasi
                        <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold bg-[var(--color-primary)] text-white absolute right-3 top-1/2 -translate-y-1/2">8</span>
                    </a>
                </li>
                <!-- Pengaturan Link -->
                <li>
                    <a href="/settings" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->is('settings*') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">
                        <span class="material-icons-outlined">settings</span>
                        Pengaturan
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
