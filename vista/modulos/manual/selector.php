<?php
$pageTitle  = 'Manuales de uso — ZFPE';
$activePage = 'manual';
$pageStyles = ['vista/assets/css/componentes.css'];

$manuales = [
    [
        'accion' => 'administrador',
        'icono'  => 'bi-shield-fill-check',
        'clase'  => 'bg-zf-navy',
        'hex'    => '#22404b',
        'titulo' => 'Manual del Administrador',
        'desc'   => 'Todo lo de Operaciones, además de usuarios del sistema y correo SMTP.',
    ],
    [
        'accion' => 'operaciones',
        'icono'  => 'bi-person-fill-gear',
        'clase'  => 'bg-zf-teal',
        'hex'    => '#1993b8',
        'titulo' => 'Manual de Operaciones',
        'desc'   => 'Configuración inicial, empresas, seguimiento, documentos, comités, cronograma y reportes.',
    ],
    [
        'accion' => 'empresa',
        'icono'  => 'bi-building',
        'clase'  => 'bg-info',
        'hex'    => '#0dcaf0',
        'titulo' => 'Manual de Empresa',
        'desc'   => 'Cronograma, estados del proceso, entidades, documentos, comités y reportes de una empresa.',
    ],
    [
        'accion' => 'usuario',
        'icono'  => 'bi-person-fill',
        'clase'  => 'bg-success',
        'hex'    => '#198754',
        'titulo' => 'Manual de Usuario',
        'desc'   => 'Primer ingreso, perfil, mis compromisos y notificaciones.',
    ],
];
?>
<?php require_once __DIR__ . '/../../parciales/cabecera.php'; ?>

<div class="app-wrapper">
  <?php require_once __DIR__ . '/../../parciales/navegacion.php'; ?>
  <?php require_once __DIR__ . '/../../parciales/menu_lateral.php'; ?>

  <main class="app-main">
    <div class="app-content-header encabezado-zf">
      <div class="container-fluid">
        <div class="row align-items-center">
          <div class="col-sm-6">
            <h3 class="mb-0 titulo-zf"><i class="bi bi-book-half me-2"></i>Manuales de uso</h3>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item active">Manuales</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="app-content">
      <div class="container-fluid">

        <p class="text-muted mb-4">Como administrador tienes acceso a los 4 manuales del sistema. Elige cuál quieres consultar.</p>

        <div class="row g-4">
          <?php foreach ($manuales as $m): ?>
          <div class="col-md-6 col-lg-3">
            <a href="index.php?modulo=manual&accion=<?= $m['accion'] ?>" class="text-decoration-none">
              <div class="card h-100 shadow-sm border-0" style="border-top:4px solid <?= $m['hex'] ?>;">
                <div class="card-body text-center p-4">
                  <div class="rounded-circle <?= $m['clase'] ?> text-white d-inline-flex align-items-center justify-content-center mb-3"
                       style="width:64px;height:64px;font-size:1.8rem;">
                    <i class="bi <?= $m['icono'] ?>"></i>
                  </div>
                  <h5 class="fw-bold mb-2"><?= htmlspecialchars($m['titulo']) ?></h5>
                  <p class="text-muted small mb-0"><?= htmlspecialchars($m['desc']) ?></p>
                </div>
              </div>
            </a>
          </div>
          <?php endforeach; ?>
        </div>

        <!-- Footer del manual -->
        <div class="text-center text-muted small py-4">
          <i class="bi bi-building me-1"></i>ZFPE · Zona Franca Internacional de Pereira ·
          <i class="bi bi-calendar3 me-1"></i><?= date('Y') ?>
        </div>

      </div>
    </div>
  </main>

  <?php require_once __DIR__ . '/../../parciales/pie.php'; ?>
</div>
