# Panduan Deploy ke Shared Hosting (cPanel)

Panduan singkat men-deploy **Madani-SMS** ke shared hosting (cPanel/DirectAdmin dengan PHP 8.2+ dan MySQL).

## 1. Persyaratan hosting

- PHP >= 8.2 dengan ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `fileinfo`, `gd` (untuk PDF rapor)
- MySQL/MariaDB
- Akses SSH lebih baik, tapi tanpa SSH juga bisa (upload zip)

## 2. Upload file

1. Jalankan di lokal dulu (sudah dilakukan): `npm run build` — folder `public/build` wajib ikut di-upload.
2. Zip seluruh project **kecuali** `node_modules` dan `.git`, upload & extract di hosting, idealnya di luar `public_html` (mis. `~/madani-sms`).
3. Folder `vendor` ikut di-upload (shared hosting sering tidak punya composer). Jika ada SSH + composer, lebih baik jalankan `composer install --no-dev --optimize-autoloader` di server.

## 3. Arahkan document root

- **Jika bisa ubah document root:** arahkan ke `~/madani-sms/public`.
- **Jika tidak bisa (addon/subdomain biasanya bisa; domain utama kadang tidak):**
  salin isi folder `public/` ke `public_html/`, lalu edit `public_html/index.php` — ubah dua path `__DIR__.'/../vendor/...'` dan `__DIR__.'/../bootstrap/...'` menjadi path ke folder project (mis. `__DIR__.'/../madani-sms/vendor/...'`).

## 4. Buat database & .env

1. Di cPanel buat database MySQL + user, catat nama & password.
2. Buat file `.env` di folder project di server, salin dari template di bawah, isi bagian yang bertanda `<...>`:

```env
APP_NAME="Madani-SMS"
APP_ENV=production
APP_KEY=            # diisi lewat: php artisan key:generate
APP_DEBUG=false
APP_URL=https://<domain-anda>

APP_LOCALE=id
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=id_ID

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=<nama_database_cpanel>
DB_USERNAME=<user_database_cpanel>
DB_PASSWORD=<password_database>

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true

QUEUE_CONNECTION=database
CACHE_STORE=database
FILESYSTEM_DISK=local

MAIL_MAILER=log
MAIL_FROM_ADDRESS="no-reply@<domain-anda>"
MAIL_FROM_NAME="${APP_NAME}"

VITE_APP_NAME="${APP_NAME}"
```

## 5. Inisialisasi aplikasi (via SSH atau Terminal cPanel)

```bash
cd ~/madani-sms
php artisan key:generate
php artisan migrate --force
php artisan db:seed --class=ProductionSeeder   # buat akun admin (password acak, dicatat dari output)
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

> `ProductionSeeder` hanya membuat 1 akun admin (`admin@madani.id`) dengan password acak yang ditampilkan sekali di terminal — **catat dan simpan**. Jangan jalankan `DatabaseSeeder` di hosting (berisi data demo; sudah diblokir otomatis saat `APP_ENV=production`).

Tanpa SSH: gunakan menu "Terminal" di cPanel, atau jalankan migrasi lewat fitur import SQL (kurang disarankan).

## 6. Cek setelah deploy

- [ ] Buka `https://<domain-anda>` — halaman login tampil, CSS/JS termuat (artinya `public/build` benar).
- [ ] Login admin dengan password dari ProductionSeeder, lalu **ganti password** dari menu profil.
- [ ] Buat data master (kelas, mapel, guru, siswa) lewat menu admin.
- [ ] Tes export PDF rapor (butuh ekstensi `gd`).
- [ ] Pastikan `APP_DEBUG=false` (cek dengan membuka URL ngawur — harus tampil error 404 biasa, bukan stack trace).

## 7. Backup (disarankan)

Di cPanel buat Cron Job harian pukul 17.00 WIB:

```
0 17 * * * mysqldump -u <user_db> -p'<password_db>' <nama_db> | gzip > ~/backup/madani-$(date +\%u).sql.gz
```

(File berputar per hari-dalam-minggu, jadi maksimal 7 file backup.)
