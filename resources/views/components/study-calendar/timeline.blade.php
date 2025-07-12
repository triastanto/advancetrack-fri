@props(['timeline'])

    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <x-heroicon-o-clock class="w-5 h-5 mr-2 text-blue-600" />
            Timeline & Riwayat Studi
        </h3>
        <span class="text-sm text-gray-600">{{ $timeline->count() }} aktivitas</span>
    </div>

    @if($timeline->count() > 0)
        <div class="flow-root">
            <ul role="list" class="-mb-8">
                @foreach($timeline as $index => $activity)
                    @php
                        // Normalize to object for property access
                        if (is_array($activity)) {
                            $activity = (object) $activity;
                        }
                        $transitionName = $activity->transition_name ?? null;
                    @endphp
                    <li>
                        <div class="relative pb-8">
                            @if($index < $timeline->count() - 1)
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                            @endif
                            <div class="relative flex space-x-3">
                                <div>
                                    <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white
                                        @switch($transitionName)
                                            @case('SUBMIT_STUDY')
                                                bg-blue-500
                                                @break
                                            @case('APPROVE_STUDY')
                                                bg-green-500
                                                @break
                                            @case('REJECT_STUDY')
                                                bg-red-500
                                                @break
                                            @case('RESUBMIT_STUDY')
                                                bg-blue-500
                                                @break
                                            @case('START_STUDY')
                                                bg-green-500
                                                @break
                                            @case('TAKE_LEAVE')
                                                bg-yellow-500
                                                @break
                                            @case('RETURN_FROM_LEAVE')
                                                bg-green-500
                                                @break
                                            @case('COMPLETE_STUDY')
                                                bg-purple-500
                                                @break
                                            @case('DROP_OUT_ACTIVE')
                                            @case('DROP_OUT_LEAVE')
                                                bg-red-500
                                                @break
                                            @default
                                                bg-gray-500
                                        @endswitch">
                                        @switch($transitionName)
                                            @case('SUBMIT_STUDY')
                                                <x-heroicon-s-cloud-arrow-up class="h-5 w-5 text-white" />
                                                @break
                                            @case('APPROVE_STUDY')
                                                <x-heroicon-s-check class="h-5 w-5 text-white" />
                                                @break
                                            @case('REJECT_STUDY')
                                                <x-heroicon-s-x-mark class="h-5 w-5 text-white" />
                                                @break
                                            @case('RESUBMIT_STUDY')
                                                <x-heroicon-s-arrow-path class="h-5 w-5 text-white" />
                                                @break
                                            @case('START_STUDY')
                                                <x-heroicon-s-play class="h-5 w-5 text-white" />
                                                @break
                                            @case('TAKE_LEAVE')
                                                <x-heroicon-s-pause class="h-5 w-5 text-white" />
                                                @break
                                            @case('RETURN_FROM_LEAVE')
                                                <x-heroicon-s-play class="h-5 w-5 text-white" />
                                                @break
                                            @case('COMPLETE_STUDY')
                                                <x-heroicon-s-academic-cap class="h-5 w-5 text-white" />
                                                @break
                                            @case('DROP_OUT_ACTIVE')
                                            @case('DROP_OUT_LEAVE')
                                                <x-heroicon-s-x-mark class="h-5 w-5 text-white" />
                                                @break
                                            @default
                                                <x-heroicon-s-cog class="h-5 w-5 text-white" />
                                        @endswitch
                                    </span>
                                </div>
                                <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <p class="text-sm font-medium text-gray-900">
                                                @switch($transitionName)
                                                    @case('SUBMIT_STUDY')
                                                        Kalender Studi Diajukan
                                                        @break
                                                    @case('APPROVE_STUDY')
                                                        Kalender Studi Disetujui
                                                        @break
                                                    @case('REJECT_STUDY')
                                                        Kalender Studi Ditolak
                                                        @break
                                                    @case('RESUBMIT_STUDY')
                                                        Kalender Studi Diajukan Ulang
                                                        @break
                                                    @case('START_STUDY')
                                                        Studi Dimulai
                                                        @break
                                                    @case('TAKE_LEAVE')
                                                        Cuti Diambil
                                                        @break
                                                    @case('RETURN_FROM_LEAVE')
                                                        Kembali dari Cuti
                                                        @break
                                                    @case('COMPLETE_STUDY')
                                                        Studi Selesai
                                                        @break
                                                    @case('DROP_OUT_ACTIVE')
                                                    @case('DROP_OUT_LEAVE')
                                                        Studi Dihentikan
                                                        @break
                                                    @default
                                                        {{ $transitionName ?? 'Tidak diketahui' }}
                                                @endswitch
                                            </p>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                @switch($transitionName)
                                                    @case('SUBMIT_STUDY')
                                                        bg-blue-100 text-blue-800
                                                        @break
                                                    @case('APPROVE_STUDY')
                                                        bg-green-100 text-green-800
                                                        @break
                                                    @case('REJECT_STUDY')
                                                        bg-red-100 text-red-800
                                                        @break
                                                    @case('RESUBMIT_STUDY')
                                                        bg-blue-100 text-blue-800
                                                        @break
                                                    @case('START_STUDY')
                                                        bg-green-100 text-green-800
                                                        @break
                                                    @case('TAKE_LEAVE')
                                                        bg-yellow-100 text-yellow-800
                                                        @break
                                                    @case('RETURN_FROM_LEAVE')
                                                        bg-green-100 text-green-800
                                                        @break
                                                    @case('COMPLETE_STUDY')
                                                        bg-purple-100 text-purple-800
                                                        @break
                                                    @case('DROP_OUT_ACTIVE')
                                                    @case('DROP_OUT_LEAVE')
                                                        bg-red-100 text-red-800
                                                        @break
                                                    @default
                                                        bg-gray-100 text-gray-800
                                                @endswitch">
                                                {{ $transitionName ?? 'Tidak diketahui' }}
                                            </span>
                                        </div>
                                        @if($activity->comment)
                                            <div class="mt-2 text-sm text-gray-600 bg-gray-50 p-3 rounded-lg">
                                                <div class="flex items-start">
                                                    <x-heroicon-o-chat-bubble-left class="w-4 h-4 text-gray-400 mr-2 mt-0.5 flex-shrink-0" />
                                                    <p class="text-sm text-gray-700">{{ $activity->comment }}</p>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="mt-2 flex items-center text-xs text-gray-500">
                                            <x-heroicon-o-user class="w-3 h-3 mr-1" />
                                            <span>{{ $activity->user_name }}</span>
                                            <span class="mx-1">•</span>
                                            <x-heroicon-o-calendar class="w-3 h-3 mr-1" />
                                            <span>{{ $activity->formatted_date }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <div class="text-center py-8">
            <x-heroicon-o-clock class="w-16 h-16 mx-auto text-gray-300 mb-4" />
            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Aktivitas</h3>
            <p class="text-gray-600">
                Timeline aktivitas akan muncul setelah Anda melakukan transisi workflow pertama.
            </p>
        </div>
    @endif
    {{-- Export/Print Options --}}
    @if($timeline->count() > 0)
        <div class="mt-6 pt-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-600">
                    Menampilkan {{ $timeline->count() }} aktivitas terbaru
                </p>
            </div>
        </div>
    @endif