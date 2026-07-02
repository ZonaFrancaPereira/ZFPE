document.addEventListener('DOMContentLoaded', function () {
  const data = window.reportesChartData;
  if (!data || typeof Chart === 'undefined') return;

  const distEl = document.getElementById('chartDistribucion');
  if (distEl && data.distribucion.data.some(v => v > 0)) {
    new Chart(distEl, {
      type: 'doughnut',
      data: {
        labels: data.distribucion.labels,
        datasets: [{
          data: data.distribucion.data,
          backgroundColor: data.distribucion.colors,
          borderWidth: 2,
          borderColor: '#fff',
        }],
      },
      options: {
        responsive: true,
        cutout: '65%',
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: (ctx) => {
                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                const pct = total > 0 ? Math.round((ctx.raw / total) * 100) : 0;
                return `${ctx.label}: ${ctx.raw} (${pct}%)`;
              },
            },
          },
        },
      },
    });
  }

  const fasesEl = document.getElementById('chartFases');
  if (fasesEl && data.fases.labels.length > 0) {
    const colorPorAvance = (pct) => {
      if (pct >= 100) return '#198754';
      if (pct > 0) return '#1993b8';
      return '#adb5bd';
    };

    const etiquetasEnBarra = {
      id: 'etiquetasEnBarra',
      afterDatasetsDraw(chart) {
        const { ctx } = chart;
        chart.getDatasetMeta(0).data.forEach((bar, i) => {
          const valor = chart.data.datasets[0].data[i];
          ctx.save();
          ctx.fillStyle = '#22404b';
          ctx.font = '600 12px sans-serif';
          ctx.textBaseline = 'middle';
          ctx.fillText(`${valor}%`, bar.x + 8, bar.y);
          ctx.restore();
        });
      },
    };

    new Chart(fasesEl, {
      type: 'bar',
      data: {
        labels: data.fases.labels,
        datasets: [{
          label: 'Avance %',
          data: data.fases.data,
          backgroundColor: data.fases.data.map(colorPorAvance),
          borderRadius: 4,
          maxBarThickness: 28,
        }],
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        layout: { padding: { right: 36 } },
        scales: {
          x: { min: 0, max: 100, ticks: { callback: (v) => v + '%' } },
        },
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: { label: (ctx) => `${ctx.raw}% de avance` },
          },
        },
      },
      plugins: [etiquetasEnBarra],
    });
  }
});
