import './bootstrap';
import Alpine from 'alpinejs';
import ApexCharts from 'apexcharts';

window.Alpine = Alpine;
Alpine.start();

const payload = document.getElementById('document-monitoring-payload');
const chartElement = document.getElementById('document-monitoring-chart');

if (payload && chartElement) {
    const series = JSON.parse(payload.textContent);
    const populatedDays = series.filter((item) => Number(item.documents) > 0);
    const pointsFor = (field) => populatedDays
        .filter((item) => Number(item[field]) > 0)
        .map((item) => ({
            x: new Intl.DateTimeFormat('id-ID', {
                day: '2-digit', month: 'short', year: 'numeric',
            }).format(new Date(`${item.date}T00:00:00`)),
            y: Number(item[field]),
        }));

    if (!populatedDays.length) {
        chartElement.replaceWith(Object.assign(document.createElement('p'), {
            className: 'empty',
            textContent: 'Belum ada data validasi pada periode terpilih.',
        }));
    } else {
        const chart = new ApexCharts(chartElement, {
            chart: { type: 'line', height: 380, toolbar: { show: false }, fontFamily: 'Segoe UI, sans-serif' },
            series: [
                { name: 'Total dokumen', data: pointsFor('documents') },
                { name: 'Menunggu validasi', data: pointsFor('pending') },
                { name: 'Valid', data: pointsFor('valid') },
                { name: 'Tidak sesuai', data: pointsFor('invalid') },
                { name: 'Uji ulang', data: pointsFor('retest') },
            ],
            colors: ['#0ea5e9', '#f59e0b', '#16a34a', '#dc2626', '#7c3aed'],
            stroke: { curve: 'straight', width: 3 },
            markers: { size: 4, hover: { size: 6 } },
            xaxis: { type: 'category', title: { text: 'Tanggal input' } },
            yaxis: { min: 0, forceNiceScale: true, decimalsInFloat: 0, title: { text: 'Jumlah dokumen' } },
            grid: { borderColor: '#edf1f4' },
            legend: { position: 'top', horizontalAlign: 'left' },
            tooltip: { shared: false, y: { formatter: (value) => `${value} dokumen` } },
        });

        chart.render();
    }
}
