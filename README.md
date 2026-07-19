# SIAKAD Pro — Sistem Informasi Akademik

Aplikasi manajemen akademik & keuangan kampus, dibangun dengan **Laravel 11 + Livewire 4 + Tailwind CSS**.

Ini adalah hasil migrasi dari versi awal aplikasi (React/Vite + Firebase mock/localStorage dari Google AI Studio) ke stack Laravel yang:

- **Server-rendered** (Blade + Livewire) — tidak butuh build step JS untuk menjalankan aplikasi sehari-hari, cukup PHP + database.
- **Mudah dioperasikan** — cocok dijalankan di shared hosting/VPS standar (cPanel, dsb) yang lazim dipakai kampus di Indonesia.
- **Mudah dirombak** — mengikuti konvensi standar Laravel (Eloquent model, migration, route, Livewire component class + Blade view terpisah), bukan pola non-standar, supaya developer PHP mana pun bisa lanjut mengembangkan.

## Status Migrasi

### Fondasi (selesai)

- Skema database lengkap (migrations) untuk seluruh entitas dari aplikasi asli: `users` (+role), `prodis`, `tahun_akademiks`, `kategori_beasiswas`, `master_biayas`, `mahasiswas`, `tagihans`, `pembayarans`, `dispensasis`, `yudisiums`, `alumni_trackings`, `profil_kampus`, `activity_logs`.
- Eloquent Model + relasi untuk semua entitas di atas.
- Enum PHP (`app/Enums`) yang setara dengan enum TypeScript di aplikasi asli (`UserRole`, `StatusAkademik`, `StatusTagihan`, `MetodeBayar`, `ClearanceStatus`, `StatusPekerjaan`).
- Autentikasi berbasis session Laravel (bukan simulasi localStorage lagi): login dengan CAPTCHA matematika, lockout setelah 3x gagal, reset password (nyata via `Password` broker Laravel — perlu konfigurasi SMTP untuk kirim email sungguhan, default `MAIL_MAILER=log` untuk development).
- Middleware role-based access (`role:admin,akademik`, dst) — meniru aturan akses menu di `Layout.tsx`/`App.tsx` versi lama.
- Layout utama (sidebar + header) mengikuti desain `Layout.tsx` asli.

### Modul yang sudah berfungsi penuh

- **Dashboard** — ringkasan statistik mahasiswa, keuangan, alumni, yudisium, log aktivitas (role-aware, sama seperti `Dashboard.tsx`).
- **Manajemen Prodi** — CRUD program studi.
- **Manajemen Tahun Akademik** — CRUD + aturan "hanya 1 tahun akademik aktif" + tahun aktif tidak bisa dihapus.
- **Manajemen Biaya (Master Biaya)** — CRUD katalog tarif per prodi/jenjang/semester.
- **Manajemen Mahasiswa** — CRUD, filter (nama/NIM/prodi/jenjang/status), proses kenaikan semester massal.
- **Kategori Beasiswa** — CRUD kategori potongan (penuh/nominal/tanpa potongan).
- **Manajemen Mata Kuliah, Input Nilai KHS, Cetak KHS** — fitur baru (tidak ada di aplikasi React asli, ditambahkan atas permintaan): katalog mata kuliah per prodi/semester, input nilai huruf (A–E) per mahasiswa/mata kuliah/tahun akademik, dan cetak Kartu Hasil Studi (kop surat, IPS, IPK).
- **Generate Tagihan (Billing)** — penerbitan tagihan per-individu maupun batch dari Master Biaya, dengan aturan asli (mahasiswa harus aktif & terdaftar di tahun akademik aktif, tidak boleh duplikat, potongan otomatis dari kategori beasiswa).
- **Pusat Pembayaran (Finance)** — pencatatan pembayaran (cicilan/lunas), riwayat pembayaran, cetak kwitansi, hapus tagihan (jika belum ada pembayaran).
- **Cetak Tanggungan** — surat keterangan tanggungan per mahasiswa dengan watermark LUNAS otomatis.
- **Laporan Keuangan** — filter multi-kriteria + ekspor **CSV** (bukan `.xlsx` asli — lihat catatan di bawah).
- **Wisuda & Alumni (Graduation)** — checklist bebas pustaka/keuangan/akademik dengan approval per role, cetak bukti pendaftaran yudisium & surat bebas tanggungan, pengaturan periode pendaftaran, serta tab pelacakan alumni (isi data tracer, kirim WhatsApp manual/API).
- **Profil Kampus** — identitas institusi + upload logo (dipakai sebagai kop surat KHS/kwitansi/yudisium).
- **Manajemen Pengguna** — CRUD akun staf lintas role + edit profil admin sendiri.
- **Manajemen Database** — backup (unduh file `.sqlite`), restore (unggah file `.sqlite`), dan reset seluruh data akademik/keuangan (akun pengguna & profil kampus tetap aman), dengan konfirmasi berlapis mengikuti UX aplikasi asli.

Semua modul di atas sudah diuji end-to-end dengan browser (login → create/edit/delete → cetak/print → logout, guard akses per role).

### Catatan penyesuaian dari versi asli

- **Import/Export Excel** (`.xlsx`) di modul Mahasiswa, Billing (import tagihan lama), dan Laporan tidak diporting apa adanya karena membutuhkan dependency tambahan (`maatwebsite/excel` + ekstensi PHP zip/xml). Laporan Keuangan sudah bisa ekspor **CSV** (dibuka normal di Excel/Google Sheets) tanpa dependency tambahan; import Excel & format `.xlsx` asli bisa ditambahkan di iterasi berikutnya jika benar-benar dibutuhkan.
- **Manajemen Database**: karena versi Laravel ini memakai database relasional sungguhan (bukan `localStorage`), backup/restore berbasis file JSON pada versi React diganti dengan backup/restore file `.sqlite` utuh (lebih aman secara integritas data & foreign key) — hanya berfungsi saat `DB_CONNECTION=sqlite`. Untuk MySQL di hosting, gunakan `mysqldump`/fitur backup panel hosting.
- **Landing page publik** dan pengisian Tracer Study mandiri oleh alumni (tanpa login) belum diporting — saat ini pengisian data tracer dilakukan oleh admin/akademik dari dalam sistem.

## Menjalankan Secara Lokal

**Kebutuhan:** PHP 8.2+, Composer, Node.js (untuk build asset Tailwind saja, tidak dibutuhkan saat runtime).

```bash
composer install
npm install && npm run build
cp .env.example .env   # lalu sesuaikan DB_* jika perlu
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Login default (dibuat oleh seeder): `admin@kampus.edu` / `admin123`.

Database default: **SQLite** (`database/database.sqlite`, otomatis dibuat) — paling mudah untuk mulai tanpa setup server database. Untuk produksi di shared hosting yang menyediakan MySQL, cukup ubah beberapa baris di `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=user_db
DB_PASSWORD=password_db
```

lalu jalankan ulang `php artisan migrate --seed`.
