<?php
/** @var array|null $empresa */
/** @var array      $entidades */
$pageTitle  = 'Entidades — ZFPE';
$activePage = 'entidades';
$pageStyles = ['vista/assets/css/componentes.css'];

$colorReq = [
    'pendiente'   => 'secondary',
    'en_progreso' => 'primary',
    'cumplido'    => 'success',
    'no_aplica'   => 'light',
];
$labelReq = [
    'pendiente'   => 'Pendiente',
    'en_progreso' => 'En progreso',
    'cumplido'    => 'Cumplido',
    'no_aplica'   => 'No aplica',
];
?>
<?php require_once __DIR__ . '/../../parciales/cabecera.php'; ?>

<div class="app-wrapper">
  <?php require_once __DIR__ . '/../../parciales/navegacion.php'; ?>
  <?php require_once __DIR__ . '/../../parciales/menu_lateral.php'; ?>

  <main class="app-main">
    <div class="app-content-header encabezado-zf">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6"><h3 class="mb-0 titulo-zf">Entidades</h3></div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item active">Entidades</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="app-content">
      <div class="container-fluid">

        <?php if (!$empresa): ?>
          <div class="card shadow-sm">
            <div class="card-body text-center py-5 text-muted">
              <i class="bi bi-bank fs-1 opacity-25 d-block mb-3"></i>
              <h5>Sin empresa asignada</h5>
              <p class="mb-0">Tu cuenta aún no tiene una empresa vinculada. Contacta al equipo de operaciones.</p>
            </div>
          </div>

        <?php elseif (empty($entidades)): ?>
          <div class="card shadow-sm">
            <div class="card-body text-center py-5 text-muted">
              <i class="bi bi-bank fs-1 opacity-25 d-block mb-3"></i>
              <h5>Sin entidades asignadas</h5>
              <p class="mb-0">Ningún requisito activo de tu empresa tiene una entidad responsable asignada.</p>
            </div>
          </div>

        <?php else: ?>

          <!-- KPI: resumen de entidades -->
          <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
              <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-primary"><?= count($entidades) ?></div>
                <div class="text-muted small">Entidades involucradas</div>
              </div>
            </div>
            <?php
              $totalReqs  = array_sum(array_column($entidades, 'total_requisitos'));
              $totalCump  = array_sum(array_column($entidades, 'cumplidos'));
              $totalPend  = array_sum(array_column($entidades, 'pendientes'));
            ?>
            <div class="col-sm-6 col-lg-3">
              <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-secondary"><?= $totalReqs ?></div>
                <div class="text-muted small">Total requisitos</div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-success"><?= $totalCump ?></div>
                <div class="text-muted small">Cumplidos</div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-warning"><?= $totalPend ?></div>
                <div class="text-muted small">Pendientes</div>
              </div>
            </div>
          </div>

          <!-- Tarjetas por entidad -->
          <div class="row g-4">
            <?php foreach ($entidades as $entidad): ?>
            <?php
              $total   = (int) $entidad['total_requisitos'];
              $cump    = (int) $entidad['cumplidos'];
              $noApl   = (int) $entidad['no_aplica'];
              $pct     = $total > 0 ? round(($cump + $noApl) / $total * 100) : 0;
              $collapseId = 'ent_' . $entidad['id'];
            ?>
            <div class="col-md-6 col-xl-4">
              <div class="card shadow-sm h-100">
                <div class="card-header d-flex align-items-center justify-content-between gap-2">
                  <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:38px;height:38px;">
                      <i class="bi bi-bank text-primary"></i>
                    </div>
                    <div>
                      <h6 class="mb-0 fw-semibold"><?= htmlspecialchars($entidad['nombre']) ?></h6>
                      <?php if (!empty($entidad['sigla'])): ?>
                        <small class="text-muted"><?= htmlspecialchars($entidad['sigla']) ?></small>
                      <?php endif; ?>
                    </div>
                  </div>
                  <button class="btn btn-sm btn-outline-secondary py-0" type="button"
                          data-bs-toggle="collapse" data-bs-target="#<?= $collapseId ?>">
                    <i class="bi bi-chevron-down"></i>
                  </button>
                </div>

                <div class="card-body pb-2">
                  <!-- Barra de progreso -->
                  <div class="d-flex justify-content-between mb-1">
                    <small class="text-muted">Avance</small>
                    <small class="fw-bold <?= $pct >= 100 ? 'text-success' : ($pct > 0 ? 'text-primary' : 'text-secondary') ?>"><?= $pct ?>%</small>
                  </div>
                  <div class="progress mb-3" style="height:7px;">
                    <div class="progress-bar <?= $pct >= 100 ? 'bg-success' : 'bg-primary' ?>"
                         style="width:<?= $pct ?>%"></div>
                  </div>

                  <!-- Stats -->
                  <div class="d-flex gap-2 flex-wrap">
                    <span class="badge text-bg-light border">
                      <i class="bi bi-list-check me-1"></i><?= $total ?> requisitos
                    </span>
                    <?php if ($cump > 0): ?>
                      <span class="badge bg-success"><i class="bi bi-check me-1"></i><?= $cump ?> cumplidos</span>
                    <?php endif; ?>
                    <?php if ((int)$entidad['en_progreso'] > 0): ?>
                      <span class="badge bg-primary"><?= (int)$entidad['en_progreso'] ?> en progreso</span>
                    <?php endif; ?>
                    <?php if ((int)$entidad['pendientes'] > 0): ?>
                      <span class="badge bg-secondary"><?= (int)$entidad['pendientes'] ?> pendientes</span>
                    <?php endif; ?>
                  </div>

                  <?php if (!empty($entidad['descripcion'])): ?>
                    <p class="text-muted small mt-2 mb-0"><?= htmlspecialchars($entidad['descripcion']) ?></p>
                  <?php endif; ?>
                </div>

                <!-- Lista de requisitos (colapsable) -->
                <div class="collapse" id="<?= $collapseId ?>">
                  <div class="card-body border-top pt-2 pb-2">
                    <p class="text-muted small fw-semibold mb-2">Requisitos</p>
                    <div class="d-flex flex-column gap-2">
                      <?php foreach ($entidad['requisitos'] as $req): ?>
                      <?php $rc = $colorReq[$req['estado_req']] ?? 'secondary'; ?>
                      <div class="d-flex align-items-start gap-2 p-2 rounded border bg-body-secondary">
                        <span class="badge bg-<?= $rc ?> mt-1 flex-shrink-0"
                              style="width:9px;height:9px;padding:0;border-radius:50%;"></span>
                        <div class="min-w-0 flex-grow-1">
                          <div class="small fw-semibold"><?= htmlspecialchars($req['nombre']) ?></div>
                          <div class="d-flex flex-wrap gap-1 mt-1">
                            <span class="badge text-bg-light border" style="font-size:.6rem;">
                              <i class="bi bi-diagram-3 me-1"></i><?= htmlspecialchars($req['etapa_nombre']) ?>
                            </span>
                            <span class="badge bg-<?= $rc ?>" style="font-size:.6rem;">
                              <?= $labelReq[$req['estado_req']] ?? $req['estado_req'] ?>
                            </span>
                            <?php if ($req['fecha_vencimiento']): ?>
                            <?php $venc = $req['fecha_vencimiento'] < date('Y-m-d') && $req['estado_req'] !== 'cumplido'; ?>
                              <span class="badge <?= $venc ? 'bg-danger' : 'text-bg-light border' ?>" style="font-size:.6rem;">
                                <i class="bi bi-calendar-event me-1"></i><?= date('d/m/Y', strtotime($req['fecha_vencimiento'])) ?>
                              </span>
                            <?php endif; ?>
                          </div>
                          <?php if ($req['observaciones']): ?>
                            <div class="text-muted mt-1 fst-italic" style="font-size:.7rem;">
                              <?= htmlspecialchars($req['observaciones']) ?>
                            </div>
                          <?php endif; ?>
                        </div>
                      </div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                </div>

              </div>
            </div>
            <?php endforeach; ?>
          </div>

        <?php endif; ?>

      </div>
    </div>
  </main>

  <?php require_once __DIR__ . '/../../parciales/pie.php'; ?>
</div>
