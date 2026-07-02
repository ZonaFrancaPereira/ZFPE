<?php
$cssComponentes = __DIR__ . '/assets/css/componentes.css';
$cssLogin       = __DIR__ . '/assets/css/login.css';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Iniciar sesión — ZFIP-E</title>
  <link rel="stylesheet" href="vista/assets/vendor/bootstrap-icons/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="vista/assets/vendor/adminlte/css/adminlte.min.css">
  <link rel="stylesheet" href="vista/assets/css/componentes.css?v=<?= file_exists($cssComponentes) ? filemtime($cssComponentes) : '1' ?>">
  <link rel="stylesheet" href="vista/assets/css/login.css?v=<?= file_exists($cssLogin) ? filemtime($cssLogin) : '1' ?>">
</head>
<body class="login-page">

<!-- Slideshow de fondo -->
<div class="login-bg" aria-hidden="true">
  <div class="login-bg-slide"></div>
  <div class="login-bg-slide"></div>
  <div class="login-bg-slide"></div>
  <div class="login-bg-slide"></div>
  <div class="login-bg-slide"></div>
  <div class="login-bg-slide"></div>
</div>

<div class="login-layout">

  <!-- Panel izquierdo: nombre institucional -->
  <div class="login-text-panel">
    <div class="login-text-content">
      <span class="login-text-eyebrow"><i class="bi bi-building me-2"></i>Sistema de Gestión</span>
      <h1 class="login-text-nombre">Zona Franca<br>Internacional<br>de Pereira</h1>
      <p class="login-text-legal">S.A.S. — Usuario Operador de Zonas Francas</p>
      <div class="login-text-divider"></div>
      <p class="login-text-tagline">Seguimiento y control empresarial integrado</p>
    </div>
    <p class="login-footer-left">&copy; <?= date('Y') ?> ZFIP-E · Todos los derechos reservados</p>
  </div>

  <!-- Panel derecho: formulario -->
  <div class="login-form-panel">
    <div class="login-box">
      <div class="login-logo mb-4">
        <span class="login-logo-badge"><i class="bi bi-shield-check"></i></span>
        <span class="login-logo-texto">ZFIP<span>-E</span></span>
        <span class="login-logo-subtitulo">Acceso al sistema</span>
      </div>

      <div class="card shadow login-card">
        <div class="card-body login-card-body">
          <p class="login-box-msg text-muted">Ingresa tus credenciales para continuar</p>

          <?php if (!empty($_SESSION['error_login'])): ?>
            <div class="alert alert-danger py-2">
              <i class="bi bi-exclamation-triangle me-1"></i>
              <?= htmlspecialchars($_SESSION['error_login']) ?>
            </div>
            <?php unset($_SESSION['error_login']); ?>
          <?php endif; ?>

          <form action="index.php?accion=login" method="POST">
            <div class="input-group mb-3">
              <input type="email" name="correo" class="form-control"
                     placeholder="Correo electrónico" required autofocus>
              <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            </div>
            <div class="input-group mb-3">
              <input type="password" name="contrasena" class="form-control"
                     placeholder="Contraseña" required>
              <span class="input-group-text"><i class="bi bi-lock"></i></span>
            </div>
            <div class="d-grid">
              <button type="submit" class="btn btn-login-zf">
                <i class="bi bi-box-arrow-in-right me-1"></i> Ingresar
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
