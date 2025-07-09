@props(['requirementsStatus'])

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <x-heroicon-o-clipboard-document-list class="w-5 h-5 mr-2 text-blue-600" />
            Ringkasan Persyaratan
        </h3>
        <div class="flex items-center space-x-2">
            @if($requirementsStatus['all_requirements_met'])
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <x-heroicon-s-check-circle class="w-4 h-4 mr-1" />
                    Semua Persyaratan Terpenuhi
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                    <x-heroicon-s-exclamation-triangle class="w-4 h-4 mr-1" />
                    Persyaratan Belum Lengkap
                </span>
            @endif
        </div>
    </div>

    {{-- Overall Status --}}
    <div class="mt-6 mb-4 p-4 rounded-lg
        @if($requirementsStatus['all_requirements_met']) bg-green-50 border border-green-200 @else bg-yellow-50 border border-yellow-200 @endif">
        <div class="flex items-center">
            @if($requirementsStatus['all_requirements_met'])
                <x-heroicon-o-check-circle class="w-5 h-5 text-green-600 mr-2" />
                <div>
                    <h4 class="text-sm font-medium text-green-800">Siap Memulai Studi</h4>
                    <p class="text-sm text-green-700 mt-1">
                        Semua persyaratan telah terpenuhi. Anda dapat memulai program studi.
                    </p>
                </div>
            @else
                <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-yellow-600 mr-2" />
                <div>
                    <h4 class="text-sm font-medium text-yellow-800">Persyaratan Belum Lengkap</h4>
                    <p class="text-sm text-yellow-700 mt-1">
                        Beberapa persyaratan belum terpenuhi. Silakan lengkapi dokumen yang diperlukan sebelum memulai studi.
                    </p>
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Academic Documents Section --}}
        <div class="border border-gray-200 rounded-lg p-4">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-md font-medium text-gray-900 flex items-center">
                    <x-heroicon-o-document-text class="w-4 h-4 mr-2 text-blue-600" />
                    Persyaratan Studi Lanjut
                </h4>
                <a href="{{ route('documents.study-requirements') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    Kelola →
                </a>
            </div>

            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Status Verifikasi:</span>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium text-gray-900">
                            {{ $requirementsStatus['academic_documents']['verified'] }}/{{ $requirementsStatus['academic_documents']['total'] }}
                        </span>
                        @if($requirementsStatus['academic_documents']['complete'])
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-green-100">
                                <x-heroicon-s-check class="w-3 h-3 text-green-600" />
                            </span>
                        @else
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-yellow-100">
                                <x-heroicon-s-exclamation-triangle class="w-3 h-3 text-yellow-600" />
                            </span>
                        @endif
                    </div>
                </div>

                <div class="w-full bg-gray-200 rounded-full h-2">
                    @php
                        $percentage = $requirementsStatus['academic_documents']['total'] > 0
                            ? ($requirementsStatus['academic_documents']['verified'] / $requirementsStatus['academic_documents']['total']) * 100
                            : 0;
                    @endphp
                    <div class="h-2 rounded-full transition-all duration-300
                        @if($percentage === 100) bg-green-500 @elseif($percentage > 0) bg-yellow-500 @else bg-gray-300 @endif"
                         style="width: {{ $percentage }}%">
                    </div>
                </div>

                @if(!$requirementsStatus['academic_documents']['complete'])
                    <div class="text-xs text-yellow-600 bg-yellow-50 p-2 rounded">
                        <x-heroicon-o-information-circle class="w-3 h-3 inline mr-1" />
                        {{ $requirementsStatus['academic_documents']['total'] - $requirementsStatus['academic_documents']['verified'] }} dokumen belum diverifikasi
                    </div>
                @endif
            </div>
        </div>

        {{-- Approval Document Section --}}
        <div class="border border-gray-200 rounded-lg p-4">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-md font-medium text-gray-900 flex items-center">
                    <x-heroicon-o-document-check class="w-4 h-4 mr-2 text-green-600" />
                    Dokumen Persetujuan
                </h4>
                <a href="{{ route('documents.study-approvals') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    Kelola →
                </a>
            </div>

            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Status Persetujuan:</span>
                    <div class="flex items-center space-x-2">
                        @if($requirementsStatus['approval_document']['exists'])
                            @if($requirementsStatus['approval_document']['approved'])
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <x-heroicon-s-check-circle class="w-3 h-3 mr-1" />
                                    Disetujui
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <x-heroicon-s-clock class="w-3 h-3 mr-1" />
                                    Menunggu Persetujuan
                                </span>
                            @endif
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <x-heroicon-s-x-circle class="w-3 h-3 mr-1" />
                                Belum Diunggah
                            </span>
                        @endif
                    </div>
                </div>

                @if(!$requirementsStatus['approval_document']['exists'])
                    <div class="text-xs text-red-600 bg-red-50 p-2 rounded">
                        <x-heroicon-o-exclamation-triangle class="w-3 h-3 inline mr-1" />
                        Dokumen persetujuan studi lanjut belum diunggah
                    </div>
                @elseif(!$requirementsStatus['approval_document']['approved'])
                    <div class="text-xs text-yellow-600 bg-yellow-50 p-2 rounded">
                        <x-heroicon-o-clock class="w-3 h-3 inline mr-1" />
                        Dokumen persetujuan sedang dalam proses approval
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>