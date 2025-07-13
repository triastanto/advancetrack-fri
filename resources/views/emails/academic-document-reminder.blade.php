@extends('emails.layouts.base')

@section('title', 'Pengingat Dokumen Laporan Semester')

@section('content')
    @php
        $badge = [
            'type' => 'warning',
            'icon' => '📚',
            'text' => 'PENGINGAT SEMESTER'
        ];
        
        $documentInfoItems = [
            ['label' => 'Nama Dosen', 'value' => $notificationData['lecturer_name'], 'bold' => true],
            ['label' => 'Semester', 'value' => $notificationData['semester'], 'bold' => true],
            ['label' => 'Dokumen yang Belum Diunggah', 'value' => $notificationData['missing_documents'], 'bold' => true],
            ['label' => 'Tanggal Pengingat', 'value' => $notificationData['reminder_date']],
        ];
        
        $buttons = [
            [
                'url' => $notificationData['action_url'],
                'text' => 'Unggah Dokumen Semester',
                'type' => 'warning',
                'icon' => '📤'
            ]
        ];
        
        $appName = config('app.name');
        $nextStepActions = [
            "Silakan login ke sistem {$appName} untuk mengunggah dokumen laporan semester yang belum lengkap.",
            'Dokumen yang diperlukan: Surat Pengantar, Transkrip Nilai, Surat Keterangan Aktif, dll.',
            'Pastikan semua dokumen diunggah sebelum batas waktu semester berakhir.'
        ];
    @endphp

    <div class="greeting">
        Halo {{ $notificationData['lecturer_name'] }}!
    </div>

    <p>Kami mengingatkan bahwa Anda belum mengunggah dokumen laporan semester {{ $notificationData['semester'] }} yang diperlukan untuk kelanjutan studi Anda.</p>

    @include('emails.partials.document-info', [
        'title' => '📋 Detail Pengingat',
        'items' => $documentInfoItems
    ])

    @include('emails.partials.action-buttons', ['buttons' => $buttons])

    @include('emails.partials.action-section', [
        'title' => '💡 Tindakan yang Diperlukan',
        'sectionClass' => 'warning',
        'actions' => $nextStepActions
    ])

    <p>Terima kasih atas perhatian Anda. Jika ada pertanyaan, silakan hubungi tim administrasi.</p>
    
    <p style="margin-top: 30px;">
        Salam,<br>
        <strong>Tim {{ config('app.name') }}</strong><br>
        <em>Sistem Manajemen Studi Lanjut</em>
    </p>
@endsection 