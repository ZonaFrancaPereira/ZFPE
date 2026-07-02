<?php
/** @var array $datos */
/** @var array $empresa */
/** @var InformesControlador $this */
$empresaId          = $datos['empresaId'];
$fases               = $datos['fases'];
$documentos          = $datos['documentos'];
$indicadores         = $datos['indicadores'];
$indicadorHistorial  = $datos['indicadorHistorial'];
$conteoEstados       = $datos['conteoEstados'];
$avanceGeneral       = $datos['avanceGeneral'];
$avancePorFase       = $datos['avancePorFase'];
$estadoLabel         = InformesControlador::ESTADO_LABEL;
$estadoColor         = InformesControlador::ESTADO_COLOR;
$totalEstados        = array_sum($conteoEstados);
$nombresIndicador    = array_column($indicadores, 'nombre', 'id');
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: Helvetica, Arial, sans-serif; font-size: 10px; color: #222; }
    h1 { font-size: 18px; color: #1993b8; margin: 0 0 4px; }
    h2 { font-size: 13px; color: #17607a; margin: 16px 0 6px; border-bottom: 1px solid #ddd; padding-bottom: 3px; }
    h3.fase-titulo { font-size: 12px; color: #fff; background: #17607a; padding: 4px 8px; margin: 14px 0 6px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
    th { background: #1993b8; color: #fff; text-align: left; padding: 3px 5px; font-size: 8.5px; }
    td { padding: 3px 5px; border-bottom: 1px solid #e5e5e5; font-size: 8.5px; vertical-align: top; }
    a { color: #0563c1; text-decoration: none; }
    .meta-table td { border: none; padding: 2px 10px 2px 0; font-size: 10px; }
    .badge { display: inline-block; padding: 2px 7px; border-radius: 8px; color: #fff; font-size: 8px; }
    .legend-chip { display: inline-block; width: 8px; height: 8px; border-radius: 2px; margin-right: 3px; }
    .legend-item { display: inline-block; margin-right: 14px; font-size: 9px; }
    .dist-table { width: 100%; height: 20px; border-collapse: collapse; margin-bottom: 8px; }
    .dist-table td { padding: 0; border: 0; height: 20px; }
    .fase-label-table { width: 100%; border-collapse: collapse; margin-bottom: 0; }
    .fase-label-table td { padding: 0; border: 0; font-size: 9px; }
    .bar-track { background: #eee; border-radius: 3px; height: 10px; margin: 2px 0 8px; }
    .bar-fill { background: #1993b8; height: 10px; border-radius: 3px; }
    .etapa-titulo { font-size: 11px; font-weight: bold; color: #17607a; margin: 10px 0 4px; }
    .req-box { border: 1px solid #ddd; border-radius: 3px; padding: 6px 8px; margin-bottom: 8px; page-break-inside: avoid; }
    .req-header { font-size: 10.5px; font-weight: bold; margin-bottom: 2px; }
    .req-meta { font-size: 8.5px; color: #555; margin-bottom: 3px; }
    .req-obs { font-size: 9px; background: #f7f7f7; padding: 4px 6px; border-radius: 3px; margin-bottom: 4px; }
    .req-docs { font-size: 8.5px; margin-bottom: 4px; }
    .subtitulo { font-size: 8.5px; font-weight: bold; color: #17607a; margin: 4px 0 2px; text-transform: uppercase; }
    .footer { font-size: 8px; color: #999; margin-top: 16px; }
</style>
</head>
<body>
    <h1>Informe ZFPE — <?= htmlspecialchars($empresa['razon_social']) ?></h1>
    <table class="meta-table">
        <tr>
            <td><strong>NIT:</strong> <?= htmlspecialchars($empresa['nit']) ?></td>
            <td><strong>Representante:</strong> <?= htmlspecialchars($empresa['representante'] ?? '—') ?></td>
        </tr>
        <tr>
            <td><strong>Avance general:</strong> <?= $avanceGeneral ?>%</td>
            <td><strong>Fecha del informe:</strong> <?= date('d/m/Y H:i') ?></td>
        </tr>
        <tr>
            <td colspan="2">
                <a href="<?= htmlspecialchars($this->baseUrl() . '/index.php?modulo=empresas&accion=ver&id=' . $empresaId) ?>">Abrir esta empresa en el sistema →</a>
            </td>
        </tr>
    </table>

    <h2>Distribución de requisitos por estado</h2>
    <?php if ($totalEstados === 0): ?>
        <p>No hay requisitos configurados todavía.</p>
    <?php else: ?>
    <table class="dist-table"><tr>
        <?php foreach ($estadoLabel as $key => $label): $pct = round(($conteoEstados[$key] ?? 0) / $totalEstados * 100, 1); if ($pct <= 0) continue; ?>
        <td style="width:<?= $pct ?>%; background:#<?= $estadoColor[$key] ?>;">&nbsp;</td>
        <?php endforeach; ?>
    </tr></table>
    <div>
        <?php foreach ($estadoLabel as $key => $label): ?>
        <span class="legend-item"><span class="legend-chip" style="background:#<?= $estadoColor[$key] ?>;"></span><?= $label ?>: <strong><?= $conteoEstados[$key] ?? 0 ?></strong></span>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <h2>Avance por fase</h2>
    <?php foreach ($avancePorFase as $af): ?>
    <div>
        <table class="fase-label-table"><tr>
            <td style="text-align:left;"><?= htmlspecialchars($af['fase']) ?></td>
            <td style="text-align:right; width:40px;"><?= $af['avance'] ?>%</td>
        </tr></table>
        <div class="bar-track"><div class="bar-fill" style="width:<?= $af['avance'] ?>%;"></div></div>
    </div>
    <?php endforeach; ?>

    <h2>Progreso detallado por etapa, requisito e ítem</h2>
    <?php foreach ($fases as $fase): ?>
        <h3 class="fase-titulo">Fase: <?= htmlspecialchars($fase['nombre']) ?></h3>
        <?php foreach ($fase['etapas'] as $etapa): ?>
            <div class="etapa-titulo"><?= htmlspecialchars($etapa['etapa_nombre']) ?> — <?= $etapa['avance'] ?>% de avance</div>
            <div class="bar-track"><div class="bar-fill" style="width:<?= $etapa['avance'] ?>%;"></div></div>
            <?php if (empty($etapa['requisitos'])): ?>
                <p>Sin requisitos configurados.</p>
            <?php endif; ?>
            <?php foreach ($etapa['requisitos'] as $req): ?>
            <div class="req-box">
                <div class="req-header">
                    <?= htmlspecialchars($req['requisito_nombre']) ?><?= $req['obligatorio'] ? ' *' : '' ?>
                    <span class="badge" style="background:#<?= $estadoColor[$req['estado_req']] ?? '6c757d' ?>"><?= $estadoLabel[$req['estado_req']] ?? $req['estado_req'] ?></span>
                    — <a href="<?= htmlspecialchars($this->urlSeguimiento($empresaId, (int) $req['requisito_id'])) ?>">Ver en Seguimiento →</a>
                </div>
                <div class="req-meta">
                    Entidad: <?= htmlspecialchars($req['entidad_nombre'] ?: '—') ?> ·
                    Responsable: <?= htmlspecialchars($req['responsable'] ?: '—') ?> ·
                    Vencimiento: <?= $req['fecha_vencimiento'] ? date('d/m/Y', strtotime($req['fecha_vencimiento'])) : '—' ?> ·
                    Cumplimiento: <?= $req['fecha_cumplimiento'] ? date('d/m/Y', strtotime($req['fecha_cumplimiento'])) : '—' ?>
                </div>
                <?php if ($req['observaciones']): ?>
                <div class="req-obs"><?= nl2br(htmlspecialchars($req['observaciones'])) ?></div>
                <?php endif; ?>

                <?php if (!empty($req['items'])): ?>
                <div class="subtitulo">Ítems del requisito</div>
                <table>
                    <thead><tr><th>Ítem</th><th>Obligatorio</th><th>Cumplido</th><th>Fecha</th></tr></thead>
                    <tbody>
                    <?php foreach ($req['items'] as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['item_nombre']) ?></td>
                            <td><?= $item['obligatorio'] ? 'Sí' : 'No' ?></td>
                            <td><?= $item['cumplido'] ? '✓ Sí' : '✗ No' ?></td>
                            <td><?= $item['fecha_cumplimiento'] ? date('d/m/Y', strtotime($item['fecha_cumplimiento'])) : '—' ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>

                <?php if (!empty($req['documentos'])): ?>
                <div class="subtitulo">Documentos</div>
                <div class="req-docs">
                    <?php foreach ($req['documentos'] as $doc): ?>
                        <a href="<?= htmlspecialchars($this->urlDocumento((int) $doc['id'])) ?>"><?= htmlspecialchars($doc['nombre_original']) ?></a><?= $doc['descripcion'] ? ' — ' . htmlspecialchars($doc['descripcion']) : '' ?><br>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($req['historial'])): ?>
                <div class="subtitulo">Historial de cambios</div>
                <table>
                    <thead><tr><th>Fecha</th><th>Estado</th><th>Observaciones</th><th>Documento</th><th>Usuario</th></tr></thead>
                    <tbody>
                    <?php foreach ($req['historial'] as $h): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($h['created_at'])) ?></td>
                            <td>
                                <?= $estadoLabel[$h['estado_nuevo']] ?? $h['estado_nuevo'] ?>
                                <?php if ($h['estado_anterior'] && $h['estado_anterior'] !== $h['estado_nuevo']): ?>
                                    (antes: <?= $estadoLabel[$h['estado_anterior']] ?? $h['estado_anterior'] ?>)
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($h['observaciones'] ?: '—') ?></td>
                            <td>
                                <?php if (!empty($h['documento_id_valido'])): ?>
                                    <a href="<?= htmlspecialchars($this->urlDocumento((int) $h['documento_id_valido'])) ?>"><?= htmlspecialchars($h['documento_nombre']) ?></a>
                                <?php else: ?>—<?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($h['usuario_nombre'] ?: '—') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php endforeach; ?>

    <h2>Documentos (<?= count($documentos) ?>)</h2>
    <?php if (empty($documentos)): ?>
        <p>No hay documentos cargados.</p>
    <?php else: ?>
    <table>
        <thead><tr><th>Etapa</th><th>Requisito</th><th>Archivo</th><th>Descripción</th><th>Fecha</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($documentos as $d): ?>
            <tr>
                <td><?= htmlspecialchars($d['etapa_nombre']) ?></td>
                <td><?= htmlspecialchars($d['requisito_nombre']) ?></td>
                <td><?= htmlspecialchars($d['nombre_original']) ?></td>
                <td><?= htmlspecialchars($d['descripcion'] ?: '—') ?></td>
                <td><?= $d['created_at'] ? date('d/m/Y', strtotime($d['created_at'])) : '' ?></td>
                <td><a href="<?= htmlspecialchars($this->urlDocumento((int) $d['id'])) ?>">Descargar</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <?php if (!empty($indicadores)): ?>
    <h2>Indicadores</h2>
    <table>
        <thead><tr><th>Indicador</th><th>Unidad</th><th>Meta</th><th>Último valor</th><th>Período</th><th>Periodicidad</th></tr></thead>
        <tbody>
        <?php foreach ($indicadores as $i): ?>
            <tr>
                <td><?= htmlspecialchars($i['nombre']) ?></td>
                <td><?= htmlspecialchars($i['unidad'] ?: '—') ?></td>
                <td><?= $i['meta'] ?? '—' ?></td>
                <td><?= $i['ultimo_valor'] ?? '—' ?></td>
                <td><?= $i['ultimo_periodo'] ?? '—' ?></td>
                <td><?= htmlspecialchars($i['periodicidad'] ?? '—') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php foreach ($indicadores as $i):
        $valoresInd = $indicadorHistorial[$i['id']] ?? [];
        if (empty($valoresInd)) continue;
        $etiquetas  = array_column($valoresInd, 'periodo');
        $numericos  = array_map(fn($v) => (float) $v['valor'], $valoresInd);
        $metaInd    = $i['meta'] !== null ? (float) $i['meta'] : null;
        $imagenGraf = $this->generarGraficoIndicador($etiquetas, $numericos, $metaInd, $i['tipo_grafico'] ?? 'linea', $i['unidad'] ?? '');
    ?>
    <div class="subtitulo"><?= htmlspecialchars($i['nombre']) ?> — gráfica de histórico<?= $metaInd !== null ? ' (meta: ' . number_format($metaInd, 2, ',', '.') . ')' : '' ?></div>
    <?php if ($imagenGraf): ?>
        <img src="<?= $imagenGraf ?>" style="width:100%; max-width:460px; margin-bottom:10px;">
    <?php endif; ?>
    <?php endforeach; ?>

    <?php if (!empty($indicadorHistorial)): ?>
    <div class="subtitulo">Histórico de valores por período (detalle)</div>
    <table>
        <thead><tr><th>Indicador</th><th>Período</th><th>Valor</th><th>Observaciones</th><th>Registrado por</th><th>Fecha</th></tr></thead>
        <tbody>
        <?php foreach ($indicadorHistorial as $indId => $valores): ?>
            <?php foreach ($valores as $v): ?>
            <tr>
                <td><?= htmlspecialchars($nombresIndicador[$indId] ?? ('Indicador #' . $indId)) ?></td>
                <td><?= htmlspecialchars($v['periodo']) ?></td>
                <td><?= $v['valor'] ?></td>
                <td><?= htmlspecialchars($v['observaciones'] ?: '—') ?></td>
                <td><?= htmlspecialchars($v['registrado_por_nombre'] ?: '—') ?></td>
                <td><?= $v['created_at'] ? date('d/m/Y', strtotime($v['created_at'])) : '' ?></td>
            </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
    <?php endif; ?>

    <div class="footer">Generado automáticamente por ZFPE el <?= date('d/m/Y H:i') ?></div>
</body>
</html>
