# Instruksi: Deploy madaniv2 ke Oracle Cloud Free Tier + Docker (Budget 0%)

## Konteks
Project Laravel 13 (madaniv2) untuk Madani-SMS mau di-hosting gratis selamanya pakai VPS Oracle Cloud Free Tier + Docker, supaya laptop nggak perlu nyala 24 jam. Kamu masih junior — jadi ini dipecah step-by-step, termasuk bagian yang harus kamu kerjakan manual (bikin akun) dan bagian yang bisa didelegasikan ke opencode.

---

## BAGIAN A — Yang Harus Kamu Kerjakan Sendiri (opencode nggak bisa bantu ini)

### 1. Daftar Oracle Cloud Free Tier
- Buka https://www.oracle.com/cloud/free/
- Daftar pakai email, wajib input kartu debit/kredit untuk verifikasi identitas (TIDAK akan ditagih untuk resource "Always Free")
- Pilih **Home Region** — sebaiknya pilih Singapore atau Jakarta kalau tersedia (lebih dekat, latency lebih rendah buat user di Aceh)

### 2. Buat VM Instance (Compute)
- Di dashboard Oracle Cloud → **Compute → Instances → Create Instance**
- Pilih image: **Ubuntu 22.04** (bukan Oracle Linux, biar instruksi opencode di bawah cocok)
- Shape: pilih **VM.Standard.A1.Flex** (ini yang free/Always Free, ARM-based) — set 2 OCPU & 12GB RAM (masih dalam batas gratis)
- Di bagian **Add SSH keys**: pilih "Generate a key pair for me", lalu **download private key**-nya (file `.key`), simpan baik-baik — ini kunci buat masuk ke server nanti
- Klik **Create**, tunggu sampai statusnya "Running"
- Catat **Public IP Address** VM-nya

### 3. Buka Port di Firewall Oracle Cloud
Ini sering jadi jebakan buat pemula — port harus dibuka di 2 tempat:
- Di Oracle Cloud dashboard → VM instance → klik **Subnet** → **Security Lists** → **Add Ingress Rules**
  - Tambah rule: port **80** (HTTP), source `0.0.0.0/0`
  - Tambah rule: port **443** (HTTPS), source `0.0.0.0/0`
  - Port **22** (SSH) biasanya sudah otomatis terbuka

### 4. Connect ke VPS dari Laptop
Di Windows (kamu pakai Laragon jadi kemungkinan Windows), buka PowerShell:
```
icacls "path\ke\private-key.key" /inheritance:r /grant:r "%username%":R
ssh -i "path\ke\private-key.key" ubuntu@<PUBLIC_IP_VPS>
```
Kalau berhasil, kamu sekarang "masuk" ke dalam VPS-nya lewat terminal.

---

## BAGIAN B — Setup VPS (jalankan setelah kamu SSH masuk ke VPS)

File deploy **sudah tersedia di repo ini** — tidak perlu bikin dari nol:

| File | Fungsi |
|------|--------|
| `Dockerfile` | Build image app (PHP 8.3-fpm) multi-stage: build Vite + composer + ekstensi PHP (pdo_mysql, mbstring, exif, pcntl, bcmath, gd, intl, zip, opcache) |
| `docker-compose.yml` | 3 service: `app`, `db` (MySQL 8.4), `nginx`; volume untuk `storage/app/public` & `storage/backups` |
| `docker/nginx/default.conf` | Reverse proxy, batas upload 25M, blokir akses `.env`, serve folder `/storage/` |
| `docker/entrypoint.sh` | `storage:link`, generate `APP_KEY` bila kosong, cache config/route/view/event |
| `docker/php/opcache.ini` | Konfigurasi OPcache + JIT untuk production |
| `deploy/.env.production.example` | Template env production (copy → isi → simpan sebagai `.env`) |
| `deploy.sh` | Update 1-command: git pull + rebuild + migrate + ProductionSeeder |

### Langkah setup sekali jalan (setelah SSH masuk)

```bash
# 1. Install Docker Engine + Compose plugin (Ubuntu 22.04)
curl -fsSL https://get.docker.com | sh
sudo usermod -aG docker "$USER"

# 2. Clone repo
cd ~
git clone https://github.com/khairurrahman24146116-ai/madaniv2.git sms
cd sms

# 3. Buat .env production
cp deploy/.env.production.example .env
#    → isi: APP_URL, DB_PASSWORD, DB_ROOT_PASSWORD, SEED_ADMIN_PASSWORD, SEED_BENDAHARA_PASSWORD
#      (DB_HOST sudah benar "db" — jangan diubah)

# 4. Build & jalankan (proses ini men-generate APP_KEY otomatis,
#    storage:link otomatis, dan menjalankan migration + ProductionSeeder)
sudo bash deploy.sh
```

> **Catatan penting:** `deploy.sh` menjalankan `db:seed --class=ProductionSeeder`
> (bukan `DatabaseSeeder`, karena yang demo **diblokir di production**).
> Admin & bendahara dibuat sekali pakai password di `.env`.

### Update aplikasi di kemudian hari
```bash
cd ~/sms
sudo bash deploy.sh
```

---

## BAGIAN C — Setelah Aplikasi Jalan (opsional tapi disarankan)

### 1. Pasang Domain (kalau punya, misal dari yayasan)
Arahkan DNS domain ke Public IP VPS (A record), lalu minta opencode setup **Nginx + Certbot** untuk HTTPS gratis (Let's Encrypt).

### 2. Auto-restart kalau VPS reboot
Sudah terpasang: setiap service di `docker-compose.yml` memakai `restart: always`, jadi container otomatis jalan lagi kalau VPS reboot — tidak perlu SSH manual.

### 3. Backup Database Otomatis
Aplikasi sudah punya command `php artisan db:backup` (simpan ke `storage/backups/*.sql`, retensi 7 file) dan jadwal harian di `routes/console.php` (terdaftar sebagai `db:backup`). Tinggal jalankan scheduler + cron di VPS:

```bash
docker compose exec -d app php artisan schedule:work
# atau pasang cron di host:
# crontab -e  →   * * * * * cd ~/sms && docker compose exec -T app php artisan schedule:run >> /dev/null 2>&1
```

> **Catatan:** command `db:backup` memanggil `mysqldump` — binary tersebut sudah
> diinstall ke dalam image app (via `default-mysql-client` di Dockerfile),
> sehingga `DB_HOST=db` terbaca otomatis dari `.env`.

---

## Catatan Penting Buat Kamu
- **Simpan private key `.key`** baik-baik — kalau hilang, kamu tidak bisa masuk ke VPS lagi (harus bikin instance baru)
- Setelah setup awal selesai, **kamu tidak perlu SSH tiap hari** — VPS jalan sendiri 24 jam. SSH cuma diperlukan pas mau update kode (`git pull` + restart container)
- Kalau nanti mau update aplikasi setelah edit kode di laptop: push ke GitHub → SSH ke VPS → `git pull` → `docker compose restart` (atau minta opencode buatkan script `deploy.sh` biar tinggal 1 command)