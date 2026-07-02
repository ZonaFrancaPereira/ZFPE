<?php
$pageTitle  = 'Nuevo ítem — ZFIP-E';
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
          <div class="col-sm-6"><h3 class="mb-0 titulo-zf">Nuevo ítem</h3></div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item"><a href="index.php?modulo=configuracion">Configuración</a></li>
              <li class="breadcrumb-item"><a href="index.php?modulo=configuracion&accion=items">Ítems</a></li>
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
              <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-ui-checks text-info fs-5"></i>
                <h5 class="card-title mb-0">Datos del ítem</h5>
              </div>

              <form method="POST" action="index.php?modulo=configuracion&accion=crear-item">
                <div class="card-body">

                  <?php if (!empty($_SESSION['flash_error'])): ?>
                    <div class="alert alert-danger">
                      <i class="bi bi-exclamation-triangle me-1"></i>
                      <?= htmlspecialchars($_SESSION['flash_error']) ?>
                    </div>
                    <?php unset($_SESSION['flash_error']); ?>
                  <?php endif; ?>

                  <div class="mb-3">
                    <label for="requisito_id" class="form-label fw-semibold">
                      Requisito <span class="text-danger">*</span>
                    </label>
                    <select id="requisito_id" name="requisito_id" class="form-select" required>
                      <option value="">— Selecciona un requisito —</option>
                      <?php
                      $etapaGrupo = null;
                      foreach ($requisitos as $r):
                        if ($etapaGrupo !== $r['etapa_nombre']):
                          if ($etapaGrupo !== null) echo '</optgroup>';
                          $etapaGrupo = $r['etapa_nombre'];
                          echo '<optgroup label="' . htmlspecialchars($etapaGrupo) . '">';
                        endif;
                        $seleccionado = ($_POST['requisito_id'] ?? $requisito_id_pre ?? '') == $r['id'] ? 'selected' : '';
                      ?>
                        <option value="<?= $r['id'] ?>" <?= $seleccionado ?>>
                          <?= htmlspecialchars($r['nombre']) ?>
                        </option>
                      <?php endforeach; ?>
                      <?php if ($etapaGrupo !== null) echo '</optgroup>'; ?>
                    </select>
                  </div>

                  <div class="mb-3">
                    <label for="nombre" class="form-label fw-semibold">
                      Nombre del ítem <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required autofocus
                           placeholder="Ej. Radicación virtual realizada, Documento adjunto"
                           value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
                  </div>

                  <div class="mb-3">
                    <label for="descripcion" class="form-label fw-semibold">Descripción</label>
                    <textarea id="descripcion" name="descripcion" class="form-control" rows="2"
                              placeholder="Descripción opcional"><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
                  </div>

                  <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                      <label for="orden" class="form-label fw-semibold">Orden</label>
                      <input type="number" id="orden" name="orden" class="form-control" min="0"
                             value="<?= htmlspecialchars($_POST['orden'] ?? $siguienteOrden ?? 1) ?>">
                    </div>
                    <div class="col-sm-6 d-flex flex-column justify-content-end gap-2">
                      <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="obligatorio" name="obligatorio" value="1"
                               <?= !isset($_POST['obligatorio']) || $_POST['obligatorio'] ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="obligatorio">Obligatorio</label>
                      </div>
                      <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1"
                               <?= !isset($_POST['activo']) || $_POST['activo'] ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="activo">Activo</label>
                      </div>
                    </div>
                  </div>

                </div>
                <div class="card-footer d-flex gap-2">
                  <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Guardar ítem
                  </button>
                  <a href="index.php?modulo=configuracion&accion=items" class="btn btn-outline-secondary">
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
