<?php
/** @var array|null $empresa */
/** @var array      $etapas */
/** @var array      $resumenEstados */
/** @var array      $vencidos */
/** @var array      $porVencer */
/** @var int        $totalDocs */
/** @var array      $todasEmpresas */
$pageTitle     = 'Reporte — ZFPE';
$activePage    = 'reportes';
$pageStyles    = ['vista/assets/css/componentes.css', 'vista/assets/css/indicadores.css'];
$esOperaciones = ($_SESSION['usuario_rol'] ?? '') === 'operaciones';
$esAdmin       = ($_SESSION['usuario_rol'] ?? '') === 'admin';

$colorEstado = ['pendiente' => 'secondary', 'en_progreso' => 'primary', 'completa' => 'success'];

// Helpers para enlazar etapas/requisitos al módulo correspondiente según el rol
$urlEtapa = function (int $etapaId) use ($esOperaciones, $esAdmin, $empresa): string {
    $modulo = ($esOperaciones || $esAdmin) ? 'seguimiento' : 'cronograma';
    return "index.php?modulo=$modulo&id={$empresa['id']}#etapa-$etapaId";
};
$urlRequisito = function (int $reqId) use ($esOperaciones, $esAdmin, $empresa): string {
    $modulo = ($esOperaciones || $esAdmin) ? 'seguimiento' : 'cronograma';
    return "index.php?modulo=$modulo&id={$empresa['id']}#req-$reqId";
};

// Agrupar etapas por fase
$repFaseGrupos = [];
foreach ($etapas as $et) {
    $fid = $et['fase_id'] ?? 0;
    if (!isset($repFaseGrupos[$fid])) {
        $repFaseGrupos[$fid] = [
            'nombre' => $et['fase_nombre'] ?? null,
            'orden'  => $et['fase_orden']  ?? 999,
            'etapas' => [],
        ];
    }
    $repFaseGrupos[$fid]['etapas'][] = $et;
}
usort($repFaseGrupos, fn($a, $b) => $a['orden'] <=> $b['orden']);

// Datos para el gráfico de avance por fase
// El nombre de la fase se acorta para el eje del gráfico (ej. "EVANCE DEL
// PROYECTO - ETAPA PREOPERATIVA" -> "Etapa Preoperativa"); el nombre completo
// se conserva para el resto de la app.
$acortarNombreFase = function (string $nombre): string {
    $partes = explode(' - ', $nombre);
    return ucwords(mb_strtolower(trim(end($partes))));
};

$chartFasesLabels = [];
$chartFasesData   = [];
foreach ($repFaseGrupos as $rFase) {
    $avances = array_column($rFase['etapas'], 'avance');
    $pct     = count($avances) > 0 ? round(array_sum($avances) / count($avances), 1) : 0;
    $chartFasesLabels[] = $acortarNombreFase($rFase['nombre'] ?? 'Sin fase');
    $chartFasesData[]   = $pct;
}

// Totales globales
$totalReq     = array_sum($resumenEstados);
$cumplidos    = $resumenEstados['cumplido']    ?? 0;
$enProgreso   = $resumenEstados['en_progreso'] ?? 0;
$pendientes   = $resumenEstados['pendiente']   ?? 0;
$noAplica     = $resumenEstados['no_aplica']   ?? 0;
$avanceGlobal = count($etapas) > 0
    ? round(array_sum(array_column($etapas, 'avance')) / count($etapas), 1)
    : 0;
$etapasCompletas = count(array_filter($etapas, fn($e) => $e['estado_progreso'] === 'completa'));

if ($empresa) {
    $pageScripts = [
        'vista/assets/vendor/chartjs/chart.umd.min.js',
        'vista/assets/js/reportes-charts.js',
        'vista/assets/js/indicadores.js',
    ];
}
?>
<?php require_once __DIR__ . '/../../parciales/cabecera.php'; ?>

<div class="app-wrapper">
  <?php require_once __DIR__ . '/../../parciales/navegacion.php'; ?>
  <?php require_once __DIR__ . '/../../parciales/menu_lateral.php'; ?>

  <main class="app-main">
    <div class="app-content-header encabezado-zf">
      <div class="container-fluid">
        <div class="row align-items-center">
          <div class="col-sm-6"><h3 class="mb-0 titulo-zf">Reportes</h3></div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item active">Reportes</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="app-content">
      <div class="container-fluid">

        <?php if (!$empresa && ($esOperaciones || $esAdmin) && !empty($todasEmpresas)): ?>
          <!-- Selector de empresa para operaciones/admin -->
          <div class="card shadow-sm">
            <div class="card-header">
              <h5 class="card-title mb-0"><i class="bi bi-building me-2 text-primary"></i>Seleccionar empresa</h5>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Empresa</th>
                      <th>NIT</th>
                      <th style="min-width:140px;">Avance general</th>
                      <th class="text-end">Reporte</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($todasEmpresas as $e): ?>
                    <tr>
                      <td>
                        <div class="fw-semibold"><?= htmlspecialchars($e['razon_social']) ?></div>
                        <small class="text-muted"><?= htmlspecialchars($e['representante'] ?? '') ?></small>
                      </td>
                      <td class="text-muted small"><?= htmlspecialchars($e['nit']) ?></td>
                      <td>
                        <?php $av = (float)($e['avance_general'] ?? 0); ?>
                        <div class="d-flex align-items-center gap-2">
                          <div class="progress flex-grow-1" style="height:6px;">
                            <div class="progress-bar bg-success" style="width:<?= $av ?>%"></div>
                          </div>
                          <small class="text-muted fw-semibold" style="min-width:34px;"><?= $av ?>%</small>
                        </div>
                      </td>
                      <td class="text-end">
                        <a href="index.php?modulo=reportes&id=<?= $e['id'] ?>"
                           class="btn btn-sm btn-outline-primary">
                          <i class="bi bi-bar-chart-fill me-1"></i>Ver reporte
                        </a>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        <?php elseif (!$empresa): ?>
          <div class="card shadow-sm">
            <div class="card-body text-center py-5 text-muted">
              <i class="bi bi-bar-chart fs-1 opacity-25 d-block mb-3"></i>
              <h5>Sin empresa asignada</h5>
              <p class="mb-0">Tu cuenta aún no tiene una empresa vinculada.</p>
            </div>
          </div>

        <?php else: ?>

          <!-- Botones de navegación para operaciones/admin -->
          <?php if ($esOperaciones || $esAdmin): ?>
          <div class="mb-3 d-flex gap-2 no-print">
            <a href="index.php?modulo=empresas&accion=ver&id=<?= $empresa['id'] ?>"
               class="btn btn-outline-secondary btn-sm">
              <i class="bi bi-arrow-left me-1"></i> Volver a la empresa
            </a>
            <a href="index.php?modulo=reportes" class="btn btn-outline-secondary btn-sm">
              <i class="bi bi-list me-1"></i> Ver todas las empresas
            </a>
          </div>
          <?php endif; ?>

          <!-- Encabezado del reporte -->
          <div class="card shadow-sm mb-4 border-0 print-header card-acento-teal">
            <div class="card-body py-3">
              <div class="row align-items-center g-3">
                <div class="col-md-6">
                  <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                         style="width:52px;height:52px;font-size:1.2rem;background:linear-gradient(135deg, var(--zf-navy,#22404b), var(--zf-teal,#1993b8));">
                      <?= mb_strtoupper(mb_substr($empresa['razon_social'], 0, 1)) ?>
                    </div>
                    <div class="min-w-0">
                      <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-1"
                           style="background:linear-gradient(90deg, var(--zf-navy,#22404b), var(--zf-teal,#1993b8));">
                        <i class="bi bi-building text-white"></i>
                        <span class="text-white fw-semibold text-truncate" style="font-size:1rem;letter-spacing:.02em;max-width:280px;">
                          <?= htmlspecialchars($empresa['razon_social']) ?>
                        </span>
                      </div>
                      <div class="text-muted small">NIT: <?= htmlspecialchars($empresa['nit']) ?></div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="text-center text-md-start">
                    <div class="fs-3 fw-bold text-<?= $avanceGlobal >= 100 ? 'success' : 'zf-teal' ?>">
                      <?= $avanceGlobal ?>%
                    </div>
                    <div class="progress mb-1" style="height:8px;">
                      <div class="progress-bar" style="width:<?= $avanceGlobal ?>%;background-color:<?= $avanceGlobal >= 100 ? '#198754' : 'var(--zf-teal,#1993b8)' ?>;"></div>
                    </div>
                    <small class="text-muted">Avance general</small>
                  </div>
                </div>
                <div class="col-md-2 text-md-end no-print">
                  <div class="d-flex flex-column gap-2 align-items-stretch align-items-md-end">
                    <a href="index.php?modulo=informes&accion=excel&id=<?= $empresa['id'] ?>"
                       class="btn btn-outline-zf-teal btn-sm">
                      <i class="bi bi-file-earmark-excel me-1 text-success"></i>Excel
                    </a>
                    <a href="index.php?modulo=informes&accion=pdf&id=<?= $empresa['id'] ?>"
                       class="btn btn-outline-zf-teal btn-sm">
                      <i class="bi bi-file-earmark-pdf me-1 text-danger"></i>PDF
                    </a>
                    <button onclick="window.print()" class="btn btn-outline-zf-teal btn-sm">
                      <i class="bi bi-printer me-1"></i>Imprimir
                    </button>
                  </div>
                </div>
                <div class="col-12 text-muted small print-only" style="display:none;">
                  Generado el <?= date('d/m/Y H:i') ?>
                </div>
              </div>
            </div>
          </div>

          <!-- KPIs -->
          <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
              <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-primary"><?= $avanceGlobal ?>%</div>
                <div class="text-muted small">Avance general</div>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-success"><?= $etapasCompletas ?>/<?= count($etapas) ?></div>
                <div class="text-muted small">Etapas completadas</div>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-success"><?= $cumplidos ?>/<?= $totalReq ?></div>
                <div class="text-muted small">Requisitos cumplidos</div>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold <?= count($vencidos) > 0 ? 'text-danger' : 'text-muted' ?>">
                  <?= count($vencidos) ?>
                </div>
                <div class="text-muted small">Requisitos vencidos</div>
              </div>
            </div>
          </div>

          <!-- Avance por etapa -->
          <div class="card shadow-sm mb-4 card-acento-teal">
            <div class="card-header">
              <h6 class="card-title mb-0"><i class="bi bi-diagram-3 me-2 text-success"></i>Avance por etapa</h6>
            </div>
            <div class="card-body p-0">
              <?php $estadoIconRep = ['pendiente' => 'bi-hourglass-split', 'en_progreso' => 'bi-arrow-repeat', 'completa' => 'bi-check-circle-fill']; ?>
              <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                  <thead>
                    <tr>
                      <th>Etapa</th>
                      <th class="text-center">Requisitos</th>
                      <th style="min-width:140px;">Avance</th>
                      <th class="text-center">Estado</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($repFaseGrupos as $rFase): ?>

                    <?php if ($rFase['nombre']): ?>
                    <tr>
                      <td colspan="4" class="py-2 px-3"
                          style="background:linear-gradient(90deg, var(--zf-navy,#22404b), var(--zf-teal,#1993b8));">
                        <span class="badge py-1 px-2" style="background:rgba(255,255,255,.15);color:#fff;">
                          <i class="bi bi-collection me-1"></i><?= htmlspecialchars($rFase['nombre']) ?>
                        </span>
                      </td>
                    </tr>
                    <?php endif; ?>

                    <?php foreach ($rFase['etapas'] as $etapa): ?>
                    <?php $ec = $colorEstado[$etapa['estado_progreso']] ?? 'secondary'; ?>
                    <tr style="border-left:3px solid var(--bs-<?= $ec ?>);">
                      <td class="fw-semibold small <?= $rFase['nombre'] ? 'ps-4' : '' ?>">
                        <a class="text-decoration-none" href="<?= htmlspecialchars($urlEtapa((int) $etapa['id'])) ?>"><?= htmlspecialchars($etapa['nombre']) ?></a>
                      </td>
                      <td class="text-center small text-muted">
                        <?= (int)$etapa['req_cumplidos'] + (int)$etapa['req_no_aplica'] ?>/<?= (int)$etapa['total_req'] ?>
                      </td>
                      <td>
                        <div class="d-flex align-items-center gap-2">
                          <div class="progress flex-grow-1" style="height:6px;">
                            <div class="progress-bar bg-<?= $ec ?>" style="width:<?= (float)$etapa['avance'] ?>%"></div>
                          </div>
                          <small class="fw-semibold text-<?= $ec ?>" style="min-width:36px;">
                            <?= (float)$etapa['avance'] ?>%
                          </small>
                        </div>
                      </td>
                      <td class="text-center">
                        <span class="badge bg-<?= $ec ?> small">
                          <i class="bi <?= $estadoIconRep[$etapa['estado_progreso']] ?? 'bi-circle' ?> me-1"></i>
                          <?= ['pendiente'=>'Pendiente','en_progreso'=>'En progreso','completa'=>'Completa'][$etapa['estado_progreso']] ?? $etapa['estado_progreso'] ?>
                        </span>
                      </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Fila de resumen: distribución, avance por fase, documentación, fechas clave -->
          <?php $etapasConFecha = array_filter($etapas, fn($e) => $e['fecha_inicio'] || $e['fecha_completado']); ?>
          <div class="row g-3 mb-4">

            <!-- Distribución de requisitos -->
            <div class="col-md-6 col-lg-3">
              <div class="card shadow-sm h-100 card-acento-teal">
                <div class="card-header">
                  <h6 class="card-title mb-0"><i class="bi bi-pie-chart me-2 text-primary"></i>Distribución</h6>
                </div>
                <div class="card-body">
                  <?php if ($totalReq === 0): ?>
                    <p class="text-muted text-center mb-0 small">Sin requisitos asignados.</p>
                  <?php else: ?>
                    <div class="mb-3" style="max-width:160px;margin-inline:auto;">
                      <canvas id="chartDistribucion" width="160" height="160"></canvas>
                    </div>
                    <?php
                    $items = [
                        ['Cumplidos',    $cumplidos,  'success',   'check-circle'],
                        ['En progreso',  $enProgreso, 'primary',   'arrow-repeat'],
                        ['Pendientes',   $pendientes, 'secondary', 'hourglass'],
                        ['No aplica',    $noAplica,   'light',     'dash-circle'],
                    ];
                    foreach ($items as [$label, $count, $color, $icon]):
                        if ($count === 0) continue;
                        $pct = round($count / $totalReq * 100);
                    ?>
                    <div class="d-flex align-items-center justify-content-between py-1 small">
                      <span class="d-flex align-items-center gap-1">
                        <i class="bi bi-<?= $icon ?> text-<?= $color ?>"></i>
                        <?= $label ?>
                      </span>
                      <span class="fw-bold"><?= $count ?> <span class="text-muted fw-normal">(<?= $pct ?>%)</span></span>
                    </div>
                    <?php endforeach; ?>
                    <div class="border-top pt-2 mt-2 d-flex justify-content-between">
                      <small class="text-muted">Total</small>
                      <small class="fw-bold"><?= $totalReq ?></small>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>

            <!-- Avance por fase -->
            <?php if (count($chartFasesLabels) > 1 || (count($chartFasesLabels) === 1 && $chartFasesLabels[0] !== 'Sin fase')): ?>
            <div class="col-md-6 col-lg-3">
              <div class="card shadow-sm h-100 card-acento-teal">
                <div class="card-header">
                  <h6 class="card-title mb-0"><i class="bi bi-bar-chart-fill me-2 text-danger"></i>Avance por fase</h6>
                </div>
                <div class="card-body">
                  <canvas id="chartFases" height="180"></canvas>
                </div>
              </div>
            </div>
            <?php endif; ?>

            <!-- Documentos -->
            <div class="col-md-6 col-lg-3">
              <div class="card shadow-sm h-100 card-acento-teal">
                <div class="card-header">
                  <h6 class="card-title mb-0"><i class="bi bi-folder2 me-2 text-warning"></i>Documentación</h6>
                </div>
                <div class="card-body text-center py-4">
                  <div class="fs-2 fw-bold text-zf-teal"><?= $totalDocs ?></div>
                  <div class="text-muted small"><?= $totalDocs === 1 ? 'documento subido' : 'documentos subidos' ?></div>
                  <?php if (($_SESSION['usuario_rol'] ?? '') === 'usuario'): ?>
                  <a href="index.php?modulo=documentos" class="btn btn-outline-zf-teal btn-sm mt-2 no-print">
                    <i class="bi bi-folder2-open me-1"></i>Ver documentos
                  </a>
                  <?php else: ?>
                  <a href="index.php?modulo=documentos&accion=ver&id=<?= $empresa['id'] ?>" class="btn btn-outline-zf-teal btn-sm mt-2 no-print">
                    <i class="bi bi-folder2-open me-1"></i>Ver documentos
                  </a>
                  <?php endif; ?>
                </div>
              </div>
            </div>

            <!-- Fechas clave -->
            <?php if (!empty($etapasConFecha)): ?>
            <div class="col-md-6 col-lg-3">
              <div class="card shadow-sm h-100 card-acento-teal">
                <div class="card-header">
                  <h6 class="card-title mb-0"><i class="bi bi-calendar-check me-2 text-info"></i>Fechas clave</h6>
                </div>
                <div class="card-body" style="max-height:260px;overflow-y:auto;">
                  <?php $totalFechasRep = count($etapasConFecha); $iRep = 0; ?>
                  <?php foreach ($etapasConFecha as $et): $iRep++; ?>
                  <div class="d-flex align-items-center justify-content-between gap-2 py-2 <?= $iRep < $totalFechasRep ? 'border-bottom' : '' ?>">
                    <div class="min-w-0">
                      <a class="text-truncate d-block" style="font-size:.72rem;font-weight:600;"
                         href="<?= htmlspecialchars($urlEtapa((int) $et['id'])) ?>"><?= htmlspecialchars($et['nombre']) ?></a>
                      <?php if (!empty($et['fase_nombre'])): ?>
                        <div class="text-muted text-truncate" style="font-size:.6rem;text-transform:uppercase;letter-spacing:.03em;">
                          <?= htmlspecialchars($et['fase_nombre']) ?>
                        </div>
                      <?php endif; ?>
                    </div>
                    <div class="d-flex flex-column align-items-end flex-shrink-0" style="font-size:.65rem;">
                      <?php if ($et['fecha_inicio']): ?>
                        <span class="text-primary"><i class="bi bi-play-circle me-1"></i><?= date('d/m/Y', strtotime($et['fecha_inicio'])) ?></span>
                      <?php endif; ?>
                      <?php if ($et['fecha_completado']): ?>
                        <span class="text-success"><i class="bi bi-check-circle me-1"></i><?= date('d/m/Y', strtotime($et['fecha_completado'])) ?></span>
                      <?php endif; ?>
                    </div>
                  </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
            <?php endif; ?>

          </div>

          <!-- Alertas de vencimiento -->
          <?php if (!empty($vencidos) || !empty($porVencer)): ?>
          <div class="card shadow-sm mb-4 card-acento-teal">
            <div class="card-header">
              <h6 class="card-title mb-0"><i class="bi bi-bell me-2 text-danger"></i>Alertas de vencimiento</h6>
            </div>
            <div class="card-body p-0">
              <div class="row g-0">
                <?php if (!empty($vencidos)): ?>
                  <div class="col-md-6">
                    <div class="px-3 pt-2 pb-1">
                      <p class="small fw-semibold text-danger mb-2">
                        <i class="bi bi-exclamation-circle me-1"></i>Vencidos (<?= count($vencidos) ?>)
                      </p>
                    </div>
                    <ul class="list-group list-group-flush">
                      <?php foreach ($vencidos as $v): ?>
                      <li class="list-group-item px-3 py-2">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                          <div>
                            <div class="small fw-semibold">
                              <a href="<?= htmlspecialchars($urlRequisito((int) $v['requisito_id'])) ?>"><?= htmlspecialchars($v['requisito']) ?></a>
                            </div>
                            <small class="text-muted"><?= htmlspecialchars($v['etapa']) ?></small>
                          </div>
                          <div class="text-end flex-shrink-0">
                            <span class="badge bg-danger"><?= date('d/m/Y', strtotime($v['fecha_vencimiento'])) ?></span>
                            <div class="text-danger small"><?= $v['dias_vencido'] ?> días vencido</div>
                          </div>
                        </div>
                      </li>
                      <?php endforeach; ?>
                    </ul>
                  </div>
                <?php endif; ?>

                <?php if (!empty($porVencer)): ?>
                  <div class="col-md-6 <?= !empty($vencidos) ? 'border-start' : '' ?>">
                    <div class="px-3 pt-2 pb-1">
                      <p class="small fw-semibold text-warning mb-2">
                        <i class="bi bi-clock me-1"></i>Próximos a vencer — 30 días (<?= count($porVencer) ?>)
                      </p>
                    </div>
                    <ul class="list-group list-group-flush">
                      <?php foreach ($porVencer as $pv): ?>
                      <li class="list-group-item px-3 py-2">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                          <div>
                            <div class="small fw-semibold">
                              <a href="<?= htmlspecialchars($urlRequisito((int) $pv['requisito_id'])) ?>"><?= htmlspecialchars($pv['requisito']) ?></a>
                            </div>
                            <small class="text-muted"><?= htmlspecialchars($pv['etapa']) ?></small>
                          </div>
                          <div class="text-end flex-shrink-0">
                            <span class="badge bg-warning text-dark"><?= date('d/m/Y', strtotime($pv['fecha_vencimiento'])) ?></span>
                            <div class="text-muted small"><?= $pv['dias_restantes'] ?> días restantes</div>
                          </div>
                        </div>
                      </li>
                      <?php endforeach; ?>
                    </ul>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <?php endif; ?>

          <!-- Indicadores -->
          <?php if (!empty($indicadoresReporte)): ?>
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="mb-0"><i class="bi bi-graph-up me-2" style="color:var(--zf-teal,#1993b8);"></i>Indicadores de seguimiento</h6>
            <a href="index.php?modulo=indicadores&id=<?= $empresa['id'] ?>" class="small text-decoration-none text-zf-teal no-print">
              Ver histórico completo <i class="bi bi-arrow-right ms-1"></i>
            </a>
          </div>
          <div class="row g-3 mb-4">
            <?php foreach ($indicadoresReporte as $ind):
              $labels  = $ind['periodos_json'] ? explode(',', $ind['periodos_json']) : [];
              $vals    = $ind['valores_json']  ? array_map('floatval', explode(',', $ind['valores_json'])) : [];
              $meta    = $ind['meta'] !== null ? (float) $ind['meta'] : null;
              $ultimo  = $ind['ultimo_valor']  !== null ? (float) $ind['ultimo_valor'] : null;
              $pct     = ($meta && $meta > 0 && $ultimo !== null) ? min(round($ultimo / $meta * 100, 1), 100) : null;
              $col     = ($pct === null) ? 'secondary' : ($pct >= 100 ? 'success' : ($pct >= 50 ? 'primary' : 'warning'));
            ?>
            <div class="col-md-6 col-lg-4">
              <div class="card border h-100 indicador-card">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="fw-semibold small"><?= htmlspecialchars($ind['nombre']) ?></div>
                    <?php if ($pct !== null): ?>
                      <span class="badge text-bg-<?= $col ?>"><?= $pct ?>%</span>
                    <?php endif; ?>
                  </div>
                  <div class="d-flex gap-3 mb-2">
                    <div>
                      <div class="indicador-stat-label">Último valor</div>
                      <div class="fw-bold text-<?= $col ?>">
                        <?= $ultimo !== null ? number_format($ultimo, 2, ',', '.') : '—' ?>
                        <?php if ($ind['unidad']): ?><small class="text-muted fw-normal"><?= htmlspecialchars($ind['unidad']) ?></small><?php endif; ?>
                      </div>
                    </div>
                    <?php if ($meta !== null): ?>
                    <div>
                      <div class="indicador-stat-label">Meta</div>
                      <div class="fw-bold">
                        <?= number_format($meta, 2, ',', '.') ?>
                        <?php if ($ind['unidad']): ?><small class="text-muted fw-normal"><?= htmlspecialchars($ind['unidad']) ?></small><?php endif; ?>
                      </div>
                    </div>
                    <?php endif; ?>
                    <div>
                      <div class="indicador-stat-label">Período</div>
                      <div class="fw-semibold small text-muted"><?= htmlspecialchars($ind['ultimo_periodo'] ?? '—') ?></div>
                    </div>
                  </div>
                  <?php if (!empty($labels)): ?>
                    <canvas class="indicador-chart"
                            data-tipo="<?= htmlspecialchars($ind['tipo_grafico'] ?? 'linea') ?>"
                            data-comparativo="<?= (int)($ind['comparativo_anual'] ?? 0) ?>"
                            data-periodicidad="<?= htmlspecialchars($ind['periodicidad'] ?? '') ?>"
                            data-meta="<?= $meta ?? '' ?>"
                            data-unidad="<?= htmlspecialchars($ind['unidad'] ?? '') ?>"
                            data-labels="<?= htmlspecialchars(json_encode($labels, JSON_UNESCAPED_UNICODE)) ?>"
                            data-values="<?= htmlspecialchars(json_encode($vals)) ?>"
                            height="90"></canvas>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>

        <?php endif; ?>

      </div>
    </div>
  </main>

  <?php if ($empresa): ?>
  <script>
    window.reportesChartData = {
      distribucion: {
        labels: ['Cumplidos', 'En progreso', 'Pendientes', 'No aplica'],
        data:   [<?= $cumplidos ?>, <?= $enProgreso ?>, <?= $pendientes ?>, <?= $noAplica ?>],
        colors: ['#28a745', '#0d6efd', '#6c757d', '#e9ecef'],
      },
      fases: {
        labels: <?= json_encode($chartFasesLabels, JSON_UNESCAPED_UNICODE) ?>,
        data:   <?= json_encode($chartFasesData) ?>,
      },
    };
  </script>
  <?php endif; ?>

  <?php require_once __DIR__ . '/../../parciales/pie.php'; ?>
</div>

<style>
@media print {
  .app-sidebar, .app-navbar, .app-content-header, .no-print { display: none !important; }
  .app-main { margin: 0 !important; padding: 0 !important; }
  .app-content { padding: 0 !important; }
  .print-only { display: block !important; }
  .card { box-shadow: none !important; border: 1px solid #dee2e6 !important; }
  .progress { border: 1px solid #dee2e6; }
  body { font-size: 12px; }
}
</style>
