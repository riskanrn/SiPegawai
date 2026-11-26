# SiPegawai — Sistem Kepegawaian dan Proyek

Mini Project Mata Kuliah Basis Data Lanjut, Kasus 5.

Aplikasi web untuk mengelola pegawai, struktur organisasi, departemen, proyek, penugasan, dan penyesuaian gaji. Dibuat dengan PHP Native, MySQL/MariaDB, Bootstrap 5, dan JavaScript.

## Teknologi
- PHP 8.0+
- MySQL 5.7+ atau MariaDB 10.4+
- Apache (disarankan XAMPP)
- HTML, CSS, Bootstrap 5, JavaScript, Chart.js
- PDO prepared statement

## Instalasi di XAMPP
1. Ekstrak folder `SiPegawai` ke `C:\xampp\htdocs\`.
2. Jalankan Apache dan MySQL dari XAMPP Control Panel.
3. Buka `http://localhost/phpmyadmin`.
4. Impor `database/01_schema_dan_data.sql`.
5. Setelah selesai, impor `database/02_tambahan.sql`.
6. Salin `config/local.example.php` menjadi `config/local.php`.
7. Sesuaikan konfigurasi MySQL di `config/local.php` bila berbeda dari bawaan XAMPP.
8. Buka `http://localhost/SiPegawai/`.

Database: `db_kepegawaian`. Konfigurasi XAMPP bawaan: host `127.0.0.1`, username `root`, password kosong. Tidak ada proses login pada versi mini project ini.

## Struktur folder
```text
SiPegawai/
├── app/core/Database.php
├── app/helpers.php
├── app/models/SiPegawaiModel.php
├── app/views/layout.php
├── app/views/pages/
├── config/config.php
├── config/local.example.php
├── database/01_schema_dan_data.sql
├── database/02_tambahan.sql
├── docs/
├── public/assets/css/style.css
├── public/assets/js/app.js
├── public/index.php
└── index.php
```

## Pemetaan Soal 5a–5j
| Soal | Fitur aplikasi | Konsep SQL |
|---|---|---|
| 5a | Nama atasan dan bagan organisasi | LEFT SELF JOIN, IFNULL |
| 5b | Statistik gaji departemen | AVG, MAX, MIN, GROUP BY, HAVING |
| 5c | Departemen tanpa pegawai | LEFT JOIN, IS NULL |
| 5d | Jumlah anggota dan total jam per proyek | COUNT, SUM, GROUP BY |
| 5e | Penyesuaian gaji massal dan view pegawai | UPDATE JOIN, CREATE VIEW |
| 5f | Atasan dengan minimal 3 bawahan | SELF JOIN, HAVING |
| 5g | Pegawai tanpa proyek | LEFT JOIN |
| 5h | Jumlah proyek dan anggaran per departemen termasuk 0 | LEFT JOIN, IFNULL |
| 5i | Pegawai dengan lebih dari 2 proyek | COUNT, SUM, HAVING |
| 5j | Gaji pegawai lebih besar daripada atasan | SELF JOIN, perbandingan kolom |

Query latihan ditulis eksplisit di `app/models/SiPegawaiModel.php` dengan komentar kode soal. Query Soal 5a–5j tidak menggunakan subquery.

## Urutan demo wajib
Dokumen proyek menyebut hasil Soal 5f–5j dihitung setelah kenaikan gaji 10% departemen Teknologi Informasi (Soal 5e). Karena itu:
1. Impor ulang skrip database 01 dan 02 agar data kembali ke kondisi awal.
2. Buka menu **Penggajian**.
3. Pilih **Teknologi Informasi**, masukkan **10%**, lalu klik **Pratinjau**.
4. Periksa daftar lima pegawai dan klik **Ya, Proses Kenaikan**.
5. Hasil yang diharapkan: UPDATE mengubah 5 baris.
6. Buka Struktur Organisasi, Pegawai, dan Laporan SQL untuk memeriksa hasil 5f–5j.

Aplikasi mencatat penyesuaian per departemen agar kenaikan gaji tidak diterapkan berulang kali. Untuk mengulang demo, jalankan kembali skrip 01 dan 02.

## Validasi dan keamanan
- Input formulir diproses dengan PDO prepared statement.
- Formulir POST menggunakan token CSRF.
- Validasi dilakukan di sisi server.
- Pegawai tidak boleh menjadi atasan bagi dirinya sendiri.
- Pegawai yang masih memiliki bawahan atau penugasan tidak dapat dihapus.
- Departemen yang masih digunakan pegawai/proyek tidak dapat dihapus.
- Proyek yang masih memiliki penugasan tidak dapat dihapus.
- Kenaikan gaji massal dijalankan dalam transaksi (`beginTransaction`, `commit`, `rollBack`).
- Foreign key tabel inti dipertahankan.
- Pencarian dan paginasi tersedia pada daftar pegawai dan proyek.

## Git
Setiap anggota kelompok sebaiknya membuat commit secara berkala sesuai instruksi dosen. Contoh:
```bash
git init
git add .
git commit -m "Inisialisasi proyek SiPegawai"
git add .
git commit -m "Membuat modul data pegawai dan departemen"
git add .
git commit -m "Membuat laporan SQL dan penyesuaian gaji"
```

## Luaran yang masih perlu dilengkapi kelompok
- Laporan akhir PDF dengan tangkapan layar aktual.
- Lembar pengujian yang telah diisi berdasarkan hasil aplikasi.
- Video demo 5–10 menit.
- Tautan repositori GitHub/GitLab.
