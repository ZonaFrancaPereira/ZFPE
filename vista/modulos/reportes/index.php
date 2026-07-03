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
          <div class="card shadow-sm mb-4 border-0 bg-body-secondary print-header">
            <div class="card-body py-3">
              <div class="row align-items-center g-3">
                <div class="col-md-7">
                  <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                         style="width:50px;height:50px;font-size:1.2rem;">
                      <?= mb_strtoupper(mb_substr($empresa['razon_social'], 0, 1)) ?>
                    </div>
                    <div>
                      <h5 class="mb-0"><?= htmlspecialchars($empresa['razon_social']) ?></h5>
                      <small class="text-muted">NIT: <?= htmlspecialchars($empresa['nit']) ?></small>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="text-center text-md-start">
                    <div class="fs-3 fw-bold text-<?= $avanceGlobal >= 100 ? 'success' : ($avanceGlobal > 0 ? 'primary' : 'secondary') ?>">
                      <?= $avanceGlobal ?>%
                    </div>
                    <div class="progress mb-1" style="height:8px;">
                      <div class="progress-bar bg-<?= $avanceGlobal >= 100 ? 'success' : 'primary' ?>"
                           style="width:<?= $avanceGlobal ?>%"></div>
                    </div>
                    <small class="text-muted">Avance general</small>
                  </div>
                </div>
                <div class="col-md-2 text-md-end no-print">
                  <div class="d-flex gap-1 justify-content-md-end flex-wrap">
                    <a href="index.php?modulo=informes&accion=excel&id=<?= $empresa['id'] ?>"
                       class="btn btn-outline-success btn-sm" title="Descargar informe en Excel">
                      <i class="bi bi-file-earmark-excel"></i>
                    </a>
                    <a href="index.php?modulo=informes&accion=pdf&id=<?= $empresa['id'] ?>"
                       class="btn btn-outline-danger btn-sm" title="Descargar informe en PDF">
                      <i class="bi bi-file-earmark-pdf"></i>
                    </a>
                    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
                      <i class="bi bi-printer me-1"></i> Imprimir
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

          <div class="row g-4">
            <!-- Columna izquierda -->
            <div class="col-lg-7">

              <!-- Avance por etapa -->
              <div class="card shadow-sm mb-4">
                <div class="card-header">
                  <h6 class="card-title mb-0"><i class="bi bi-diagram-3 me-2 text-success"></i>Avance por etapa</h6>
                </div>
                <div class="card-body p-0">
                  <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                      <thead class="table-light">
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
                        <tr style="background:#fef3f3;">
                          <td colspan="4" class="py-1 px-3">
                            <span class="badge text-bg-danger me-1">
                              <i class="bi bi-collection me-1"></i><?= htmlspecialchars($rFase['nombre']) ?>
                            </span>
                          </td>
                        </tr>
                        <?php endif; ?>

                        <?php foreach ($rFase['etapas'] as $etapa): ?>
                        <?php $ec = $colorEstado[$etapa['estado_progreso']] ?? 'secondary'; ?>
                        <tr>
                          <td class="fw-semibold small <?= $rFase['nombre'] ? 'ps-4' : '' ?>">
                            <a href="<?= htmlspecialchars($urlEtapa((int) $etapa['id'])) ?>"><?= htmlspecialchars($etapa['nombre']) ?></a>
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

              <!-- Alertas de vencimiento -->
              <?php if (!empty($vencidos) || !empty($porVencer)): ?>
              <div class="card shadow-sm mb-4">
                <div class="card-header">
                  <h6 class="card-title mb-0"><i class="bi bi-bell me-2 text-danger"></i>Alertas de vencimiento</h6>
                </div>
                <div class="card-body p-0">
                  <?php if (!empty($vencidos)): ?>
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
                  <?php endif; ?>

                  <?php if (!empty($porVencer)): ?>
                    <div class="px-3 pt-2 pb-1 <?= !empty($vencidos) ? 'border-top' : '' ?>">
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
                  <?php endif; ?>
                </div>
              </div>
              <?php endif; ?>

            </div>

            <!-- Columna derecha -->
            <div class="col-lg-5">

              <!-- Alertas ejecutivas -->
              <?php
              $prioridadInfo = [
                  'alta'  => ['danger',  'bi-exclamation-octagon-fill', 'Requiere decisión'],
                  'media' => ['warning', 'bi-exclamation-triangle-fill', 'Atención'],
                  'baja'  => ['success', 'bi-check-circle-fill', 'Sin novedad'],
              ];
              // Una reunión es informativa: siempre azul, sin importar la prioridad guardada.
              $infoAlerta = function (array $alerta) use ($prioridadInfo): array {
                  if ($alerta['tipo'] === 'reunion') {
                      return ['info', 'bi-camera-video-fill', 'Reunión'];
                  }
                  return $prioridadInfo[$alerta['prioridad']] ?? ['secondary', 'bi-info-circle-fill', ucfirst($alerta['prioridad'])];
              };
              ?>
              <div class="card shadow-sm mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                  <h6 class="card-title mb-0"><i class="bi bi-megaphone-fill me-2 text-danger"></i>Alertas ejecutivas</h6>
                  <?php if ($esOperaciones || $esAdmin): ?>
                  <button type="button" class="btn btn-sm btn-outline-danger no-print" data-bs-toggle="modal" data-bs-target="#modalNuevaAlerta">
                    <i class="bi bi-plus-lg"></i>
                  </button>
                  <?php endif; ?>
                </div>
                <div class="card-body p-0">
                  <?php if (empty($alertasEjecutivas)): ?>
                    <div class="text-center text-muted py-4 small">
                      <i class="bi bi-check-circle fs-3 d-block mb-1 opacity-25"></i>
                      Sin alertas activas.
                    </div>
                  <?php else: ?>
                    <?php
                    $hexAlerta = ['danger' => '#dc3545', 'warning' => '#ffc107', 'success' => '#198754', 'info' => '#0dcaf0', 'secondary' => '#6c757d'];
                    ?>
                    <ul class="list-group list-group-flush">
                      <?php foreach ($alertasEjecutivas as $alerta):
                        [$aColor, $aIcono, $aLabel] = $infoAlerta($alerta);
                      ?>
                      <li class="list-group-item bg-<?= $aColor ?>-subtle bg-opacity-50 py-2"
                          style="border-left:4px solid <?= $hexAlerta[$aColor] ?? '#6c757d' ?>;">
                        <div class="d-flex align-items-start justify-content-between gap-2">
                          <div class="min-w-0">
                            <div class="fw-semibold small text-<?= $aColor ?>">
                              <i class="bi <?= $aIcono ?> me-1"></i><?= $aLabel ?>
                            </div>
                            <div class="small"><?= htmlspecialchars($alerta['mensaje']) ?></div>
                            <?php if (!empty($alerta['enlace_reunion'])): ?>
                              <a href="<?= htmlspecialchars($alerta['enlace_reunion']) ?>" target="_blank" rel="noopener"
                                 class="btn btn-sm btn-info text-white mt-1">
                                <i class="bi bi-camera-video me-1"></i>Unirse a la reunión
                              </a>
                            <?php endif; ?>
                            <?php if (!empty($alerta['destinatario_nombres'])): ?>
                              <div class="text-muted mt-1" style="font-size:.7rem;">
                                <i class="bi bi-person-check me-1"></i>Para: <?= htmlspecialchars($alerta['destinatario_nombres']) ?>
                              </div>
                            <?php endif; ?>
                          </div>
                          <?php if ($esOperaciones || $esAdmin): ?>
                          <button type="button" class="btn btn-sm btn-outline-secondary no-print flex-shrink-0"
                                  title="Marcar resuelta" data-bs-toggle="modal" data-bs-target="#modalResolverAlerta"
                                  data-id="<?= $alerta['id'] ?>" data-mensaje="<?= htmlspecialchars(mb_substr($alerta['mensaje'], 0, 80)) ?>">
                            <i class="bi bi-check-lg"></i>
                          </button>
                          <?php endif; ?>
                        </div>
                      </li>
                      <?php endforeach; ?>
                    </ul>
                  <?php endif; ?>
                </div>
              </div>

              <!-- Distribución de requisitos -->
              <div class="card shadow-sm mb-4">
                <div class="card-header">
                  <h6 class="card-title mb-0"><i class="bi bi-pie-chart me-2 text-primary"></i>Distribución de requisitos</h6>
                </div>
                <div class="card-body">
                  <?php if ($totalReq === 0): ?>
                    <p class="text-muted text-center mb-0 small">Sin requisitos asignados.</p>
                  <?php else: ?>
                    <div class="mb-3" style="max-width:220px;margin-inline:auto;">
                      <canvas id="chartDistribucion" width="220" height="220"></canvas>
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
                      <small class="text-muted">Total requisitos</small>
                      <small class="fw-bold"><?= $totalReq ?></small>
                    </div>
                  <?php endif; ?>
                </div>
              </div>

              <!-- Avance por fase -->
              <?php if (count($chartFasesLabels) > 1 || (count($chartFasesLabels) === 1 && $chartFasesLabels[0] !== 'Sin fase')): ?>
              <div class="card shadow-sm mb-4">
                <div class="card-header">
                  <h6 class="card-title mb-0"><i class="bi bi-bar-chart-fill me-2 text-danger"></i>Avance por fase</h6>
                </div>
                <div class="card-body">
                  <canvas id="chartFases" height="<?= max(120, count($chartFasesLabels) * 50) ?>"></canvas>
                </div>
              </div>
              <?php endif; ?>

              <!-- Documentos -->
              <div class="card shadow-sm mb-4">
                <div class="card-header">
                  <h6 class="card-title mb-0"><i class="bi bi-folder2 me-2 text-warning"></i>Documentación</h6>
                </div>
                <div class="card-body text-center py-4">
                  <div class="fs-2 fw-bold text-primary"><?= $totalDocs ?></div>
                  <div class="text-muted small"><?= $totalDocs === 1 ? 'documento subido' : 'documentos subidos' ?></div>
                  <?php if (($_SESSION['usuario_rol'] ?? '') === 'usuario'): ?>
                  <a href="index.php?modulo=documentos" class="btn btn-outline-primary btn-sm mt-2 no-print">
                    <i class="bi bi-folder2-open me-1"></i>Ver documentos
                  </a>
                  <?php else: ?>
                  <a href="index.php?modulo=documentos&accion=ver&id=<?= $empresa['id'] ?>" class="btn btn-outline-primary btn-sm mt-2 no-print">
                    <i class="bi bi-folder2-open me-1"></i>Ver documentos
                  </a>
                  <?php endif; ?>
                </div>
              </div>

              <!-- Fechas clave -->
              <?php
              $etapasConFecha = array_filter($etapas, fn($e) => $e['fecha_inicio'] || $e['fecha_completado']);
              ?>
              <?php if (!empty($etapasConFecha)): ?>
              <div class="card shadow-sm">
                <div class="card-header">
                  <h6 class="card-title mb-0"><i class="bi bi-calendar-check me-2 text-info"></i>Fechas clave</h6>
                </div>
                <ul class="list-group list-group-flush">
                  <?php
                  $lastFaseId = -1;
                  foreach ($etapasConFecha as $et):
                      $etFaseId = $et['fase_id'] ?? 0;
                  ?>
                  <?php if ($etFaseId !== $lastFaseId && !empty($et['fase_nombre'])): ?>
                    <?php $lastFaseId = $etFaseId; ?>
                    <li class="list-group-item px-3 py-1" style="background:#fef3f3;">
                      <span class="badge text-bg-danger" style="font-size:.7rem;">
                        <i class="bi bi-collection me-1"></i><?= htmlspecialchars($et['fase_nombre']) ?>
                      </span>
                    </li>
                  <?php endif; ?>
                  <li class="list-group-item px-3 py-2 <?= !empty($et['fase_nombre']) ? 'ps-4' : '' ?>">
                    <div class="small fw-semibold mb-1">
                      <a href="<?= htmlspecialchars($urlEtapa((int) $et['id'])) ?>"><?= htmlspecialchars($et['nombre']) ?></a>
                    </div>
                    <div class="d-flex flex-wrap gap-3 text-muted" style="font-size:.75rem;">
                      <?php if ($et['fecha_inicio']): ?>
                        <span><i class="bi bi-play-circle text-primary me-1"></i><?= date('d/m/Y', strtotime($et['fecha_inicio'])) ?></span>
                      <?php endif; ?>
                      <?php if ($et['fecha_completado']): ?>
                        <span><i class="bi bi-check-circle text-success me-1"></i><?= date('d/m/Y', strtotime($et['fecha_completado'])) ?></span>
                      <?php endif; ?>
                    </div>
                  </li>
                  <?php endforeach; ?>
                </ul>
              </div>
              <?php endif; ?>

            </div>
          </div>

          <!-- Indicadores -->
          <?php if (!empty($indicadoresReporte)): ?>
          <div class="mt-2">
            <div class="card shadow-sm">
              <div class="card-header">
                <h6 class="card-title mb-0 d-flex align-items-center justify-content-between">
                  <span><i class="bi bi-graph-up me-2" style="color:var(--zf-teal,#1993b8);"></i>Indicadores de seguimiento</span>
                  <a href="index.php?modulo=indicadores&id=<?= $empresa['id'] ?>" class="small no-print">Ver histórico completo →</a>
                </h6>
              </div>
              <div class="card-body">
                <div class="row g-3">
                  <?php foreach ($indicadoresReporte as $ind):
                    $labels  = $ind['periodos_json'] ? explode(',', $ind['periodos_json']) : [];
                    $vals    = $ind['valores_json']  ? array_map('floatval', explode(',', $ind['valores_json'])) : [];
                    $meta    = $ind['meta'] !== null ? (float) $ind['meta'] : null;
                    $ultimo  = $ind['ultimo_valor']  !== null ? (float) $ind['ultimo_valor'] : null;
                    $pct     = ($meta && $meta > 0 && $ultimo !== null) ? min(round($ultimo / $meta * 100, 1), 100) : null;
                    $col     = ($pct === null) ? 'secondary' : ($pct >= 100 ? 'success' : ($pct >= 50 ? 'primary' : 'warning'));
                  ?>
                  <div class="col-md-6">
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
                                  height="100"></canvas>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
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

<?php if ($empresa && ($esOperaciones || $esAdmin)): ?>
<!-- Modal nueva alerta ejecutiva -->
<div class="modal fade" id="modalNuevaAlerta" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form method="POST" action="index.php?modulo=reportes&accion=crear-alerta&id=<?= $empresa['id'] ?>">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-megaphone-fill me-2 text-danger"></i>Nueva alerta ejecutiva</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">

          <div class="mb-3">
            <label class="form-label fw-semibold">1. ¿Qué tipo de alerta es?</label>
            <select name="tipo" id="tipoAlertaSelect" class="form-select">
              <option value="pendiente">Pendiente</option>
              <option value="decision">Decisión</option>
              <option value="vencimiento">Vencimiento</option>
              <option value="bloqueo">Bloqueo</option>
              <option value="documento">Documento</option>
              <option value="reunion">Reunión (solo informativa, no es un riesgo)</option>
            </select>
          </div>

          <div class="mb-3" id="campoPrioridad">
            <label class="form-label fw-semibold d-block">2. ¿Qué tan urgente es?</label>
            <div class="row g-2">
              <div class="col-4">
                <label class="rol-card card h-100 text-center p-2 border-2" for="nivelBaja">
                  <input type="radio" name="prioridad" id="nivelBaja" value="baja" class="d-none">
                  <i class="bi bi-check-circle-fill fs-3 mb-1 text-success d-block"></i>
                  <div class="fw-semibold small">Sin novedad</div>
                  <small class="text-muted" style="font-size:.68rem;">Solo informativo</small>
                </label>
              </div>
              <div class="col-4">
                <label class="rol-card card h-100 text-center p-2 border-2 selected" for="nivelMedia">
                  <input type="radio" name="prioridad" id="nivelMedia" value="media" class="d-none" checked>
                  <i class="bi bi-exclamation-triangle-fill fs-3 mb-1 text-warning d-block"></i>
                  <div class="fw-semibold small">Atención</div>
                  <small class="text-muted" style="font-size:.68rem;">Avisa a Gerencia</small>
                </label>
              </div>
              <div class="col-4">
                <label class="rol-card card h-100 text-center p-2 border-2" for="nivelAlta">
                  <input type="radio" name="prioridad" id="nivelAlta" value="alta" class="d-none">
                  <i class="bi bi-exclamation-octagon-fill fs-3 mb-1 text-danger d-block"></i>
                  <div class="fw-semibold small">Requiere decisión</div>
                  <small class="text-muted" style="font-size:.68rem;">Decisión conjunta</small>
                </label>
              </div>
            </div>
          </div>

          <div class="mb-3 d-none" id="campoEnlaceReunion">
            <label class="form-label fw-semibold">Enlace de conexión</label>
            <input type="url" name="enlace_reunion" class="form-control" placeholder="https://meet.google.com/...">
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">3. Mensaje</label>
            <textarea name="mensaje" class="form-control" rows="3" required
                      placeholder="Ej. Definir ampliación de área declarada. Aprobar inversión proyectada."></textarea>
          </div>

          <div>
            <label class="form-label fw-semibold d-block">4. ¿Quién debe verla?</label>
            <div class="form-check">
              <input type="radio" name="modoDestinatarios" id="modoAutomatico" class="form-check-input" checked>
              <label class="form-check-label" for="modoAutomatico">
                Automático — la ve el/la gerente de la empresa y todo Operaciones/Admin
              </label>
            </div>
            <div class="form-check mb-2">
              <input type="radio" name="modoDestinatarios" id="modoEspecifico" class="form-check-input">
              <label class="form-check-label" for="modoEspecifico">
                Elegir personas específicas (ej. una decisión conjunta con alguien puntual)
              </label>
            </div>

            <div id="listaDestinatarios" class="d-none border rounded p-2" style="max-height:180px;overflow-y:auto;">
              <?php if (!empty($usuariosEmpresa)): ?>
                <div class="text-muted text-uppercase fw-semibold mb-1" style="font-size:.65rem;letter-spacing:.04em;">Empresa</div>
                <?php foreach ($usuariosEmpresa as $u): ?>
                  <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="destinatarios[]" value="<?= $u['id'] ?>" id="dest-<?= $u['id'] ?>">
                    <label class="form-check-label small" for="dest-<?= $u['id'] ?>">
                      <?= htmlspecialchars($u['nombre']) ?><?= !empty($u['es_gerente']) ? ' <span class="badge text-bg-light border">Gerente</span>' : '' ?>
                    </label>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
              <?php if (!empty($equipoInterno)): ?>
                <div class="text-muted text-uppercase fw-semibold mb-1 mt-2" style="font-size:.65rem;letter-spacing:.04em;">Operaciones / Admin</div>
                <?php foreach ($equipoInterno as $u): ?>
                  <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="destinatarios[]" value="<?= $u['id'] ?>" id="dest-<?= $u['id'] ?>">
                    <label class="form-check-label small" for="dest-<?= $u['id'] ?>"><?= htmlspecialchars($u['nombre']) ?></label>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-danger"><i class="bi bi-plus-lg me-1"></i>Registrar alerta</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal resolver alerta -->
<div class="modal fade" id="modalResolverAlerta" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" id="formResolverAlerta">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-check-circle-fill me-2 text-success"></i>Marcar alerta como resuelta</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p class="small text-muted mb-2" id="mensajeAlertaResolver"></p>
          <label class="form-label fw-semibold">Comentario (opcional)</label>
          <textarea name="comentario" class="form-control" rows="3"
                    placeholder="Ej. Se aprobó la ampliación en reunión del 3 de julio."></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Marcar resuelta</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.getElementById('tipoAlertaSelect').addEventListener('change', function () {
  const esReunion = this.value === 'reunion';
  document.getElementById('campoEnlaceReunion').classList.toggle('d-none', !esReunion);
  document.getElementById('campoPrioridad').classList.toggle('d-none', esReunion);
});

document.querySelectorAll('#modalNuevaAlerta input[name="prioridad"]').forEach(radio => {
  radio.addEventListener('change', () => {
    document.querySelectorAll('#modalNuevaAlerta .rol-card').forEach(c => c.classList.remove('selected'));
    radio.closest('.rol-card').classList.add('selected');
  });
});

document.querySelectorAll('input[name="modoDestinatarios"]').forEach(radio => {
  radio.addEventListener('change', () => {
    const especifico = document.getElementById('modoEspecifico').checked;
    document.getElementById('listaDestinatarios').classList.toggle('d-none', !especifico);
    // Si vuelve a "automático", se descartan las selecciones para que no se envíen ocultas.
    if (!especifico) {
      document.querySelectorAll('#listaDestinatarios input[type="checkbox"]').forEach(cb => cb.checked = false);
    }
  });
});

document.getElementById('modalResolverAlerta').addEventListener('show.bs.modal', function (e) {
  const btn = e.relatedTarget;
  document.getElementById('formResolverAlerta').action =
    'index.php?modulo=reportes&accion=resolver-alerta&id=' + btn.dataset.id;
  document.getElementById('mensajeAlertaResolver').textContent = btn.dataset.mensaje;
});
</script>
<?php endif; ?>

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
