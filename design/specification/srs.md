# Spesifikasi Kebutuhan Perangkat Lunak (Software Requirement Specification)

## 1. Pendahuluan

### 1.1 Latar Belakang
Sistem ini dikembangkan untuk mendukung proses administrasi dan monitoring studi lanjut dosen di Fakultas Rekaya Industri (FRI). Sistem akan memfasilitasi pengelolaan data, dokumen, notifikasi, serta pelaporan yang terintegrasi dan efisien.

### 1.2 Tujuan
Dokumen ini bertujuan untuk mendefinisikan kebutuhan perangkat lunak berdasarkan user stories yang telah disusun, sehingga pengembangan sistem dapat berjalan sesuai harapan seluruh pemangku kepentingan.

### 1.3 Lingkup
Sistem akan digunakan oleh Dosen, Staf SDM & Keuangan, Kepala Urusan SDM & Keuangan, dan Wakil Dekan II FRI 2 untuk mengelola data dan dokumen studi lanjut, serta mendukung proses administrasi terkait.

---

## 2. Kebutuhan Fungsional

### 2.1 Manajemen Data dan Dokumen Dosen
- Dosen dapat mengunggah dan memperbarui data pribadi serta dokumen persyaratan persyaratan studi lanjut.
- Dosen hanya dapat melihat data diri dan dokumen miliknya sendiri.
- Dosen dapat mengunggah dokumen laporan per semester selama masa studi lanjut.
- Dosen dapat mengunggah laporan akhir studi lanjut (disertasi) dan dokumen kelulusan.
- Dosen dapat menambah dokumen lain di luar dokumen yang telah ditentukan.
- Dosen menerima notifikasi otomatis jika ada dokumen yang belum lengkap atau perlu diperbarui.

### 2.2 Verifikasi dan Administrasi oleh Staf SDM & Keuangan
- Staf SDM & Keuangan dapat memverifikasi dokumen persyaratan studi lanjut yang diunggah oleh dosen.
- Staf SDM & Keuangan dapat mengunggah dokumen kesesuaian studi lanjut, berita acara, dan NDE permintaan studi lanjut.
- Staf SDM & Keuangan dapat membaca seluruh data dan dokumen dalam sistem.
- Sistem secara otomatis merekap data dosen studi lanjut per semester dan per prodi.

### 2.3 Pengelolaan Dokumen Perjanjian Ikatan Dinas
- Kepala Urusan SDM & Keuangan dapat mengunggah dokumen perjanjian ikatan dinas yang sudah ditandatangani pemangku kepentingan.
- Dokumen perjanjian dapat diakses oleh dosen yang bersangkutan.

### 2.4 Monitoring dan Pengambilan Keputusan
- Wakil Dekan II FRI 2 dapat membaca seluruh data dan dokumen dalam sistem untuk mendukung pengambilan keputusan strategis.

### 2.5 Notifikasi dan Reminder Otomatis
- Sistem menampilkan dot merah pada ikon lonceng dan mengirim email notifikasi kepada pengguna terkait.
- Sistem memberikan notifikasi jika dokumen studi lanjut belum terverifikasi.
- Sistem mengirim reminder pengisian laporan per semester kepada dosen.
- Sistem memberikan notifikasi kepada dosen ketika PID sudah diunggah oleh Staf SDM & Keuangan.
- Sistem memberikan notifikasi kepada dosen ketika sudah melewati masa studi agar mengajukan PID.

---

## 3. Kebutuhan Non-Fungsional

### 3.1 Keamanan
- Setiap pengguna hanya dapat mengakses data dan dokumen sesuai hak aksesnya.
- Data dan dokumen yang diunggah harus tersimpan dengan aman dan hanya dapat diakses oleh pihak yang berwenang.

### 3.2 Ketersediaan
- Sistem harus dapat diakses secara online selama jam kerja.

### 3.3 Kemudahan Penggunaan
- Antarmuka pengguna harus sederhana dan mudah dipahami oleh seluruh aktor.

### 3.4 Skalabilitas
- Sistem mampu menangani pertambahan jumlah pengguna dan dokumen tanpa penurunan performa signifikan.

---

## 4. Daftar Istilah/Jargon

| Jargon/Akronim | Penjelasan                                                                 |
|----------------|----------------------------------------------------------------------------|
| FRI            | Fakultas Rekaya Industri                                              |
| PID            | Perjanjian Ikatan Dinas                                                    |
| NDE            | Nota Dinas Elektronik (surat permintaan studi lanjut)                      |
| Studi Lanjut   | Program pendidikan lanjutan (misal: S2, S3)                                |

