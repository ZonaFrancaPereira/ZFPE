document.addEventListener('DOMContentLoaded', function () {

    var ZF_TEAL  = '#1993b8';
    var ZF_NAVY  = '#22404b';
    var ZF_GREEN = '#198754';
    var PALETTE  = ['#1993b8','#198754','#dc3545','#fd7e14','#6f42c1','#0dcaf0','#20c997','#ffc107'];
    var MESES    = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];

    var SCALE_XY = {
        x: { grid: { display: false }, ticks: { font: { size: 11 } } },
        y: { beginAtZero: true, ticks: { font: { size: 11 } }, grid: { color: 'rgba(0,0,0,.05)' } }
    };

    function fmtVal(v, unidad) {
        return (v !== null && v !== undefined
            ? Number(v).toLocaleString('es', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
            : '—') + (unidad ? ' ' + unidad : '');
    }

    function subLabel(s) {
        var n = parseInt(s, 10);
        return (!isNaN(n) && n >= 1 && n <= 12) ? MESES[n - 1] : s;
    }

    // ── Inicializar gráficas ──────────────────────────────────
    document.querySelectorAll('.indicador-chart').forEach(function (canvas) {
        var labels      = JSON.parse(canvas.dataset.labels      || '[]');
        var values      = JSON.parse(canvas.dataset.values      || '[]');
        var tipo        = canvas.dataset.tipo                    || 'linea';
        var comparativo = canvas.dataset.comparativo            === '1';
        var meta        = canvas.dataset.meta !== ''            ? parseFloat(canvas.dataset.meta) : null;
        var unidad      = canvas.dataset.unidad                  || '';

        if (!labels.length) return;

        if (comparativo) {
            renderComparativo(canvas, labels, values, tipo, meta, unidad);
        } else {
            renderSimple(canvas, labels, values, tipo, meta, unidad);
        }
    });

    // ── SIMPLE (una serie en el tiempo) ──────────────────────

    function renderSimple(canvas, labels, values, tipo, meta, unidad) {
        switch (tipo) {
            case 'radar': return renderRadarSimple(canvas, labels, values, unidad);
            case 'torta': return renderTortaSimple(canvas, labels, values, unidad);
            case 'combo': return renderComboSimple(canvas, labels, values, meta, unidad);
            default:      return renderSerieSimple(canvas, labels, values, tipo, meta, unidad);
        }
    }

    // línea / barra / área
    function renderSerieSimple(canvas, labels, values, tipo, meta, unidad) {
        var isBarra = tipo === 'barra';
        var isArea  = tipo === 'area';

        var datasets = [{
            label:           'Valor' + (unidad ? ' (' + unidad + ')' : ''),
            data:            values,
            borderColor:     ZF_TEAL,
            backgroundColor: isBarra ? 'rgba(25,147,184,.7)' : (isArea ? 'rgba(25,147,184,.15)' : 'transparent'),
            borderWidth:     isBarra ? 0 : 2,
            fill:            isArea,
            tension:         0.35,
            pointRadius:     isBarra ? 0 : 4,
            pointHoverRadius: 6,
        }];

        if (meta !== null && !isBarra) {
            datasets.push({
                label: 'Meta', data: labels.map(function () { return meta; }),
                borderColor: ZF_GREEN, backgroundColor: 'transparent',
                borderWidth: 1.5, borderDash: [6, 3], pointRadius: 0, fill: false,
            });
        }

        new Chart(canvas, {
            type: isBarra ? 'bar' : 'line',
            data: { labels: labels, datasets: datasets },
            options: {
                responsive: true, maintainAspectRatio: true,
                plugins: {
                    legend: { display: datasets.length > 1, position: 'top' },
                    tooltip: { callbacks: { label: function (ctx) { return ' ' + fmtVal(ctx.parsed.y, unidad); } } }
                },
                scales: SCALE_XY
            }
        });
    }

    // radar simple: sub-períodos como ejes
    function renderRadarSimple(canvas, labels, values, unidad) {
        var L = labels.slice(-12).map(subLabel);
        var V = values.slice(-12);
        new Chart(canvas, {
            type: 'radar',
            data: {
                labels: L,
                datasets: [{
                    label: 'Valor', data: V,
                    borderColor: ZF_TEAL, backgroundColor: 'rgba(25,147,184,.2)',
                    borderWidth: 2, pointRadius: 3,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: function (ctx) { return ' ' + fmtVal(ctx.parsed.r, unidad); } } }
                },
                scales: { r: { beginAtZero: true, ticks: { font: { size: 10 }, backdropColor: 'transparent' } } }
            }
        });
    }

    // torta/dona simple: últimos períodos como sectores
    function renderTortaSimple(canvas, labels, values, unidad) {
        var L = labels.slice(-12).map(subLabel);
        var V = values.slice(-12).map(function (v) { return v !== null && v > 0 ? v : 0; });
        new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: L,
                datasets: [{
                    data: V,
                    backgroundColor: PALETTE.concat(PALETTE).slice(0, L.length),
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: true,
                plugins: {
                    legend: { display: true, position: 'right', labels: { font: { size: 10 } } },
                    tooltip: { callbacks: { label: function (ctx) { return ' ' + ctx.label + ': ' + fmtVal(ctx.parsed, unidad); } } }
                }
            }
        });
    }

    // combo simple: barras + línea de tendencia
    function renderComboSimple(canvas, labels, values, meta, unidad) {
        var datasets = [
            {
                type: 'bar', label: 'Valor' + (unidad ? ' (' + unidad + ')' : ''),
                data: values, backgroundColor: 'rgba(25,147,184,.65)', borderRadius: 3,
            },
            {
                type: 'line', label: 'Tendencia',
                data: values, borderColor: ZF_NAVY, backgroundColor: 'transparent',
                borderWidth: 2, tension: 0.4, pointRadius: 0,
            }
        ];

        if (meta !== null) {
            datasets.push({
                type: 'line', label: 'Meta',
                data: labels.map(function () { return meta; }),
                borderColor: ZF_GREEN, backgroundColor: 'transparent',
                borderWidth: 1.5, borderDash: [6, 3], pointRadius: 0,
            });
        }

        new Chart(canvas, {
            type: 'bar',
            data: { labels: labels, datasets: datasets },
            options: {
                responsive: true, maintainAspectRatio: true,
                plugins: {
                    legend: { display: true, position: 'top' },
                    tooltip: { callbacks: { label: function (ctx) { return ' ' + ctx.dataset.label + ': ' + fmtVal(ctx.parsed.y, unidad); } } }
                },
                scales: SCALE_XY
            }
        });
    }

    // ── COMPARATIVO ANUAL ─────────────────────────────────────

    function renderComparativo(canvas, labels, values, tipo, meta, unidad) {
        var first   = labels[0] || '';
        var isAnual = /^\d{4}$/.test(first);

        // Anual: no hay sub-período — mostrar como simple
        if (isAnual) {
            renderSimple(canvas, labels, values, tipo === 'combo' ? 'barra' : tipo, meta, unidad);
            return;
        }

        // Agrupar: year → { sub → valor }
        var yearData = {};
        var subOrder = {};
        var idx      = 0;

        labels.forEach(function (p, i) {
            var dashAt = p.indexOf('-');
            var year   = dashAt >= 4 ? p.substring(0, 4) : p;
            var sub    = dashAt >= 4 ? p.substring(5)    : p;
            if (!yearData[year]) yearData[year] = {};
            yearData[year][sub] = values[i];
            if (!subOrder.hasOwnProperty(sub)) subOrder[sub] = idx++;
        });

        var subs    = Object.keys(subOrder).sort(function (a, b) { return subOrder[a] - subOrder[b]; });
        var xLabels = subs.map(subLabel);
        var years   = Object.keys(yearData).sort();

        switch (tipo) {
            case 'radar': return renderComparativoRadar(canvas, years, subs, xLabels, yearData, unidad);
            case 'torta': return renderComparativoTorta(canvas, years, yearData, unidad);
            case 'combo': return renderComparativoCombo(canvas, years, subs, xLabels, yearData, unidad);
            default:      return renderComparativoSerie(canvas, years, subs, xLabels, yearData, tipo, unidad);
        }
    }

    // comparativo línea / barra / área
    function renderComparativoSerie(canvas, years, subs, xLabels, yearData, tipo, unidad) {
        var isBarra = tipo === 'barra';
        var isArea  = tipo === 'area';

        var datasets = years.map(function (yr, i) {
            var c = PALETTE[i % PALETTE.length];
            return {
                label:            yr,
                data:             subs.map(function (s) { return yearData[yr].hasOwnProperty(s) ? yearData[yr][s] : null; }),
                borderColor:      c,
                backgroundColor:  isBarra ? c + 'bb' : (isArea ? c + '44' : c + '33'),
                borderWidth:      isBarra ? 0 : 2,
                tension:          0.3,
                pointRadius:      isBarra ? 0 : 4,
                pointHoverRadius: 6,
                fill:             isArea,
                spanGaps:         true,
            };
        });

        new Chart(canvas, {
            type: isBarra ? 'bar' : 'line',
            data: { labels: xLabels, datasets: datasets },
            options: {
                responsive: true, maintainAspectRatio: true,
                plugins: {
                    legend: { display: true, position: 'top' },
                    tooltip: { callbacks: { label: function (ctx) { return ' ' + ctx.dataset.label + ': ' + fmtVal(ctx.parsed.y, unidad); } } }
                },
                scales: SCALE_XY
            }
        });
    }

    // comparativo radar: un polígono por año
    function renderComparativoRadar(canvas, years, subs, xLabels, yearData, unidad) {
        var datasets = years.map(function (yr, i) {
            var c = PALETTE[i % PALETTE.length];
            return {
                label:           yr,
                data:            subs.map(function (s) { return yearData[yr].hasOwnProperty(s) ? (yearData[yr][s] || 0) : 0; }),
                borderColor:     c,
                backgroundColor: c + '33',
                borderWidth:     2,
                pointRadius:     3,
            };
        });

        new Chart(canvas, {
            type: 'radar',
            data: { labels: xLabels, datasets: datasets },
            options: {
                responsive: true, maintainAspectRatio: true,
                plugins: {
                    legend: { display: true, position: 'top' },
                    tooltip: { callbacks: { label: function (ctx) { return ' ' + ctx.dataset.label + ': ' + fmtVal(ctx.parsed.r, unidad); } } }
                },
                scales: { r: { beginAtZero: true, ticks: { font: { size: 10 }, backdropColor: 'transparent' } } }
            }
        });
    }

    // comparativo torta: total acumulado por año como sector
    function renderComparativoTorta(canvas, years, yearData, unidad) {
        var totals = years.map(function (yr) {
            return Object.values(yearData[yr]).reduce(function (acc, v) {
                return acc + (v !== null ? parseFloat(v) : 0);
            }, 0);
        });

        new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: years,
                datasets: [{
                    data: totals,
                    backgroundColor: years.map(function (_, i) { return PALETTE[i % PALETTE.length]; }),
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: true,
                plugins: {
                    legend: { display: true, position: 'right', labels: { font: { size: 11 } } },
                    tooltip: { callbacks: { label: function (ctx) { return ' ' + ctx.label + ': ' + fmtVal(ctx.parsed, unidad); } } }
                }
            }
        });
    }

    // comparativo combo: año más reciente como barras, años anteriores como líneas
    function renderComparativoCombo(canvas, years, subs, xLabels, yearData, unidad) {
        var lastYear = years[years.length - 1];

        var datasets = years.map(function (yr, i) {
            var c      = PALETTE[i % PALETTE.length];
            var isLast = yr === lastYear;
            return {
                type:            isLast ? 'bar' : 'line',
                label:           yr,
                data:            subs.map(function (s) { return yearData[yr].hasOwnProperty(s) ? yearData[yr][s] : null; }),
                borderColor:     c,
                backgroundColor: isLast ? c + 'bb' : 'transparent',
                borderWidth:     isLast ? 0 : 2,
                tension:         0.3,
                pointRadius:     isLast ? 0 : 4,
                spanGaps:        true,
            };
        });

        new Chart(canvas, {
            type: 'bar',
            data: { labels: xLabels, datasets: datasets },
            options: {
                responsive: true, maintainAspectRatio: true,
                plugins: {
                    legend: { display: true, position: 'top' },
                    tooltip: { callbacks: { label: function (ctx) { return ' ' + ctx.dataset.label + ': ' + fmtVal(ctx.parsed.y, unidad); } } }
                },
                scales: SCALE_XY
            }
        });
    }

    // ── Modal: agregar valor por período ─────────────────────
    var modalAgregar = document.getElementById('modalAgregarValor');
    if (modalAgregar) {
        document.querySelectorAll('.btn-agregar-valor').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.getElementById('inputIndicadorId').value           = btn.dataset.id;
                document.getElementById('labelNombreIndicador').textContent = btn.dataset.nombre;
                document.getElementById('inputValor').value                 = '';
                document.getElementById('inputObservaciones').value         = '';

                var periodicidad = btn.dataset.periodicidad;
                var select = document.getElementById('selectPeriodo');
                var help   = document.getElementById('periodoHelp');
                select.innerHTML = '';

                generarPeriodos(periodicidad).forEach(function (p) {
                    var opt = document.createElement('option');
                    opt.value = p.key; opt.textContent = p.label;
                    select.appendChild(opt);
                });

                var formatos = { mensual: '2026-01', trimestral: '2026-T1', semestral: '2026-S1', anual: '2026' };
                help.textContent = 'Formato: ' + (formatos[periodicidad] || '');

                bootstrap.Modal.getOrCreateInstance(modalAgregar).show();
            });
        });
    }

    // ── Modal: desasignar indicador ───────────────────────────
    var modalDesasignar = document.getElementById('modalDesasignar');
    if (modalDesasignar) {
        document.querySelectorAll('.btn-desasignar-ind').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.getElementById('inputDesasignarId').value           = btn.dataset.id;
                document.getElementById('labelNombreDesasignar').textContent = btn.dataset.nombre;
                bootstrap.Modal.getOrCreateInstance(modalDesasignar).show();
            });
        });
    }

    // ── Generador de períodos disponibles ─────────────────────
    function generarPeriodos(periodicidad) {
        var now = new Date(), result = [];

        if (periodicidad === 'mensual') {
            for (var i = 35; i >= 0; i--) {
                var d   = new Date(now.getFullYear(), now.getMonth() - i, 1);
                var key = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0');
                result.push({ key: key, label: d.toLocaleString('es-CO', { month: 'long', year: 'numeric' }) });
            }
        } else if (periodicidad === 'trimestral') {
            var totalQ = now.getFullYear() * 4 + Math.floor(now.getMonth() / 3);
            for (var j = 15; j >= 0; j--) {
                var q = totalQ - j, yr = Math.floor(q / 4), qn = (q % 4) + 1;
                result.push({ key: yr + '-T' + qn, label: 'T' + qn + ' ' + yr });
            }
        } else if (periodicidad === 'semestral') {
            var totalS = now.getFullYear() * 2 + Math.floor(now.getMonth() / 6);
            for (var k = 9; k >= 0; k--) {
                var s = totalS - k, yrS = Math.floor(s / 2), sn = (s % 2) + 1;
                result.push({ key: yrS + '-S' + sn, label: 'S' + sn + ' ' + yrS });
            }
        } else {
            for (var m = 4; m >= 0; m--) {
                var y = now.getFullYear() - m;
                result.push({ key: String(y), label: String(y) });
            }
        }
        return result;
    }

});
