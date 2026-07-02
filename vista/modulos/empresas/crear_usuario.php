<?php
/** @var array $empresa */
$pageTitle  = 'Nuevo usuario — ZFPE';
$activePage = 'empresas';
$pageStyles = ['vista/assets/css/componentes.css'];

$v = fn(string $k) => htmlspecialchars($_POST[$k] ?? '');
?>
<?php require_once __DIR__ . '/../../parciales/cabecera.php'; ?>

<div class="app-wrapper">
  <?php require_once __DIR__ . '/../../parciales/navegacion.php'; ?>
  <?php require_once __DIR__ . '/../../parciales/menu_lateral.php'; ?>

  <main class="app-main">
    <div class="app-content-header encabezado-zf">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6"><h3 class="mb-0 titulo-zf">Nuevo usuario</h3></div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item"><a href="index.php?modulo=empresas">Empresas</a></li>
              <li class="breadcrumb-item"><a href="index.php?modulo=empresas&accion=ver&id=<?= $empresa['id'] ?>"><?= htmlspecialchars($empresa['razon_social']) ?></a></li>
              <li class="breadcrumb-item active">Nuevo usuario</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="app-content">
      <div class="container-fluid">
        <div class="row justify-content-center">
          <div class="col-lg-6 col-xl-5">

            <div class="card shadow-sm">
              <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-person-plus text-primary fs-5"></i>
                <div>
                  <h5 class="card-title mb-0">Nuevo usuario</h5>
                  <small class="text-muted"><i class="bi bi-building me-1"></i><?= htmlspecialchars($empresa['razon_social']) ?></small>
                </div>
              </div>

              <form method="POST" action="index.php?modulo=empresas&accion=crear-usuario&id=<?= $empresa['id'] ?>">
                <div class="card-body">

                  <?php if (!empty($_SESSION['flash_error'])): ?>
                    <div class="alert alert-danger">
                      <i class="bi bi-exclamation-triangle me-1"></i>
                      <?= htmlspecialchars($_SESSION['flash_error']) ?>
                    </div>
                    <?php unset($_SESSION['flash_error']); ?>
                  <?php endif; ?>

                  <div class="mb-3">
                    <label for="nombre" class="form-label fw-semibold">
                      Nombre completo <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="nombre" name="nombre" class="form-control"
                           value="<?= $v('nombre') ?>" required autofocus>
                  </div>

                  <div class="mb-3">
                    <label for="correo" class="form-label fw-semibold">
                      Correo electrónico <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                      <input type="email" id="correo" name="correo" class="form-control"
                             value="<?= $v('correo') ?>" required>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label for="contrasena" class="form-label fw-semibold">
                      Contraseña <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                      <input type="password" id="contrasena" name="contrasena" class="form-control"
                             placeholder="Mínimo 8 caracteres" minlength="8" required>
                      <button type="button" class="btn btn-outline-secondary"
                              onclick="togglePass('contrasena', this)">
                        <i class="bi bi-eye"></i>
                      </button>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label for="contrasena2" class="form-label fw-semibold">
                      Confirmar contraseña <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                      <input type="password" id="contrasena2" name="contrasena2" class="form-control"
                             placeholder="Repita la contraseña" minlength="8" required>
                      <button type="button" class="btn btn-outline-secondary"
                              onclick="togglePass('contrasena2', this)">
                        <i class="bi bi-eye"></i>
                      </button>
                    </div>
                    <div id="errorPass" class="text-danger small mt-1" style="display:none;">Las contraseñas no coinciden.</div>
                  </div>

                  <div class="alert alert-info py-2 mb-0 small">
                    <i class="bi bi-info-circle me-1"></i>
                    El usuario tendrá acceso como <strong>usuario de empresa</strong> y podrá ver el avance de <?= htmlspecialchars($empresa['razon_social']) ?>.
                  </div>

                </div>
                <div class="card-footer d-flex gap-2">
                  <button type="submit" class="btn btn-primary" id="btnGuardar">
                    <i class="bi bi-check-lg me-1"></i> Crear usuario
                  </button>
                  <a href="index.php?modulo=empresas&accion=ver&id=<?= $empresa['id'] ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg me-1"></i> Cancelar
                  </a>
                </div>
              </form>
            </div>

          </div>
        </div>
      </div>
    </div>
  </main>

  <?php require_once __DIR__ . '/../../parciales/pie.php'; ?>
</div>

<script>
function togglePass(id, btn) {
  const input = document.getElementById(id);
  const icon  = btn.querySelector('i');
  input.type  = input.type === 'password' ? 'text' : 'password';
  icon.classList.toggle('bi-eye');
  icon.classList.toggle('bi-eye-slash');
}

document.querySelector('form').addEventListener('submit', function(e) {
  const p1  = document.getElementById('contrasena').value;
  const p2  = document.getElementById('contrasena2').value;
  const err = document.getElementById('errorPass');
  if (p1 !== p2) {
    e.preventDefault();
    err.style.display = 'block';
    document.getElementById('contrasena2').classList.add('is-invalid');
  } else {
    err.style.display = 'none';
    document.getElementById('contrasena2').classList.remove('is-invalid');
  }
});
</script>
