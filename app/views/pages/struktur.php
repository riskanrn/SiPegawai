<?php
$renderNode=function($node) use (&$renderNode) {
    echo '<li class="org-item"><div class="org-node"><span class="org-avatar">'.e(strtoupper(substr($node['nama'],0,1))).'</span><div><strong>'.e($node['nama']).'</strong><small>'.e($node['jabatan']).'</small></div><span class="org-salary">'.e(rupiah($node['gaji'])).'</span></div>';
    if(!empty($node['children'])){echo '<ul class="org-children">';foreach($node['children'] as $child)$renderNode($child);echo '</ul>';}
    echo '</li>';
};
?>
<div class="row g-3"><div class="col-lg-8"><div class="panel-card"><div class="panel-heading"><div><h5>Bagan Organisasi</h5><p>Relasi atasan–bawahan dari kolom id_atasan · <span class="sql-tag">Self Join · 5a</span></p></div></div><div class="org-chart"><ul class="org-root"><?php foreach($tree as $node)$renderNode($node); ?></ul></div></div></div>
<div class="col-lg-4"><div class="panel-card"><div class="panel-heading"><div><h5>Rentang Kendali</h5><p>Atasan dengan minimal tiga bawahan · <span class="sql-tag">5f</span></p></div></div>
<?php foreach($supervisors as $s): ?><div class="metric-row"><span><?= e($s['atasan']) ?></span><strong><?= (int)$s['jumlah_bawahan'] ?> bawahan</strong></div><?php endforeach; ?>
<?php if(!$supervisors): ?><div class="empty-state">Belum ada atasan dengan minimal tiga bawahan.</div><?php endif; ?>
<a class="btn btn-outline-primary w-100 mt-3" href="<?= e(page_url('pegawai')) ?>">Kelola Data Pegawai</a></div></div></div>
