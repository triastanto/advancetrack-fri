@extends('emails.layouts.base')

@section('title', 'Dokumen Diperbaiki - Perlu Verifikasi Ulang')

@section('content')
    @php
        $badge = [
            'type' => 'info',
            'icon' => '🔄',
            'text' => 'DOKUMEN DIPERBAIKI'
        ];
        
        $documentInfoItems = [
            ['label' => 'Nama Dokumen', 'value' => $document->file_name, 'bold' => true],
            ['label' => 'Jenis Dokumen', 'value' => $documentType],
            ['label' => 'Diperbaiki oleh', 'value' => $employeeName],
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
        
        $documentInfoItems[] = ['label' => 'Waktu Resubmit', 'value' => $submissionDate];
        $documentInfoItems[] = ['label' => 'Status', 'value' => 'Menunggu Verifikasi Ulang'];
        
        if($resubmissionComment && $resubmissionComment !== 'Tidak ada catatan.') {
            $documentInfoItems[] = ['label' => 'Catatan Perbaikan', 'value' => $resubmissionComment];
        }
        
        $buttons = [
            [
                'url' => $verificationUrl,
                'text' => 'Verifikasi Dokumen yang Diperbaiki',
                'type' => 'primary',
                'icon' => 'heroicon-o-magnifying-glass'
            ]
        ];
        
        $appName = config('app.name');
        $nextStepActions = [
            "Silakan login ke sistem {$appName} untuk melakukan verifikasi ulang dokumen ini.",
            'Periksa perbaikan yang telah dilakukan berdasarkan feedback sebelumnya.',
            'Dokumen dapat disetujui jika sudah memenuhi persyaratan atau ditolak kembali jika masih perlu perbaikan.',
            'Berikan feedback yang konstruktif untuk membantu proses perbaikan selanjutnya.'
        ];
        
        // Get original rejection info if available from document history
        $originalRejectionInfo = [
            ['label' => 'Dokumen Sebelumnya', 'value' => 'Ditolak untuk perbaikan'],
            ['label' => 'Status Saat Ini', 'value' => 'Telah diperbaiki dan siap untuk diverifikasi ulang', 'bold' => true],
        ];
    @endphp

    <div class="greeting">
        Selamat pagi/siang, Tim Verifikasi!
    </div>

    <p>Terdapat dokumen yang telah diperbaiki dan memerlukan verifikasi ulang dari Tim Administrasi.</p>

    @include('emails.partials.document-info', [
        'title' => '📋 Detail Dokumen yang Diperbaiki',
        'items' => $documentInfoItems
    ])

    @include('emails.partials.highlight-section', [
        'title' => '🔄 Status Perbaikan',
        'sectionClass' => 'info',
        'items' => $originalRejectionInfo,
        'content' => '<p><strong>Catatan:</strong> Dokumen ini sebelumnya ditolak dan kini telah diperbaiki oleh pemilik dokumen. Silakan lakukan verifikasi ulang untuk memastikan semua isu telah teratasi.</p>'
    ])

    @include('emails.partials.action-buttons', ['buttons' => $buttons])

    @include('emails.partials.action-section', [
        'title' => '📝 Tindakan yang Diperlukan',
        'sectionClass' => 'warning',
        'actions' => $nextStepActions
    ])

    <p>Terima kasih atas perhatian dan kerjasama Anda dalam proses verifikasi ulang ini.</p>
    
    <p style="margin-top: 30px;">
        Salam,<br>
        <strong>Tim {{ config('app.name') }}</strong><br>
        <em>Sistem Manajemen Studi Lanjut</em>
    </p>
@endsection
