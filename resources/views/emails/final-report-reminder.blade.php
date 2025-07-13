@extends('emails.layouts.base')

@section('title', 'Pengingat Dokumen Laporan Akhir')

@section('content')
    @php
        $badge = [
            'type' => 'danger',
            'icon' => '🎓',
            'text' => 'PENGINGAT AKHIR'
        ];
        
        $documentInfoItems = [
            ['label' => 'Nama Dosen', 'value' => $notificationData['lecturer_name'], 'bold' => true],
            ['label' => 'Hari Menuju Penyelesaian', 'value' => $notificationData['days_until_completion'] . ' hari', 'bold' => true],
            ['label' => 'Dokumen yang Belum Diunggah', 'value' => $notificationData['missing_documents'], 'bold' => true],
            ['label' => 'Tanggal Pengingat', 'value' => $notificationData['reminder_date']],
        ];
        
        $buttons = [
            [
                'url' => $notificationData['action_url'],
                'text' => 'Unggah Dokumen Akhir',
                'type' => 'danger',
                'icon' => '📤'
            ]
        ];
        
        $appName = config('app.name');
        $nextStepActions = [
            "Silakan login ke sistem {$appName} untuk mengunggah dokumen laporan akhir yang belum lengkap.",
            'Dokumen yang diperlukan: Ijazah, Transkrip Nilai Akhir, Surat Keterangan Lulus, dll.',
            'Pastikan semua dokumen diunggah sebelum studi selesai untuk kelulusan yang tepat waktu.'
        ];
    @endphp

    <div class="greeting">
        Halo {{ $notificationData['lecturer_name'] }}!
    </div>

    <p>Kami mengingatkan bahwa studi Anda akan selesai dalam {{ $notificationData['days_until_completion'] }} hari, namun Anda belum mengunggah dokumen laporan akhir yang diperlukan.</p>

    @include('emails.partials.document-info', [
        'title' => '📋 Detail Pengingat',
        'items' => $documentInfoItems
    ])

    @include('emails.partials.action-buttons', ['buttons' => $buttons])

    @include('emails.partials.action-section', [
        'title' => '💡 Tindakan yang Diperlukan',
        'sectionClass' => 'danger',
        'actions' => $nextStepActions
    ])

    <p>Terima kasih atas perhatian Anda. Jika ada pertanyaan, silakan hubungi tim administrasi.</p>
    
    <p style="margin-top: 30px;">
        Salam,<br>
        <strong>Tim {{ config('app.name') }}</strong><br>
        <em>Sistem Manajemen Studi Lanjut</em>
    </p>
@endsection 