<div align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Madani Al-Aziziyah">
</div>

# Madani Al-Aziziyah — Sistem Manajemen Sekolah (SMS)

Sistem informasi akademik terpadu untuk **SMA Dayah Madani Al-Aziziyah**. Mencakup manajemen kurikulum, kelas, absensi, penilaian, rapor, dan komunikasi sekolah.

## Fitur

- **Manajemen Akademik**: Kelas, siswa, guru, mata pelajaran, mapping guru-mapel
- **Jadwal Pelajaran**: Jadwal mingguan dengan validasi waktu
- **Absensi Siswa**: Presensi Hadir/Sakit/Izin/Alpa dengan batas waktu input
- **Penilaian**: Komponen nilai (Tugas/PH/UTS/UAS), input batch, import Excel
- **Rapor**: PDF rapor otomatis, preview online
- **Absensi Guru**: Presensi harian guru
- **Surat Menyurat**: Pengajuan & cetak surat (PDF)
- **Kontak & Pertemuan**: Pesan orang tua ke sekolah, jadwal pertemuan
- **Role-Based Access**: Admin, Guru, Wali Murid
- **Activity Log**: Audit trail semua operasi CRUD

## Tech Stack

- **Backend**: Laravel 13, PHP 8.3
- **Database**: MySQL
- **Frontend**: Tailwind CSS 4, Alpine.js, Blade
- **PDF**: barryvdh/laravel-dompdf
- **Spreadsheet**: phpoffice/phpspreadsheet
- **Auth**: Laravel Sanctum (session + token API)

## Persyaratan Sistem

- PHP >= 8.3
- MySQL / MariaDB
- Composer
- Node.js & npm
- Ekstensi PHP: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `fileinfo`, `gd`

## Instalasi Lokal

```bash
git clone <repo-url> madani-al-aziziyah
cd madani-al-aziziyah

composer install
npm install

cp .env.example .env
php artisan key:generate

# Setup database MySQL, lalu edit .env (DB_DATABASE, DB_USERNAME, DB_PASSWORD)
php artisan migrate --seed

npm run build
php artisan storage:link

php artisan serve
```

Akses di `http://localhost:8000`.

> Untuk deploy ke production, lihat [DEPLOY.md](DEPLOY.md).

## Akun Default (Development)

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@madani.id | (lihat output ProductionSeeder) |
| Guru | (dibuat via admin) | (dibuat via admin) |
| Wali Murid | (dibuat via admin) | (dibuat via admin) |

## Testing

```bash
php artisan test
```

## Dokumentasi

- [DEPLOY.md](DEPLOY.md) — Panduan deploy ke shared hosting
- [DESIGN.md](DESIGN.md) — Design system & token
- [FLOWCHART.md](FLOWCHART.md) — Flowchart sistem
- [prd.md](prd.md) — Product Requirements Document

## Lisensi

Hak cipta © SMA Dayah Madani Al-Aziziyah.
