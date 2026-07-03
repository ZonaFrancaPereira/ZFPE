<?php
/** @var bool $esAdmin */
$pageTitle  = 'Manual de Empresa — ZFPE';
$activePage = 'manual';
$pageStyles = ['vista/assets/css/componentes.css'];
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
            <h3 class="mb-0 titulo-zf"><i class="bi bi-book-half me-2"></i>Manual de uso</h3>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <?php if ($esAdmin): ?>
                <li class="breadcrumb-item"><a href="index.php?modulo=manual">Manuales</a></li>
              <?php endif; ?>
              <li class="breadcrumb-item active">Empresa</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="app-content">
      <div class="container-fluid">
        <?php require __DIR__ . '/_contenido_empresa.php'; ?>

        <!-- Footer del manual -->
        <div class="text-center text-muted small py-3">
          <i class="bi bi-building me-1"></i>ZFPE · Zona Franca Internacional de Pereira ·
          <i class="bi bi-calendar3 me-1"></i><?= date('Y') ?>
        </div>
      </div>
    </div>
  </main>

  <?php require_once __DIR__ . '/../../parciales/pie.php'; ?>
</div>
