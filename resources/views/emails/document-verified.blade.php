@extends('emails.layouts.base')

@section('title', 'Dokumen Telah Diverifikasi')

@section('content')
    @php
        $badge = [
            'type' => 'success',
            'icon' => '✓',
            'text' => 'DOKUMEN DIVERIFIKASI'
        ];
        
        $documentInfoItems = [
            ['label' => 'Nama Dokumen', 'value' => $document->file_name, 'bold' => true],
            ['label' => 'Jenis Dokumen', 'value' => $documentType],
            ['label' => 'Pemilik', 'value' => $employeeName . ' (' . $employeeNip . ')'],
            ['label' => 'Tanggal Diverifikasi', 'value' => $approvalTimestamp],
        ];
        
        $verificationItems = [
            ['label' => 'Diverifikasi oleh', 'value' => $verifyingStaff, 'bold' => true],
            ['label' => 'Status', 'value' => $notificationData['to_state'], 'bold' => true],
        ];
        
        $nextStepActions = [
            'Dokumen Anda kini telah resmi diverifikasi dan dapat digunakan sesuai keperluan',
            'Anda dapat mengunduh salinan dokumen yang telah diverifikasi dari sistem',
            'Jika diperlukan, Anda dapat meminta surat keterangan verifikasi',
            'Simpan notifikasi ini sebagai bukti verifikasi dokumen'
        ];
        
        $buttons = [
            [
                'url' => config('app.url') . '/documents',
                'text' => 'Lihat Dokumen di Sistem',
                'type' => 'success'
            ]
        ];
    @endphp

    <div class="greeting">
        Selamat, {{ $employeeName }}!
    </div>

    <p>Dokumen Anda telah berhasil diverifikasi dan disetujui oleh tim verifikasi kami.</p>

    @include('emails.partials.document-info', [
        'title' => '📄 Informasi Dokumen',
        'items' => $documentInfoItems
    ])

    @include('emails.partials.highlight-section', [
        'title' => '✅ Detail Verifikasi',
        'sectionClass' => 'success',
        'items' => $verificationItems,
        'comment' => $verificationComment,
        'commentLabel' => 'Catatan Verifikasi'
    ])

    @include('emails.partials.action-section', [
        'title' => '📋 Langkah Selanjutnya',
        'sectionClass' => 'warning',
        'actions' => $nextStepActions
    ])

    @include('emails.partials.action-buttons', ['buttons' => $buttons])
@endsection
