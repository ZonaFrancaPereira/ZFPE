<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $pageTitle ?? 'ZFPE' ?></title>

  <!-- Aplica el tema guardado antes de renderizar para evitar parpadeo -->
  <script>
    (function () {
      const stored = localStorage.getItem('tema');
      const resuelto = (stored === 'auto' || !stored)
        ? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
        : stored;
      document.documentElement.setAttribute('data-bs-theme', resuelto);
    })();
  </script>

  <!-- Bootstrap Icons (local) -->
  <link rel="stylesheet" href="vista/assets/vendor/bootstrap-icons/font/bootstrap-icons.min.css">
  <!-- AdminLTE 4 CSS (incluye Bootstrap 5) (local) -->
  <link rel="stylesheet" href="vista/assets/vendor/adminlte/css/adminlte.min.css">
  <!-- CSS específico de la página -->
  <?php foreach ($pageStyles ?? [] as $css): ?>
    <?php $ruta = __DIR__ . '/../../' . $css; ?>
    <link rel="stylesheet" href="<?= $css ?>?v=<?= file_exists($ruta) ? filemtime($ruta) : '1' ?>">
  <?php endforeach; ?>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">

<?php if (!empty($_SESSION['flash_success']) || !empty($_SESSION['flash_error'])): ?>
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1100;">
  <?php if (!empty($_SESSION['flash_success'])): ?>
  <div class="toast align-items-center text-bg-success border-0" role="alert">
    <div class="d-flex">
      <div class="toast-body">
        <i class="bi bi-check-circle me-1"></i>
        <?= htmlspecialchars($_SESSION['flash_success']) ?>
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
  <?php unset($_SESSION['flash_success']); ?>
  <?php endif; ?>
  <?php if (!empty($_SESSION['flash_error'])): ?>
  <div class="toast align-items-center text-bg-danger border-0" role="alert">
    <div class="d-flex">
      <div class="toast-body">
        <i class="bi bi-exclamation-triangle me-1"></i>
        <?= htmlspecialchars($_SESSION['flash_error']) ?>
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
  <?php unset($_SESSION['flash_error']); ?>
  <?php endif; ?>
</div>
<?php endif; ?>
