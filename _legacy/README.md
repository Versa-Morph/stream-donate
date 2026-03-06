# StreamDonate

Platform donasi real-time untuk streamer. Penonton bisa kirim donasi langsung dari browser, alert muncul otomatis di OBS via Browser Source — tanpa install apapun, tanpa database.

---

## Fitur

- **Alert donasi real-time** di OBS via Server-Sent Events (SSE)
- **Antrian donasi** — beberapa donasi masuk diproses satu per satu
- **Request video YouTube** — donatur bisa request video yang langsung diputar di alert
- **Leaderboard overlay** — sidebar top donatur yang update otomatis
- **Milestone/progress bar** — target donasi stream hari ini
- **Dashboard** — riwayat, statistik, dan top donatur
- **Notifikasi suara** synthesized via Web Audio API

---

## Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | PHP 8+ (vanilla, tanpa framework) |
| Server | Apache (Laragon / XAMPP) |
| Real-time | Server-Sent Events (SSE) |
| Frontend | Vanilla JavaScript |
| Storage | JSON flat files (tanpa database) |
| Fonts | Inter + Space Grotesk (Google Fonts) |

---

## Cara Menjalankan

### Prasyarat

- **PHP 8.0+**
- **Apache** dengan `mod_headers` aktif (Laragon, XAMPP, atau server Apache lain)
- Browser modern (Chrome/Firefox/Edge)

### Langkah

**1. Clone atau copy project ke web root**

```
# Laragon
C:\laragon\www\streamdonate\

# XAMPP
C:\xampp\htdocs\streamdonate\
```

**2. Pastikan folder `data/` bisa ditulis oleh PHP**

```bash
# Linux/Mac
chmod 755 data/
chmod 644 data/*.json
```

Di Windows dengan Laragon/XAMPP biasanya sudah otomatis.

**3. Aktifkan Apache dan buka browser**

```
http://localhost/streamdonate/
```

atau jika menggunakan virtual host Laragon:

```
http://streamdonate.test/
```

**4. Sesuaikan URL widget di tab Overlay**

Ganti URL di kolom Widget URL sesuai domain lokal atau server kamu, lalu copy ke OBS.

---

## Struktur File

```
streamdonate/
├── index.php          # Panel utama streamer (4 tab: Donasi, Overlay, Dashboard, Settings)
├── overlay.php        # OBS Browser Source — alert donasi
├── leaderboard.php    # OBS Browser Source — sidebar top donatur
├── milestone.php      # OBS Browser Source — progress bar target donasi
├── .htaccess          # Konfigurasi Apache untuk SSE (disable buffering)
│
├── api/
│   ├── push.php       # POST — terima donasi baru
│   ├── stream.php     # GET  — SSE broadcast ke semua client
│   ├── stats.php      # GET  — statistik & leaderboard
│   └── config.php     # POST — simpan konfigurasi
│
└── data/
    ├── config.json    # Konfigurasi milestone & leaderboard
    ├── history.json   # Riwayat donasi permanen
    └── queue.json     # Antrian alert (TTL 5 menit)
```

---

## Setup OBS

1. Di OBS, klik **+** di panel Sources
2. Pilih **Browser**
3. Masukkan salah satu URL berikut:

| Widget | URL | Ukuran |
|---|---|---|
| Alert donasi | `http://domain-kamu/overlay.php` | 1920 × 1080 |
| Leaderboard | `http://domain-kamu/leaderboard.php` | 1920 × 1080 |
| Milestone bar | `http://domain-kamu/milestone.php` | 1920 × 1080 |

4. Centang **"Shutdown source when not visible"** dan **aktifkan transparent background**
5. Sesuaikan posisi di kanvas OBS

URL lengkap bisa langsung di-copy dari tab **Overlay** di panel streamer.

---

## Konfigurasi

Semua konfigurasi bisa diubah dari tab **Settings** di panel utama:

| Setting | Deskripsi |
|---|---|
| Judul milestone | Teks yang tampil di progress bar |
| Target donasi | Nominal target (Rp) |
| Judul leaderboard | Teks header sidebar top donatur |
| Jumlah donatur tampil | 3 – 20 entri |

Konfigurasi disimpan ke `data/config.json`.

---

## Reset Data

Tab **Settings** → bagian **Danger Zone** → tombol **Reset Riwayat Donasi**.

Atau manual: hapus isi `data/history.json` dan isi dengan `[]`.

---

## Catatan

- Project ini dirancang untuk **lokal / self-hosted**. Jika deploy ke server publik, tambahkan autentikasi pada endpoint `api/push.php` agar tidak bisa diakses sembarang orang.
- `data/queue.json` dibersihkan otomatis oleh `push.php` setelah 5 menit per item — tidak perlu maintenance manual.
- SSE reconnect otomatis setiap 3 detik jika koneksi terputus.
