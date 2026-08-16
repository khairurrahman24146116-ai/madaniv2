#!/usr/bin/env bash
# ============================================================
# deploy.sh — update aplikasi di VPS (dijalankan PAKAI BASH)
#
# Penggunaan:
#   sudo bash deploy.sh            # jalankan sebagai root
#   PUBLIC_IP=1.2.3.4 bash deploy.sh  # sekaligus set APP_URL
#
# Asumsi:
#   - Sudah pernah clone repo + buat file .env (lihat DEPLOY.md)
#   - File deploy.sh ada di root repo di VPS
# ============================================================
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

if [ ! -f .env ]; then
    echo "ERROR: file .env belum ada di $SCRIPT_DIR"
    echo "Buat dulu dari deploy/.env.production.example dan isi nilainya."
    exit 1
fi

echo "==> Update kode dari git..."
git pull --ff-only

echo "==> Build & jalankan container..."
docker compose up -d --build

echo "==> Tunggu database siap..."
for i in $(seq 1 30); do
    if docker compose exec -T db mysqladmin ping -h localhost -u root --password="$(grep -E '^DB_ROOT_PASSWORD=' .env | cut -d= -f2-)" --silent 2>/dev/null; then
        echo "    DB siap."
        break
    fi
    [ "$i" = 30 ] && { echo "ERROR: database tidak siap setelah 30x percobaan."; exit 1; }
    sleep 2
done

echo "==> Jalankan migration..."
docker compose exec -T app php artisan migrate --force

echo "==> Jalankan ProductionSeeder (aman, idempotent) — hanya bikin admin & bendahara..."
docker compose exec -T app php artisan db:seed --class=ProductionSeeder --force

PUBLIC_IP="${PUBLIC_IP:-$(grep -E '^APP_URL=' .env | cut -d= -f2-)}"
echo ""
echo "============================================="
echo "  Deploy selesai."
echo "  Buka aplikasi: ${PUBLIC_IP}"
echo "  Cek log:        docker compose logs -f app"
echo "============================================="