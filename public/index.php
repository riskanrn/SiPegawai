<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/config.php';
require_once APP_ROOT . '/app/core/Database.php';
require_once APP_ROOT . '/app/helpers.php';
require_once APP_ROOT . '/app/models/SiPegawaiModel.php';

$model = new SiPegawaiModel(Database::connection());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $redirectPage = (string)($_POST['redirect_page'] ?? 'dashboard');
    try {
        verify_csrf();
        switch ((string)($_POST['action'] ?? '')) {
            case 'save_pegawai':
                $model->saveEmployee($_POST, ($_POST['mode'] ?? '') === 'edit');
                set_flash('success', 'Data pegawai berhasil disimpan.'); $redirectPage = 'pegawai'; break;
            case 'delete_pegawai':
                $model->deleteEmployee((int)($_POST['id_pegawai'] ?? 0));
                set_flash('success', 'Data pegawai berhasil dihapus.'); $redirectPage = 'pegawai'; break;
            case 'save_departemen':
                $model->saveDepartment($_POST, ($_POST['mode'] ?? '') === 'edit');
                set_flash('success', 'Data departemen berhasil disimpan.'); $redirectPage = 'departemen'; break;
            case 'delete_departemen':
                $model->deleteDepartment((int)($_POST['id_dept'] ?? 0));
                set_flash('success', 'Data departemen berhasil dihapus.'); $redirectPage = 'departemen'; break;
            case 'save_proyek':
                $model->saveProject($_POST, ($_POST['mode'] ?? '') === 'edit');
                set_flash('success', 'Data proyek berhasil disimpan.'); $redirectPage = 'proyek'; break;
            case 'delete_proyek':
                $model->deleteProject((int)($_POST['id_proyek'] ?? 0));
                set_flash('success', 'Data proyek berhasil dihapus.'); $redirectPage = 'proyek'; break;
            case 'save_penugasan':
                $edit = ($_POST['mode'] ?? '') === 'edit';
                $model->saveAssignment($_POST, $edit,
                    $edit ? (int)($_POST['old_id_pegawai'] ?? 0) : null,
                    $edit ? (int)($_POST['old_id_proyek'] ?? 0) : null);
                set_flash('success', $edit ? 'Jam kerja penugasan berhasil diperbarui.' : 'Penugasan berhasil ditambahkan.');
                $redirectPage = 'penugasan'; break;
            case 'delete_penugasan':
                $model->deleteAssignment((int)($_POST['id_pegawai'] ?? 0),(int)($_POST['id_proyek'] ?? 0));
                set_flash('success', 'Penugasan berhasil dihapus.'); $redirectPage = 'penugasan'; break;
            case 'apply_raise':
                $affected = $model->applyRaise((int)($_POST['id_dept'] ?? 0),(float)($_POST['persentase'] ?? 0));
                set_flash('success', "Penyesuaian gaji berhasil. UPDATE mengubah {$affected} baris.");
                $redirectPage = 'penggajian'; break;
            default:
                throw new RuntimeException('Aksi tidak dikenali.');
        }
    } catch (Throwable $e) {
        if ($e instanceof PDOException && (string)$e->getCode() === '23000') {
            set_flash('danger', 'Data tidak dapat diproses karena ID sudah digunakan atau masih terhubung dengan data lain. Periksa kembali pilihan relasi dan riwayat datanya.');
        } else {
            set_flash('danger', $e->getMessage());
        }
    }
    redirect_to(page_url($redirectPage));
}

$page = (string)($_GET['page'] ?? 'dashboard');
$allowed = ['dashboard','struktur','pegawai','pegawai-form','departemen','departemen-form','proyek','proyek-form','penugasan','penugasan-form','penggajian','laporan'];
if (!in_array($page, $allowed, true)) $page = 'dashboard';
$view = $page;
$pageTitle = 'Dashboard';
$activePage = $page;
$data = [];

try {
    switch ($page) {
        case 'dashboard':
            $pageTitle='Dashboard'; $data['stats']=$model->dashboardData(); break;
        case 'struktur':
            $pageTitle='Struktur Organisasi';
            $employees=$model->getOrganizationEmployees(); $nodes=[];
            foreach($employees as $emp){$emp['children']=[];$nodes[$emp['id_pegawai']]=$emp;}
            $tree=[];
            foreach($employees as $emp){
                $id=$emp['id_pegawai'];$parent=$emp['id_atasan'];
                if($parent!==null && isset($nodes[$parent])) $nodes[$parent]['children'][]=&$nodes[$id];
                else $tree[]=&$nodes[$id];
            }
            $data['tree']=$tree;$data['supervisors']=$model->reportSupervisors();break;
        case 'pegawai':
            $pageTitle='Data Pegawai';$q=trim((string)($_GET['q']??''));$dept=(int)($_GET['dept']??0);
            $p=max(1,(int)($_GET['p']??1));$per=10;$pagination=pagination_data($model->employeeCount($q,$dept),$p,$per);
            $data += ['employees'=>$model->getEmployees($q,$dept,$per,($pagination['page']-1)*$per),
                'departments'=>$model->getDepartmentOptions(),'search'=>$q,'deptId'=>$dept,'pagination'=>$pagination];break;
        case 'pegawai-form':
            $pageTitle=isset($_GET['id'])?'Ubah Pegawai':'Tambah Pegawai';$id=(int)($_GET['id']??0);
            $data['employee']=$id?$model->getEmployee($id):null;
            if($id && !$data['employee'])throw new RuntimeException('Data pegawai tidak ditemukan.');
            $data['departments']=$model->getDepartmentOptions();$data['supervisors']=$model->getEmployeeOptions($id?:null);break;
        case 'departemen':
            $pageTitle='Data Departemen';$q=trim((string)($_GET['q']??''));$p=max(1,(int)($_GET['p']??1));$per=10;
            $pagination=pagination_data($model->departmentCount($q),$p,$per);
            $data['departments']=$model->getDepartments($q,$per,($pagination['page']-1)*$per);
            $data['search']=$q;$data['pagination']=$pagination;break;
        case 'departemen-form':
            $pageTitle=isset($_GET['id'])?'Ubah Departemen':'Tambah Departemen';$id=(int)($_GET['id']??0);
            $data['department']=$id?$model->getDepartment($id):null;
            if($id && !$data['department'])throw new RuntimeException('Data departemen tidak ditemukan.');break;
        case 'proyek':
            $pageTitle='Data Proyek';$q=trim((string)($_GET['q']??''));$p=max(1,(int)($_GET['p']??1));$per=10;
            $pagination=pagination_data($model->projectCount($q),$p,$per);
            $data += ['projects'=>$model->getProjects($q,$per,($pagination['page']-1)*$per),'search'=>$q,'pagination'=>$pagination];break;
        case 'proyek-form':
            $pageTitle=isset($_GET['id'])?'Ubah Proyek':'Tambah Proyek';$id=(int)($_GET['id']??0);
            $data['project']=$id?$model->getProject($id):null;
            if($id && !$data['project'])throw new RuntimeException('Data proyek tidak ditemukan.');
            $data['departments']=$model->getDepartmentOptions();break;
        case 'penugasan':
            $pageTitle='Penugasan Proyek';$q=trim((string)($_GET['q']??''));$p=max(1,(int)($_GET['p']??1));$per=10;$pagination=pagination_data($model->assignmentCount($q),$p,$per);
            $data['assignments']=$model->getAssignments($q,$per,($pagination['page']-1)*$per);$data['pagination']=$pagination;$data['search']=$q;break;
        case 'penugasan-form':
            $pageTitle=isset($_GET['pegawai_id'])?'Ubah Penugasan':'Tambah Penugasan';
            $eid=(int)($_GET['pegawai_id']??0);$pid=(int)($_GET['proyek_id']??0);
            $data['assignment']=($eid&&$pid)?$model->getAssignment($eid,$pid):null;
            if($eid&&$pid&&!$data['assignment'])throw new RuntimeException('Penugasan tidak ditemukan.');
            $data['employees']=$model->getEmployeeOptions();$data['projects']=$model->getProjectOptions();break;
        case 'penggajian':
            $pageTitle='Penggajian';$dept=(int)($_GET['dept']??1);$pct=(float)($_GET['persentase']??10);
            $data['departments']=$model->getDepartmentOptions();$data['selectedDept']=$dept;$data['percent']=$pct;
            $data['preview']=[];
            if(isset($_GET['preview'])){
                if($pct<=0||$pct>100)throw new InvalidArgumentException('Persentase harus lebih dari 0 dan maksimal 100%.');
                $data['preview']=$model->raisePreview($dept,$pct);
            }
            $data['raiseLog']=$model->getRaiseLog($dept);$data['anomalies']=$model->reportSalaryAnomalies();break;
        case 'laporan':
            $pageTitle='Laporan SQL';
            $data=['salary'=>$model->reportDepartmentSalary(),'emptyDepartments'=>$model->reportEmptyDepartments(),
                'departmentProjects'=>$model->reportDepartmentProjects(),'supervisors'=>$model->reportSupervisors(),
                'withoutProjects'=>$model->reportEmployeesWithoutProjects(),'multiProjects'=>$model->reportEmployeesWithManyProjects(),
                'anomalies'=>$model->reportSalaryAnomalies(),'projects'=>$model->getProjects('',100,0)];break;
    }
} catch (Throwable $e) {
    set_flash('danger',$e->getMessage());
    $page='dashboard';$view='dashboard';$pageTitle='Dashboard';$activePage='dashboard';$data=['stats'=>$model->dashboardData()];
}

extract($data, EXTR_SKIP);
ob_start();
require APP_ROOT . '/app/views/pages/' . $view . '.php';
$content=ob_get_clean();
$flash=pull_flash();
require APP_ROOT . '/app/views/layout.php';
