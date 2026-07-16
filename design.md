# Design System — Madani-SMS
### Sistem Informasi Manajemen SMA (Sore) Dayah Madani Al-Aziziyah
*Modul: Absensi Digital & Jadwal Pelajaran*

---

## 1. Ringkasan Produk

Madani-SMS mendigitalisasi absensi per jam pelajaran dan jadwal blok sore (14.00–16.00 WIB) untuk SMA formal di lingkungan Dayah Madani Al-Aziziyah, menggantikan proses manual yang memakan waktu KBM yang sudah sangat terbatas (hanya 2 jam/hari).

**Tiga peran pengguna:**
| Peran | Kebutuhan Utama |
|---|---|
| **Guru Bidang Studi** | Absensi cepat (<1 menit) sebelum mengajar, input nilai per komponen (Tugas, Harian, UTS, UAS) |
| **Admin / Kepala Sekolah** | Mapping guru↔mapel, atur jadwal blok sore, pantau kehadiran guru, ekspor e-rapor PDF |
| **Wali Murid / Santri** | Memantau absensi & nilai anak secara periodik dari luar dayah |

**Target performa:** halaman absensi termuat < 2 detik di 3G/4G, sinkron submit < 3 detik, mobile-first, RBAC per kelas/mapel guru, backup harian jam 17.00 WIB.

---

## 2. Dua Mode Desain

Repo ini berisi **dua sistem desain paralel** untuk produk yang sama, dipakai sesuai konteks perangkat/pengguna:

| | ☀️ Academic Precision (Light) | 🌙 Academic Precision Dark |
|---|---|---|
| **Kepribadian** | Authoritative, dependable, terorganisir — "structured calm" | Intelektual, fokus malam hari — "nocturnal focus / digital sanctuary" |
| **Font Headline** | Inter (Sans, 700) | Source Serif 4 (Serif, 700) |
| **Font Body** | Inter | Hanken Grotesk |
| **Font Label/Data** | Inter (uppercase, tracking) | JetBrains Mono (monospace) |
| **Primary Color** | `#2563EB` Trust Blue | `#60A5FA` Electric Blue (softened) |
| **Background** | `#F7F9FB` off-white | `#0B1326` deep navy / ink |
| **Radius dasar** | 8px (rounded, approachable) | 4px (rectilinear, "arsitektural") |
| **Depth** | Soft shadow (blur+opacity) | Tonal layering + 1px outline, tanpa shadow |
| **Dipakai di** | `absensi_siswa`, `jadwal_pelajaran`, `absensi_siswa_mobile`, `jadwal_pelajaran_mobile`, dashboard & modul Madani-SMS (light) | `absensi_siswa_mobile_dark`, `jadwal_pelajaran_mobile_dark` |

Spesifikasi token lengkap (colors, typography, spacing) ada di masing-masing:
- `academic_precision/DESIGN.md` (light)
- `academic_precision_dark/DESIGN.md` (dark)

---

## 3. Warna

### Light Mode
| Token | Hex | Peran |
|---|---|---|
| Primary | `#2563EB` | Tombol utama, nav aktif, focus state |
| Secondary | `#64748B` | Teks & ikon pendukung |
| Surface / Card | `#FFFFFF` | Kartu, container |
| Background | `#F8FAFC` | Kanvas utama |
| Border | `#E2E8F0` | Garis pemisah kartu (1px) |
| Presence (Hadir) | Emerald (bg muda, teks hijau tua) | Badge kehadiran |
| Permit (Izin/Sakit) | Amber (bg muda, teks amber tua) | Badge izin/sakit |
| Absence (Alpa) | Rose (bg muda, teks merah tua) | Badge alpa |

### Dark Mode
| Token | Hex | Peran |
|---|---|---|
| Primary | `#60A5FA` | Tombol utama, aksen fokus |
| Background (Ink) | `#020617` / `#0B1326` | Kanvas terjauh |
| Surface Level 1 | `#0F172A` | Card & container default |
| Surface Level 2 | `#1E293B` | Hover / modal / elevated |
| Outline | `#334155` (Slate 700) | Border 1px pengganti shadow |
| Text | Off-white pada headline serif, Slate untuk body |

> Palet semantik absensi (Hadir/Izin/Sakit/Alpa) tetap konsisten secara fungsi di kedua mode — hanya kontras & saturasi disesuaikan untuk latar gelap/terang.

---

## 4. Tipografi

**Light Mode — Inter (single family, semua berat):**
- `headline-xl` 36px/700 — judul halaman
- `headline-lg` 24px/600 (20px di mobile) — judul kartu/section
- `headline-md` 18px/600
- `body-lg` 16px/400, `body-md` 14px/400
- `label-md` 12px/600, uppercase, tracking 0.05em — label status, header tabel
- `caption` 12px/400 — metadata

**Dark Mode — Kombinasi 3 font:**
- **Source Serif 4** (headline) — kesan "terbit/tercetak", otoritatif
- **Hanken Grotesk** (body) — teks antarmuka & baca panjang
- **JetBrains Mono** (label & data tabular) — jam pelajaran, angka nilai, kode kelas → memperkuat kesan presisi

---

## 5. Layout & Grid

- Grid **12 kolom** desktop, **4 kolom** mobile.
- Baseline spacing **4px**; padding komponen umum 16px (md) / 24px (lg).
- **Grid Jadwal Pelajaran:** baris = jam pelajaran (blok sore 14.00–16.00), kolom = hari — pada mobile berubah jadi tampilan **List-Agenda vertikal** per hari.
- Margin luar: 48px (light) / 64px (dark) di desktop, 16px di mobile.
- Mobile-first wajib untuk halaman Absensi & Input Nilai (dipakai guru langsung dari kelas via smartphone).

---

## 6. Elevasi

| Level | Light | Dark |
|---|---|---|
| 0 – Base | `#F8FAFC` flat | `#020617` flat |
| 1 – Card | Putih + border 1px `#E2E8F0`, tanpa shadow | `#0F172A` + top-border 2px "ledger accent" |
| 2 – Hover/Interaksi | Shadow blur 4px, opacity 10%, offset-y 2px | Background shift ke `#1E293B` |
| 3 – Modal/Popover | Shadow blur 12px, opacity 15% | Outline glow primary 10–15% opacity |

---

## 7. Shape

| Elemen | Light | Dark |
|---|---|---|
| Checkbox/elemen kecil | 4px | — |
| Button, Input, Card | 8px | 4px |
| Modal/Banner besar | 12–16px | 8px |
| Badge status | Pill penuh (999px) | Hindari pill kecuali toggle ikon |

Dark mode sengaja **lebih rektilinear** (radius lebih kecil, tanpa pill) untuk menegaskan nuansa data/akademik yang presisi, dibanding light mode yang lebih "approachable".

---

## 8. Komponen Kunci

- **Badge Absensi** — pill (light) / mono chip (dark), 4 status: **H**adir (hijau/emerald), **S**akit & **I**zin (amber), **A**lpa (merah/rose). Muncul konsisten di semua halaman absensi & e-rapor.
- **Kartu Jadwal** — accent border kiri berwarna per kategori mapel, blok waktu terkunci ke rentang 14.00–16.00 WIB.
- **Tabel Nilai** — kolom komponen (Tugas / Harian / UTS / UAS) + Nilai Akhir terhitung otomatis; header pakai `label-md`/`label-caps`, angka pakai font tabular (mono di dark mode).
- **Form Input** — border 1px, berubah ke warna primary saat fokus; label di atas field.
- **Tombol** — Primary: solid primary color + teks kontras. Secondary: ghost/outline.
- **Preview E-Rapor** — layout mirip dokumen cetak resmi, siap ekspor PDF.

---

## 9. Peta Halaman (Screens dalam Zip)

| File / Folder | Deskripsi | Mode |
|---|---|---|
| `dashboard_guru_madani_sms` | Dashboard ringkasan guru: jadwal hari ini, status absensi, akses cepat | Light |
| `absensi_real_time_madani_sms` | Form absensi per jam pelajaran, checklist H/S/I/A | Light |
| `input_nilai_madani_sms` | Input nilai Tugas/Harian/UTS/UAS per bidang studi | Light |
| `pratinjau_e_rapor_madani_sms` | Preview e-rapor gabungan sebelum ekspor PDF | Light |
| `absensi_siswa` (+ `_mobile`, `_mobile_dark`) | Manajemen kehadiran siswa, desktop & mobile | Light / Dark |
| `jadwal_pelajaran` (+ `_mobile`, `_mobile_dark`) | Jadwal pelajaran mingguan, grid desktop & agenda mobile | Light / Dark |

> Catatan: sebagian file (`absensi_siswa*`, `jadwal_pelajaran*`) memakai judul generik "EduFlow Pro / EduManage" — ini adalah versi eksplorasi awal Stitch sebelum branding difinalkan menjadi **Madani-SMS**. Semua tetap mengikuti token desain `academic_precision` yang sama.

---

## 10. Prinsip Desain

1. **Kecepatan di atas segalanya** — jam KBM sore hanya 2 jam; setiap layar absensi harus bisa diselesaikan dalam hitungan detik, kontras tinggi, teks ≥12pt.
2. **Struktur ketat, bukan dekoratif** — grid & tipografi konsisten mengurangi beban kognitif guru yang terburu-buru.
3. **Semantik warna yang jelas** — status kehadiran & nilai harus bisa dikenali sekilas tanpa membaca teks.
4. **Mobile-first untuk guru, desktop-rich untuk admin** — dashboard/e-rapor lebih kaya data di desktop, sementara absensi dioptimalkan untuk smartphone di kelas.
5. **Dua mode, satu identitas** — light untuk siang hari/penggunaan aktif kelas, dark untuk kerja administratif/analisis malam hari — tetap terasa sebagai satu produk yang sama.