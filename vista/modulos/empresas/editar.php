<?php
/** @var array $empresa */
$pageTitle = 'Editar empresa — ZFIP-E';
$activePage = 'empresas';
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
          <div class="col-sm-6">
            <h3 class="mb-0 titulo-zf">Editar empresa</h3>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item"><a href="index.php?modulo=empresas">Empresas</a></li>
              <li class="breadcrumb-item active">Editar</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="app-content">
      <div class="container-fluid">
        <form method="POST" action="index.php?modulo=empresas&accion=editar&id=<?= $empresa['id'] ?>" id="formEmpresa">
          <div class="card mb-4">
            <div class="card-header">
              <h3 class="card-title"><i class="bi bi-building me-2"></i>Información de la empresa</h3>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label class="form-label">NIT <span class="text-danger">*</span></label>
                  <input type="text" name="nit" class="form-control"
                         value="<?= htmlspecialchars($empresa['nit']) ?>" required>
                </div>
                <div class="col-md-8 mb-3">
                  <label class="form-label">Razón social <span class="text-danger">*</span></label>
                  <input type="text" name="razon_social" class="form-control"
                         value="<?= htmlspecialchars($empresa['razon_social']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Representante legal</label>
                  <input type="text" name="representante" class="form-control"
                         value="<?= htmlspecialchars($empresa['representante']) ?>">
                </div>
                <div class="col-md-3 mb-3">
                  <label class="form-label">Teléfono</label>
                  <input type="text" name="telefono" class="form-control"
                         value="<?= htmlspecialchars($empresa['telefono']) ?>">
                </div>
                <div class="col-md-3 mb-3">
                  <label class="form-label">Correo electrónico</label>
                  <input type="email" name="correo" class="form-control"
                         value="<?= htmlspecialchars($empresa['correo']) ?>">
                </div>
              </div>
            </div>
          </div>

          <div class="card mb-4">
            <div class="card-header">
              <h3 class="card-title"><i class="bi bi-shield-lock me-2"></i>Cambiar contraseña</h3>
            </div>
            <div class="card-body">
              <p class="text-muted small mb-3">Deje estos campos en blanco si no desea cambiar la contraseña.</p>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Nueva contraseña</label>
                  <div class="input-group">
                    <input type="password" name="contrasena" id="contrasena" class="form-control" placeholder="Mínimo 8 caracteres" minlength="8">
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('contrasena', this)">
                      <i class="bi bi-eye"></i>
                    </button>
                  </div>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Confirmar nueva contraseña</label>
                  <div class="input-group">
                    <input type="password" name="contrasena_confirmar" id="contrasena_confirmar" class="form-control" placeholder="Repita la contraseña" minlength="8">
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('contrasena_confirmar', this)">
                      <i class="bi bi-eye"></i>
                    </button>
                  </div>
                  <div id="errorContrasena" class="invalid-feedback d-block" style="display:none!important"></div>
                </div>
              </div>
            </div>
            <div class="card-footer d-flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i> Actualizar empresa
              </button>
              <a href="index.php?modulo=empresas" class="btn btn-secondary">Cancelar</a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </main>

  <?php require_once __DIR__ . '/../../parciales/pie.php'; ?>

</div>

<script>
function togglePassword(fieldId, btn) {
  const input = document.getElementById(fieldId);
  const icon = btn.querySelector('i');
  if (input.type === 'password') {
    input.type = 'text';
    icon.classList.replace('bi-eye', 'bi-eye-slash');
  } else {
    input.type = 'password';
    icon.classList.replace('bi-eye-slash', 'bi-eye');
  }
}

document.getElementById('formEmpresa').addEventListener('submit', function(e) {
  const p1 = document.getElementById('contrasena').value;
  const p2 = document.getElementById('contrasena_confirmar').value;
  const err = document.getElementById('errorContrasena');
  if (p1 && p1 !== p2) {
    e.preventDefault();
    err.textContent = 'Las contraseñas no coinciden.';
    err.style.display = 'block';
    document.getElementById('contrasena_confirmar').classList.add('is-invalid');
  } else {
    err.style.display = 'none';
    document.getElementById('contrasena_confirmar').classList.remove('is-invalid');
  }
});
</script>
