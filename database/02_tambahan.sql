USE db_kepegawaian;

CREATE OR REPLACE VIEW v_pegawai_dept AS
SELECT p.id_pegawai, p.nama, p.jabatan, p.gaji, d.nama_dept, p.id_dept, p.id_atasan
FROM pegawai p
LEFT JOIN departemen d ON d.id_dept = p.id_dept;

CREATE TABLE IF NOT EXISTS log_penyesuaian_gaji (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    id_dept INT NOT NULL,
    persentase DECIMAL(5,2) NOT NULL,
    jumlah_pegawai INT NOT NULL,
    tanggal_penyesuaian TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_log_penyesuaian_dept (id_dept),
    CONSTRAINT fk_log_penyesuaian_dept FOREIGN KEY (id_dept) REFERENCES departemen(id_dept)
) ENGINE=InnoDB;
