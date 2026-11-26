# Naskah Video Demo SiPegawai (Durasi 5–10 Menit)

## 0:00–0:40 — Pembukaan
"Pada video ini saya akan mendemonstrasikan aplikasi SiPegawai, yaitu aplikasi pengelolaan kepegawaian dan proyek yang dibuat untuk Mini Project Basis Data Lanjut. Aplikasi menggunakan PHP Native, MySQL, Bootstrap, dan JavaScript."

## 0:40–1:20 — Dashboard
Tampilkan dashboard. Jelaskan ringkasan jumlah pegawai, departemen, proyek, penugasan, total gaji, total anggaran, dan grafik gaji per departemen.

## 1:20–2:20 — Struktur Organisasi dan Pegawai
Buka Struktur Organisasi. Jelaskan bahwa hubungan atasan dan bawahan dibentuk melalui kolom `id_atasan` yang mereferensikan `pegawai.id_pegawai` (self join/relasi rekursif). Tunjukkan Hartono, Wulan, Yusuf, dan bawahan masing-masing. Tunjukkan rentang kendali minimal tiga bawahan (Soal 5a dan 5f).

Buka Data Pegawai. Tunjukkan kolom nama atasan, departemen, gaji, dan jumlah proyek. Jelaskan sumber data daftar pegawai menggunakan view `v_pegawai_dept` dan self join. Tunjukkan penanda pegawai tanpa proyek (Soal 5e dan 5g).

## 2:20–3:10 — Departemen
Buka Departemen. Tunjukkan data departemen, lokasi, jumlah pegawai, fungsi pencarian, serta tombol tambah, ubah, dan hapus. Jelaskan bahwa departemen yang masih digunakan pegawai/proyek tidak dapat dihapus karena foreign key.

## 3:10–4:10 — Proyek dan Penugasan
Buka Proyek. Tunjukkan jumlah anggota dan total jam kerja per proyek (Soal 5d). Buka Penugasan, tunjukkan data hubungan pegawai dengan proyek dan demonstrasikan tambah/ubah jam kerja bila diperlukan.

## 4:10–5:50 — Penggajian (Soal 5e dan 5j)
Pastikan database baru saja diimpor ulang sehingga data masih awal. Buka Penggajian, pilih departemen Teknologi Informasi dan kenaikan 10%. Klik Pratinjau untuk memperlihatkan lima pegawai beserta gaji lama dan gaji baru. Setelah memastikan datanya, klik Ya, Proses Kenaikan.

Jelaskan bahwa proses memakai satu perintah `UPDATE ... JOIN` dalam transaksi. Tunjukkan notifikasi jumlah baris yang berubah, yaitu lima baris. Kemudian tunjukkan anomali gaji: Doni memiliki gaji Rp17.600.000, lebih tinggi daripada atasannya Wulan sebesar Rp16.500.000 (Soal 5j).

## 5:50–7:30 — Laporan SQL
Buka Laporan SQL. Tunjukkan:
- Statistik gaji departemen (Soal 5b) — sebaiknya sudah diperiksa sebelum kenaikan gaji dijalankan.
- Departemen tanpa pegawai (Soal 5c), yaitu Legal.
- Ringkasan proyek (Soal 5d).
- Pegawai tanpa proyek (Soal 5g), yaitu Hartono dan Yusuf.
- Jumlah proyek dan total anggaran per departemen termasuk Legal bernilai nol (Soal 5h).
- Pegawai dengan lebih dari dua proyek (Soal 5i), yaitu Rudi dengan tiga proyek dan 240 jam.

## 7:30–8:00 — Penutup
"Demikian demonstrasi SiPegawai. Seluruh fitur utama mengintegrasikan query SQL latihan ke dalam aplikasi web. Aplikasi juga menerapkan prepared statement, validasi input, foreign key, dan transaksi untuk menjaga keamanan serta konsistensi data."
