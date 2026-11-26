# Panduan Laporan PDF SiPegawai

Gunakan kerangka ini untuk menyusun laporan akhir. Tambahkan identitas kelompok, tangkapan layar aktual, hasil pengujian, dan tautan repositori sebelum diekspor menjadi PDF.

## 1. Deskripsi Sistem
SiPegawai merupakan aplikasi web untuk mengelola data pegawai, hubungan atasan dan bawahan, departemen, proyek, penugasan pegawai, serta penyesuaian gaji. Basis data relasional menggunakan empat tabel inti: `departemen`, `pegawai`, `proyek`, dan `penugasan`.

## 2. Teknologi
- Backend: PHP Native.
- Database: MySQL/MariaDB.
- Frontend: HTML, CSS, Bootstrap 5, JavaScript.
- Akses data: PDO prepared statement.
- Grafik: Chart.js.

## 3. Rancangan Basis Data
Sertakan ERD Kasus 5 dari dokumen latihan. Jelaskan relasi departemen–pegawai (1:N), departemen–proyek (1:N), pegawai–pegawai (rekursif melalui `id_atasan`), dan pegawai–proyek (M:N melalui `penugasan`).

## 4. Implementasi Fitur dan Query
| Soal | Fitur | Metode query |
|---|---|---|
| 5a | Nama atasan dan bagan organisasi | `getOrganizationEmployees()`, `getEmployees()` |
| 5b | Statistik gaji departemen | `reportDepartmentSalary()` |
| 5c | Departemen tanpa pegawai | `reportEmptyDepartments()` |
| 5d | Jumlah anggota dan jam proyek | `getProjects()` |
| 5e | Kenaikan gaji dan view | `applyRaise()`, `02_tambahan.sql` |
| 5f | Atasan minimal tiga bawahan | `reportSupervisors()` |
| 5g | Pegawai tanpa proyek | `reportEmployeesWithoutProjects()` |
| 5h | Proyek dan anggaran per departemen | `reportDepartmentProjects()` |
| 5i | Pegawai di lebih dari dua proyek | `reportEmployeesWithManyProjects()` |
| 5j | Anomali gaji pegawai dan atasan | `reportSalaryAnomalies()` |

Cantumkan query SQL dari model sesuai kode soal, uraikan konsep JOIN/agregasi/HAVING, dan lampirkan hasil aktual dari aplikasi. Query latihan tidak menggunakan subquery.

## 5. Tangkapan Layar
Ambil screenshot aktual dari Dashboard, Struktur Organisasi, Daftar Pegawai, Form Pegawai, Departemen, Proyek, Penugasan, Pratinjau Penyesuaian Gaji, dan Laporan SQL. Beri nomor dan keterangan pada setiap gambar.

## 6. Pengujian
Lampirkan lembar pengujian `LEMBAR_PENGUJIAN.md` yang telah diisi berdasarkan hasil nyata, disertai bukti screenshot.

## 7. Kesimpulan
Simpulkan hasil pengembangan setelah semua fitur diuji. Jelaskan penerapan query SQL, prepared statement, validasi, foreign key, dan transaksi pada penyesuaian gaji.

## 8. Lampiran
Tautan repositori GitHub/GitLab, tautan video demo 5–10 menit, dan skrip SQL.
