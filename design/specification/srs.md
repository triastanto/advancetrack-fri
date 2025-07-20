# Spesifikasi Kebutuhan Perangkat Lunak (Software Requirement Specification)

## 1. Pendahuluan

### 1.1 Latar Belakang
Sistem ini dikembangkan untuk mendukung proses administrasi dan monitoring studi lanjut dosen di Fakultas Rekaya Industri (FRI). Sistem akan memfasilitasi pengelolaan data, dokumen, notifikasi, serta pelaporan yang terintegrasi dan efisien.

### 1.2 Tujuan
Dokumen ini bertujuan untuk mendefinisikan kebutuhan perangkat lunak berdasarkan user stories dan use cases yang telah disusun, sehingga pengembangan sistem dapat berjalan sesuai harapan seluruh pemangku kepentingan.

### 1.3 Lingkup
Sistem akan digunakan oleh Lecturer, hr_finance_staff, head_of_hr_finance, fri_vice_dean, head_of_study_program, head_of_research_group untuk mengelola data dan dokumen studi lanjut, serta mendukung proses administrasi terkait.

---

## 2. Kebutuhan Fungsional

### 2.1 User & Authentication
- [ ] hr_finance_staff mendaftarkan akun pengguna baru
- [ ] Semua peran login/logout
- [ ] Semua peran reset dan ubah password
- [ ] Semua peran verifikasi email
- [ ] Semua peran mengelola profil sendiri (update nama, email, dsb)

### 2.2 Employee Management
- [ ] hr_finance_staff menambah/mengedit/menghapus data pegawai
- [ ] hr_finance_staff mengaitkan user ke pegawai
- [ ] hr_finance_staff mengatur penugasan pegawai ke laboratorium, kelompok keilmuan, dan program studi
- [ ] hr_finance_staff mengatur peran pegawai (lecturer, hr_finance_staff, head_of_hr_finance, fri_vice_dean, head_of_study_program, head_of_research_group)
- [ ] Semua peran melihat daftar dan detail pegawai

### 2.3 Document Management
- [ ] lecturer, hr_finance_staff, head_of_hr_finance, fri_vice_dean, head_of_study_program, head_of_research_group mengunggah, mengedit, menghapus, dan melihat dokumen
- [ ] Semua peran memilih jenis dokumen dan melihat status workflow serta verifikasi dokumen
- [ ] Semua peran menampilkan daftar dokumen berdasarkan pegawai, jenis, atau status (sesuai hak akses)

### 2.4 Document Verification Workflow
- [ ] lecturer submit dokumen untuk verifikasi
- [ ] hr_finance_staff memverifikasi dokumen (approve/reject) dan menambah catatan verifikasi
- [ ] lecturer merevisi dan resubmit dokumen yang ditolak
- [ ] Semua peran melihat riwayat workflow dokumen dan menerima notifikasi perubahan status (sesuai hak akses)

### 2.5 Management Multi-Level Approval Workflow
- [ ] hr_finance_staff submit dokumen untuk persetujuan manajemen
- [ ] head_of_study_program approve/reject (Level 1)
- [ ] head_of_research_group approve/reject (Level 2)
- [ ] hr_finance_staff merevisi dan resubmit dokumen yang ditolak
- [ ] Semua peran melihat riwayat workflow persetujuan dan menerima notifikasi perubahan status (sesuai hak akses)

### 2.6 Study Calendar Workflow
- [ ] lecturer membuat/mengedit Masa Studi dan submit untuk persetujuan
- [ ] head_of_study_program dan fri_vice_dean approve/reject Masa Studi
- [ ] lecturer merevisi dan resubmit Masa Studi
- [ ] lecturer, head_of_study_program, fri_vice_dean memulai studi, cuti, kembali dari cuti, menyelesaikan, atau mengundurkan diri dari studi
- [ ] Semua peran melihat riwayat workflow Masa Studi dan menerima notifikasi perubahan status studi (sesuai hak akses)

### 2.7 Study Details & Academic Tracking
- [ ] lecturer, hr_finance_staff, head_of_hr_finance, fri_vice_dean, head_of_study_program, head_of_research_group menambah/mengedit detail studi, promotor, supervisor, dan tanggung jawab pengajaran
- [ ] hr_finance_staff melacak periode penugasan supervisor dan penugasan mengajar per semester/tahun

### 2.8 Reporting & Monitoring
- [ ] Semua peran melihat dashboard, notifikasi, laporan, dan audit log (sesuai hak akses)
- [ ] hr_finance_staff, head_of_hr_finance, fri_vice_dean, head_of_study_program, head_of_research_group menghasilkan dan mengekspor laporan
- [ ] Semua peran memonitor progres verifikasi dokumen dan studi (sesuai hak akses)

### 2.9 Research Organization Management
- [ ] hr_finance_staff menambah/mengedit kelompok keilmuan, laboratorium riset, kepala lab, dan penugasan pegawai
- [ ] Semua peran melihat struktur organisasi riset (sesuai hak akses)

### 2.10 Notification & Audit
- [ ] Semua peran menerima notifikasi in-app/email dan melihat riwayat notifikasi (sesuai hak akses)
- [ ] Semua peran melihat audit log seluruh transisi workflow (sesuai hak akses)
- [ ] hr_finance_staff mengekspor data audit

### 2.11 System Administration
- [ ] hr_finance_staff mengatur setting workflow, jenis dokumen, program studi, peran, hak akses, jam kerja, dan hari libur

---

## 3. Kebutuhan Non-Fungsional

### 3.1 Keamanan
- [ ] Setiap pengguna hanya dapat mengakses data dan dokumen sesuai hak aksesnya.
- [ ] Data dan dokumen yang diunggah harus tersimpan dengan aman dan hanya dapat diakses oleh pihak yang berwenang.

### 3.2 Ketersediaan
- [ ] Sistem harus dapat diakses secara online selama jam kerja.

### 3.3 Kemudahan Penggunaan
- [ ] Antarmuka pengguna harus sederhana dan mudah dipahami oleh seluruh aktor.

### 3.4 Skalabilitas
- [ ] Sistem mampu menangani pertambahan jumlah pengguna dan dokumen tanpa penurunan performa signifikan.

---
