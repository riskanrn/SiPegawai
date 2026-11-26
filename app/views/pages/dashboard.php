<div class="row g-3 mb-4">
<?php foreach([['Total Pegawai',$stats['pegawai'],'♙','purple'],['Departemen',$stats['departemen'],'▤','blue'],['Proyek',$stats['proyek'],'▣','orange'],['Penugasan',$stats['penugasan'],'⇄','green']] as $s): ?>
<div class="col-6 col-xl-3"><div class="stat-card"><div class="stat-icon <?= e($s[3]) ?>"><?= e($s[2]) ?></div><div class="stat-label"><?= e($s[0]) ?></div><div class="stat-value"><?= (int)$s[1] ?></div><div class="stat-foot">Data terdaftar</div></div></div>
<?php endforeach; ?>
</div>
<div class="row g-3 mb-4">
<div class="col-lg-7"><div class="panel-card h-100"><div class="panel-heading"><div><h5>Total Gaji per Departemen</h5><p>Ringkasan agregasi gaji pegawai</p></div><span class="sql-tag">SUM · GROUP BY</span></div><div class="chart-wrap"><canvas id="salaryChart"></canvas></div>
<script>window.siPegawaiChart={labels:<?= json_encode(array_column($stats['chart_departemen'],'nama_dept'),JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>,values:<?= json_encode(array_map('floatval',array_column($stats['chart_departemen'],'total_gaji'))) ?>};</script>
</div></div>
<div class="col-lg-5"><div class="panel-card h-100"><div class="panel-heading"><div><h5>Ringkasan Keuangan</h5><p>Agregasi seluruh perusahaan</p></div></div>
<div class="metric-row"><span>Total gaji bulanan</span><strong><?= rupiah($stats['total_gaji']) ?></strong></div>
<div class="metric-row"><span>Total anggaran proyek</span><strong><?= rupiah($stats['total_anggaran']) ?></strong></div>
<div class="metric-row"><span>Jumlah penugasan</span><strong><?= (int)$stats['penugasan'] ?> relasi</strong></div>
<a class="btn btn-primary w-100 mt-3" href="<?= e(page_url('laporan')) ?>">Lihat Laporan SQL</a></div></div>
</div>
<div class="panel-card"><div class="panel-heading"><div><h5>Daftar Proyek</h5><p>Proyek yang tercatat pada basis data</p></div><a class="btn btn-sm btn-outline-primary" href="<?= e(page_url('proyek')) ?>">Kelola Proyek</a></div>
<div class="table-responsive"><table class="table app-table align-middle"><thead><tr><th>Nama Proyek</th><th>Departemen</th><th class="text-end">Anggaran</th></tr></thead><tbody>
<?php foreach($stats['proyek_terbaru'] as $p): ?><tr><td class="fw-semibold"><?= e($p['nama_proyek']) ?></td><td><?= e($p['nama_dept']) ?></td><td class="text-end"><?= rupiah($p['anggaran']) ?></td></tr><?php endforeach; ?>
</tbody></table></div></div>
