# 📚 Navigasi Sistem Studi Lanjut (Tampilan Visual Hirarki)

## 👥 Role-Based Navigation Structure

### 🎓 Dosen (Lecturer)
*Pengguna dengan peran dosen studi lanjut*

### 👨‍💼 Staf SDM & Keuangan (HR Finance Staff)
*Pengguna dengan peran staf administrasi*

### 🏢 Kepala Urusan SDM & Keuangan (Head of HR Finance)
*Pengguna dengan peran kepala urusan administrasi*

### 🎯 Ketua Program Studi (Head of Study Program)
*Pengguna dengan peran ketua program studi*

### 🔬 Ketua Kelompok Keilmuan (Head of Research Group)
*Pengguna dengan peran ketua kelompok keilmuan*

### 🏛️ Wakil Dekan II FRI (FRI Vice Dean)
*Pengguna dengan peran wakil dekan*

---

## 📋 Menu Navigasi Berdasarkan Peran

```
├── 🏠 Dashboard
│   ├── Ringkasan Status Dokumen
│   ├── Notifikasi Terbaru
│   ├── Reminder Laporan Semester
│   └── Info Masa Studi
│   👥 **Akses**: Semua peran

├── 👤 Data Pribadi (Dosen)
│   ├── Profil Dosen
│   └── Riwayat Pendidikan
│   👥 **Akses**: Dosen (lecturer)

├── 📅 Kalender Studi Lanjut (Workflow Utama)
│   ├── Kelola Kalender Studi Lanjut (Dosen)
│   ├── Linimasa Kalender Studi Lanjut
│   └── Persetujuan Kalender Studi Lanjut (Supervisor)
│   👥 **Akses**: 
│   - Kelola & Linimasa: Dosen (lecturer)
│   - Persetujuan: Ketua Program Studi, Wakil Dekan II FRI

├── 📁 Dokumen Akademik (Academic Documents)
│   ├── 📂 Persyaratan Studi Lanjut (Study Requirements)
│   │   ├── Letter of Acceptance
│   │   ├── Surat Pengantar Beasiswa
│   │   ├── Surat Izin Rektor
│   │   ├── SK Dosen Tetap Yayasan
│   │   ├── Ijazah
│   │   ├── Transkrip Nilai S1
│   │   ├── Transkrip Nilai S2
│   │   ├── SK Inpassing
│   │   ├── SK JAD
│   │   └── Surat Pernyataan Melaporkan Kelulusan
│   ├── 📂 Laporan Per Semester (LKS)
│   │   ├── Surat Pengantar dari Dosen
│   │   ├── Transkrip Nilai
│   │   ├── Surat Keterangan Aktif
│   │   ├── Bukti Unggah Publikasi di Igracias
│   │   ├── Bukti Pembayaran Biaya Pendidikan
│   │   └── Surat Keterangan Progres Studi
│   ├── 📂 Laporan Akhir & Kelulusan
│   │   ├── Ijazah
│   │   ├── Transkrip Nilai Akhir
│   │   ├── Surat Keterangan Lulus
│   │   └── Surat Pernyataan Telah Menyelesaikan Studi
│   └── Status & Riwayat Verifikasi Dokumen
│   👥 **Akses**: Dosen (lecturer)

├── 📄 Persetujuan Studi Lanjut (Approval Documents)
│   ├── Upload/Submit Persetujuan Studi Lanjut (Dosen/Staf)
│   ├── Status & Progress Persetujuan (L1, L2, etc)
│   └── Riwayat & Komentar Approval
│   👥 **Akses**: Dosen (lecturer), Staf SDM & Keuangan

├── 🗂️ Administrasi Dokumen (Staf SDM & Keuangan / Kepala Urusan SDM & Keuangan)
│   ├── 🔍 Cari & Pilih Dosen
│   ├── 📥 Unggah Persetujuan Studi Lanjut
│   │   ├── Dokumen Kesesuaian Studi Lanjut
│   │   ├── Berita Acara Studi Lanjut
│   │   ├── NDE Studi Lanjut
│   │   └── Perjanjian Ikatan Dinas (PID)
│   ├── ✅ Verifikasi Persyaratan
│   └── 🛡️ Persetujuan Manajemen
│   👥 **Akses**: Staf SDM & Keuangan, Kepala Urusan SDM & Keuangan, Ketua Program Studi, Ketua Kelompok Keilmuan, Wakil Dekan II FRI

├── 📊 Monitoring & Laporan
│   ├── 📈 Dasbor Analitik
│   │   ├── Overview Statistik Umum (Grafik jumlah dosen aktif, lulus, dan dropout)
│   │   ├── Tren Studi Lanjut per Tahun (Analisis tren pertumbuhan studi lanjut)
│   │   ├── Distribusi Program Studi (Sebaran dosen berdasarkan program yang diambil melalui study calendars)
│   │   └── Rata-rata Masa Studi (Analisis durasi penyelesaian studi)
│   👥 **Akses**: Staf SDM & Keuangan, Kepala Urusan SDM & Keuangan, Ketua Program Studi, Ketua Kelompok Keilmuan, Wakil Dekan II FRI
│   ├── 📋 Laporan Dosen
│   │   ├── Rekap Dosen Studi Lanjut per Semester (Daftar dosen aktif per periode)
│   │   ├── Rekap Dosen per Program Studi (Pengelompokan berdasarkan prodi asal melalui study calendars)
│   │   ├── Dosen Mendekati Batas Studi (Alert dosen yang akan habis masa studi)
│   │   └── Laporan Kelulusan Dosen (Rekap dosen yang telah lulus)
│   👥 **Akses**: Staf SDM & Keuangan, Kepala Urusan SDM & Keuangan, Ketua Program Studi, Ketua Kelompok Keilmuan, Wakil Dekan II FRI
│   ├── 📄 Status Dokumen
│   │   ├── Status Verifikasi Dokumen (Overview status verifikasi semua dokumen)
│   │   ├── Dokumen Belum Lengkap (Daftar dosen dengan dokumen yang kurang)
│   │   ├── Dokumen Pending Verifikasi (Dokumen yang menunggu approval)
│   │   └── Riwayat Perubahan Dokumen (Log perubahan dan update dokumen)
│   👥 **Akses**: Staf SDM & Keuangan, Kepala Urusan SDM & Keuangan, Ketua Program Studi, Ketua Kelompok Keilmuan, Wakil Dekan II FRI
│   ├── 🔍 Audit & Log
│   │   ├── Log Aktivitas Pengguna (Catatan aktivitas sistem untuk audit)
│   │   ├── Riwayat Login User (Tracking akses masuk pengguna)
│   │   ├── Log Perubahan Data (Catatan perubahan data penting)
│   │   └── Export Data Audit (Ekspor data audit untuk keperluan external)
│   👥 **Akses**: Staf SDM & Keuangan, Kepala Urusan SDM & Keuangan, Ketua Program Studi, Ketua Kelompok Keilmuan, Wakil Dekan II FRI
│   ├── 🔔 Notifikasi
│   │   ├── Dokumen Belum Lengkap / Belum Diverifikasi
│   │   ├── Reminder Pengisian LKS
│   │   ├── Info Persetujuan Studi Lanjut Telah Diunggah
│   │   └── Peringatan Melewati Masa Studi
│   👥 **Akses**: Semua peran
│   └── ⚙️ Pengaturan
│       ├── Manajemen Akun & Hak Akses
│       ├── Ganti Kata Sandi
│       └── Preferensi Notifikasi
│       👥 **Akses**: Semua peran

---

## 🔐 Role-Based Access Control

### 📋 Matriks Akses Berdasarkan Peran

| Menu/Fitur | Dosen | Staf SDM | Kepala SDM | Ketua Prodi | Ketua KK | Wakil Dekan |
|------------|-------|----------|------------|-------------|----------|-------------|
| Dashboard | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Data Pribadi | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Kalender Studi (Kelola) | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Kalender Studi (Persetujuan) | ❌ | ❌ | ❌ | ✅ | ❌ | ✅ |
| Dokumen Akademik | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Persetujuan Studi Lanjut | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| Administrasi Dokumen | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Verifikasi Persyaratan | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Monitoring & Laporan | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Notifikasi | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Pengaturan | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |

### 🔄 Workflow Integration

#### 📋 Verifikasi Persyaratan (verification_by_staff)
- **Dosen**: Submit dokumen untuk verifikasi
- **Staf SDM**: Verifikasi dokumen (approve/reject)
- **Status**: DRAFT → PENDING → VERIFIED/REJECTED

#### 🛡️ Persetujuan Manajemen (verification_by_management)
- **Staf SDM**: Submit dokumen untuk persetujuan
- **Ketua Prodi**: Persetujuan Level 1
- **Ketua KK**: Persetujuan Level 2
- **Wakil Dekan**: Persetujuan Level 1 & 2
- **Status**: DRAFT → PENDING_L1 → PENDING_L2 → APPROVED/REJECTED

#### 📅 Kalender Studi Lanjut (study_calendar)
- **Dosen**: Submit kalender studi dengan detail program studi
- **Ketua Prodi & Wakil Dekan**: Persetujuan kalender
- **Status**: DRAFT → PENDING_APPROVAL → APPROVED → ACTIVE → FINISHED/DROP_OUT

---

## 📱 Responsive Design

### 🖥️ Desktop View
- Sidebar navigation dengan grouping berdasarkan peran
- Tab-based interface untuk Persetujuan Manajemen
- Dashboard dengan widget dan grafik

### 📱 Mobile View
- Collapsible sidebar navigation
- Bottom navigation untuk fitur utama
- Touch-friendly interface dengan large buttons

### 🎯 Progressive Enhancement
- Core functionality works without JavaScript
- Enhanced experience dengan Livewire components
- Real-time updates untuk workflow status
