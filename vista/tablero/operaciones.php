<?php
$pageTitle  = 'Tablero Operaciones — ZFIP-E';
$activePage = 'tablero';
$pageStyles = ['vista/assets/css/componentes.css', 'vista/assets/css/tablero.css'];
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
?>

<div class="app-wrapper">

  <?php require_once __DIR__ . '/../parciales/navegacion.php'; ?>
  <?php require_once __DIR__ . '/../parciales/menu_lateral.php'; ?>

  <main class="app-main">
    <div class="app-content-header encabezado-zf">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h3 class="mb-0 titulo-zf">Tablero</h3>
          </div>
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
            <div>
              <h5 class="mb-0"><?= $saludo ?>, <?= htmlspecialchars($nombreUsuario) ?></h5>
              <small class="text-muted">
                <span class="badge text-bg-warning me-1">Operaciones</span>
                <?= date('l, d \d\e F \d\e Y') ?>
              </small>
            </div>
          </div>
        </div>

        <!-- Tarjetas de estadísticas -->
        <div class="row g-3 mb-4">

          <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm h-100">
              <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:54px;height:54px;font-size:1.4rem;">
                  <i class="bi bi-building"></i>
                </div>
                <div>
                  <div class="fs-2 fw-bold lh-1"><?= number_format($totalEmpresas) ?></div>
                  <div class="text-muted small">Empresas en seguimiento</div>
                </div>
              </div>
              <div class="card-footer bg-transparent pt-0 pb-2 border-0">
                <a href="index.php?modulo=empresas" class="small text-primary text-decoration-none">
                  Ver empresas <i class="bi bi-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>

          <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm h-100">
              <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:54px;height:54px;font-size:1.4rem;">
                  <i class="bi bi-diagram-3"></i>
                </div>
                <div>
                  <div class="fs-2 fw-bold lh-1"><?= number_format($totalEtapas) ?></div>
                  <div class="text-muted small">Etapas configuradas</div>
                </div>
              </div>
              <div class="card-footer bg-transparent pt-0 pb-2 border-0">
                <a href="index.php?modulo=configuracion" class="small text-success text-decoration-none">
                  Gestionar <i class="bi bi-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>

          <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm h-100">
              <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:54px;height:54px;font-size:1.4rem;">
                  <i class="bi bi-list-check"></i>
                </div>
                <div>
                  <div class="fs-2 fw-bold lh-1"><?= number_format($totalRequisitos) ?></div>
                  <div class="text-muted small">Requisitos activos</div>
                </div>
              </div>
              <div class="card-footer bg-transparent pt-0 pb-2 border-0">
                <a href="index.php?modulo=configuracion" class="small text-info text-decoration-none">
                  Ver matriz <i class="bi bi-arrow-right"></i>
                </a>
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
                  <div class="fs-2 fw-bold lh-1"><?= number_format($totalAlertas) ?></div>
                  <div class="text-muted small">Alertas pendientes</div>
                </div>
              </div>
              <div class="card-footer bg-transparent pt-0 pb-2 border-0">
                <span class="small text-muted">Todas las empresas</span>
              </div>
            </div>
          </div>

        </div>

        <!-- Accesos rápidos -->
        <div class="row g-3">
          <div class="col-12">
            <h6 class="text-muted text-uppercase fw-semibold mb-2" style="font-size:.75rem;letter-spacing:.05em;">
              Accesos rápidos
            </h6>
          </div>

          <div class="col-sm-6 col-md-4 col-xl-3">
            <a href="index.php?modulo=empresas&accion=crear" class="card shadow-sm text-decoration-none atajo-card h-100">
              <div class="card-body d-flex align-items-center gap-3 py-3">
                <span class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0"
                      style="width:40px;height:40px;font-size:1.1rem;">
                  <i class="bi bi-building-add"></i>
                </span>
                <div>
                  <div class="fw-semibold text-body">Nueva empresa</div>
                  <small class="text-muted">Registrar ZFPE</small>
                </div>
              </div>
            </a>
          </div>

          <div class="col-sm-6 col-md-4 col-xl-3">
            <a href="index.php?modulo=configuracion" class="card shadow-sm text-decoration-none atajo-card h-100">
              <div class="card-body d-flex align-items-center gap-3 py-3">
                <span class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center flex-shrink-0"
                      style="width:40px;height:40px;font-size:1.1rem;">
                  <i class="bi bi-sliders"></i>
                </span>
                <div>
                  <div class="fw-semibold text-body">Configuración</div>
                  <small class="text-muted">Parametrizar matriz</small>
                </div>
              </div>
            </a>
          </div>

          <div class="col-sm-6 col-md-4 col-xl-3">
            <a href="index.php?modulo=comites&accion=crear" class="card shadow-sm text-decoration-none atajo-card h-100">
              <div class="card-body d-flex align-items-center gap-3 py-3">
                <span class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center flex-shrink-0"
                      style="width:40px;height:40px;font-size:1.1rem;">
                  <i class="bi bi-calendar-plus"></i>
                </span>
                <div>
                  <div class="fw-semibold text-body">Nuevo comité</div>
                  <small class="text-muted">Programar reunión</small>
                </div>
              </div>
            </a>
          </div>

          <div class="col-sm-6 col-md-4 col-xl-3">
            <a href="index.php?modulo=empresas" class="card shadow-sm text-decoration-none atajo-card h-100">
              <div class="card-body d-flex align-items-center gap-3 py-3">
                <span class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center flex-shrink-0"
                      style="width:40px;height:40px;font-size:1.1rem;">
                  <i class="bi bi-graph-up"></i>
                </span>
                <div>
                  <div class="fw-semibold text-body">Seguimiento</div>
                  <small class="text-muted">Ver todas las empresas</small>
                </div>
              </div>
            </a>
          </div>

        </div>

      </div>
    </div>
  </main>

  <?php require_once __DIR__ . '/../parciales/pie.php'; ?>

</div>
