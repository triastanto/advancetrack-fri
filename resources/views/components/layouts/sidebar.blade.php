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
            <img src="/logo.png" alt="AdvanceTrack FRI Logo" class="h-10" />
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
                    <a href={{ route('help') }} class="flex items-center gap-2 px-3 py-2 text-sm text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]">
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
            @auth
            @php
                $role = Auth::user()->employee->role ?? null;
                $position = Auth::user()->employee->position ?? null;
                $roleLabel = [
                    'lecturer' => 'Dosen Studi Lanjut',
                    'hr_finance_staff' => 'Staf SDM & Keuangan',
                    'head_of_hr_finance' => 'Kepala Urusan SDM & Keuangan',
                    'fri_vice_dean' => 'Wakil Dekan II FRI',
                    'head_of_study_program' => 'Ketua Program Studi',
                    'head_of_research_group' => 'Ketua Kelompok Keilmuan',
                ][$role] ?? ucfirst($role);
            @endphp
            <div class="mt-3 bg-[var(--color-primary-bg)] rounded-lg px-3 py-2 text-xs text-[var(--color-text-main)]">
                <div class="font-semibold">Peran: <span class="font-normal">{{ $roleLabel ?? '-' }}</span></div>
                @if($position)
                <div class="font-semibold">Jabatan: <span class="font-normal">{{ $position }}</span></div>
                @endif
            </div>
            @endauth
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-4">
            <ul class="mt-4">
                <!-- Dashboard Section -->
                <x-sidebar.dashboard />
                @auth
                @if(Auth::user()->employee && Auth::user()->employee->role === 'lecturer')
                    <x-sidebar.personal-data />
                @endif
                <x-sidebar.study-calendar />
                @if(Auth::user()->employee && Auth::user()->employee->role === 'lecturer')
                    <x-sidebar.documents />
                @endif
                @if(Auth::user()->employee && Auth::user()->employee->role !== 'lecturer')
                    <x-sidebar.administration />
                    <x-sidebar.monitoring />
                @endif
                @endauth
            </ul>
        </nav>
    </aside>
</div>
