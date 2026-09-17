import './bootstrap';
import Alpine from 'alpinejs';
import ApexCharts from 'apexcharts';

window.Alpine = Alpine;
Alpine.start();

const payload = document.getElementById('document-monitoring-payload');

if (payload) {
    const series = JSON.parse(payload.textContent);
    const chart = new ApexCharts(document.getElementById('document-monitoring-chart'), {
        chart: {
            type: 'line',
            height: 380,
            toolbar: { show: false },
            zoom: { enabled: false },
            selection: { enabled: false },
            fontFamily: 'Segoe UI, sans-serif',
        },
        series: [
            { name: 'Total dokumen', data: series.map((item) => Number(item.documents)) },
            { name: 'Menunggu validasi', data: series.map((item) => Number(item.pending)) },
            { name: 'Valid', data: series.map((item) => Number(item.valid)) },
            { name: 'Tidak sesuai', data: series.map((item) => Number(item.invalid)) },
            { name: 'Uji ulang', data: series.map((item) => Number(item.retest)) },
        ],
        colors: ['#0ea5e9', '#f59e0b', '#16a34a', '#dc2626', '#7c3aed'],
        stroke: { curve: 'smooth', width: 3 },
        markers: { size: 4 },
        xaxis: { categories: series.map((item) => item.date), title: { text: 'Tanggal input' } },
        yaxis: { min: 0, forceNiceScale: true, decimalsInFloat: 0, title: { text: 'Jumlah dokumen' } },
        grid: { borderColor: '#edf1f4' },
        legend: { position: 'top', horizontalAlign: 'left' },
        noData: { text: 'Belum ada dokumen pada periode terpilih.' },
        tooltip: { y: { formatter: (value) => `${value} dokumen` } },
    });

    chart.render();
}
