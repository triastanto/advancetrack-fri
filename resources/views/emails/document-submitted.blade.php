@extends('emails.layouts.base')

@section('title', 'Dokumen Baru Memerlukan Verifikasi')

@section('content')
    @php
        $badge = [
            'type' => 'warning',
            'icon' => '⏳',
            'text' => 'PERLU VERIFIKASI'
        ];
        
        $documentInfoItems = [
            ['label' => 'Nama Dokumen', 'value' => $document->file_name, 'bold' => true],
            ['label' => 'Jenis Dokumen', 'value' => $documentType],
            ['label' => 'Disubmit oleh', 'value' => $employeeName],
            ['label' => 'NIP', 'value' => $employeeNip],
        ];
        
        // Get study program from employee's active study calendar
        $activeStudy = $document->employee->studyCalendars()
            ->with('studyDetail.studyProgram')
            ->where('workflow_state', 5) // ACTIVE state
            ->latest()
            ->first();
        if (!$activeStudy) {
            $activeStudy = $document->employee->studyCalendars()
                ->with('studyDetail.studyProgram')
                ->latest()
                ->first();
        }
        if($activeStudy && $activeStudy->studyDetail && $activeStudy->studyDetail->studyProgram) {
            $documentInfoItems[] = ['label' => 'Program Studi', 'value' => $activeStudy->studyDetail->studyProgram->name];
        }
        
        $documentInfoItems[] = ['label' => 'Waktu Submit', 'value' => $submissionDate];
        $documentInfoItems[] = ['label' => 'Status', 'value' => 'Menunggu Verifikasi'];
        
        if($notificationData['comment']) {
            $documentInfoItems[] = ['label' => 'Catatan', 'value' => $notificationData['comment']];
        }
        
        $buttons = [
            [
                'url' => $verificationUrl,
                'text' => 'Lihat & Verifikasi Dokumen',
                'type' => 'primary',
                'icon' => '🔍'
            ]
        ];
        
        $appName = config('app.name');
        $nextStepActions = [
            "Silakan login ke sistem {$appName} untuk melakukan verifikasi dokumen ini.",
            'Dokumen dapat disetujui atau ditolak dengan memberikan catatan yang sesuai.',
            'Gunakan tombol di bawah untuk mengakses halaman verifikasi dengan cepat.'
        ];
    @endphp

    <div class="greeting">
        Selamat pagi/siang, Tim Verifikasi!
    </div>

    <p>Terdapat dokumen baru yang telah disubmit dan memerlukan verifikasi dari Tim Administrasi.</p>

    @include('emails.partials.document-info', [
        'title' => '📋 Detail Dokumen',
        'items' => $documentInfoItems
    ])

    @include('emails.partials.action-buttons', ['buttons' => $buttons])

    @include('emails.partials.action-section', [
        'title' => '💡 Tindakan yang Diperlukan',
        'sectionClass' => 'info',
        'actions' => $nextStepActions
    ])

    <p>Terima kasih atas perhatian dan kerjasama Anda.</p>
    
    <p style="margin-top: 30px;">
        Salam,<br>
        <strong>Tim {{ config('app.name') }}</strong><br>
        <em>Sistem Manajemen Studi Lanjut</em>
    </p>
@endsection
