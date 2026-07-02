<?php
/** @var array       $usuario */
/** @var array|null  $empresa */
/** @var array       $empresas */
$pageTitle   = 'Editar usuario — ZFPE';
$activePage  = 'empresas';
$pageStyles  = ['vista/assets/css/componentes.css'];
$pageScripts = ['vista/assets/js/editar-usuario-empresa.js'];

$v       = fn(string $k) => htmlspecialchars($_POST[$k] ?? $usuario[$k] ?? '');
$inicial = mb_strtoupper(mb_substr($usuario['nombre'] ?? 'U', 0, 1));
$back    = $empresa
    ? 'index.php?modulo=empresas&accion=ver&id=' . $empresa['id']
    : 'index.php?modulo=empresas&accion=usuarios';
?>
<?php require_once __DIR__ . '/../../parciales/cabecera.php'; ?>

<div class="app-wrapper">
  <?php require_once __DIR__ . '/../../parciales/navegacion.php'; ?>
  <?php require_once __DIR__ . '/../../parciales/menu_lateral.php'; ?>

  <main class="app-main">
    <div class="app-content-header encabezado-zf">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6"><h3 class="mb-0 titulo-zf">Editar usuario</h3></div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item"><a href="index.php?modulo=empresas">Empresas</a></li>
              <?php if ($empresa): ?>
                <li class="breadcrumb-item">
                  <a href="index.php?modulo=empresas&accion=ver&id=<?= $empresa['id'] ?>">
                    <?= htmlspecialchars($empresa['razon_social']) ?>
                  </a>
                </li>
              <?php else: ?>
                <li class="breadcrumb-item"><a href="index.php?modulo=empresas&accion=usuarios">Usuarios</a></li>
              <?php endif; ?>
              <li class="breadcrumb-item active">Editar</li>
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
              <div class="card-header d-flex align-items-center gap-3">
                <span class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-semibold flex-shrink-0"
                      style="width:42px;height:42px;font-size:1rem;">
                  <?= $inicial ?>
                </span>
                <div>
                  <h5 class="card-title mb-0"><?= htmlspecialchars($usuario['nombre']) ?></h5>
                  <?php if ($empresa): ?>
                    <small class="text-muted"><i class="bi bi-building me-1"></i><?= htmlspecialchars($empresa['razon_social']) ?></small>
                  <?php endif; ?>
                </div>
              </div>

              <form method="POST" action="index.php?modulo=empresas&accion=editar-usuario&id=<?= $usuario['id'] ?>">
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

                  <div class="mb-4">
                    <label for="empresa_id" class="form-label fw-semibold">
                      <i class="bi bi-building me-1 text-primary"></i>Empresa asignada
                    </label>
                    <select id="empresa_id" name="empresa_id" class="form-select">
                      <option value="">— Sin empresa —</option>
                      <?php
                      $empresaIdActual = (int) ($_POST['empresa_id'] ?? $usuario['empresa_id'] ?? 0);
                      foreach ($empresas as $emp):
                      ?>
                        <option value="<?= $emp['id'] ?>" <?= $empresaIdActual === (int) $emp['id'] ? 'selected' : '' ?>>
                          <?= htmlspecialchars($emp['razon_social']) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                    <div class="form-text">Puede reasignar este usuario a otra empresa o dejarlo sin empresa.</div>
                  </div>

                  <hr>
                  <p class="text-muted small mb-3">
                    <i class="bi bi-shield-lock me-1"></i>
                    Deje estos campos en blanco si no desea cambiar la contraseña.
                  </p>

                  <div class="mb-3">
                    <label for="contrasena" class="form-label fw-semibold">Nueva contraseña</label>
                    <div class="input-group">
                      <input type="password" id="contrasena" name="contrasena" class="form-control"
                             placeholder="Mínimo 8 caracteres" minlength="8">
                      <button type="button" class="btn btn-outline-secondary btn-toggle-pass" data-target="contrasena">
                        <i class="bi bi-eye"></i>
                      </button>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label for="contrasena2" class="form-label fw-semibold">Confirmar nueva contraseña</label>
                    <div class="input-group">
                      <input type="password" id="contrasena2" name="contrasena2" class="form-control"
                             placeholder="Repita la contraseña" minlength="8">
                      <button type="button" class="btn btn-outline-secondary btn-toggle-pass" data-target="contrasena2">
                        <i class="bi bi-eye"></i>
                      </button>
                    </div>
                    <div id="errorPass" class="text-danger small mt-1" style="display:none;">Las contraseñas no coinciden.</div>
                  </div>

                </div>
                <div class="card-footer d-flex gap-2">
                  <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Actualizar usuario
                  </button>
                  <a href="<?= $back ?>" class="btn btn-outline-secondary">
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
