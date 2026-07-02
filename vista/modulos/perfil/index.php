<?php
/** @var array $usuario */
/** @var array|null $empresa */
$pageTitle   = 'Mi perfil — ZFPE';
$activePage  = 'perfil';
$pageStyles  = ['vista/assets/css/componentes.css'];
$pageScripts = ['vista/assets/js/perfil.js'];

$rolLabel = ['admin' => 'Administrador', 'operaciones' => 'Operaciones', 'usuario' => 'Usuario'][$usuario['rol']] ?? $usuario['rol'];
$rolColor = ['admin' => 'danger', 'operaciones' => 'warning', 'usuario' => 'primary'][$usuario['rol']] ?? 'secondary';
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
            <h3 class="mb-0 titulo-zf"><i class="bi bi-person-circle me-2"></i>Mi perfil</h3>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item active">Mi perfil</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="app-content">
      <div class="container-fluid">
        <div class="row justify-content-center">
          <div class="col-lg-7 col-xl-6">

            <div class="card shadow-sm">
              <div class="card-header d-flex align-items-center gap-3">
                <span id="avatarPreview"
                      class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-semibold flex-shrink-0"
                      style="width:46px;height:46px;font-size:1.1rem;">
                  <?= mb_strtoupper(mb_substr($usuario['nombre'], 0, 1)) ?>
                </span>
                <div>
                  <h5 class="card-title mb-0"><?= htmlspecialchars($usuario['nombre']) ?></h5>
                  <span class="badge text-bg-<?= $rolColor ?>" style="font-size:.65rem;"><?= htmlspecialchars($rolLabel) ?></span>
                  <?php if ($empresa): ?>
                    <span class="badge text-bg-light border text-muted" style="font-size:.65rem;">
                      <i class="bi bi-building me-1"></i><?= htmlspecialchars($empresa['razon_social']) ?>
                    </span>
                  <?php endif; ?>
                </div>
              </div>

              <form method="POST" action="index.php?modulo=perfil&accion=actualizar" id="formPerfil">
                <div class="card-body">

                  <div class="mb-3">
                    <label for="nombre" class="form-label fw-semibold">
                      Nombre completo <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="nombre" name="nombre" class="form-control"
                           value="<?= htmlspecialchars($usuario['nombre']) ?>" required autofocus>
                  </div>

                  <div class="mb-4">
                    <label for="correo" class="form-label fw-semibold">
                      Correo electrónico <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                      <input type="email" id="correo" name="correo" class="form-control"
                             value="<?= htmlspecialchars($usuario['correo']) ?>" required>
                    </div>
                  </div>

                  <hr>

                  <h6 class="fw-semibold mb-3"><i class="bi bi-shield-lock me-2 text-muted"></i>Cambiar contraseña</h6>
                  <p class="text-muted small mb-3">Déjalo en blanco si no quieres cambiar tu contraseña.</p>

                  <div class="mb-3">
                    <label for="contrasena_actual" class="form-label fw-semibold">Contraseña actual</label>
                    <div class="input-group">
                      <input type="password" id="contrasena_actual" name="contrasena_actual" class="form-control"
                             placeholder="Tu contraseña actual" autocomplete="current-password">
                      <button type="button" class="btn btn-outline-secondary toggle-pwd" data-target="contrasena_actual" tabindex="-1">
                        <i class="bi bi-eye"></i>
                      </button>
                    </div>
                  </div>

                  <div class="row g-3">
                    <div class="col-sm-6">
                      <label for="nueva_contrasena" class="form-label fw-semibold">Nueva contraseña</label>
                      <div class="input-group">
                        <input type="password" id="nueva_contrasena" name="nueva_contrasena" class="form-control"
                               placeholder="Mín. 6 caracteres" minlength="6" autocomplete="new-password">
                        <button type="button" class="btn btn-outline-secondary toggle-pwd" data-target="nueva_contrasena" tabindex="-1">
                          <i class="bi bi-eye"></i>
                        </button>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <label for="confirmar_contrasena" class="form-label fw-semibold">Confirmar</label>
                      <div class="input-group">
                        <input type="password" id="confirmar_contrasena" name="confirmar_contrasena" class="form-control"
                               placeholder="Repita la nueva contraseña" autocomplete="new-password">
                        <button type="button" class="btn btn-outline-secondary toggle-pwd" data-target="confirmar_contrasena" tabindex="-1">
                          <i class="bi bi-eye"></i>
                        </button>
                      </div>
                      <div id="confirmarError" class="text-danger small mt-1" style="display:none;">
                        Las contraseñas no coinciden.
                      </div>
                    </div>
                  </div>

                </div>
                <div class="card-footer d-flex gap-2">
                  <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Guardar cambios
                  </button>
                  <a href="index.php" class="btn btn-outline-secondary">
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
