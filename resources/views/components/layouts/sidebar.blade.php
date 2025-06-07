{{-- Sidebar Toggle Button (Mobile Only) --}}
<div x-data="{ sidebarOpen: false }">
    <!-- Mobile Sidebar Toggle Button -->
    <button id="sidebar-toggle" @click="sidebarOpen = !sidebarOpen" class="md:hidden fixed top-8 right-10 z-50 p-2 rounded bg-[var(--color-bg-alt,rgba(0,0,0,0.04))] text-[var(--color-primary)] focus:outline-none">
        <span class="material-icons-outlined">menu</span>
    </button>

    <!-- Sidebar Container -->
    <aside id="sidebar" x-data="{ userMenuOpen: false }" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="flex flex-col h-screen w-72 bg-[var(--color-bg)] border-r border-[var(--color-border)] fixed top-0 left-0 z-40 transform md:translate-x-0 transition-transform duration-200 ease-in-out md:static md:flex md:translate-x-0">
        <!-- Sidebar Header / Logo -->
        <div class="flex items-center gap-3 px-6 py-6">
            <img src="/build/logo.svg" alt="AdvanceTrack FRI Logo" class="h-8 w-8 rounded shadow-sm" />
            <span class="text-[var(--color-primary)] font-bold text-lg">AdvanceTrack FRI</span>
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
                <!-- Lecturers Link -->
                <li>
                    <a href="{{ route('lecturer') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('lecturer') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">
                        <span class="material-icons-outlined">groups</span>
                        Lecturers
                    </a>
                </li>
                <!-- Verifications Link with Badge -->
                <li>
                    <a href="/verifications" class="flex items-center gap-3 px-3 py-2 rounded-lg relative {{ request()->is('verifications*') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">
                        <span class="material-icons-outlined">verified</span>
                        Verifications
                        <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold bg-[var(--color-primary)] text-white absolute right-3 top-1/2 -translate-y-1/2">3</span>
                    </a>
                </li>
                <!-- Official Document Link with Badge -->
                <li>
                    <a href="/official-documents" class="flex items-center gap-3 px-3 py-2 rounded-lg relative {{ request()->is('official-documents*') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">
                        <span class="material-icons-outlined">description</span>
                        Official Document
                        <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold bg-[var(--color-primary)] text-white absolute right-3 top-1/2 -translate-y-1/2">5</span>
                    </a>
                </li>
                <!-- Reports Link -->
                <li>
                    <a href="/reports" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->is('reports*') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">
                        <span class="material-icons-outlined">bar_chart</span>
                        Reports
                    </a>
                </li>
                <!-- Notifications Link with Badge -->
                <li>
                    <a href="/notifications" class="flex items-center gap-3 px-3 py-2 rounded-lg relative {{ request()->is('notifications*') ? 'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' : 'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' }}">
                        <span class="material-icons-outlined">notifications</span>
                        Notifications
                        <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold bg-[var(--color-primary)] text-white absolute right-3 top-1/2 -translate-y-1/2">8</span>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- User Menu Section -->
        <div class="mt-auto px-6 pb-6">
            <div class="flex items-center gap-3 relative w-full">
                <!-- User Menu Toggle Button -->
                <button id="user-menu-toggle" @click="userMenuOpen = !userMenuOpen" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-[var(--color-primary-bg)] focus:outline-none">
                    <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="User" class="h-10 w-10 rounded-lg border border-[var(--color-border)]" />
                    <div class="flex-1 min-w-0 text-left">
                        <div class="font-semibold text-[var(--color-text-main)] truncate">Nama Pengguna</div>
                        <div class="text-xs text-[var(--color-text-secondary)]">Jabatan</div>
                    </div>
                    <span class="material-icons-outlined text-sm" :class="{'rotate-180': userMenuOpen}">expand_less</span>
                </button>
                <!-- Dropup User Menu -->
                <div id="user-menu" x-show="userMenuOpen" @click.away="userMenuOpen = false" x-transition class="absolute left-0 bottom-16 w-full bg-white border border-[var(--color-border)] rounded shadow-lg z-50">
                    <!-- Settings Link -->
                    <a href="/settings" class="flex items-center gap-2 px-3 py-2 text-base text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]">
                        <span class="material-icons-outlined text-xl">settings</span>
                        Settings
                    </a>
                    <!-- Help Link -->
                    <a href="/help" class="flex items-center gap-2 px-3 py-2 text-base text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]">
                        <span class="material-icons-outlined text-xl">help_outline</span>
                        Help
                    </a>
                    <hr class="my-1 border-t border-[var(--color-border)]">
                    <!-- Logout Form -->
                    <form method="POST" action="/logout">
                        <button type="submit" class="flex items-center gap-2 w-full text-left px-3 py-2 text-base text-[var(--color-primary)] hover:bg-[var(--color-primary-bg)]">
                            <span class="material-icons-outlined text-xl">logout</span>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>
</div>

<!-- Responsive Sidebar Toggle Button Style -->
<style>
    @media (min-width: 768px) {
        #sidebar-toggle { display: none; }
    }
</style>
