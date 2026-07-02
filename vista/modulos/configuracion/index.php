<?php
$pageTitle  = 'Configuración — ZFPE';
$activePage = 'configuracion';
$pageStyles = ['vista/assets/css/componentes.css'];
?>
<?php require_once __DIR__ . '/../../parciales/cabecera.php'; ?>

<div class="app-wrapper">
  <?php require_once __DIR__ . '/../../parciales/navegacion.php'; ?>
  <?php require_once __DIR__ . '/../../parciales/menu_lateral.php'; ?>

  <main class="app-main">
    <div class="app-content-header encabezado-zf">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6"><h3 class="mb-0 titulo-zf">Configuración</h3></div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item active">Configuración</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="app-content">
      <div class="container-fluid">

        <p class="text-muted mb-4">
          Administra todos los elementos de la matriz de seguimiento.
          Nada queda fijo en el código — todo es parametrizable desde aquí.
        </p>

        <div class="row g-3">

          <div class="col-sm-6 col-xl-3">
            <a href="index.php?modulo=configuracion&accion=fases" class="card shadow-sm text-decoration-none atajo-card h-100">
              <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:54px;height:54px;font-size:1.4rem;">
                  <i class="bi bi-collection"></i>
                </div>
                <div>
                  <div class="fs-2 fw-bold lh-1"><?= number_format($totalFases) ?></div>
                  <div class="fw-semibold text-body">Fases</div>
                  <small class="text-muted">Preoperativa, Operativa…</small>
                </div>
              </div>
            </a>
          </div>

          <div class="col-sm-6 col-xl-3">
            <a href="index.php?modulo=configuracion&accion=entidades" class="card shadow-sm text-decoration-none atajo-card h-100">
              <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:54px;height:54px;font-size:1.4rem;">
                  <i class="bi bi-bank"></i>
                </div>
                <div>
                  <div class="fs-2 fw-bold lh-1"><?= number_format($totalEntidades) ?></div>
                  <div class="fw-semibold text-body">Entidades</div>
                  <small class="text-muted">DIAN, MINCIT, etc.</small>
                </div>
              </div>
            </a>
          </div>

          <div class="col-sm-6 col-xl-3">
            <a href="index.php?modulo=configuracion&accion=etapas" class="card shadow-sm text-decoration-none atajo-card h-100">
              <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:54px;height:54px;font-size:1.4rem;">
                  <i class="bi bi-diagram-3"></i>
                </div>
                <div>
                  <div class="fs-2 fw-bold lh-1"><?= number_format($totalEtapas) ?></div>
                  <div class="fw-semibold text-body">Etapas</div>
                  <small class="text-muted">Estructuración, DIAN, etc.</small>
                </div>
              </div>
            </a>
          </div>

          <div class="col-sm-6 col-xl-3">
            <a href="index.php?modulo=configuracion&accion=requisitos" class="card shadow-sm text-decoration-none atajo-card h-100">
              <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:54px;height:54px;font-size:1.4rem;">
                  <i class="bi bi-list-check"></i>
                </div>
                <div>
                  <div class="fs-2 fw-bold lh-1"><?= number_format($totalRequisitos) ?></div>
                  <div class="fw-semibold text-body">Requisitos</div>
                  <small class="text-muted">Por etapa y entidad</small>
                </div>
              </div>
            </a>
          </div>

          <div class="col-sm-6 col-xl-3">
            <a href="index.php?modulo=configuracion&accion=items" class="card shadow-sm text-decoration-none atajo-card h-100">
              <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:54px;height:54px;font-size:1.4rem;">
                  <i class="bi bi-ui-checks"></i>
                </div>
                <div>
                  <div class="fs-2 fw-bold lh-1"><?= number_format($totalItems) ?></div>
                  <div class="fw-semibold text-body">Ítems</div>
                  <small class="text-muted">Sub-ítems por requisito</small>
                </div>
              </div>
            </a>
          </div>

          <div class="col-sm-6 col-xl-3">
            <a href="index.php?modulo=configuracion&accion=indicadores" class="card shadow-sm text-decoration-none atajo-card h-100">
              <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:54px;height:54px;font-size:1.4rem;background-color:rgba(25,147,184,.12);">
                  <i class="bi bi-graph-up" style="color:var(--zf-teal,#1993b8);"></i>
                </div>
                <div>
                  <div class="fs-2 fw-bold lh-1"><?= number_format($totalIndicadores) ?></div>
                  <div class="fw-semibold text-body">Indicadores</div>
                  <small class="text-muted">KPIs por empresa</small>
                </div>
              </div>
            </a>
          </div>

        </div>

      </div>
    </div>
  </main>

  <?php require_once __DIR__ . '/../../parciales/pie.php'; ?>
</div>
