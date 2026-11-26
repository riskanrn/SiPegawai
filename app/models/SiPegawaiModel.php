<?php
declare(strict_types=1);

final class SiPegawaiModel
{
    private PDO $db;

    public function __construct(PDO $db) { $this->db = $db; }

    private function all(string $sql, array $params = []): array {
        $s = $this->db->prepare($sql); $s->execute($params); return $s->fetchAll();
    }
    private function one(string $sql, array $params = []): ?array {
        $s = $this->db->prepare($sql); $s->execute($params); $r = $s->fetch(); return $r ?: null;
    }
    private function val(string $sql, array $params = []) {
        $s = $this->db->prepare($sql); $s->execute($params); return $s->fetchColumn();
    }

    public function dashboardData(): array {
        return [
            'pegawai' => (int)$this->val('SELECT COUNT(*) FROM pegawai'),
            'departemen' => (int)$this->val('SELECT COUNT(*) FROM departemen'),
            'proyek' => (int)$this->val('SELECT COUNT(*) FROM proyek'),
            'penugasan' => (int)$this->val('SELECT COUNT(*) FROM penugasan'),
            'total_gaji' => (float)$this->val('SELECT IFNULL(SUM(gaji),0) FROM pegawai'),
            'total_anggaran' => (float)$this->val('SELECT IFNULL(SUM(anggaran),0) FROM proyek'),
            'chart_departemen' => $this->all(
                'SELECT d.nama_dept, IFNULL(SUM(p.gaji),0) AS total_gaji
                 FROM departemen d LEFT JOIN pegawai p ON p.id_dept=d.id_dept
                 GROUP BY d.id_dept,d.nama_dept ORDER BY d.nama_dept'
            ),
            'proyek_terbaru' => $this->all(
                'SELECT pr.nama_proyek,d.nama_dept,pr.anggaran FROM proyek pr
                 LEFT JOIN departemen d ON d.id_dept=pr.id_dept ORDER BY pr.id_proyek DESC LIMIT 5'
            )
        ];
    }

    public function departmentCount(string $q=''): int {
        if($q!=='') return (int)$this->val('SELECT COUNT(*) FROM departemen WHERE nama_dept LIKE ? OR lokasi LIKE ?', ['%'.$q.'%','%'.$q.'%']);
        return (int)$this->val('SELECT COUNT(*) FROM departemen');
    }
    public function getDepartments(string $q='',int $limit=10,int $offset=0): array {
        $sql='SELECT d.id_dept,d.nama_dept,d.lokasi,COUNT(p.id_pegawai) AS jumlah_pegawai
              FROM departemen d LEFT JOIN pegawai p ON p.id_dept=d.id_dept';
        $params=[];
        if($q!=='') { $sql.=' WHERE (d.nama_dept LIKE ? OR d.lokasi LIKE ?)'; $params=['%'.$q.'%','%'.$q.'%']; }
        $sql.=' GROUP BY d.id_dept,d.nama_dept,d.lokasi ORDER BY d.id_dept LIMIT ? OFFSET ?';
        $s=$this->db->prepare($sql);$i=1;
        foreach($params as $v)$s->bindValue($i++,$v,PDO::PARAM_STR);
        $s->bindValue($i++,$limit,PDO::PARAM_INT);$s->bindValue($i,$offset,PDO::PARAM_INT);
        $s->execute();return $s->fetchAll();
    }
    public function getDepartment(int $id): ?array {
        return $this->one('SELECT id_dept,nama_dept,lokasi FROM departemen WHERE id_dept=?',[$id]);
    }
    public function getDepartmentOptions(): array {
        return $this->all('SELECT id_dept,nama_dept FROM departemen ORDER BY nama_dept');
    }
    public function getEmployeeOptions(?int $exclude = null): array {
        if ($exclude !== null) return $this->all(
            'SELECT id_pegawai,nama,jabatan FROM pegawai WHERE id_pegawai<>? ORDER BY nama',[$exclude]
        );
        return $this->all('SELECT id_pegawai,nama,jabatan FROM pegawai ORDER BY nama');
    }
    public function getProjectOptions(): array {
        return $this->all('SELECT id_proyek,nama_proyek FROM proyek ORDER BY nama_proyek');
    }

    public function employeeCount(string $q='', int $dept=0): int {
        $where=[]; $p=[];
        if ($q!=='') { $where[]='(nama LIKE ? OR jabatan LIKE ?)'; $p[]='%'.$q.'%'; $p[]='%'.$q.'%'; }
        if ($dept>0) { $where[]='id_dept=?'; $p[]=$dept; }
        return (int)$this->val('SELECT COUNT(*) FROM pegawai'.($where?' WHERE '.implode(' AND ',$where):''),$p);
    }
    public function getEmployees(string $q='', int $dept=0, int $limit=10, int $offset=0): array {
        // Soal 5a dan 5e: sumber data dari view dan LEFT SELF JOIN untuk nama atasan.
        // Soal 5g: LEFT JOIN penugasan membuat pegawai tanpa proyek tetap tampil.
        $where=[]; $p=[];
        if ($q!=='') { $where[]='(v.nama LIKE ? OR v.jabatan LIKE ?)'; $p[]='%'.$q.'%'; $p[]='%'.$q.'%'; }
        if ($dept>0) { $where[]='v.id_dept=?'; $p[]=$dept; }
        $sql='SELECT v.id_pegawai,v.nama,v.jabatan,v.gaji,v.nama_dept,v.id_dept,v.id_atasan,
                     IFNULL(a.nama,\'-\') AS atasan,COUNT(DISTINCT t.id_proyek) AS jumlah_proyek
              FROM v_pegawai_dept v
              LEFT JOIN pegawai a ON a.id_pegawai=v.id_atasan
              LEFT JOIN penugasan t ON t.id_pegawai=v.id_pegawai'.
              ($where?' WHERE '.implode(' AND ',$where):'').
              ' GROUP BY v.id_pegawai,v.nama,v.jabatan,v.gaji,v.nama_dept,v.id_dept,v.id_atasan,a.nama
                ORDER BY v.id_pegawai LIMIT ? OFFSET ?';
        $s=$this->db->prepare($sql); $i=1;
        foreach($p as $v) $s->bindValue($i++,$v,PDO::PARAM_STR);
        $s->bindValue($i++,$limit,PDO::PARAM_INT); $s->bindValue($i,$offset,PDO::PARAM_INT);
        $s->execute(); return $s->fetchAll();
    }
    public function getEmployee(int $id): ?array {
        return $this->one('SELECT id_pegawai,nama,jabatan,gaji,id_dept,id_atasan FROM pegawai WHERE id_pegawai=?',[$id]);
    }
    public function getOrganizationEmployees(): array {
        // Soal 5a: self join menampilkan atasan; pegawai tanpa atasan tetap ditampilkan.
        return $this->all(
            'SELECT p.id_pegawai,p.nama,p.jabatan,p.gaji,p.id_dept,p.id_atasan,IFNULL(a.nama,\'-\') AS atasan
             FROM pegawai p LEFT JOIN pegawai a ON a.id_pegawai=p.id_atasan ORDER BY p.id_pegawai'
        );
    }
    public function saveEmployee(array $d, bool $edit=false): void {
        $id=(int)($d['id_pegawai']??0); $name=trim((string)($d['nama']??''));
        $job=trim((string)($d['jabatan']??'')); $salary=(float)($d['gaji']??0);
        $dept=(int)($d['id_dept']??0); $superRaw=(string)($d['id_atasan']??'');
        $super=$superRaw===''?null:(int)$superRaw;
        if($id<=0 || $name==='' || $dept<=0 || $salary<=0) throw new InvalidArgumentException('ID, nama, departemen, dan gaji wajib diisi dengan benar.');
        if($super!==null && $super===$id) throw new InvalidArgumentException('Pegawai tidak boleh menjadi atasan bagi dirinya sendiri.');
        if($super!==null && !(int)$this->val('SELECT COUNT(*) FROM pegawai WHERE id_pegawai=?',[$super])) throw new InvalidArgumentException('Atasan yang dipilih tidak ditemukan.');
        if(!(int)$this->val('SELECT COUNT(*) FROM departemen WHERE id_dept=?',[$dept])) throw new InvalidArgumentException('Departemen yang dipilih tidak ditemukan.');
        if($edit) $this->db->prepare('UPDATE pegawai SET nama=?,jabatan=?,gaji=?,id_dept=?,id_atasan=? WHERE id_pegawai=?')->execute([$name,$job,$salary,$dept,$super,$id]);
        else $this->db->prepare('INSERT INTO pegawai(id_pegawai,nama,jabatan,gaji,id_dept,id_atasan) VALUES(?,?,?,?,?,?)')->execute([$id,$name,$job,$salary,$dept,$super]);
    }
    public function deleteEmployee(int $id): void {
        if((int)$this->val('SELECT COUNT(*) FROM pegawai WHERE id_atasan=?',[$id])>0) throw new RuntimeException('Pegawai tidak dapat dihapus karena masih memiliki bawahan.');
        if((int)$this->val('SELECT COUNT(*) FROM penugasan WHERE id_pegawai=?',[$id])>0) throw new RuntimeException('Pegawai tidak dapat dihapus karena masih memiliki penugasan proyek.');
        $this->db->prepare('DELETE FROM pegawai WHERE id_pegawai=?')->execute([$id]);
    }

    public function saveDepartment(array $d, bool $edit=false): void {
        $id=(int)($d['id_dept']??0); $name=trim((string)($d['nama_dept']??'')); $loc=trim((string)($d['lokasi']??''));
        if($id<=0 || $name==='') throw new InvalidArgumentException('ID dan nama departemen wajib diisi.');
        if($edit) $this->db->prepare('UPDATE departemen SET nama_dept=?,lokasi=? WHERE id_dept=?')->execute([$name,$loc,$id]);
        else $this->db->prepare('INSERT INTO departemen(id_dept,nama_dept,lokasi) VALUES(?,?,?)')->execute([$id,$name,$loc]);
    }
    public function deleteDepartment(int $id): void {
        $used=(int)$this->val('SELECT COUNT(*) FROM pegawai WHERE id_dept=?',[$id])+(int)$this->val('SELECT COUNT(*) FROM proyek WHERE id_dept=?',[$id]);
        if($used>0) throw new RuntimeException('Departemen tidak dapat dihapus karena masih digunakan oleh pegawai atau proyek.');
        $this->db->prepare('DELETE FROM departemen WHERE id_dept=?')->execute([$id]);
    }

    public function getProjects(string $q='', int $limit=10, int $offset=0): array {
        // Soal 5d: COUNT dan SUM per proyek; LEFT JOIN mempertahankan proyek tanpa anggota.
        $sql='SELECT pr.id_proyek,pr.nama_proyek,pr.id_dept,pr.anggaran,d.nama_dept,
                     COUNT(DISTINCT t.id_pegawai) AS jumlah_pegawai,IFNULL(SUM(t.jam_kerja),0) AS total_jam
              FROM proyek pr LEFT JOIN departemen d ON d.id_dept=pr.id_dept
              LEFT JOIN penugasan t ON t.id_proyek=pr.id_proyek';
        $p=[]; if($q!=='') { $sql.=' WHERE pr.nama_proyek LIKE ?'; $p[]='%'.$q.'%'; }
        $sql.=' GROUP BY pr.id_proyek,pr.nama_proyek,pr.id_dept,pr.anggaran,d.nama_dept
                ORDER BY total_jam DESC,pr.id_proyek LIMIT ? OFFSET ?';
        $s=$this->db->prepare($sql); $i=1; foreach($p as $v)$s->bindValue($i++,$v,PDO::PARAM_STR);
        $s->bindValue($i++,$limit,PDO::PARAM_INT);$s->bindValue($i,$offset,PDO::PARAM_INT);$s->execute();return $s->fetchAll();
    }
    public function projectCount(string $q=''): int {
        return $q!==''?(int)$this->val('SELECT COUNT(*) FROM proyek WHERE nama_proyek LIKE ?',['%'.$q.'%']):(int)$this->val('SELECT COUNT(*) FROM proyek');
    }
    public function getProject(int $id): ?array { return $this->one('SELECT id_proyek,nama_proyek,id_dept,anggaran FROM proyek WHERE id_proyek=?',[$id]); }
    public function saveProject(array $d,bool $edit=false): void {
        $id=(int)($d['id_proyek']??0);$name=trim((string)($d['nama_proyek']??''));$dept=(int)($d['id_dept']??0);$budget=(float)($d['anggaran']??-1);
        if($id<=0||$name===''||$dept<=0||$budget<0)throw new InvalidArgumentException('ID, nama proyek, departemen, dan anggaran wajib diisi dengan benar.');
        if($edit)$this->db->prepare('UPDATE proyek SET nama_proyek=?,id_dept=?,anggaran=? WHERE id_proyek=?')->execute([$name,$dept,$budget,$id]);
        else $this->db->prepare('INSERT INTO proyek(id_proyek,nama_proyek,id_dept,anggaran) VALUES(?,?,?,?)')->execute([$id,$name,$dept,$budget]);
    }
    public function deleteProject(int $id): void {
        if((int)$this->val('SELECT COUNT(*) FROM penugasan WHERE id_proyek=?',[$id])>0)throw new RuntimeException('Proyek tidak dapat dihapus karena masih memiliki anggota tim.');
        $this->db->prepare('DELETE FROM proyek WHERE id_proyek=?')->execute([$id]);
    }

    public function assignmentCount(string $q=''): int {
        if($q!=='') return (int)$this->val(
            'SELECT COUNT(*) FROM penugasan t INNER JOIN pegawai p ON p.id_pegawai=t.id_pegawai
             INNER JOIN proyek pr ON pr.id_proyek=t.id_proyek
             WHERE p.nama LIKE ? OR pr.nama_proyek LIKE ?',
            ['%'.$q.'%','%'.$q.'%']
        );
        return (int)$this->val('SELECT COUNT(*) FROM penugasan');
    }
    public function getAssignments(string $q='',int $limit=10,int $offset=0): array {
        $sql='SELECT t.id_pegawai,t.id_proyek,t.jam_kerja,p.nama AS nama_pegawai,p.jabatan,pr.nama_proyek
              FROM penugasan t INNER JOIN pegawai p ON p.id_pegawai=t.id_pegawai
              INNER JOIN proyek pr ON pr.id_proyek=t.id_proyek';
        $params=[];
        if($q!=='') { $sql.=' WHERE p.nama LIKE ? OR pr.nama_proyek LIKE ?'; $params=['%'.$q.'%','%'.$q.'%']; }
        $sql.=' ORDER BY pr.nama_proyek,p.nama LIMIT ? OFFSET ?';
        $s=$this->db->prepare($sql);$i=1;
        foreach($params as $v)$s->bindValue($i++,$v,PDO::PARAM_STR);
        $s->bindValue($i++,$limit,PDO::PARAM_INT);$s->bindValue($i,$offset,PDO::PARAM_INT);
        $s->execute();return $s->fetchAll();
    }
    public function getAssignment(int $employee,int $project): ?array {
        return $this->one('SELECT id_pegawai,id_proyek,jam_kerja FROM penugasan WHERE id_pegawai=? AND id_proyek=?',[$employee,$project]);
    }
    public function saveAssignment(array $d,bool $edit=false,?int $oldEmployee=null,?int $oldProject=null): void {
        $employee=(int)($d['id_pegawai']??0);$project=(int)($d['id_proyek']??0);$hours=(int)($d['jam_kerja']??-1);
        if($employee<=0||$project<=0||$hours<0)throw new InvalidArgumentException('Pegawai, proyek, dan jam kerja wajib diisi dengan benar.');
        if($edit && $oldEmployee!==null && $oldProject!==null)
            $this->db->prepare('UPDATE penugasan SET jam_kerja=? WHERE id_pegawai=? AND id_proyek=?')->execute([$hours,$oldEmployee,$oldProject]);
        else $this->db->prepare('INSERT INTO penugasan(id_pegawai,id_proyek,jam_kerja) VALUES(?,?,?)')->execute([$employee,$project,$hours]);
    }
    public function deleteAssignment(int $employee,int $project): void {
        $this->db->prepare('DELETE FROM penugasan WHERE id_pegawai=? AND id_proyek=?')->execute([$employee,$project]);
    }

    public function reportDepartmentSalary(): array {
        // Soal 5b: AVG, MAX, MIN, GROUP BY, HAVING.
        return $this->all(
            'SELECT d.nama_dept,ROUND(AVG(p.gaji),0) AS rata_gaji,MAX(p.gaji) AS gaji_tertinggi,MIN(p.gaji) AS gaji_terendah
             FROM departemen d INNER JOIN pegawai p ON p.id_dept=d.id_dept
             GROUP BY d.id_dept,d.nama_dept HAVING AVG(p.gaji)>? ORDER BY rata_gaji DESC',[8000000]
        );
    }
    public function reportEmptyDepartments(): array {
        // Soal 5c: LEFT JOIN + IS NULL.
        return $this->all(
            'SELECT d.nama_dept FROM departemen d LEFT JOIN pegawai p ON p.id_dept=d.id_dept
             WHERE p.id_pegawai IS NULL ORDER BY d.nama_dept'
        );
    }
    public function reportDepartmentProjects(): array {
        // Soal 5h: LEFT JOIN + IFNULL, termasuk departemen dengan nol proyek.
        return $this->all(
            'SELECT d.nama_dept,COUNT(pr.id_proyek) AS jumlah_proyek,IFNULL(SUM(pr.anggaran),0) AS total_anggaran
             FROM departemen d LEFT JOIN proyek pr ON pr.id_dept=d.id_dept
             GROUP BY d.id_dept,d.nama_dept ORDER BY d.id_dept'
        );
    }
    public function reportSupervisors(): array {
        // Soal 5f: self join + HAVING untuk atasan dengan minimal tiga bawahan.
        return $this->all(
            'SELECT a.nama AS atasan,COUNT(b.id_pegawai) AS jumlah_bawahan
             FROM pegawai a INNER JOIN pegawai b ON b.id_atasan=a.id_pegawai
             GROUP BY a.id_pegawai,a.nama HAVING COUNT(b.id_pegawai)>=3 ORDER BY a.nama'
        );
    }
    public function reportEmployeesWithoutProjects(): array {
        // Soal 5g: LEFT JOIN + IS NULL.
        return $this->all(
            'SELECT p.nama,p.jabatan FROM pegawai p LEFT JOIN penugasan t ON t.id_pegawai=p.id_pegawai
             WHERE t.id_pegawai IS NULL ORDER BY p.nama'
        );
    }
    public function reportEmployeesWithManyProjects(): array {
        // Soal 5i: COUNT, SUM, GROUP BY, HAVING.
        return $this->all(
            'SELECT p.nama,COUNT(t.id_proyek) AS jumlah_proyek,SUM(t.jam_kerja) AS total_jam
             FROM pegawai p INNER JOIN penugasan t ON t.id_pegawai=p.id_pegawai
             GROUP BY p.id_pegawai,p.nama HAVING COUNT(t.id_proyek)>2 ORDER BY jumlah_proyek DESC'
        );
    }
    public function reportSalaryAnomalies(): array {
        // Soal 5j: self join dan perbandingan gaji pegawai dengan gaji atasan.
        return $this->all(
            'SELECT p.nama AS pegawai,p.gaji AS gaji_pegawai,a.nama AS atasan,a.gaji AS gaji_atasan
             FROM pegawai p INNER JOIN pegawai a ON a.id_pegawai=p.id_atasan
             WHERE p.gaji>a.gaji ORDER BY p.nama'
        );
    }

    public function raisePreview(int $dept,float $percent): array {
        return $this->all(
            'SELECT p.id_pegawai,p.nama,p.jabatan,p.gaji AS gaji_lama,
                    ROUND(p.gaji*(1+(?/100)),2) AS gaji_baru,ROUND(p.gaji*(?/100),2) AS selisih
             FROM pegawai p WHERE p.id_dept=? ORDER BY p.nama',
            [$percent,$percent,$dept]
        );
    }
    public function getRaiseLog(int $dept): ?array {
        return $this->one(
            'SELECT l.*,d.nama_dept FROM log_penyesuaian_gaji l
             INNER JOIN departemen d ON d.id_dept=l.id_dept WHERE l.id_dept=?',[$dept]
        );
    }
    public function applyRaise(int $dept,float $percent): int {
        if($percent<=0||$percent>100)throw new InvalidArgumentException('Persentase kenaikan harus lebih dari 0 dan maksimal 100%.');
        $this->db->beginTransaction();
        try {
            if($this->getRaiseLog($dept))throw new RuntimeException('Penyesuaian gaji untuk departemen ini sudah pernah dijalankan. Impor ulang database untuk mengulang demo.');
            // Soal 5e: satu perintah UPDATE ... JOIN sesuai instruksi latihan.
            $s=$this->db->prepare(
                'UPDATE pegawai p INNER JOIN departemen d ON d.id_dept=p.id_dept
                 SET p.gaji=ROUND(p.gaji*(1+(?/100)),2) WHERE d.id_dept=?'
            );
            $s->execute([$percent,$dept]);$affected=$s->rowCount();
            if($affected<1)throw new RuntimeException('Tidak ada pegawai yang diperbarui pada departemen tersebut.');
            $this->db->prepare('INSERT INTO log_penyesuaian_gaji(id_dept,persentase,jumlah_pegawai) VALUES(?,?,?)')
                     ->execute([$dept,$percent,$affected]);
            $this->db->commit();return $affected;
        } catch(Throwable $e) {
            if($this->db->inTransaction())$this->db->rollBack();
            throw $e;
        }
    }
}
