<?php
$pageTitle   = 'Nuevo usuario — ZFPE';
$activePage  = 'usuarios';
$pageStyles  = ['vista/assets/css/componentes.css'];
$pageScripts = ['vista/assets/js/usuarios-crear.js'];
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
            <h3 class="mb-0 titulo-zf">Nuevo usuario</h3>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item"><a href="index.php?modulo=usuarios">Usuarios</a></li>
              <li class="breadcrumb-item active">Nuevo</li>
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
                      class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center fw-semibold flex-shrink-0"
                      style="width:42px;height:42px;font-size:1rem;">
                  <i class="bi bi-person"></i>
                </span>
                <h3 class="card-title mb-0">Datos del usuario</h3>
              </div>

              <form method="POST" action="index.php?modulo=usuarios&accion=crear" id="formCrear">
                <div class="card-body">

                  <div class="mb-3">
                    <label for="nombre" class="form-label fw-semibold">
                      Nombre completo <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="nombre" name="nombre" class="form-control"
                           placeholder="Ej. María García" required autofocus>
                  </div>

                  <div class="mb-3">
                    <label for="correo" class="form-label fw-semibold">
                      Correo electrónico <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                      <input type="email" id="correo" name="correo" class="form-control"
                             placeholder="correo@ejemplo.com" required>
                    </div>
                  </div>

                  <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                      <label for="contrasena" class="form-label fw-semibold">
                        Contraseña <span class="text-danger">*</span>
                      </label>
                      <div class="input-group">
                        <input type="password" id="contrasena" name="contrasena" class="form-control"
                               placeholder="Mín. 6 caracteres" required minlength="6">
                        <button type="button" class="btn btn-outline-secondary toggle-pwd"
                                data-target="contrasena" tabindex="-1">
                          <i class="bi bi-eye"></i>
                        </button>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <label for="confirmar" class="form-label fw-semibold">
                        Confirmar <span class="text-danger">*</span>
                      </label>
                      <div class="input-group">
                        <input type="password" id="confirmar" class="form-control"
                               placeholder="Repita la contraseña" required>
                        <button type="button" class="btn btn-outline-secondary toggle-pwd"
                                data-target="confirmar" tabindex="-1">
                          <i class="bi bi-eye"></i>
                        </button>
                      </div>
                      <div id="confirmarError" class="text-danger small mt-1" style="display:none;">
                        Las contraseñas no coinciden.
                      </div>
                    </div>
                  </div>

                  <div>
                    <label class="form-label fw-semibold">Rol <span class="text-danger">*</span></label>
                    <div class="row g-2">
                      <div class="col-4">
                        <label class="rol-card card h-100 text-center p-3 border-2 selected" for="rolUsuario">
                          <input type="radio" name="rol" id="rolUsuario" value="usuario" class="d-none" checked>
                          <i class="bi bi-person fs-2 mb-1 text-primary d-block"></i>
                          <div class="fw-semibold">Usuario</div>
                          <small class="text-muted">Acceso estándar</small>
                        </label>
                      </div>
                      <div class="col-4">
                        <label class="rol-card card h-100 text-center p-3 border-2" for="rolOperaciones">
                          <input type="radio" name="rol" id="rolOperaciones" value="operaciones" class="d-none">
                          <i class="bi bi-gear fs-2 mb-1 text-warning d-block"></i>
                          <div class="fw-semibold">Operaciones</div>
                          <small class="text-muted">Gestión y seguimiento</small>
                        </label>
                      </div>
                      <div class="col-4">
                        <label class="rol-card card h-100 text-center p-3 border-2" for="rolAdmin">
                          <input type="radio" name="rol" id="rolAdmin" value="admin" class="d-none">
                          <i class="bi bi-shield-check fs-2 mb-1 text-danger d-block"></i>
                          <div class="fw-semibold">Administrador</div>
                          <small class="text-muted">Acceso completo</small>
                        </label>
                      </div>
                    </div>
                  </div>

                </div>
                <div class="card-footer d-flex gap-2">
                  <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Guardar usuario
                  </button>
                  <a href="index.php?modulo=usuarios" class="btn btn-outline-secondary">
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
