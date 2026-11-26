document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!window.confirm(form.getAttribute('data-confirm') || 'Lanjutkan tindakan ini?')) event.preventDefault();
        });
    });
    const canvas = document.getElementById('salaryChart');
    if (canvas && window.siPegawaiChart && window.Chart) {
        new Chart(canvas, {
            type: 'bar',
            data: { labels: window.siPegawaiChart.labels, datasets: [{
                label: 'Total Gaji (Rp)', data: window.siPegawaiChart.values,
                backgroundColor: ['#6c58d8','#8d7ee6','#b4aaf0','#d2ccf7'], borderRadius: 7, maxBarThickness: 48
            }]},
            options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}},
                scales:{x:{grid:{display:false},ticks:{color:'#9290a5',font:{size:10}}},
                    y:{beginAtZero:true,grid:{color:'#f0eff5'},ticks:{color:'#9290a5',font:{size:10},
                        callback:function(v){return new Intl.NumberFormat('id-ID',{notation:'compact'}).format(v);}}}}
            }
        });
    }
});
