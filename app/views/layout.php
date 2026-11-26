<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> | SiPegawai</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= e(BASE_URL) ?>/public/assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar topbar sticky-top"><div class="container-fluid px-4">
<a class="navbar-brand fw-bold text-white" href="<?= e(page_url('dashboard')) ?>"><span class="brand-mark">SP</span> SiPegawai</a>
<div class="ms-auto d-flex align-items-center gap-3 text-white small"><span>Staf SDM</span><span class="user-avatar">SD</span></div>
</div></nav>
<div class="app-shell">
<aside class="sidebar">
<div class="sidebar-label">MENU UTAMA</div>
<a class="side-link <?= $activePage==='dashboard'?'active':'' ?>" href="<?= e(page_url('dashboard')) ?>">▦ <span>Dashboard</span></a>
<a class="side-link <?= $activePage==='struktur'?'active':'' ?>" href="<?= e(page_url('struktur')) ?>">⌘ <span>Struktur Organisasi</span></a>
<a class="side-link <?= in_array($activePage,['pegawai','pegawai-form'],true)?'active':'' ?>" href="<?= e(page_url('pegawai')) ?>">♙ <span>Pegawai</span></a>
<a class="side-link <?= in_array($activePage,['departemen','departemen-form'],true)?'active':'' ?>" href="<?= e(page_url('departemen')) ?>">▤ <span>Departemen</span></a>
<a class="side-link <?= in_array($activePage,['proyek','proyek-form'],true)?'active':'' ?>" href="<?= e(page_url('proyek')) ?>">▣ <span>Proyek</span></a>
<a class="side-link <?= in_array($activePage,['penugasan','penugasan-form'],true)?'active':'' ?>" href="<?= e(page_url('penugasan')) ?>">⇄ <span>Penugasan</span></a>
<div class="sidebar-label mt-4">PENGELOLAAN</div>
<a class="side-link <?= $activePage==='penggajian'?'active':'' ?>" href="<?= e(page_url('penggajian')) ?>">▧ <span>Penggajian</span></a>
<a class="side-link <?= $activePage==='laporan'?'active':'' ?>" href="<?= e(page_url('laporan')) ?>">▥ <span>Laporan SQL</span></a>
<div class="sidebar-note"><strong>Mini Project</strong><span>Basis Data Lanjut</span><small>Kasus 5 · SiPegawai</small></div>
</aside>
<main class="main-content">
<div class="page-heading"><div><div class="eyebrow">SISTEM INFORMASI KEPEGAWAIAN</div><h1><?= e($pageTitle) ?></h1></div><div class="date-chip">● Data Perusahaan</div></div>
<?php if($flash): ?><div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show shadow-sm" role="alert"><?= e($flash['message']) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?= $content ?>
<footer class="footer-note">SiPegawai · Mini Project Basis Data Lanjut · 2026/2027</footer>
</main></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="<?= e(BASE_URL) ?>/public/assets/js/app.js"></script>
</body></html>
