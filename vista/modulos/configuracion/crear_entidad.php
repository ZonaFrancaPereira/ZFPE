<?php
$pageTitle  = 'Nueva entidad — ZFPE';
$activePage = 'configuracion';
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
          <div class="col-sm-6"><h3 class="mb-0 titulo-zf">Nueva entidad</h3></div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item"><a href="index.php?modulo=configuracion">Configuración</a></li>
              <li class="breadcrumb-item"><a href="index.php?modulo=configuracion&accion=entidades">Entidades</a></li>
              <li class="breadcrumb-item active">Nueva</li>
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
              <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-bank text-primary fs-5"></i>
                <h5 class="card-title mb-0">Datos de la entidad</h5>
              </div>

              <form method="POST" action="index.php?modulo=configuracion&accion=crear-entidad">
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
                      Nombre <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="nombre" name="nombre" class="form-control"
                           placeholder="Ej. DIAN, MINCIT, Usuario Operador" required autofocus
                           value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
                  </div>

                  <div class="mb-3">
                    <label for="descripcion" class="form-label fw-semibold">Descripción</label>
                    <textarea id="descripcion" name="descripcion" class="form-control" rows="3"
                              placeholder="Descripción opcional de la entidad"><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
                  </div>

                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1"
                           <?= !isset($_POST['activo']) || $_POST['activo'] ? 'checked' : '' ?>>
                    <label class="form-check-label fw-semibold" for="activo">Entidad activa</label>
                  </div>

                </div>
                <div class="card-footer d-flex gap-2">
                  <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Guardar entidad
                  </button>
                  <a href="index.php?modulo=configuracion&accion=entidades" class="btn btn-outline-secondary">
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
