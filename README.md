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
- **Manajemen Mahasiswa** — CRUD, filter (nama/NIM/prodi/jenjang/status), proses kenaikan semester massal. (Import/export Excel dari versi asli **belum** diporting — akan ditambahkan pada iterasi berikutnya jika dibutuhkan.)

Semua modul di atas sudah diuji end-to-end dengan browser (login → create/edit/delete → logout, guard akses).

### Belum diporting (menyusul di iterasi berikutnya)

Modul-modul berikut ada di aplikasi React asli tapi belum dipindahkan (skema DB & model sudah siap, tinggal dibangunkan UI Livewire-nya):

- Generate Tagihan & Pusat Pembayaran (`BillingModule`, `FinanceModule`)
- Cetak Tanggungan & Laporan Keuangan (`CetakTanggunganModule`, `ReportModule`)
- Wisuda & Alumni / Yudisium & Tracer Study (`GraduationModule`, `TracerStudyModule`)
- Profil Kampus, Manajemen Pengguna, Kategori Beasiswa, Manajemen Database (`CampusProfileModule`, `UserManagementModule`, `KategoriBeasiswaModule`, `DatabaseModule`)
- Landing page publik

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
