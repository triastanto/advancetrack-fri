<html>
<head>
    <meta charset="UTF-8">
    <title>Masa Studi Kedaluwarsa</title>
</head>
<body>
    <h2>Masa Studi Kedaluwarsa</h2>
    <p>Halo {{ isset($user) ? $user->name : 'Pengguna' }},</p>
    <p>
        Masa studi Anda atau mahasiswa yang Anda bimbing telah melewati tanggal akhir yang ditetapkan dan kini berstatus <strong>kedaluwarsa</strong>.
    </p>
    @isset($notificationData['study_calendar_id'])
        <p><strong>ID Masa Studi:</strong> {{ $notificationData['study_calendar_id'] }}</p>
    @endisset
    <p>
        Silakan cek detail masa studi pada sistem untuk melakukan tindak lanjut jika diperlukan.
    </p>
    <p>
        <a href="{{ url('/study-calendar/manage') }}" style="display:inline-block;padding:10px 20px;background:#3490dc;color:#fff;text-decoration:none;border-radius:4px;">Lihat Masa Studi</a>
    </p>
    <p>Terima kasih telah menggunakan sistem AdvanceTrack.</p>
    <p>Salam,<br>Tim AdvanceTrack</p>
</body>
</html> 