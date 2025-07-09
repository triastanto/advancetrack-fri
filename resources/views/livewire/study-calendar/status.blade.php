<x-ui.page-container title="Linimasa Kalender Studi Lanjut">
    <x-ui.card>
        @if($studyCalendar)
            <div class="mb-3">
                <strong>Dosen:</strong> {{ $studyCalendar->employee->user->name ?? '-' }}<br>
                <strong>Program Studi:</strong> {{ $studyCalendar->studyDetail->program_studi ?? '-' }}<br>
                <strong>Status Saat Ini:</strong> {{ $studyCalendar->workflow_state }}
            </div>
            <div class="card">
                <div class="card-header">Riwayat Status & Transisi</div>
                <ul class="list-group list-group-flush">
                    @forelse($timeline as $item)
                        <li class="list-group-item">
                            <div><strong>{{ $item['transition_name'] }}</strong> <span class="text-muted">({{ $item['formatted_date'] }})</span></div>
                            <div>Dari: <span class="badge bg-secondary">{{ $item['from_state'] }}</span> → <span class="badge bg-success">{{ $item['to_state'] }}</span></div>
                            <div>Oleh: <span class="text-primary">{{ $item['user_name'] }}</span></div>
                            @if($item['comment'])
                                <div>Komentar: <em>{{ $item['comment'] }}</em></div>
                            @endif
                        </li>
                    @empty
                        <li class="list-group-item text-muted">Belum ada riwayat transisi.</li>
                    @endforelse
                </ul>
            </div>
        @else
            <div class="alert alert-warning">Data kalender studi tidak ditemukan.</div>
        @endif
    </x-ui.card>
</x-ui.page-container>