# DTZY Profile API

Website portfolio/profil berbasis **PHP Native + MySQL + Bootstrap 5** dengan dua integrasi REST API:

- 🎵 **iTunes Search API** — pencarian lagu, artwork, dan preview audio.
- 🌤️ **Open-Meteo API** — pencarian lokasi + data cuaca real-time.
- 🗄️ **MySQL** — menyimpan profil, riwayat pencarian, dan lagu favorit.
- ❤️ **Favorit Musik** — simpan/hapus lagu favorit ke database.
- 📱 Responsive untuk desktop dan HP.

## 1. Persiapan Laragon

Salin folder `dtzy-profile-api` ke:

`C:\laragon\www\`

Kemudian jalankan **Apache** dan **MySQL** dari Laragon.

## 2. Import database

Buka:

`http://localhost/phpmyadmin`

Pilih menu **Import**, lalu import:

`database/dtzy_profile.sql`

Database `dtzy_profile` akan dibuat otomatis.

## 3. Cek konfigurasi database

File:

`config/database.php`

Default Laragon:

- host: localhost
- database: dtzy_profile
- username: root
- password: kosong

Kalau konfigurasi MySQL kamu berbeda, ubah file tersebut.

## 4. Jalankan

Buka:

`http://localhost/dtzy-profile-api/`

## 5. Struktur project

```text
dtzy-profile-api/
├── api/
│   ├── music.php
│   └── weather.php
├── assets/
│   ├── css/style.css
│   └── js/app.js
├── config/
│   ├── app.php
│   └── database.php
├── database/
│   └── dtzy_profile.sql
├── index.php
└── README.md
```

## API yang digunakan

### Music
Frontend memanggil:

`api/music.php?q=NIKI`

PHP server kemudian mengambil data dari iTunes Search API dan menyesuaikan hasilnya untuk ditampilkan pada aplikasi.

### Weather
Frontend memanggil:

`api/weather.php?city=Jakarta`

PHP mencari koordinat kota melalui Open-Meteo Geocoding API, lalu mengambil data cuaca Open-Meteo.

Kedua API pada versi ini tidak membutuhkan API key.

## Catatan
Pastikan PHP memiliki akses HTTPS keluar (`allow_url_fopen` aktif). Jika server tidak mengizinkan `file_get_contents()` untuk URL, endpoint API perlu diubah menggunakan cURL.

## Favorit Musik
Tombol **Favorit** pada hasil pencarian musik menyimpan data lagu ke tabel `favorite_music`. Bagian Musik Favorit menampilkan semua lagu yang tersimpan dan menyediakan tombol hapus.
