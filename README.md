# StreamDonate

Platform donasi real-time untuk streamer. Penonton bisa kirim donasi langsung dari browser, alert muncul otomatis di OBS via Browser Source — tanpa plugin tambahan, tanpa layanan pihak ketiga.

---

## Fitur

- **Alert donasi real-time** di OBS via Server-Sent Events (SSE)
- **Antrian donasi** — beberapa donasi masuk diproses satu per satu, tidak saling tumpang tindih
- **Request video YouTube** — donatur bisa request video yang diputar langsung di alert
- **Leaderboard overlay** — panel top donatur yang update otomatis
- **Milestone overlay** — progress bar target donasi stream
- **Dashboard streamer** — riwayat, statistik, laporan CSV/PDF
- **Panel admin** — manajemen user, impersonate, log aktivitas
- **Notifikasi suara** synthesized via Web Audio API (5 tema: default, minimal, neon, fire, ice)
- **Live config sync** — ganti tema/suara di Settings langsung berlaku di OBS tanpa refresh

---

## Tech Stack

| Layer | Teknologi |
|---|---|
| Framework | Laravel 12 |
| PHP | 8.2+ |
| Database | SQLite (default) |
| Queue | Database (sync di local) |
| Real-time | Server-Sent Events (SSE) |
| Frontend | Vanilla JS + Blade |
| CSS | Custom Properties, Inter + Space Grotesk |
| Package | barryvdh/laravel-dompdf, simplesoftwareio/simple-qrcode |

---

## Requirements

- **PHP 8.2+** dengan ekstensi: `pdo`, `pdo_sqlite`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`
- **Composer 2.x**
- **Node.js 18+** + npm
- **Laragon** (Windows) — atau Apache/Nginx lain yang support `.htaccess`
- **SQLite** — sudah built-in di PHP, tidak perlu install database terpisah

---

## Instalasi di Laragon

### 1. Clone ke folder www

```bash
cd C:\laragon\www
git clone <repo-url> streamdonate-versamorph
```

Atau jika sudah ada foldernya, pastikan Laragon sudah mengenali virtual host-nya.

### 2. Aktifkan Pretty URL di Laragon

Laragon otomatis membuat virtual host untuk setiap folder di `www\`. Buka Laragon → klik kanan tray → **Apache → httpd.conf** dan pastikan `mod_rewrite` aktif (biasanya sudah aktif secara default).

### 3. Install dependensi PHP

```bash
cd C:\laragon\www\streamdonate-versamorph
composer install
```

### 4. Buat file `.env`

```bash
copy .env.example .env
```

Edit `.env`, sesuaikan nilai berikut:

```env
APP_NAME=StreamDonate
APP_URL=http://streamdonate-versamorph.test

DB_CONNECTION=sqlite
# Baris DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD boleh dibiarkan dikomentari

QUEUE_CONNECTION=sync
```

> **Catatan `QUEUE_CONNECTION=sync`**: Di lokal/Laragon tidak perlu menjalankan queue worker terpisah. Gunakan `sync` agar job diproses langsung saat donasi masuk.

### 5. Generate app key

```bash
php artisan key:generate
```

### 6. Buat file database SQLite

```bash
php artisan migrate
```

Jika file `database/database.sqlite` belum ada, buat dulu secara manual:

```bash
# Windows CMD
type nul > database\database.sqlite

# atau lewat Git Bash / PowerShell
New-Item database/database.sqlite -ItemType File
```

Lalu jalankan ulang:

```bash
php artisan migrate
```

### 7. Install dependensi Node & build assets

```bash
npm install
npm run build
```

> Untuk development dengan hot-reload: `npm run dev` (jalankan di terminal terpisah)

### 8. Set permission storage (jika perlu)

Di Windows + Laragon biasanya tidak diperlukan, tapi jika ada error permission:

```bash
php artisan storage:link
```

### 9. Buka di browser

```
http://streamdonate-versamorph.test
```

Akan diredirect ke halaman login. Buat akun admin pertama lewat Tinker:

```bash
php artisan tinker
```

```php
$user = \App\Models\User::create([
    'name'     => 'Admin',
    'email'    => 'admin@streamdonate.test',
    'password' => bcrypt('password'),
    'role'     => 'admin',
]);
$user->markEmailAsVerified();
```

---

## Setup OBS

### Tambahkan widget sebagai Browser Source

1. Di OBS, klik **+** di panel Sources → pilih **Browser**
2. Centang **"Use custom frame rate"** → set ke **30**
3. Centang **"Shutdown source when not visible"**
4. Pastikan **transparent background** aktif (Width: 1920, Height: 1080)

| Widget | URL | Keterangan |
|---|---|---|
| Alert donasi | `http://[domain]/[slug]/obs/overlay?key=[api_key]` | Posisi bebas di kanvas OBS |
| Leaderboard | `http://[domain]/[slug]/obs/leaderboard?key=[api_key]` | Panel `300px`, posisikan via crop/transform OBS |
| Milestone | `http://[domain]/[slug]/obs/milestone?key=[api_key]` | Panel `340px` bottom-left |

URL lengkap sudah tersedia di halaman **Dashboard → tab Overlay** milik masing-masing streamer, tinggal copy-paste.

### Mendapatkan API Key

Login sebagai streamer → **Dashboard** → bagian **Widget URLs** — API key sudah otomatis terlampir di URL.

---

## Struktur Aplikasi

```
streamdonate-versamorph/
│
├── app/
│   ├── Http/Controllers/
│   │   ├── DonationController.php      # Form donasi publik
│   │   ├── SseController.php           # SSE broadcast + stats endpoint
│   │   ├── ObsController.php           # Render OBS widget views
│   │   ├── StreamerDashboardController.php
│   │   └── AdminController.php
│   ├── Jobs/
│   │   └── ProcessDonationJob.php      # Proses donasi → simpan + broadcast SSE
│   └── Models/
│       ├── Streamer.php                # buildStats() untuk leaderboard & milestone
│       └── Donation.php
│
├── resources/views/
│   ├── obs/
│   │   ├── overlay.blade.php           # Alert donasi (5 tema, flat minimal)
│   │   ├── leaderboard.blade.php       # Top donatur floating panel
│   │   └── milestone.blade.php         # Progress bar compact panel
│   └── streamer/
│       ├── dashboard.blade.php
│       └── settings.blade.php
│
├── routes/web.php
├── database/database.sqlite            # Auto-created saat migrate
└── .env
```

---

## Perintah Berguna

```bash
# Jalankan semua (server + queue + vite) sekaligus
composer run dev

# Clear semua cache
php artisan optimize:clear

# Reset database (hati-hati: hapus semua data)
php artisan migrate:fresh

# Lihat log real-time
php artisan pail
```

---

## Catatan

- File legacy PHP vanilla (sebelum migrasi ke Laravel) tersimpan di folder `_legacy/` sebagai referensi.
- Untuk deploy ke server publik, pastikan `APP_DEBUG=false` dan `QUEUE_CONNECTION=database` dengan queue worker aktif (`php artisan queue:work`).
- SSE reconnect otomatis setiap 3 detik jika koneksi terputus.
- Perubahan tema/suara di halaman Settings akan berlaku di OBS dalam ~20 detik tanpa perlu refresh Browser Source.
