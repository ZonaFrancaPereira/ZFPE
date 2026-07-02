<?php
/** @var array $etapa */
/** @var array $fases */
$pageTitle  = 'Editar etapa — ZFIP-E';
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
          <div class="col-sm-6"><h3 class="mb-0 titulo-zf">Editar etapa</h3></div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item"><a href="index.php?modulo=configuracion">Configuración</a></li>
              <li class="breadcrumb-item"><a href="index.php?modulo=configuracion&accion=etapas">Etapas</a></li>
              <li class="breadcrumb-item active">Editar</li>
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
                <i class="bi bi-diagram-3 text-success fs-5"></i>
                <div>
                  <h5 class="card-title mb-0"><?= htmlspecialchars($etapa['nombre']) ?></h5>
                  <small class="text-muted">ID #<?= $etapa['id'] ?> · Orden <?= $etapa['orden'] ?></small>
                </div>
              </div>

              <form method="POST" action="index.php?modulo=configuracion&accion=editar-etapa&id=<?= $etapa['id'] ?>">
                <div class="card-body">

                  <?php if (!empty($_SESSION['flash_error'])): ?>
                    <div class="alert alert-danger">
                      <i class="bi bi-exclamation-triangle me-1"></i>
                      <?= htmlspecialchars($_SESSION['flash_error']) ?>
                    </div>
                    <?php unset($_SESSION['flash_error']); ?>
                  <?php endif; ?>

                  <div class="mb-3">
                    <label for="fase_id" class="form-label fw-semibold">Fase</label>
                    <select id="fase_id" name="fase_id" class="form-select">
                      <option value="">— Sin fase asignada —</option>
                      <?php
                        $selectedFase = $_POST['fase_id'] ?? $etapa['fase_id'] ?? '';
                        foreach ($fases as $f):
                      ?>
                        <option value="<?= $f['id'] ?>"
                          <?= $selectedFase == $f['id'] ? 'selected' : '' ?>>
                          <?= htmlspecialchars($f['nombre']) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <div class="mb-3">
                    <label for="nombre" class="form-label fw-semibold">
                      Nombre <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required autofocus
                           value="<?= htmlspecialchars($_POST['nombre'] ?? $etapa['nombre']) ?>">
                  </div>

                  <div class="mb-3">
                    <label for="descripcion" class="form-label fw-semibold">Descripción</label>
                    <textarea id="descripcion" name="descripcion" class="form-control" rows="3"><?= htmlspecialchars($_POST['descripcion'] ?? $etapa['descripcion'] ?? '') ?></textarea>
                  </div>

                  <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                      <label for="orden" class="form-label fw-semibold">Orden</label>
                      <input type="number" id="orden" name="orden" class="form-control" min="0"
                             value="<?= htmlspecialchars($_POST['orden'] ?? $etapa['orden']) ?>">
                      <small class="text-muted">Define el orden en la línea de tiempo.</small>
                    </div>
                    <div class="col-sm-6">
                      <label for="peso_porcentual" class="form-label fw-semibold">Peso porcentual</label>
                      <div class="input-group">
                        <input type="number" id="peso_porcentual" name="peso_porcentual"
                               class="form-control" min="0" max="100" step="0.01"
                               value="<?= htmlspecialchars($_POST['peso_porcentual'] ?? $etapa['peso_porcentual']) ?>">
                        <span class="input-group-text">%</span>
                      </div>
                      <small class="text-muted">Peso dentro del avance total del proyecto.</small>
                    </div>
                  </div>

                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1"
                           <?= ($_POST['activo'] ?? $etapa['activo']) ? 'checked' : '' ?>>
                    <label class="form-check-label fw-semibold" for="activo">Etapa activa</label>
                  </div>

                </div>
                <div class="card-footer d-flex gap-2">
                  <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Actualizar etapa
                  </button>
                  <a href="index.php?modulo=configuracion&accion=etapas" class="btn btn-outline-secondary">
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
