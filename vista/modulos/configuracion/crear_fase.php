<?php
$pageTitle  = 'Nueva fase — ZFPE';
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
          <div class="col-sm-6"><h3 class="mb-0 titulo-zf">Nueva fase</h3></div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item"><a href="index.php?modulo=configuracion">Configuración</a></li>
              <li class="breadcrumb-item"><a href="index.php?modulo=configuracion&accion=fases">Fases</a></li>
              <li class="breadcrumb-item active">Nueva</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="app-content">
      <div class="container-fluid">
        <div class="row justify-content-center">
          <div class="col-lg-6">

            <?php if (!empty($error)): ?>
              <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="card shadow-sm">
              <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-collection me-2 text-primary"></i>Datos de la fase</h5>
              </div>
              <div class="card-body">
                <form method="POST" action="index.php?modulo=configuracion&accion=crear-fase">

                  <div class="mb-3">
                    <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control"
                           value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>"
                           placeholder="Ej: Etapa Preoperativa" required maxlength="150">
                    <div class="form-text">Nombre identificador de la fase (ej: "Etapa Preoperativa", "Etapa Operativa").</div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label fw-semibold">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="2"
                              placeholder="Descripción opcional..."><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
                  </div>

                  <div class="mb-3">
                    <label class="form-label fw-semibold">Orden</label>
                    <input type="number" name="orden" class="form-control" style="max-width:120px;"
                           value="<?= htmlspecialchars($_POST['orden'] ?? '0') ?>" min="0">
                    <div class="form-text">Número que determina el orden de aparición (menor = primero).</div>
                  </div>

                  <div class="mb-4">
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" name="activo" id="chkActivo" value="1"
                             <?= (!isset($_POST['activo']) || !empty($_POST['activo'])) ? 'checked' : '' ?>>
                      <label class="form-check-label" for="chkActivo">Fase activa</label>
                    </div>
                  </div>

                  <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                      <i class="bi bi-check-lg me-1"></i> Crear fase
                    </button>
                    <a href="index.php?modulo=configuracion&accion=fases" class="btn btn-outline-secondary">
                      Cancelar
                    </a>
                  </div>

                </form>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </main>
  <?php require_once __DIR__ . '/../../parciales/pie.php'; ?>
</div>
