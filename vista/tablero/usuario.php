<?php
/** @var array|null $empresa */
/** @var array $alertas */
/** @var array $requisitos_pendientes */
$pageTitle  = 'Inicio — ZFPE';
$activePage = 'tablero';
$pageStyles  = ['vista/assets/css/componentes.css', 'vista/assets/css/tablero.css', 'vista/assets/css/indicadores.css', 'vista/assets/css/cronograma.css'];
$pageScripts = ['vista/assets/vendor/chartjs/chart.umd.min.js', 'vista/assets/js/indicadores.js', 'vista/assets/js/reportes-charts.js'];
?>
<?php require_once __DIR__ . '/../parciales/cabecera.php'; ?>

<?php
$nombreUsuario = $_SESSION['usuario_nombre'] ?? 'Usuario';
$hora          = (int) date('H');
$saludo        = $hora < 12 ? 'Buenos días' : ($hora < 18 ? 'Buenas tardes' : 'Buenas noches');

$paleta      = ['primary', 'success', 'danger', 'warning', 'info'];
$hashStr     = fn(string $s) => abs(array_reduce(str_split($s), fn($a, $c) => (31 * $a + ord($c)) & 0x7FFFFFFF, 0));
$avatarColor = $paleta[$hashStr($nombreUsuario) % 5];
$inicial     = mb_strtoupper(mb_substr($nombreUsuario, 0, 1));

$estadoColor = [
    'pendiente'   => 'secondary',
    'en_progreso' => 'primary',
    'completa'    => 'success',
];
$estadoLabel = [
    'pendiente'   => 'Pendiente',
    'en_progreso' => 'En progreso',
    'completa'    => 'Completa',
];
$prioridadInfoDash = [
    'alta'  => ['danger',  'bi-exclamation-octagon-fill',  'Requiere decisión'],
    'media' => ['warning', 'bi-exclamation-triangle-fill', 'Atención'],
    'baja'  => ['success', 'bi-check-circle-fill',         'Sin novedad'],
];
// Una reunión es informativa: siempre azul, sin importar la prioridad guardada.
$infoAlertaDash = function (array $alerta) use ($prioridadInfoDash): array {
    if ($alerta['tipo'] === 'reunion') {
        return ['info', 'bi-camera-video-fill', 'Reunión'];
    }
    return $prioridadInfoDash[$alerta['prioridad']] ?? ['secondary', 'bi-info-circle-fill', ucfirst($alerta['prioridad'])];
};
$hexAlertaDash = ['danger' => '#dc3545', 'warning' => '#ffc107', 'success' => '#198754', 'info' => '#0dcaf0', 'secondary' => '#6c757d'];
?>

<div class="app-wrapper">

  <?php require_once __DIR__ . '/../parciales/navegacion.php'; ?>
  <?php require_once __DIR__ . '/../parciales/menu_lateral.php'; ?>

  <main class="app-main">
    <div class="app-content-header encabezado-zf">
      <div class="container-fluid">
        <div class="row align-items-center">
          <div class="col-sm-6">
            <h3 class="mb-0 titulo-zf">Centro de Control Gerencial</h3>
            <?php if ($empresa): ?>
              <div class="d-inline-flex align-items-center gap-2 mt-2 px-3 py-1 rounded-pill"
                   style="background:linear-gradient(90deg, var(--zf-navy,#22404b), var(--zf-teal,#1993b8));">
                <i class="bi bi-building text-white"></i>
                <span class="text-white fw-semibold" style="font-size:1.05rem;letter-spacing:.02em;">
                  <?= htmlspecialchars($empresa['razon_social']) ?>
                </span>
              </div>
            <?php endif; ?>
          </div>
          <div class="col-sm-6 d-flex flex-column align-items-sm-end gap-2">
            <ol class="breadcrumb breadcrumb-zf mb-0">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item active">Tablero</li>
            </ol>
            <?php if ($empresa): ?>
            <div class="d-flex gap-2">
              <a href="index.php?modulo=informes&accion=excel" class="btn btn-zf-gradient btn-sm rounded-pill px-3">
                <i class="bi bi-file-earmark-excel me-1"></i>Descargar Excel
              </a>
              <a href="index.php?modulo=informes&accion=pdf" class="btn btn-zf-gradient btn-sm rounded-pill px-3">
                <i class="bi bi-file-earmark-pdf me-1"></i>Descargar PDF
              </a>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="app-content">
      <div class="container-fluid">

        <!-- Bienvenida -->
        <div class="card shadow-sm mb-4 border-0 bg-body-secondary">
          <div class="card-body d-flex align-items-center gap-3 py-3">
            <span class="rounded-circle bg-<?= $avatarColor ?> text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                  style="width:52px;height:52px;font-size:1.25rem;">
              <?= $inicial ?>
            </span>
            <div class="flex-grow-1">
              <h5 class="mb-0"><?= $saludo ?>, <?= htmlspecialchars($nombreUsuario) ?></h5>
              <small class="text-muted">
                <?php if ($empresa): ?>
                  <i class="bi bi-building me-1"></i><?= htmlspecialchars($empresa['razon_social']) ?> ·
                <?php endif; ?>
                <?= date('l, d \d\e F \d\e Y') ?>
              </small>
            </div>
          </div>
        </div>

        <?php if (!$empresa): ?>
          <!-- Sin empresa asignada -->
          <div class="card shadow-sm border-warning">
            <div class="card-body text-center py-5">
              <i class="bi bi-building-x fs-1 text-warning opacity-50 d-block mb-3"></i>
              <h5 class="text-muted">No tienes una empresa asignada</h5>
              <p class="text-muted small">
                Comunícate con el equipo de operaciones para que te vinculen a tu empresa.
              </p>
            </div>
          </div>

        <?php else: ?>

          <!-- KPIs -->
          <?php
          $totalEtapas      = count($empresa['etapas']);
          $etapasCompletas  = count(array_filter($empresa['etapas'], fn($e) => $e['estado_progreso'] === 'completa'));
          $totalRequisitos  = count($empresa['requisitos']);
          $reqCumplidos     = count(array_filter($empresa['requisitos'], fn($r) => $r['estado_req'] === 'cumplido'));
          $reqPendientes    = count(array_filter($empresa['requisitos'], fn($r) => in_array($r['estado_req'], ['pendiente', 'en_progreso'])));
          $enProgreso  = array_filter($empresa['etapas'], fn($e) => $e['estado_progreso'] === 'en_progreso');
          $pendientes  = array_filter($empresa['etapas'], fn($e) => $e['estado_progreso'] === 'pendiente');
          $etapaActual = !empty($enProgreso) ? reset($enProgreso) : (!empty($pendientes) ? reset($pendientes) : null);
          ?>

          <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
              <div class="card shadow-sm h-100 card-acento-teal">
                <div class="card-body d-flex align-items-center gap-3">
                  <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center flex-shrink-0"
                       style="width:54px;height:54px;font-size:1.4rem;">
                    <i class="bi bi-graph-up"></i>
                  </div>
                  <div>
                    <div class="fs-2 fw-bold lh-1"><?= $empresa['avance_general'] ?>%</div>
                    <div class="text-muted small">Avance general</div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-sm-6 col-xl-3">
              <div class="card shadow-sm h-100 card-acento-teal">
                <div class="card-body d-flex align-items-center gap-3">
                  <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0"
                       style="width:54px;height:54px;font-size:1.4rem;">
                    <i class="bi bi-diagram-3"></i>
                  </div>
                  <div>
                    <div class="fs-2 fw-bold lh-1"><?= $etapasCompletas ?>/<?= $totalEtapas ?></div>
                    <div class="text-muted small">Etapas completadas</div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-sm-6 col-xl-3">
              <div class="card shadow-sm h-100 card-acento-teal">
                <div class="card-body d-flex align-items-center gap-3">
                  <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center flex-shrink-0"
                       style="width:54px;height:54px;font-size:1.4rem;">
                    <i class="bi bi-list-check"></i>
                  </div>
                  <div>
                    <div class="fs-2 fw-bold lh-1"><?= $reqPendientes ?></div>
                    <div class="text-muted small">Requisitos pendientes</div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-sm-6 col-xl-3">
              <div class="card shadow-sm h-100 card-acento-teal">
                <div class="card-body d-flex align-items-center gap-3">
                  <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center flex-shrink-0"
                       style="width:54px;height:54px;font-size:1.4rem;">
                    <i class="bi bi-bell"></i>
                  </div>
                  <div>
                    <div class="fs-2 fw-bold lh-1"><?= count($alertas) ?></div>
                    <div class="text-muted small">Alertas activas</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Avance del proyecto por fase (cronograma resumido) -->
          <?php if (!empty($etapasCronograma)):
            $tabFaseGrupos = [];
            foreach ($etapasCronograma as $et) {
                $fid = $et['fase_id'] ?? 0;
                if (!isset($tabFaseGrupos[$fid])) {
                    $tabFaseGrupos[$fid] = [
                        'nombre' => $et['fase_nombre'] ?? 'Etapas',
                        'orden'  => $et['fase_orden']  ?? 999,
                        'etapas' => [],
                    ];
                }
                $tabFaseGrupos[$fid]['etapas'][] = $et;
            }
          ?>
          <?php foreach ($tabFaseGrupos as $tFase):
            $tFaseAvances   = array_column($tFase['etapas'], 'avance');
            $tFaseAvancePct = count($tFaseAvances) > 0 ? round(array_sum($tFaseAvances) / count($tFaseAvances), 1) : 0;
          ?>
          <div class="card shadow-sm mb-3 fase-card">
            <div class="card-header py-2 d-flex align-items-center justify-content-between"
                 style="background:linear-gradient(90deg, var(--zf-navy,#22404b), var(--zf-teal,#1993b8));border-bottom:none;">
              <span class="badge py-1 px-2" style="background:rgba(255,255,255,.15);color:#fff;">
                <i class="bi bi-briefcase me-1"></i><?= htmlspecialchars(mb_strtoupper($tFase['nombre'])) ?>
              </span>
              <div class="d-flex align-items-center gap-2">
                <div class="progress" style="width:120px;height:7px;background:rgba(255,255,255,.25);">
                  <div class="progress-bar bg-warning" style="width:<?= $tFaseAvancePct ?>%"></div>
                </div>
                <small class="fw-bold text-white" style="min-width:38px;"><?= $tFaseAvancePct ?>%</small>
              </div>
            </div>
            <div class="card-body py-3 px-4 overflow-auto">
              <div class="d-flex align-items-start gap-0">
                <?php foreach ($tFase['etapas'] as $si => $etapa): ?>
                <?php $color = $estadoColor[$etapa['estado_progreso']] ?? 'secondary'; ?>
                <div class="d-flex align-items-start <?= $si > 0 ? 'flex-grow-1' : '' ?>">
                  <?php if ($si > 0): ?>
                    <div class="flex-grow-1 border-top border-2
                                <?= $etapa['estado_progreso'] === 'completa' ? 'border-success' : ($etapa['estado_progreso'] === 'en_progreso' ? 'border-primary' : 'border-secondary') ?>"
                         style="min-width:24px;margin-top:19px;"></div>
                  <?php endif; ?>
                  <a href="index.php?modulo=cronograma#etapa-<?= $etapa['id'] ?>" class="text-decoration-none stepper-node">
                    <div class="d-flex flex-column align-items-center text-center flex-shrink-0" style="min-width:150px;max-width:150px;">
                      <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold mb-1 text-white bg-<?= $color ?>"
                           style="width:38px;height:38px;font-size:.85rem;">
                        <?php if ($etapa['estado_progreso'] === 'completa'): ?>
                          <i class="bi bi-check-lg"></i>
                        <?php else: ?>
                          <?= $si + 1 ?>
                        <?php endif; ?>
                      </div>
                      <small class="text-muted fw-semibold" style="font-size:.7rem;line-height:1.2;">
                        <?= htmlspecialchars($etapa['nombre']) ?>
                      </small>
                      <small class="fw-semibold text-<?= $color ?> mb-2" style="font-size:.65rem;">
                        <?= (float)$etapa['avance'] ?>%
                      </small>
                      <?php if (!empty($etapa['requisitos'])): ?>
                        <ul class="list-unstyled text-start mb-0 w-100 p-2 rounded-3" style="font-size:.65rem;line-height:1.4;background:var(--bs-body-tertiary);">
                          <?php foreach ($etapa['requisitos'] as $req): ?>
                            <li class="d-flex align-items-start gap-1 mb-1">
                              <i class="bi bi-circle-fill text-zf-teal flex-shrink-0 mt-1" style="font-size:.4rem;"></i>
                              <span class="text-muted"><?= htmlspecialchars($req['nombre']) ?></span>
                            </li>
                          <?php endforeach; ?>
                        </ul>
                      <?php endif; ?>
                    </div>
                  </a>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
          <?php endif; ?>

          <!-- Entidades involucradas -->
          <?php if (!empty($entidadesResumen)): ?>
          <div class="d-flex align-items-center justify-content-between py-2 px-3 rounded-2 mb-3"
               style="background:linear-gradient(90deg, var(--zf-navy,#22404b), var(--zf-teal,#1993b8));">
            <span class="badge py-1 px-2" style="background:rgba(255,255,255,.15);color:#fff;">
              <i class="bi bi-bank me-1"></i>ENTIDADES
            </span>
            <a href="index.php?modulo=entidades" class="small text-decoration-none text-white fw-semibold">
              Ver detalle <i class="bi bi-arrow-right ms-1"></i>
            </a>
          </div>
          <div class="row g-3 mb-4">
            <?php foreach ($entidadesResumen as $entidad):
              $entTotal = (int) $entidad['total_requisitos'];
              $entCump  = (int) $entidad['cumplidos'];
              $entNoApl = (int) $entidad['no_aplica'];
              $entPct   = $entTotal > 0 ? round(($entCump + $entNoApl) / $entTotal * 100) : 0;
            ?>
            <div class="col-sm-6 col-xl-4">
              <div class="card shadow-sm h-100 card-acento-teal">
                <div class="card-body">
                  <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:34px;height:34px;background:rgba(25,147,184,.12);">
                      <i class="bi bi-bank text-zf-teal"></i>
                    </div>
                    <div class="min-w-0">
                      <div class="fw-semibold small text-truncate"><?= htmlspecialchars($entidad['nombre']) ?></div>
                      <?php if (!empty($entidad['sigla'])): ?>
                        <small class="text-muted"><?= htmlspecialchars($entidad['sigla']) ?></small>
                      <?php endif; ?>
                    </div>
                  </div>
                  <div class="d-flex justify-content-between mb-1">
                    <small class="text-muted">Avance</small>
                    <small class="fw-bold <?= $entPct >= 100 ? 'text-success' : 'text-zf-teal' ?>"><?= $entPct ?>%</small>
                  </div>
                  <div class="progress mb-2" style="height:6px;">
                    <div class="progress-bar <?= $entPct >= 100 ? 'bg-success' : 'bg-zf-teal' ?>"
                         style="width:<?= $entPct ?>%;<?= $entPct < 100 ? 'background-color:var(--zf-teal,#1993b8);' : '' ?>"></div>
                  </div>
                  <?php if (!empty($entidad['requisitos'])): ?>
                    <ul class="list-unstyled mb-0 mt-2 pt-2 border-top">
                      <?php foreach ($entidad['requisitos'] as $req): $reqOk = $req['estado_req'] === 'cumplido'; ?>
                        <li class="d-flex align-items-center justify-content-between gap-2 py-1">
                          <span class="small text-truncate"><?= htmlspecialchars($req['nombre']) ?></span>
                          <?php if ($reqOk): ?>
                            <i class="bi bi-check-lg text-success flex-shrink-0"></i>
                          <?php else: ?>
                            <i class="bi bi-x-lg text-danger flex-shrink-0"></i>
                          <?php endif; ?>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <?php endforeach; ?>

            <!-- Documentos recientes: aprovecha el espacio sobrante de la grilla de entidades -->
            <div class="col-sm-6 col-xl-4">
              <div class="card shadow-sm h-100 card-acento-teal">
                <div class="card-body">
                  <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2">
                      <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                           style="width:34px;height:34px;background:rgba(25,147,184,.12);">
                        <i class="bi bi-folder2-open text-zf-teal"></i>
                      </div>
                      <div class="fw-semibold small">Documentos</div>
                    </div>
                    <a href="index.php?modulo=documentos" class="text-decoration-none text-zf-teal" title="Ver todos">
                      <i class="bi bi-arrow-right"></i>
                    </a>
                  </div>
                  <?php if (empty($documentosRecientes)): ?>
                    <div class="text-muted text-center small py-3">
                      <i class="bi bi-folder-x fs-4 d-block mb-1 opacity-25"></i>
                      Sin documentos aún
                    </div>
                  <?php else: ?>
                    <ul class="list-unstyled mb-0 mt-2 pt-2 border-top">
                      <?php foreach ($documentosRecientes as $doc):
                        $fecha = $doc['created_at'] ? date('d/m/Y', strtotime($doc['created_at'])) : '';
                      ?>
                        <li class="py-1">
                          <div class="d-flex align-items-center justify-content-between gap-2">
                            <span class="small text-truncate" title="<?= htmlspecialchars($doc['nombre_original']) ?>">
                              <?= htmlspecialchars($doc['nombre_original']) ?>
                            </span>
                            <a href="index.php?modulo=documentos&accion=descargar&id=<?= $doc['id'] ?>"
                               class="text-zf-teal flex-shrink-0" title="Descargar">
                              <i class="bi bi-download"></i>
                            </a>
                          </div>
                          <?php if (!empty($doc['descripcion'])): ?>
                            <div class="text-muted fst-italic text-truncate" style="font-size:.7rem;" title="<?= htmlspecialchars($doc['descripcion']) ?>">
                              <?= htmlspecialchars($doc['descripcion']) ?>
                            </div>
                          <?php endif; ?>
                          <?php if ($fecha): ?>
                            <div class="text-muted" style="font-size:.68rem;"><?= $fecha ?></div>
                          <?php endif; ?>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  <?php endif; ?>
                </div>
              </div>
            </div>

          </div>
          <?php endif; ?>

          <?php
            // --- Datos tomados de Reportes: distribución de requisitos y avance por fase ---
            $distribucionReq = ['cumplido' => 0, 'en_progreso' => 0, 'pendiente' => 0, 'no_aplica' => 0];
            foreach ($empresa['requisitos'] as $r) {
                $distribucionReq[$r['estado_req']] = ($distribucionReq[$r['estado_req']] ?? 0) + 1;
            }
            $totalReqDash = array_sum($distribucionReq);

            $acortarNombreFaseDash = function (string $nombre): string {
                $partes = explode(' - ', $nombre);
                return ucwords(mb_strtolower(trim(end($partes))));
            };
            $faseAvgGrupos = [];
            foreach ($etapasCronograma as $et) {
                $fid = $et['fase_id'] ?? 0;
                $faseAvgGrupos[$fid]['nombre']   ??= $et['fase_nombre'] ?? 'Sin fase';
                $faseAvgGrupos[$fid]['orden']    ??= $et['fase_orden']  ?? 999;
                $faseAvgGrupos[$fid]['avances'][] = (float) $et['avance'];
            }
            usort($faseAvgGrupos, fn($a, $b) => $a['orden'] <=> $b['orden']);
            $chartFasesLabelsDash = [];
            $chartFasesDataDash   = [];
            foreach ($faseAvgGrupos as $fg) {
                $chartFasesLabelsDash[] = $acortarNombreFaseDash($fg['nombre']);
                $chartFasesDataDash[]   = count($fg['avances']) > 0 ? round(array_sum($fg['avances']) / count($fg['avances']), 1) : 0;
            }

            // Fechas clave: etapas con fecha de inicio o de finalización registrada
            $etapasConFechaDash = array_filter($etapasCronograma, fn($e) => $e['fecha_inicio'] || $e['fecha_completado']);
          ?>

          <!-- Indicadores -->
          <?php if (!empty($indicadoresResumen) || !empty($requisitosVencidos) || !empty($requisitosPorVencer) || $totalReqDash > 0 || !empty($etapasConFechaDash)): ?>
          <div class="d-flex align-items-center justify-content-between py-2 px-3 rounded-2 mb-3"
               style="background:linear-gradient(90deg, var(--zf-navy,#22404b), var(--zf-teal,#1993b8));">
            <span class="badge py-1 px-2" style="background:rgba(255,255,255,.15);color:#fff;">
              <i class="bi bi-graph-up me-1"></i>INDICADORES
            </span>
            <a href="index.php?modulo=indicadores" class="small text-decoration-none text-white fw-semibold">
              Ver todos <i class="bi bi-arrow-right ms-1"></i>
            </a>
          </div>
          <div class="row g-3 mb-4">
            <?php foreach ($indicadoresResumen as $ind):
              $labels = $ind['periodos_json'] ? explode(',', $ind['periodos_json']) : [];
              $vals   = $ind['valores_json']  ? array_map('floatval', explode(',', $ind['valores_json'])) : [];
              $meta   = $ind['meta'] !== null ? (float) $ind['meta'] : null;
              $ultimo = $ind['ultimo_valor'] !== null ? (float) $ind['ultimo_valor'] : null;
              $pct    = ($meta && $meta > 0 && $ultimo !== null) ? min(round($ultimo / $meta * 100, 1), 100) : null;
              $col    = ($pct === null) ? 'secondary' : ($pct >= 100 ? 'success' : ($pct >= 50 ? 'primary' : 'warning'));
            ?>
            <div class="col-md-6 col-lg-4">
              <div class="card shadow-sm h-100 indicador-card">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="fw-semibold small"><?= htmlspecialchars($ind['nombre']) ?></div>
                    <?php if ($pct !== null): ?>
                      <span class="badge text-bg-<?= $col ?>"><?= $pct ?>%</span>
                    <?php endif; ?>
                  </div>
                  <div class="d-flex gap-3 mb-2">
                    <div>
                      <div class="indicador-stat-label">Último</div>
                      <div class="fw-bold text-<?= $col ?>" style="font-size:1.1rem;">
                        <?= $ultimo !== null ? number_format($ultimo, 2, ',', '.') : '—' ?>
                        <?php if ($ind['unidad']): ?><small class="text-muted fw-normal"><?= htmlspecialchars($ind['unidad']) ?></small><?php endif; ?>
                      </div>
                    </div>
                    <?php if ($meta !== null): ?>
                    <div>
                      <div class="indicador-stat-label">Meta</div>
                      <div class="fw-bold" style="font-size:1.1rem;">
                        <?= number_format($meta, 2, ',', '.') ?>
                        <?php if ($ind['unidad']): ?><small class="text-muted fw-normal"><?= htmlspecialchars($ind['unidad']) ?></small><?php endif; ?>
                      </div>
                    </div>
                    <?php endif; ?>
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
                            height="80"></canvas>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <?php endforeach; ?>

            <div class="col-md-6 col-lg-4">
              <div class="card shadow-sm h-100 card-acento-teal">
                <div class="card-header">
                  <h6 class="card-title mb-0"><i class="bi bi-pie-chart me-2 text-primary"></i>Distribución de requisitos</h6>
                </div>
                <div class="card-body">
                  <?php if ($totalReqDash === 0): ?>
                    <p class="text-muted text-center mb-0 small">Sin requisitos asignados.</p>
                  <?php else: ?>
                    <div class="mb-3" style="max-width:200px;margin-inline:auto;">
                      <canvas id="chartDistribucion" width="200" height="200"></canvas>
                    </div>
                    <?php
                    $itemsDash = [
                        ['Cumplidos',   $distribucionReq['cumplido'],    'success',   'check-circle'],
                        ['En progreso', $distribucionReq['en_progreso'], 'primary',   'arrow-repeat'],
                        ['Pendientes',  $distribucionReq['pendiente'],   'secondary', 'hourglass'],
                        ['No aplica',   $distribucionReq['no_aplica'],   'light',     'dash-circle'],
                    ];
                    foreach ($itemsDash as [$label, $count, $color, $icon]):
                        if ($count === 0) continue;
                        $pct = round($count / $totalReqDash * 100);
                    ?>
                    <div class="d-flex align-items-center justify-content-between py-1 small">
                      <span class="d-flex align-items-center gap-1">
                        <i class="bi bi-<?= $icon ?> text-<?= $color ?>"></i><?= $label ?>
                      </span>
                      <span class="fw-bold"><?= $count ?> <span class="text-muted fw-normal">(<?= $pct ?>%)</span></span>
                    </div>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </div>
              </div>
            </div>

            <?php if (count($chartFasesLabelsDash) > 0): ?>
            <div class="col-md-6 col-lg-4">
              <div class="card shadow-sm h-100 card-acento-teal">
                <div class="card-header">
                  <h6 class="card-title mb-0"><i class="bi bi-bar-chart-fill me-2 text-zf-teal"></i>Avance por fase</h6>
                </div>
                <div class="card-body">
                  <canvas id="chartFases" height="180"></canvas>
                </div>
              </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($etapasConFechaDash)): ?>
            <div class="col-md-6 col-lg-4">
              <div class="card shadow-sm h-100 card-acento-teal">
                <div class="card-header">
                  <h6 class="card-title mb-0"><i class="bi bi-calendar-check me-2 text-zf-teal"></i>Fechas clave</h6>
                </div>
                <div class="card-body" style="max-height:260px;overflow-y:auto;">
                  <?php $totalFechas = count($etapasConFechaDash); $i = 0; ?>
                  <?php foreach ($etapasConFechaDash as $et): $i++; ?>
                  <div class="d-flex align-items-center justify-content-between gap-2 py-2 <?= $i < $totalFechas ? 'border-bottom' : '' ?>">
                    <div class="min-w-0">
                      <div class="fw-semibold text-truncate" style="font-size:.72rem;"><?= htmlspecialchars($et['nombre']) ?></div>
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

            <div class="col-md-6 col-lg-4">
              <div class="card shadow-sm h-100 card-acento-teal">
                <div class="card-header">
                  <h6 class="card-title mb-0"><i class="bi bi-diagram-3 me-2 text-success"></i>Avance por etapa</h6>
                </div>
                <div class="card-body">
                  <?php foreach ($empresa['etapas'] as $etapa): ?>
                  <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                      <span class="text-truncate" style="font-size:.72rem;font-weight:600;"><?= htmlspecialchars($etapa['nombre']) ?></span>
                      <small class="fw-semibold text-muted flex-shrink-0 ms-1"><?= number_format($etapa['avance'], 1) ?>%</small>
                    </div>
                    <div class="progress" style="height:6px;">
                      <div class="progress-bar <?= $etapa['avance'] >= 100 ? 'bg-success' : ($etapa['avance'] > 0 ? 'bg-primary' : 'bg-secondary') ?> bg-opacity-<?= $etapa['avance'] > 0 ? '100' : '25' ?>"
                           style="width:<?= max($etapa['avance'], $etapa['avance'] > 0 ? 3 : 0) ?>%"></div>
                    </div>
                  </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </div>

          <!-- Alertas de vencimiento -->
          <?php if (!empty($requisitosVencidos) || !empty($requisitosPorVencer)): ?>
          <div class="card shadow-sm mb-4 card-acento-teal">
            <div class="card-header">
              <h6 class="card-title mb-0"><i class="bi bi-bell me-2 text-danger"></i>Alertas de vencimiento</h6>
            </div>
            <div class="card-body p-0">
              <?php if (!empty($requisitosVencidos)): ?>
                <div class="px-3 pt-2 pb-1">
                  <p class="small fw-semibold text-danger mb-2">
                    <i class="bi bi-exclamation-circle me-1"></i>Vencidos (<?= count($requisitosVencidos) ?>)
                  </p>
                </div>
                <ul class="list-group list-group-flush">
                  <?php foreach ($requisitosVencidos as $v): ?>
                  <li class="list-group-item px-3 py-2">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                      <div>
                        <div class="small fw-semibold"><?= htmlspecialchars($v['requisito']) ?></div>
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

              <?php if (!empty($requisitosPorVencer)): ?>
                <div class="px-3 pt-2 pb-1 <?= !empty($requisitosVencidos) ? 'border-top' : '' ?>">
                  <p class="small fw-semibold text-warning mb-2">
                    <i class="bi bi-clock me-1"></i>Próximos a vencer — 30 días (<?= count($requisitosPorVencer) ?>)
                  </p>
                </div>
                <ul class="list-group list-group-flush">
                  <?php foreach ($requisitosPorVencer as $pv): ?>
                  <li class="list-group-item px-3 py-2">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                      <div>
                        <div class="small fw-semibold"><?= htmlspecialchars($pv['requisito']) ?></div>
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

          <?php endif; ?>

          <!-- Compromisos -->
          <?php if (!empty($misCompromisos)): ?>
          <div class="d-flex align-items-center justify-content-between py-2 px-3 rounded-2 mb-3"
               style="background:linear-gradient(90deg, var(--zf-navy,#22404b), var(--zf-teal,#1993b8));">
            <span class="badge py-1 px-2" style="background:rgba(255,255,255,.15);color:#fff;">
              <i class="bi bi-clipboard-check me-1"></i>COMPROMISOS
            </span>
            <a href="index.php?modulo=mis-compromisos" class="small text-decoration-none text-white fw-semibold">
              Ver todos <i class="bi bi-arrow-right ms-1"></i>
            </a>
          </div>
          <div class="row g-3 mb-4">
            <?php
              $estadoCompDash = [
                  'pendiente'   => ['secondary', 'bi-hourglass-split',   'Pendiente'],
                  'en_progreso' => ['primary',   'bi-arrow-repeat',      'En progreso'],
                  'cumplido'    => ['success',   'bi-check-circle-fill', 'Cumplido'],
              ];
            ?>
            <?php foreach ($misCompromisos as $comp):
              $compVencido = ($comp['estado'] !== 'cumplido' && !empty($comp['fecha_limite']) && $comp['fecha_limite'] < date('Y-m-d'));
              [$ccolor, $cicon, $clabel] = $compVencido
                  ? ['danger', 'bi-exclamation-circle-fill', 'Vencido']
                  : ($estadoCompDash[$comp['estado']] ?? ['secondary', 'bi-circle', $comp['estado']]);
            ?>
            <div class="col-sm-6 col-xl-4">
              <div class="card shadow-sm h-100 card-acento-teal">
                <div class="card-body">
                  <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                    <span class="small fw-semibold text-truncate" title="<?= htmlspecialchars($comp['comite_titulo']) ?>">
                      <i class="bi bi-people-fill me-1 text-zf-teal"></i><?= htmlspecialchars($comp['comite_titulo']) ?>
                    </span>
                    <span class="badge bg-<?= $ccolor ?> flex-shrink-0" style="font-size:.62rem;">
                      <i class="bi <?= $cicon ?> me-1"></i><?= $clabel ?>
                    </span>
                  </div>
                  <p class="small text-muted mb-2" style="display:-webkit-box;-webkit-line-clamp:2;line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                    <?= htmlspecialchars($comp['descripcion']) ?>
                  </p>
                  <?php if ($comp['fecha_limite']): ?>
                    <small class="<?= $compVencido ? 'text-danger fw-semibold' : 'text-muted' ?>">
                      <i class="bi bi-calendar-event me-1"></i><?= date('d/m/Y', strtotime($comp['fecha_limite'])) ?>
                    </small>
                  <?php endif; ?>

                  <?php $historial = $historialPorCompromiso[$comp['id']] ?? []; ?>
                  <?php if (!empty($historial)): ?>
                    <div class="fw-semibold text-muted text-uppercase mt-3 mb-2" style="font-size:.65rem;letter-spacing:.04em;">
                      <i class="bi bi-clock-history me-1 text-zf-teal"></i>Historial
                    </div>
                    <ul class="ch-timeline">
                      <?php foreach (array_slice($historial, 0, 2) as $h):
                        [$hcolor, $hicon] = $estadoCompDash[$h['estado']] ?? ['secondary', 'bi-circle', ''];
                      ?>
                        <li class="ch-timeline-item">
                          <span class="ch-timeline-dot bg-<?= $hcolor ?>"><i class="bi <?= $hicon ?>"></i></span>
                          <div class="ch-timeline-content">
                            <div class="small">
                              <span class="fw-semibold"><?= htmlspecialchars($h['usuario_nombre'] ?? 'Usuario') ?></span>
                              <span class="text-muted"> · <?= date('d/m/Y', strtotime($h['created_at'])) ?></span>
                            </div>
                            <?php if (!empty($h['observaciones'])): ?>
                              <div class="text-muted" style="font-size:.72rem;display:-webkit-box;-webkit-line-clamp:2;line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                <?= htmlspecialchars($h['observaciones']) ?>
                              </div>
                            <?php endif; ?>
                          </div>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                    <?php if (count($historial) > 2): ?>
                      <a href="index.php?modulo=mis-compromisos#compromiso-<?= $comp['id'] ?>" class="small text-decoration-none text-zf-teal">
                        Ver <?= count($historial) - 2 ?> actualizaciones más
                      </a>
                    <?php endif; ?>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>

          <!-- Alertas ejecutivas -->
          <?php if (!empty($alertas)): ?>
          <div class="row g-4 mb-4">
            <div class="col-lg-8 mx-auto">
              <div class="card shadow-sm">
                <div class="card-header">
                  <h5 class="card-title mb-0"><i class="bi bi-megaphone-fill me-2 text-danger"></i>Alertas ejecutivas</h5>
                </div>
                <div class="card-body p-0">
                  <ul class="list-group list-group-flush">
                    <?php foreach ($alertas as $alerta):
                      [$aColorDash, $aIconoDash, $aLabelDash] = $infoAlertaDash($alerta);
                    ?>
                    <li class="list-group-item bg-<?= $aColorDash ?>-subtle bg-opacity-50 py-2"
                        style="border-left:4px solid <?= $hexAlertaDash[$aColorDash] ?? '#6c757d' ?>;">
                      <div class="fw-semibold small text-<?= $aColorDash ?>">
                        <i class="bi <?= $aIconoDash ?> me-1"></i><?= $aLabelDash ?>
                      </div>
                      <div class="small"><?= htmlspecialchars($alerta['mensaje']) ?></div>
                      <?php if (!empty($alerta['enlace_reunion'])): ?>
                        <a href="<?= htmlspecialchars($alerta['enlace_reunion']) ?>" target="_blank" rel="noopener"
                           class="btn btn-sm btn-info text-white mt-1">
                          <i class="bi bi-camera-video me-1"></i>Unirse a la reunión
                        </a>
                      <?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <?php endif; ?>

          <!-- Requisitos pendientes -->
          <div class="d-flex align-items-center justify-content-between py-2 px-3 rounded-2 mb-3"
               style="background:linear-gradient(90deg, var(--zf-navy,#22404b), var(--zf-teal,#1993b8));">
            <span class="badge py-1 px-2" style="background:rgba(255,255,255,.15);color:#fff;">
              <i class="bi bi-hourglass-split me-1"></i>REQUISITOS PENDIENTES
            </span>
            <a href="index.php?modulo=cronograma" class="small text-decoration-none text-white fw-semibold">
              Ver cronograma <i class="bi bi-arrow-right ms-1"></i>
            </a>
          </div>
          <?php if (empty($requisitos_pendientes)): ?>
            <div class="card shadow-sm mb-4 card-acento-teal">
              <div class="card-body text-center text-muted py-4">
                <i class="bi bi-check-circle fs-2 text-success opacity-50 d-block mb-2"></i>
                ¡Sin requisitos pendientes!
              </div>
            </div>
          <?php else: ?>
            <div class="row g-3 mb-4">
              <?php foreach ($requisitos_pendientes as $req): ?>
              <div class="col-sm-6 col-xl-4">
                <div class="card shadow-sm h-100 card-acento-teal">
                  <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between gap-2 mb-1">
                      <span class="small fw-semibold"><?= htmlspecialchars($req['nombre']) ?></span>
                      <?php if ($req['obligatorio']): ?>
                        <span class="badge text-bg-danger flex-shrink-0" style="font-size:.6rem;">Obligatorio</span>
                      <?php endif; ?>
                    </div>
                    <div class="text-muted small mb-2">
                      <?= htmlspecialchars($req['etapa_nombre']) ?>
                      <?php if ($req['entidad_nombre']): ?>
                        · <span class="text-zf-teal"><?= htmlspecialchars($req['entidad_nombre']) ?></span>
                      <?php endif; ?>
                    </div>
                    <?php if ($req['fecha_vencimiento']): ?>
                      <small class="text-danger"><i class="bi bi-calendar-x me-1"></i><?= date('d/m/Y', strtotime($req['fecha_vencimiento'])) ?></small>
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

  <?php if ($empresa && (!empty($distribucionReq) || !empty($chartFasesLabelsDash))): ?>
  <script>
    window.reportesChartData = {
      distribucion: {
        labels: ['Cumplidos', 'En progreso', 'Pendientes', 'No aplica'],
        data:   [<?= $distribucionReq['cumplido'] ?>, <?= $distribucionReq['en_progreso'] ?>, <?= $distribucionReq['pendiente'] ?>, <?= $distribucionReq['no_aplica'] ?>],
        colors: ['#28a745', '#0d6efd', '#6c757d', '#e9ecef'],
      },
      fases: {
        labels: <?= json_encode($chartFasesLabelsDash, JSON_UNESCAPED_UNICODE) ?>,
        data:   <?= json_encode($chartFasesDataDash) ?>,
      },
    };
  </script>
  <?php endif; ?>

  <?php require_once __DIR__ . '/../parciales/pie.php'; ?>

</div>
