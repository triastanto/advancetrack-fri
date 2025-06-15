@extends('emails.layouts.base')

@section('title', 'Dokumen Perlu Diperbaiki')

@section('content')
    @php
        $badge = [
            'type' => 'danger',
            'icon' => '⚠️',
            'text' => 'DOKUMEN DITOLAK'
        ];
        
        $documentInfoItems = [
            ['label' => 'Nama Dokumen', 'value' => $document->file_name, 'bold' => true],
            ['label' => 'Jenis Dokumen', 'value' => $documentType],
            ['label' => 'Pemilik', 'value' => $employeeName . ' (' . $employeeNip . ')'],
            ['label' => 'Tanggal Ditolak', 'value' => $rejectionTimestamp],
        ];
        
        $rejectionItems = [
            ['label' => 'Ditolak oleh', 'value' => $rejectingStaff, 'bold' => true],
            ['label' => 'Status', 'value' => $notificationData['to_state'], 'bold' => true],
        ];
        
        $nextStepActions = [
            'Periksa catatan revisi yang diberikan dengan teliti',
            'Perbaiki dokumen sesuai dengan feedback yang diberikan',
            'Upload ulang dokumen yang sudah diperbaiki melalui sistem',
            'Dokumen akan kembali masuk ke antrian verifikasi setelah diperbaiki'
        ];
        
        $buttons = [
            [
                'url' => config('app.url') . '/documents',
                'text' => 'Perbaiki & Upload Ulang',
                'type' => 'warning'
            ]
        ];
    @endphp

    <div class="greeting">
        Halo, {{ $employeeName }}
    </div>

    <p>Dokumen yang Anda submit perlu diperbaiki sebelum dapat diverifikasi. Silakan lihat detail feedback di bawah ini.</p>

    @include('emails.partials.document-info', [
        'title' => '📄 Informasi Dokumen',
        'items' => $documentInfoItems
    ])

    @include('emails.partials.highlight-section', [
        'title' => '❌ Detail Penolakan',
        'sectionClass' => 'danger',
        'items' => $rejectionItems,
        'comment' => $rejectionComment,
        'commentLabel' => 'Alasan Penolakan'
    ])

    @include('emails.partials.action-section', [
        'title' => '🔧 Langkah Perbaikan',
        'sectionClass' => 'warning',
        'actions' => $nextStepActions
    ])

    @include('emails.partials.action-buttons', ['buttons' => $buttons])

    <p>Jika Anda memiliki pertanyaan tentang feedback yang diberikan, silakan hubungi tim administrasi.</p>
@endsection
