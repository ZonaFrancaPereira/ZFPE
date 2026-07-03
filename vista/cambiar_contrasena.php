<?php
$cssComponentes = __DIR__ . '/assets/css/componentes.css';
$cssLogin       = __DIR__ . '/assets/css/login.css';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Cambiar contraseña — ZFPE</title>
  <link rel="stylesheet" href="vista/assets/vendor/bootstrap-icons/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="vista/assets/vendor/adminlte/css/adminlte.min.css">
  <link rel="stylesheet" href="vista/assets/css/componentes.css?v=<?= file_exists($cssComponentes) ? filemtime($cssComponentes) : '1' ?>">
  <link rel="stylesheet" href="vista/assets/css/login.css?v=<?= file_exists($cssLogin) ? filemtime($cssLogin) : '1' ?>">
</head>
<body class="login-page">

<div class="login-bg" aria-hidden="true">
  <div class="login-bg-slide"></div>
  <div class="login-bg-slide"></div>
  <div class="login-bg-slide"></div>
  <div class="login-bg-slide"></div>
  <div class="login-bg-slide"></div>
  <div class="login-bg-slide"></div>
</div>

<div class="login-layout">

  <div class="login-text-panel">
    <div class="login-text-content">
      <span class="login-text-eyebrow"><i class="bi bi-shield-lock me-2"></i>Seguridad de la cuenta</span>
      <h1 class="login-text-nombre">Zona Franca<br>Internacional<br>de Pereira</h1>
      <p class="login-text-legal">S.A.S. — Usuario Operador de Zonas Francas</p>
      <div class="login-text-divider"></div>
      <p class="login-text-tagline">Seguimiento y control empresarial integrado</p>
    </div>
    <p class="login-footer-left">&copy; <?= date('Y') ?> ZFPE · Todos los derechos reservados</p>
  </div>

  <div class="login-form-panel">
    <div class="login-box">
      <div class="login-logo mb-4">
        <img src="vista/img/logo2-recortado.png" alt="Zona Franca Internacional de Pereira" class="login-logo-img">
        <span class="login-logo-subtitulo">Cambio de contraseña obligatorio</span>
      </div>

      <div class="card shadow login-card">
        <div class="card-body login-card-body">
          <p class="login-box-msg text-muted">
            Por seguridad debes definir una nueva contraseña antes de continuar.
          </p>

          <?php if (!empty($_SESSION['error_cambio'])): ?>
            <div class="alert alert-danger py-2">
              <i class="bi bi-exclamation-triangle me-1"></i>
              <?= htmlspecialchars($_SESSION['error_cambio']) ?>
            </div>
            <?php unset($_SESSION['error_cambio']); ?>
          <?php endif; ?>

          <form action="index.php?accion=cambiar-contrasena" method="POST">
            <div class="input-group mb-3">
              <input type="password" name="nueva_contrasena" class="form-control"
                     placeholder="Nueva contraseña" required minlength="8" autofocus>
              <span class="input-group-text"><i class="bi bi-lock"></i></span>
            </div>
            <div class="input-group mb-3">
              <input type="password" name="confirmar_contrasena" class="form-control"
                     placeholder="Confirmar contraseña" required minlength="8">
              <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
            </div>
            <div class="d-grid">
              <button type="submit" class="btn btn-login-zf">
                <i class="bi bi-check-lg me-1"></i> Guardar y continuar
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

</div>

<script src="vista/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vista/assets/vendor/adminlte/js/adminlte.min.js"></script>
</body>
</html>
