DROP DATABASE IF EXISTS db_kepegawaian;
CREATE DATABASE db_kepegawaian CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_kepegawaian;

CREATE TABLE departemen (
  id_dept INT PRIMARY KEY,
  nama_dept VARCHAR(50) NOT NULL,
  lokasi VARCHAR(30)
) ENGINE=InnoDB;

CREATE TABLE pegawai (
  id_pegawai INT PRIMARY KEY,
  nama VARCHAR(50) NOT NULL,
  jabatan VARCHAR(30),
  gaji DECIMAL(12,2) NOT NULL,
  id_dept INT,
  id_atasan INT,
  FOREIGN KEY (id_dept) REFERENCES departemen(id_dept),
  FOREIGN KEY (id_atasan) REFERENCES pegawai(id_pegawai)
) ENGINE=InnoDB;

CREATE TABLE proyek (
  id_proyek INT PRIMARY KEY,
  nama_proyek VARCHAR(50) NOT NULL,
  id_dept INT,
  anggaran DECIMAL(15,2),
  FOREIGN KEY (id_dept) REFERENCES departemen(id_dept)
) ENGINE=InnoDB;

CREATE TABLE penugasan (
  id_pegawai INT NOT NULL,
  id_proyek INT NOT NULL,
  jam_kerja INT NOT NULL DEFAULT 0,
  PRIMARY KEY (id_pegawai, id_proyek),
  FOREIGN KEY (id_pegawai) REFERENCES pegawai(id_pegawai),
  FOREIGN KEY (id_proyek) REFERENCES proyek(id_proyek)
) ENGINE=InnoDB;

INSERT INTO departemen VALUES
(1, 'Teknologi Informasi', 'Jakarta'),
(2, 'Keuangan', 'Jakarta'),
(3, 'Pemasaran', 'Bandung'),
(4, 'Legal', 'Jakarta');

INSERT INTO pegawai VALUES
(101, 'Hartono', 'Direktur', 25000000, 1, NULL),
(102, 'Wulan', 'Manajer TI', 15000000, 1, 101),
(103, 'Yusuf', 'Manajer Keuangan', 14000000, 2, 101),
(104, 'Rudi', 'Programmer', 9000000, 1, 102),
(105, 'Sari', 'Programmer', 8500000, 1, 102),
(106, 'Doni', 'Analis Sistem', 16000000, 1, 102),
(107, 'Indah', 'Akuntan', 7500000, 2, 103),
(108, 'Bima', 'Staf Pemasaran', 6000000, 3, 101);

INSERT INTO proyek VALUES
(201, 'Sistem ERP', 1, 500000000),
(202, 'Aplikasi Mobile', 1, 300000000),
(203, 'Audit Internal', 2, 150000000),
(204, 'Kampanye Digital', 3, 200000000);

INSERT INTO penugasan VALUES
(102, 201, 30),
(104, 201, 120),
(104, 202, 80),
(104, 204, 40),
(105, 201, 100),
(106, 201, 60),
(106, 202, 90),
(107, 203, 110),
(108, 204, 70);
