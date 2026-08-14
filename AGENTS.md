<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.3
- laravel/framework (LARAVEL) - v13
- laravel/prompts (PROMPTS) - v0
- laravel/sanctum (SANCTUM) - v4
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- phpunit/phpunit (PHPUNIT) - v12
- tailwindcss (TAILWINDCSS) - v4

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should cover all happy paths, failure paths, and edge cases.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

## Running Tests

- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test --compact`.
- To run all tests in a file: `php artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --compact --filter=testName` (recommended after making a change to a related file).

</laravel-boost-guidelines>

<!-- ============================================================
     RENCANA / PEMBAHASAN PRODUK (untuk klien) — 13 Agust 2026
     ============================================================ -->

## Pembahasan Fitur Baru (disetujui klien)

### 1. Peran & Kontrol Admin terhadap Bendahara
- Admin tetap **mengelola akun** bendahara: nonaktifkan/aktifkan, reset password sekali pakai.
- Pencatatan & pembatalan SPP **hanya** dilakukan bendahara (pemisahan tugas tetap berlaku; admin hanya melihat rekap). [Alasan: anti-manipulasi/check-and-balance]

### 2. Admin: Tambah / Edit / Hapus Pengguna
- Admin dapat membuat akun baru (role: admin, bendahara, guru, wali_murid) lengkap dengan password default yang **wajib diganti saat login pertama**.
- Bisa edit data akun (nama, email, role, telepon, alamat) dan hapus akun.
- Guard: tidak bisa hapus diri sendiri; tidak bisa nonaktif/hapus admin aktif terakhir; tidak bisa hapus pengguna yang masih terkait data (wali→siswa, guru→mata pelajaran).

### 3. Modul Pengumuman & Lowongan Guru
- Modul **Pengumuman** baru: admin bisa membuat/mengedit/menghapus/menerbitkan pengumuman.
- Kategori: `umum` dan `lowongan_guru`; status: draft/terbit.
- Halaman publik **Lowongan** (tanpa login, `/lowongan`) menampilkan pengumuman kategori lowongan_guru yang sudah diterbitkan, ditaut dari beranda/login.
- Widget "Pengumuman Sekolah" di dashboard wali murid memakai data asli (tidak lagi statis).

### Status
- [ ] Belum dieksekusi (menunggu persetujuan klien).

<!-- ============================================================
     PEMBAHASAN UX RAPOR WALI MURID — 13 Agust 2026
     ============================================================ -->

## Pembahasan Rapor Wali Murid (disetujui)

### Temuan
- Rapor dihitung **otomatis** dari nilai yang diisi guru pengampu/admin (menu *Nilai*) × bobot komponen Tugas/PH/UTS/UAS (`score_components`, dikonfigurasi admin di *Bobot Nilai*). Tidak ada entri khusus rapor.
- Data DB saat ini: 72 nilai untuk 9/9 siswa, seluruhnya di TA **2025/2026 ganjil**; komponen bobot ada untuk ganjil & genap. Jadi semua siswa punya rapor untuk ganjil 2025/2026.
- **Akar masalah: wali murid tidak punya menu/akses Rapor.**
  - Nav wali murid (`resources/views/layouts/app.blade.php:22-29`): tidak ada item Rapor.
  - Dashboard wali (`resources/views/wali-murid/dashboard.blade.php`): hanya menampilkan siswa pertama tanpa tombol/link Rapor.
  - Nav "E-Rapor" hanya untuk guru/admin; route `scores.rapor-preview` dilindungi `Gate::authorize('view', $student)` → wali murid 403.
- Route rapor wali sebenarnya ada & berfungsi: `/app/wali-murid/rapor/{student}` (`routes/web.php:159`, name `wali-murid.rapor`) — tapi tidak pernah ditautkan.

### Rencana (disetujui untuk dieksekusi)
1. **Entry point**: tambah item nav "Rapor" (icon `assignment`) untuk role wali murid → `route('wali-murid.rapor', <siswa pertama>)`; tambah tombol "Lihat Rapor" di kartu profil siswa pada dashboard wali (sebaris dengan Surat Aktif / Surat).
2. **Halaman `wali-murid/rapor.blade.php`**: tampilkan semua mapel kelas siswa dengan badge "Nilai belum diisi guru pengampu" untuk yang kosong; pesan kosong total lebih informatif; default dropdown tetap ganjil + 2025/2026.
3. **Verifikasi**: `tests/Feature/ScoreTest.php` (policy rapor wali), `php artisan test --compact`, `npm run build`, cek manual mode wali murid.

### Status
- [ ] Belum dieksekusi.

<!-- ============================================================
     PEMBAHASAN REFACTOR ROUTING — 13 Agust 2026
     ============================================================ -->

## Refactor: Pindahkan Logika dari routes/web.php ke Controller (disetujui)

### Temuan
- `routes/web.php` = 826 baris dengan ±49 route closure berisi logika (CRUD siswa/mapel/jadwal/bobot, dashboard, absensi, users, rapor, profil). Ini menghambat:
  - `php artisan route:cache` **tidak bisa** dijalankan (ada route closure) → wajib untuk produksi.
  - Sulit dibaca/di-test (logika tidak reusable, tidak bisa unit-test langsung).
- Pola benar sudah ada: `ClassroomController`/`SubjectController` memakai metode `webIndex/webCreate/webStore/webEdit/webUpdate/webDestroy` untuk halaman + metode JSON untuk API.
- `StudentController`, `TeacherSubjectController`, `ScheduleController`, `ScoreComponentController` hanya punya metode API (dipakai `routes/api.php`) → logika web diduplikasi di closure. API tidak boleh diubah; hanya tambah metode `web*`.

### Rencana (disetujui untuk dieksekusi)
1. **Controller baru**: `HomeController` (redirect `/` & `/login` by role), `ProfileController`, `WaliMuridController` (dashboard + rapor), `DashboardController` (guru), `AdminController` (admin dashboard), `UserController` (index/reset-password/toggle-active/password-reveal), `ActivityLogController`.
2. **Tambah metode `web*`** ke existing controller (isi = logika closure di-copy verbatim): `StudentController` (+8), `TeacherSubjectController` (+6), `ScheduleController` (guru: `webIndex`/`webMobile`; admin CRUD `webAdminIndex`+5), `ScoreComponentController` (+6), `AttendanceController` (`webIndex`/`webForm`/`webRealtime`), `TeacherAttendanceController` (`webForm`/`webIndex`/`webAdminIndex`), `ScoreController` (`webCreate`/`webRaporPreview`).
3. **`routes/web.php`**: ganti semua closure → `[Controller::class, 'method']`; pertahankan middleware & throttle; hapus `use` tidak terpakai. Target ±330 baris murni routing.
4. **Bonus (opsional)**: dedupe query jadwal `->unique(fn...)` yang diulang 4×.
5. **Verifikasi**: `php artisan route:list` (nama/URI identik), `php artisan route:cache` harus berhasil, `php artisan test --compact` (137 test), `vendor/bin/pint --format agent`, smoke test manual.

### Status
- [ ] Belum dieksekusi.


Konten yang akan ditambahkan:
<!-- ============================================================
     PEMBAHASAN BAYAR SPP ONLINE WALI MURID — 14 Agust 2026
     ============================================================ -->

## Pembayaran SPP Online Wali Murid (disetujui)

### Temuan
- Wali murid saat ini TIDAK bisa membayar SPP digital; tidak ada tombol "Bayar Online" aktif:
  - `spp/index.blade.php:75` — tombol Bayar/Batalkan hanya dirender untuk bendahara.
  - `spp/payer.blade.php:127-132` — wali hanya lihat badge statis "Menunggu konfirmasi pembayaran".
  - `POST /app/spp/bayar` (`markPaid`) dibungkus `role:bendahara` (`web.php:236`) → wali 403.
- Bug lama yang diperbaiki (Opsi A): tombol "Bayar Sekarang" dashboard wali POST ke route GET `spp.index` (405) → jadi tautan ke `spp.payer`; `Carbon::day('Do MMMM YYYY')` (500) → `isoFormat()`.
- Tidak ada payment gateway terpasang (composer.json/config/env kosong). Klien belum punya akun merchant.

### Keputusan klien (14 Agust 2026)
- Alur: **upload bukti + verifikasi bendahara** (bukan integrasi gateway sungguhan).
- Bukti transfer/QRIS **wajib** diunggah wali.
- Bulan pembayaran dikunci ke **bulan berjalan** saja.
- Check-and-balance tetap: hanya bendahara yang mencatat lunas (membuat kwitansi) — wali hanya mengajukan.

### Rencana (disetujui untuk dieksekusi)
1. **Migration + model baru `PaymentSubmission`** (`payment_submissions`): student_id, month, year, amount, method, reference, proof_path, note, status (pending/approved/rejected), submitted_by, reviewed_by, reviewed_at, reject_reason; unique [student_id, month, year].
2. **`StorePaymentSubmissionRequest`**: validasi method valid, bukti wajib, bulan = bulan berjalan.
3. **`SPPController`**: `submitProof` (wali), `submissionsIndex` (bendahara), `approveSubmission` (buat `PaymentReceipt` via `ReceiptNumberService` + set `StudentFee.is_paid`, dalam `DB::transaction` + `ActivityLogger`), `rejectSubmission` (status rejected + alasan, tanpa kwitansi).
4. **Routes**: `POST /app/spp/bukti` (wali), `GET /app/bendahara/verifikasi` + `POST /{submission}/setujui` + `POST /{submission}/tolak` (bendahara).
5. **Views**: `spp/payer.blade.php` (form upload + panel status submission), `wali-murid/dashboard.blade.php` (badge "Menunggu Verifikasi"), baru `bendahara/submissions.blade.php` (antrian + lihat bukti + setujui/tolak), dashboard bendahara badge "X bukti menunggu".
6. **Config**: `config/school.php` berisi rekening/QRIS resmi (tujuan transfer, diisi ulang sesuai akun madrasah).
7. **Test**: `tests/Feature/SPPTest.php` +8 test (submit valid, ownership guard, duplikat, approve→kwitansi, reject, 403 role lain).

### Status
- [ ] Belum dieksekusi.
Catatan: blok diskusi sebelumnya di AGENTS.md memang berada di dalam tag <laravel-boost-guidelines> (baris 173–241 sebenarnya sudah di luar tag penutup 171? — cek: tag ditutup di baris 171, blok diskusi ada setelahnya, jadi saya append di paling akhir file, konsisten).
Setujui untuk saya tulis blok ini ke AGENTS.md? (Saya masih di plan mode, tidak akan mengeksekusi sebelum Anda konfirmasi.)
</laravel-boost-guidelines>
