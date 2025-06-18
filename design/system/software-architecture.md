# Arsitektur Perangkat Lunak

## 1. Pendahuluan

Dokumen ini menjelaskan arsitektur sistem perangkat lunak untuk pengelolaan studi lanjut dosen S3. Arsitektur ini dirancang untuk memenuhi kebutuhan fungsional dan non-fungsional yang telah didefinisikan dalam dokumen SRS.

---

## 2. Arsitektur Sistem

### 2.1 Tipe Arsitektur

Sistem menggunakan arsitektur **client-server** berbasis web, dengan pembagian utama sebagai berikut:
- **Frontend**: Antarmuka pengguna berbasis web menggunakan Laravel Livewire.
- **Backend**: Layanan aplikasi menggunakan Laravel.
- **Database**: Penyimpanan data terpusat menggunakan MySQL.

### 2.2 Komponen Utama

#### a. Frontend
- Dibangun menggunakan Laravel Livewire untuk antarmuka reaktif dan interaktif.
- Menyediakan halaman login, dashboard, manajemen dokumen, notifikasi, dan pelaporan.
- Berkomunikasi dengan backend melalui komponen Livewire dan HTTP request.

#### b. Backend
- Mengelola logika bisnis, autentikasi, otorisasi, dan pengelolaan dokumen menggunakan Laravel.
- Menyediakan endpoint dan layanan untuk frontend Livewire.
- Mengirim email notifikasi dan reminder otomatis.
- Melakukan verifikasi dokumen dan rekap data.

#### c. Database
- Menyimpan data pengguna, dokumen, status verifikasi, log notifikasi, dan data pelaporan.
- Menggunakan MySQL sebagai sistem manajemen basis data relasional.

#### d. Layanan Notifikasi
- Modul khusus di Laravel untuk mengelola pengiriman notifikasi (dot merah, email, reminder).

---

## 3. Diagram Arsitektur

```
+-------------------+        HTTP/Livewire        +-----------+        SQL        +-----------+
| Frontend (Laravel | <------------------------> |  Backend  | <---------------> |  Database |
| Livewire)         |                            | (Laravel) |                   | (MySQL)   |
+-------------------+                            +-----------+                   +-----------+
        |                                             |
        |                                             v
        |                                    +------------------+
        |                                    | Layanan Notifikasi|
        |                                    +------------------+
```

---

## 4. Integrasi dan Keamanan

- **Autentikasi**: Setiap pengguna wajib login menggunakan akun terdaftar.
- **Otorisasi**: Hak akses diatur berdasarkan peran (Dosen Studi Lanjut, Staf SDM & Keuangan, Kepala Urusan SDM & Keuangan, Wakil Dekan II FRI, Ketua Program Studi, Ketua Kelompok Keilmuan).
- **Keamanan Data**: Dokumen dan data sensitif dienkripsi dan hanya dapat diakses oleh pihak berwenang.
- **Audit Log**: Setiap perubahan data dan dokumen tercatat untuk keperluan audit.

---

## 5. Deployment

- Sistem di-deploy pada server kampus atau cloud dengan akses melalui jaringan internet/intranet.
- Backup data dilakukan secara berkala.

---

## 6. Skalabilitas dan Pemeliharaan

- Arsitektur modular memudahkan pengembangan fitur baru dan pemeliharaan sistem.
- Sistem dapat diskalakan secara horizontal (penambahan server) jika jumlah pengguna meningkat.

---
