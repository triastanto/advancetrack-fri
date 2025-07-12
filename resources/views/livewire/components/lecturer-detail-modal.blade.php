<div>
    @if($isOpen && $lecturer)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true"
         x-data="{ show: false }"
         x-init="$nextTick(() => { if (@js($isOpen)) { show = true; } })"
         x-show="show"
         @keydown.escape.window="show = false; setTimeout(() => $wire.close(), 200)"
         style="display: none;"
         x-cloak>
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-30 backdrop-blur-sm"></div>

        <!-- Modal content container -->
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <!-- Modal content -->
                <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl sm:my-8 sm:w-full sm:max-w-6xl sm:p-6"
                     @click.stop
                     x-show="show"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95">

                    <!-- Modal Header -->
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 rounded-full bg-[#f3f3f3] flex items-center justify-center shadow-inner overflow-hidden">
                                @if (!empty($lecturer->photo))
                                    <img src="{{ $lecturer->photo }}" alt="Foto {{ $lecturer->user->name }}" class="w-16 h-16 rounded-full object-cover" />
                                @else
                                    <span class="text-2xl font-bold text-[#009444]">{{ strtoupper(Str::substr($lecturer->user->name, 0, 1)) }}</span>
                                @endif
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900" id="modal-title">
                                    {{ $lecturer->user->name }}
                                </h3>
                                <p class="text-sm text-gray-500">{{ $lecturer->position }} • {{ $this->getRoleLabel($lecturer->role) }}</p>
                            </div>
                        </div>
                        <button
                            wire:click="close"
                            class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-[#009444] focus:ring-offset-2"
                        >
                            <x-heroicon-o-x-mark class="h-6 w-6" />
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="space-y-6">
                        <!-- Personal Information Section -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <x-heroicon-o-user class="w-5 h-5 mr-2 text-[#009444]" />
                                Informasi Pribadi
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">NIDN</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $lecturer->nidn ?? 'Tidak tersedia' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $lecturer->user->email }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tempat Lahir</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $lecturer->birth_place ?? 'Tidak tersedia' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $lecturer->birth_date ? $lecturer->birth_date->format('d F Y') : 'Tidak tersedia' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $this->getGenderLabel($lecturer->gender) }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Jabatan Fungsional</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $lecturer->functional_position ?? 'Tidak tersedia' }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Alamat Asal</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $lecturer->origin_address ?? 'Tidak tersedia' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Telepon</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $lecturer->contact_phone ?? 'Tidak tersedia' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email Kontak</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $lecturer->contact_email ?? 'Tidak tersedia' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Research Lab Information -->
                        @if($lecturer->researchLab)
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <x-heroicon-o-beaker class="w-5 h-5 mr-2 text-[#009444]" />
                                Laboratorium Riset
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Laboratorium</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $lecturer->researchLab->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Kelompok Riset</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $lecturer->researchLab->researchGroup->name }}</p>
                                </div>
                                @if($lecturer->is_lab_head)
                                <div class="md:col-span-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#009444] text-white">
                                        <x-heroicon-o-star class="w-4 h-4 mr-1" />
                                        Ketua Laboratorium
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Education History Section -->
                        @if($educations->count() > 0)
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <x-heroicon-o-academic-cap class="w-5 h-5 mr-2 text-[#009444]" />
                                Riwayat Pendidikan
                            </h4>
                            <div class="space-y-4">
                                @foreach($educations as $education)
                                <div class="bg-white rounded-lg p-4 border border-gray-200">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h5 class="font-semibold text-gray-900">{{ $education->degree }}</h5>
                                            <p class="text-sm text-gray-600">{{ $education->major }}</p>
                                            <p class="text-sm text-gray-500">{{ $education->institution }}</p>
                                        </div>
                                        <div class="text-right">
                                            @if($education->graduation_year)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $education->graduation_year }}
                                                </span>
                                            @endif
                                            @if($education->gpa)
                                                <div class="mt-1 text-sm text-gray-500">
                                                    IPK: {{ number_format($education->gpa, 2) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Study Calendar Section -->
                        @if($studyCalendars->count() > 0)
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <x-heroicon-o-calendar class="w-5 h-5 mr-2 text-[#009444]" />
                                Kalender Studi
                            </h4>
                            <div class="space-y-4">
                                @foreach($studyCalendars as $calendar)
                                <div class="bg-white rounded-lg p-4 border border-gray-200">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h5 class="font-semibold text-gray-900">
                                                {{ $calendar->studyDetail->university_name ?? 'Universitas tidak tersedia' }}
                                            </h5>
                                            @if($calendar->studyDetail)
                                                <p class="text-sm text-gray-600">{{ $calendar->studyDetail->studyProgram->name ?? 'Program studi tidak tersedia' }}</p>
                                                <p class="text-sm text-gray-500">{{ $calendar->studyDetail->study_level ?? 'Level studi tidak tersedia' }}</p>
                                            @endif
                                        </div>
                                        @php $state = $this->getWorkflowStateLabel($calendar->workflow_state); @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white {{ $state['color'] }}">
                                            {{ $state['label'] }}
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <label class="font-medium text-gray-700">Mulai Studi</label>
                                            <p class="text-gray-900">{{ $calendar->study_start ? \Carbon\Carbon::parse($calendar->study_start)->format('d F Y') : 'Tidak tersedia' }}</p>
                                        </div>
                                        <div>
                                            <label class="font-medium text-gray-700">Estimasi Selesai</label>
                                            <p class="text-gray-900">{{ $calendar->estimated_study_end ? \Carbon\Carbon::parse($calendar->estimated_study_end)->format('d F Y') : 'Tidak tersedia' }}</p>
                                        </div>
                                        @if($calendar->graduation_date)
                                        <div>
                                            <label class="font-medium text-gray-700">Tanggal Lulus</label>
                                            <p class="text-gray-900">{{ \Carbon\Carbon::parse($calendar->graduation_date)->format('d F Y') }}</p>
                                        </div>
                                        @endif
                                    </div>
                                    @if($calendar->studyDetail && $calendar->studyDetail->promotors->count() > 0)
                                    <div class="mt-3 pt-3 border-t border-gray-200">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Promotor/Pembimbing</label>
                                        <div class="space-y-1">
                                            @foreach($calendar->studyDetail->promotors as $promotor)
                                            <div class="flex items-center justify-between text-sm">
                                                <span class="text-gray-900">{{ $promotor->name }}</span>
                                                @if($promotor->is_primary)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-[#009444] text-white">
                                                        Utama
                                                    </span>
                                                @endif
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Course Responsibilities Section -->
                        @if($courseResponsibilities->count() > 0)
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <x-heroicon-o-book-open class="w-5 h-5 mr-2 text-[#009444]" />
                                Tanggung Jawab Mata Kuliah
                            </h4>
                            <div class="space-y-3">
                                @foreach($courseResponsibilities as $course)
                                <div class="bg-white rounded-lg p-4 border border-gray-200">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <h5 class="font-semibold text-gray-900">{{ $course->course_name }}</h5>
                                            @if($course->semester || $course->academic_year)
                                                <p class="text-sm text-gray-600">
                                                    @if($course->semester) Semester {{ $course->semester }}@endif
                                                    @if($course->academic_year) • Tahun {{ $course->academic_year }}@endif
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Study Programs Section -->
                        @if($lecturer->studyPrograms->count() > 0)
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <x-heroicon-o-academic-cap class="w-5 h-5 mr-2 text-[#009444]" />
                                Program Studi
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($lecturer->studyPrograms as $program)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ $program->name }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Modal Footer -->
                    <div class="mt-6 flex justify-end">
                        <button
                            wire:click="close"
                            class="inline-flex justify-center rounded-md border border-transparent bg-[#009444] px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-[#007a35] focus:outline-none focus:ring-2 focus:ring-[#009444] focus:ring-offset-2"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div> 