# Outline Desain UI

## Ringkasan Aplikasi (Untuk Prompt Generator AI UI)

Aplikasi "AdvanceTrack FRI" adalah sistem manajemen studi lanjut dosen berbasis web yang memudahkan proses administrasi, monitoring, dan pelaporan studi lanjut di lingkungan Fakultas Sains dan Pendidikan. Sistem ini mendukung berbagai peran pengguna seperti Dosen, Staf FSDP, Kepala Urusan, dan Wakil Dekan 2. Fitur utama meliputi pengelolaan data pribadi dan dokumen dosen, upload laporan studi lanjut, verifikasi dokumen, rekapitulasi data, pengelolaan dokumen perjanjian ikatan dinas, notifikasi real-time, serta akses data dan pelaporan yang komprehensif. Antarmuka dirancang intuitif dengan navigasi sidebar, filter prodi, grid kartu dosen, dan menu-menu utama yang mudah diakses sesuai kebutuhan pengguna.

- **Login & Autentikasi**: Halaman login dengan email/NIP & password, serta fitur reset password.
- **Dashboard**: Ringkasan status dokumen, notifikasi, statistik studi lanjut, dan shortcut ke fitur utama.
- **Manajemen Data Pribadi Dosen**: Formulir data pribadi, upload/update dokumen persyaratan, dan daftar dokumen beserta status verifikasi.
- **Laporan Studi Lanjut**: Upload laporan per semester (LKS), laporan akhir/disertasi, dokumen kelulusan, dan dokumen tambahan.
- **Notifikasi**: Ikon lonceng dengan dot merah, daftar notifikasi (filter belum/sudah dibaca), dan pengaturan preferensi notifikasi.
- **Verifikasi Dokumen (Staf FSDP)**: Daftar dokumen yang perlu diverifikasi, detail dokumen, status, tombol verifikasi/tolak, dan kolom catatan.
- **Rekap & Pelaporan (Staf FSDP)**: Rekap data dosen studi lanjut per semester/prodi dan ekspor data.
- **Pengelolaan Dokumen Perjanjian Ikatan Dinas**: Upload dan daftar dokumen perjanjian sesuai peran.
- **Akses Data & Dokumen (Pimpinan)**: Tampilan seluruh data & dokumen, fitur pencarian dan filter.
- **Pengaturan Akun**: Ubah password dan kelola profil pengguna.

Setiap fitur memiliki tampilan yang intuitif, akses berbasis peran, dan navigasi yang jelas. Notifikasi dan status dokumen selalu ter-update secara real-time.

---

## UI Design System Guideline (English Translation)

### 1. Primary Colors
- **Dark Green**: #009444 (main for header, primary buttons, active sidebar)
- **Light Green**: #E6F4EC (element background, hover)
- **White**: #FFFFFF (main background, cards, tables)
- **Light Grey**: #F8F9FB (sidebar background, empty areas)
- **Grey**: #E5E7EB (borders, table lines)
- **Black/Dark Grey**: #222222 (main text, titles)

### 2. Typography
- **Font**: Modern sans-serif (e.g., Inter, Arial, or similar)
- **Title Size**: 24-32px (e.g., "Dosen Studi Lanjut FRI", "Laporan dan Rekapitulasi")
- **Subtitle Size**: 18-20px (e.g., "Kalender Studi Lanjut")
- **Body Text**: 14-16px
- **Sidebar & Label Text**: 14px, bold for active menu

### 3. UI Components

#### a. Sidebar Navigation
- Logo and app name at the top, green color.
- Vertical menu with icon on the left, label on the right.
- Active menu highlighted with dark green, white text.
- User profile at the bottom of the sidebar, showing name, position, and a square logout button with exit icon.

#### b. Header & Greeting
- Header with personal greeting ("Hello [Name] 👋,").
- Large page title, dark green color.

#### c. Study Program Filter
- Three large horizontal buttons, each with a user icon and study program label.
- Active button in dark green, others outlined in green.

#### d. Card & Box
- All cards and boxes use large border-radius (rounded corners).
- Soft shadow for floating effect.
- Ample spacing between elements, neat and responsive layout.

#### e. Lecturer Card Grid
- Rectangular card with dark green background.
- Lecturer name and NIP centered, white text.
- Circular avatar above the name.
- Responsive grid: 3 columns (desktop), 1 column (mobile).
- Search feature at the top right of the grid.

#### f. Study Calendar Table
- Table header in light grey, bold text.
- Table rows white, bottom border in grey.
- Columns: Avatar, Lecturer Name, NIP, Study Start, Estimated End, Calendar (green "View" link).
- Pagination below the table if there is a lot of data.

#### g. Report and Recap Menu
- 6 large buttons with icon and label (Personal Data, Requirement Documents, Minutes, Electronic Official Note, Service Bond Agreement, Supporting Documents).
- Rectangular buttons, dark green, large white icon on the left, label on the right.

#### h. Buttons & Interactions
- Primary button: dark green, white text, rounded corners.
- Secondary button: green outline, green text.
- "Back" button at the bottom right of detail pages, small, with left arrow icon.

### 4. Iconography
- Use simple, consistent, and easily recognizable icons (e.g., user, file, calendar, briefcase).
- Sidebar and main menu icons in green or white, matching the background.

### 5. Layout & Spacing
- Padding inside cards/boxes: 24-32px.
- Margin between main elements: 24px or more.
- Responsive layout, remains neat on small screens.

### 6. Status & Notifications
- Active sidebar menu highlighted.
- New notifications marked with a red dot on the bell icon.
- "View" link in tables is green and underlined on hover.

---

## 1. Login & Autentikasi
Deskripsi: Halaman untuk autentikasi pengguna sebelum mengakses sistem. Pengguna dapat login menggunakan email/NIP dan password, serta melakukan reset password jika lupa.

- Halaman login (email/NIP & password)
- Lupa password/reset password

## 2. Dashboard
Deskripsi: Halaman utama setelah login yang menampilkan ringkasan status dokumen, notifikasi terbaru, statistik studi lanjut (khusus staf FSDP & pimpinan), serta shortcut ke fitur-fitur utama.

**Detail tambahan dari desain:**
- Sidebar kiri dengan logo dan nama aplikasi ("AdvanceTrack FRI").
- Menu navigasi di sidebar: Beranda, Laporan dan Rekapitulasi, Kalender Studi Lanjut.
- Menu aktif diberi highlight (misal: "Kalender Studi Lanjut" berwarna hijau).
- Profil pengguna di sidebar kiri bawah, menampilkan nama, jabatan, dan tombol logout.
- Header utama menyapa pengguna (misal: "Halo Evano 👋,").
- Panel filter prodi di bagian atas dashboard (SI Teknik Industri, SI Sistem Informasi, SI Teknik Logistik) dengan ikon dan tombol besar.
- Section utama "Dosen Studi Lanjut FRI" dengan tombol filter prodi.
- Section "Kalender Studi Lanjut" menampilkan tabel dosen:
  - Kolom: Nama Dosen (dengan avatar), NIP, Mulai Masa Studi, Perkiraan Akhir Studi, Kalender (link "Lihat").
  - Tabel dengan desain bersih, setiap baris menampilkan avatar, nama, NIP, tahun mulai, tahun akhir, dan link ke detail kalender.
- Seluruh tampilan menggunakan card/box dengan sudut membulat dan bayangan lembut.
- Layout responsif dan rapi, dengan ruang putih yang cukup di sekitar elemen.
- Navigasi halaman (pagination) jika data dosen banyak.
- Tombol kembali (Back) di halaman detail (tidak tampil di halaman utama).

- Ringkasan status dokumen & notifikasi
- Statistik studi lanjut (khusus staf FSDP & pimpinan)
- Shortcut ke fitur utama
- Sidebar navigasi dan profil pengguna
- Filter prodi dengan tombol besar dan ikon
- Section menu laporan dan rekapitulasi dengan 6 tombol utama (di halaman lain)
- Section kalender studi lanjut dengan tabel dosen dan link detail kalender
- Pagination daftar dosen (jika ada)
- Tombol kembali (Back) di halaman detail

## 3. Manajemen Data Pribadi Dosen
Deskripsi: Fitur untuk dosen mengelola data pribadi dan dokumen persyaratan studi lanjut. Terdapat formulir data pribadi, upload/update dokumen, serta daftar dokumen yang telah diunggah beserta status verifikasinya.

- Formulir data pribadi
- Upload & update dokumen persyaratan studi lanjut
- Daftar dokumen yang telah diunggah (dengan status verifikasi)

## 4. Laporan Studi Lanjut
Deskripsi: Fitur untuk dosen mengunggah laporan per semester (LKS), laporan akhir/disertasi, dokumen kelulusan, serta dokumen tambahan jika diperlukan.

- Upload laporan per semester (LKS)
- Upload laporan akhir/disertasi & dokumen kelulusan
- Upload dokumen tambahan (opsional)

## 5. Notifikasi
Deskripsi: Sistem notifikasi untuk seluruh pengguna. Terdapat ikon lonceng dengan dot merah jika ada notifikasi baru, daftar notifikasi yang dapat difilter, serta pengaturan preferensi notifikasi (opsional).

- Ikon lonceng dengan dot merah jika ada notifikasi baru
- Daftar notifikasi (dengan filter: belum dibaca/sudah dibaca)
- Pengaturan preferensi notifikasi (opsional)

## 6. Verifikasi Dokumen (Staf FSDP)
Deskripsi: Fitur khusus staf FSDP untuk memverifikasi dokumen yang diunggah dosen. Menampilkan daftar dokumen yang perlu diverifikasi, detail dokumen, status, serta tombol verifikasi/tolak dan kolom catatan.

- Daftar dokumen yang perlu diverifikasi
- Detail dokumen & status verifikasi
- Tombol verifikasi/tolak dokumen & beri catatan

## 7. Rekap & Pelaporan (Staf FSDP)
Deskripsi: Fitur untuk staf FSDP melakukan rekap data dosen studi lanjut per semester dan per prodi, serta opsi ekspor data untuk keperluan pelaporan.

- Rekap data dosen studi lanjut per semester & per prodi
- Ekspor data (opsional)

## 8. Pengelolaan Dokumen Perjanjian Ikatan Dinas
Deskripsi: Fitur untuk kepala urusan mengunggah dokumen perjanjian ikatan dinas dan daftar dokumen yang dapat diakses sesuai peran pengguna.

- Upload dokumen perjanjian (Kepala Urusan)
- Daftar dokumen perjanjian (akses sesuai peran)

## 9. Akses Data & Dokumen (Pimpinan)
Deskripsi: Tampilan khusus bagi Wakil Dekan 2 dan Staf FSDP untuk mengakses seluruh data dan dokumen, dilengkapi fitur pencarian dan filter data.

- Tampilan seluruh data & dokumen (khusus Wakil Dekan 2 & Staf FSDP)
- Fitur pencarian & filter data

## 10. Pengaturan Akun
Deskripsi: Fitur untuk pengguna mengelola akun, seperti mengubah password dan memperbarui profil.

- Ubah password
- Kelola profil pengguna
