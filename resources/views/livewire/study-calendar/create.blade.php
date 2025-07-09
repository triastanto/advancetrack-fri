<x-ui.page-container title="Kelola Kalender Studi Lanjut">
    <x-ui.card class="max-w-2xl mx-auto shadow-lg">
        <x-study-calendar.create-progress-bar :progress="$this->getProgressBarData()" />
        {{-- <x-study-calendar.progress.bar :progress="$this->getProgressBarData()" /> --}}

        {{-- Validation Error Summary --}}
        @if($this->getErrorStep() && $this->getErrorStep() !== $step)
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg" id="error-summary">
                <div class="flex items-center">
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-red-600 mr-2" />
                    <div>
                        <h4 class="text-sm font-medium text-red-800">
                            Ada kesalahan validasi pada langkah {{ $this->getErrorStep() }}
                        </h4>
                        <p class="text-sm text-red-700 mt-1">
                            Silakan perbaiki kesalahan tersebut sebelum melanjutkan. Klik tombol "Perbaiki Kesalahan" untuk kembali ke langkah yang bermasalah.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Data Preservation Notice --}}
        @if($this->getErrorStep() && $this->getErrorStep() !== $step && ($university_name || $study_program_name || $funding_source))
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center">
                    <x-heroicon-o-information-circle class="w-5 h-5 text-blue-600 mr-2" />
                    <div>
                        <h4 class="text-sm font-medium text-blue-800">
                            Data Anda Telah Disimpan
                        </h4>
                        <p class="text-sm text-blue-700 mt-1">
                            Data yang telah Anda isi pada langkah sebelumnya telah disimpan. Setelah memperbaiki kesalahan pada langkah ini, Anda dapat melanjutkan ke langkah berikutnya.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <form wire:submit.prevent="submit">
            @if ($step === 1)
                <div>
                    <h2 class="text-xl font-semibold mb-4 flex items-center">
                        <x-heroicon-o-calendar class="w-6 h-6 text-blue-500 mr-2" />
                        Informasi Dasar Studi
                    </h2>
                    <div class="mb-4">
                        <label for="start_date" class="block font-medium mb-1">Tanggal Mulai Studi <span class="text-red-500">*</span></label>
                        <input type="date" id="start_date" wire:model.defer="start_date" class="form-input w-full @error('start_date') border-red-500 @enderror" autocomplete="off" />
                        @error('start_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label for="end_date" class="block font-medium mb-1">Perkiraan Tanggal Selesai Studi <span class="text-red-500">*</span></label>
                        <input type="date" id="end_date" wire:model.defer="end_date" class="form-input w-full @error('end_date') border-red-500 @enderror" autocomplete="off" />
                        @error('end_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4 p-3 bg-blue-50 rounded text-blue-700 text-sm">
                        <x-heroicon-o-light-bulb class="w-5 h-5 inline mr-1" />
                        <span>Tips: Pastikan tanggal sesuai dengan rencana studi dan peraturan universitas.</span>
                    </div>
                </div>
            @elseif ($step === 2)
                <div>
                    <h2 class="text-xl font-semibold mb-4 flex items-center">
                        <x-heroicon-o-academic-cap class="w-6 h-6 text-blue-500 mr-2" />
                        Detail Studi Lanjut
                    </h2>
                    <div class="mb-4">
                        <label for="university_name" class="block font-medium mb-1">Nama Universitas <span class="text-red-500">*</span></label>
                        <input type="text" id="university_name" wire:model.defer="university_name" class="form-input w-full @error('university_name') border-red-500 @enderror" />
                        @error('university_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label for="university_address" class="block font-medium mb-1">Alamat Universitas <span class="text-red-500">*</span></label>
                        <input type="text" id="university_address" wire:model.defer="university_address" class="form-input w-full @error('university_address') border-red-500 @enderror" />
                        @error('university_address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label for="study_program_id" class="block font-medium mb-1">Nama Program Studi <span class="text-red-500">*</span></label>
                        <select id="study_program_id" wire:model.defer="study_program_id" class="form-select w-full @error('study_program_id') border-red-500 @enderror">
                            <option value="">Pilih Program Studi</option>
                            @foreach($availableStudyPrograms as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('study_program_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label for="study_level" class="block font-medium mb-1">Tingkat Studi <span class="text-red-500">*</span></label>
                        <select id="study_level" wire:model.defer="study_level" class="form-select w-full @error('study_level') border-red-500 @enderror">
                            <option value="">Pilih Tingkat Studi</option>
                            @foreach($this->studyLevels as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('study_level') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label for="scholarship" class="block font-medium mb-1">Beasiswa (Opsional)</label>
                        <input type="text" id="scholarship" wire:model.defer="scholarship" class="form-input w-full @error('scholarship') border-red-500 @enderror" />
                        @error('scholarship') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label for="funding_source" class="block font-medium mb-1">Sumber Pendanaan <span class="text-red-500">*</span></label>
                        <select id="funding_source" wire:model.defer="funding_source" class="form-select w-full @error('funding_source') border-red-500 @enderror">
                            <option value="">Pilih Sumber Pendanaan</option>
                            @foreach($this->fundingSources as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('funding_source') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label for="study_address" class="block font-medium mb-1">Alamat Selama Studi <span class="text-red-500">*</span></label>
                        <input type="text" id="study_address" wire:model.defer="study_address" class="form-input w-full @error('study_address') border-red-500 @enderror" />
                        @error('study_address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            @elseif ($step === 3)
                <div>
                    <h2 class="text-xl font-semibold mb-4 flex items-center">
                        <x-heroicon-o-clipboard-document-list class="w-6 h-6 text-blue-500 mr-2" />
                        Review & Konfirmasi
                    </h2>
                    <div class="mb-4 p-4 bg-blue-50 rounded-lg">
                        <h3 class="font-semibold mb-2 text-blue-700">Ringkasan Data</h3>
                        <ul class="text-sm text-gray-700 space-y-1">
                            <li><strong>Tanggal Mulai:</strong> {{ $start_date }}</li>
                            <li><strong>Tanggal Selesai:</strong> {{ $end_date }}</li>
                            <li><strong>Universitas:</strong> {{ $university_name }}</li>
                            <li><strong>Alamat Universitas:</strong> {{ $university_address }}</li>
                            <li><strong>Program Studi:</strong> {{ $availableStudyPrograms[$study_program_id] ?? '-' }}</li>
                            <li><strong>Tingkat Studi:</strong> {{ $studyLevels[$study_level] ?? $study_level }}</li>
                            <li><strong>Beasiswa:</strong> {{ $scholarship ?: '-' }}</li>
                            <li><strong>Sumber Pendanaan:</strong> {{ $fundingSources[$funding_source] ?? $funding_source }}</li>
                            <li><strong>Alamat Studi:</strong> {{ $study_address }}</li>
                        </ul>
                    </div>
                    <div class="mb-4 p-3 bg-blue-100 rounded text-blue-800 text-sm">
                        <x-heroicon-o-information-circle class="w-5 h-5 inline mr-1" />
                        Pastikan semua data sudah benar sebelum melanjutkan. Setelah submit, Anda dapat mengunggah dokumen persyaratan.
                    </div>
                    <div class="mb-4 flex items-center">
                        <input type="checkbox" id="agreement" wire:model.defer="agreed" class="mr-2 @error('agreed') border-red-500 @enderror" />
                        <label for="agreement" class="text-sm">Saya menyetujui syarat dan ketentuan pembuatan kalender studi.</label>
                    </div>
                    @error('agreed') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            @endif

            <div class="flex justify-between mt-8">
                @if ($step > 1)
                    <x-ui.button type="button" variant="secondary" wire:click="previousStep" :loading="$isLoading">
                        <x-heroicon-o-arrow-left class="w-4 h-4 mr-1" /> Sebelumnya
                    </x-ui.button>
                @else
                    <span></span>
                @endif

                @if ($step < 3)
                    <div class="flex space-x-2">
                        @if($this->hasErrorsOnOtherSteps())
                            <x-ui.button type="button" variant="danger" wire:click="goToErrorStep" :loading="$isLoading">
                                <x-heroicon-o-exclamation-triangle class="w-4 h-4 mr-1" /> Perbaiki Kesalahan
                            </x-ui.button>
                        @endif
                        <x-ui.button type="button" variant="primary" wire:click="nextStep" :loading="$isLoading">
                            Selanjutnya <x-heroicon-o-arrow-right class="w-4 h-4 ml-1" />
                        </x-ui.button>
                    </div>
                @else
                    <div class="flex space-x-2">
                        @if($this->hasErrorsOnOtherSteps())
                            <x-ui.button type="button" variant="danger" wire:click="goToErrorStep" :loading="$isLoading">
                                <x-heroicon-o-exclamation-triangle class="w-4 h-4 mr-1" /> Perbaiki Kesalahan
                            </x-ui.button>
                        @endif
                        <x-ui.button type="submit" variant="primary" :loading="$isLoading" loading-text="Menyimpan...">
                            <x-heroicon-o-check class="w-4 h-4 mr-1" /> Submit
                        </x-ui.button>
                    </div>
                @endif
            </div>
        </form>

        <div class="mt-6">
            <x-ui.alert-message />
        </div>
    </x-ui.card>

    <script>
        // Auto-scroll to error summary when validation errors occur
        document.addEventListener('livewire:load', function () {
            Livewire.on('validation-error', function () {
                const errorSummary = document.getElementById('error-summary');
                if (errorSummary) {
                    errorSummary.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    </script>
</x-ui.page-container>