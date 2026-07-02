<?php
/** @var array|null $empresa */
/** @var array $alertas */
/** @var array $requisitos_pendientes */
$pageTitle  = 'Inicio — ZFIP-E';
$activePage = 'tablero';
$pageStyles  = ['vista/assets/css/componentes.css', 'vista/assets/css/tablero.css', 'vista/assets/css/indicadores.css'];
$pageScripts = ['vista/assets/vendor/chartjs/chart.umd.min.js', 'vista/assets/js/indicadores.js'];
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
$prioridadColor = ['alta' => 'danger', 'media' => 'warning', 'baja' => 'info'];
?>

<div class="app-wrapper">

  <?php require_once __DIR__ . '/../parciales/navegacion.php'; ?>
  <?php require_once __DIR__ . '/../parciales/menu_lateral.php'; ?>

  <main class="app-main">
    <div class="app-content-header encabezado-zf">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6"><h3 class="mb-0 titulo-zf">Tablero</h3></div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item active">Tablero</li>
            </ol>
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
              <div class="card shadow-sm h-100">
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
              <div class="card shadow-sm h-100">
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
              <div class="card shadow-sm h-100">
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
              <div class="card shadow-sm h-100">
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

          <div class="row g-4">

            <!-- Avance por etapa -->
            <div class="col-lg-7">
              <div class="card shadow-sm h-100">
                <div class="card-header">
                  <h5 class="card-title mb-0">
                    <i class="bi bi-diagram-3 me-2 text-success"></i>Avance por etapa
                  </h5>
                </div>
                <div class="card-body">
                  <?php foreach ($empresa['etapas'] as $etapa): ?>
                  <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                      <div class="d-flex align-items-center gap-2">
                        <span class="small fw-semibold"><?= htmlspecialchars($etapa['nombre']) ?></span>
                        <span class="badge text-bg-<?= $estadoColor[$etapa['estado_progreso']] ?? 'secondary' ?>" style="font-size:.65rem;">
                          <?= $estadoLabel[$etapa['estado_progreso']] ?? $etapa['estado_progreso'] ?>
                        </span>
                      </div>
                      <small class="fw-semibold text-muted"><?= number_format($etapa['avance'], 1) ?>%</small>
                    </div>
                    <div class="progress" style="height:8px;">
                      <div class="progress-bar <?= $etapa['avance'] >= 100 ? 'bg-success' : ($etapa['avance'] > 0 ? 'bg-primary' : 'bg-secondary') ?> bg-opacity-<?= $etapa['avance'] > 0 ? '100' : '25' ?>"
                           style="width:<?= max($etapa['avance'], $etapa['avance'] > 0 ? 3 : 0) ?>%"></div>
                    </div>
                  </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>

            <!-- Requisitos pendientes + Alertas -->
            <div class="col-lg-5 d-flex flex-column gap-4">

              <!-- Alertas -->
              <?php if (!empty($alertas)): ?>
              <div class="card shadow-sm border-danger border-opacity-50">
                <div class="card-header bg-danger bg-opacity-10">
                  <h5 class="card-title mb-0 text-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>Alertas activas
                  </h5>
                </div>
                <div class="card-body p-0">
                  <ul class="list-group list-group-flush">
                    <?php foreach ($alertas as $alerta): ?>
                    <li class="list-group-item px-3 py-2">
                      <div class="d-flex align-items-start gap-2">
                        <span class="badge text-bg-<?= $prioridadColor[$alerta['prioridad']] ?? 'secondary' ?> mt-1 flex-shrink-0">
                          <?= ucfirst($alerta['prioridad']) ?>
                        </span>
                        <small><?= htmlspecialchars($alerta['mensaje']) ?></small>
                      </div>
                    </li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              </div>
              <?php endif; ?>

              <!-- Requisitos pendientes -->
              <div class="card shadow-sm flex-grow-1">
                <div class="card-header">
                  <h5 class="card-title mb-0">
                    <i class="bi bi-hourglass-split me-2 text-warning"></i>Requisitos pendientes
                  </h5>
                </div>
                <div class="card-body p-0">
                  <?php if (empty($requisitos_pendientes)): ?>
                    <div class="text-center text-muted py-4">
                      <i class="bi bi-check-circle fs-2 text-success opacity-50 d-block mb-2"></i>
                      ¡Sin requisitos pendientes!
                    </div>
                  <?php else: ?>
                    <ul class="list-group list-group-flush">
                      <?php foreach ($requisitos_pendientes as $req): ?>
                      <li class="list-group-item px-3 py-2">
                        <div class="d-flex align-items-start justify-content-between gap-2">
                          <div>
                            <div class="small fw-semibold"><?= htmlspecialchars($req['nombre']) ?></div>
                            <small class="text-muted">
                              <?= htmlspecialchars($req['etapa_nombre']) ?>
                              <?php if ($req['entidad_nombre']): ?>
                                · <span class="text-primary"><?= htmlspecialchars($req['entidad_nombre']) ?></span>
                              <?php endif; ?>
                            </small>
                          </div>
                          <div class="d-flex flex-column align-items-end gap-1">
                            <?php if ($req['obligatorio']): ?>
                              <span class="badge text-bg-danger" style="font-size:.6rem;">Obligatorio</span>
                            <?php endif; ?>
                            <?php if ($req['fecha_vencimiento']): ?>
                              <small class="text-danger"><i class="bi bi-calendar-x me-1"></i><?= date('d/m/Y', strtotime($req['fecha_vencimiento'])) ?></small>
                            <?php endif; ?>
                          </div>
                        </div>
                      </li>
                      <?php endforeach; ?>
                    </ul>
                  <?php endif; ?>
                </div>
              </div>

            </div>

          </div>

        <?php endif; ?>

        <!-- Indicadores de la empresa -->
        <?php if (!empty($indicadoresResumen)): ?>
        <div class="mt-4">
          <h6 class="text-muted fw-semibold mb-3" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.08em;">
            <i class="bi bi-graph-up me-1" style="color:var(--zf-teal,#1993b8);"></i>Indicadores
          </h6>
          <div class="row g-3">
            <?php foreach ($indicadoresResumen as $ind):
              $labels = $ind['periodos_json'] ? explode(',', $ind['periodos_json']) : [];
              $vals   = $ind['valores_json']  ? array_map('floatval', explode(',', $ind['valores_json'])) : [];
              $meta   = $ind['meta'] !== null ? (float) $ind['meta'] : null;
              $ultimo = $ind['ultimo_valor'] !== null ? (float) $ind['ultimo_valor'] : null;
              $pct    = ($meta && $meta > 0 && $ultimo !== null) ? min(round($ultimo / $meta * 100, 1), 100) : null;
              $col    = ($pct === null) ? 'secondary' : ($pct >= 100 ? 'success' : ($pct >= 50 ? 'primary' : 'warning'));
            ?>
            <div class="col-md-6 col-xl-4">
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
          </div>
        </div>
        <?php endif; ?>

      </div>
    </div>
  </main>

  <?php require_once __DIR__ . '/../parciales/pie.php'; ?>

</div>
