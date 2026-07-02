<?php
$pageTitle  = 'Nuevo indicador — ZFIP-E';
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
          <div class="col-sm-6"><h3 class="mb-0 titulo-zf">Nuevo indicador</h3></div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item"><a href="index.php?modulo=configuracion">Configuración</a></li>
              <li class="breadcrumb-item"><a href="index.php?modulo=configuracion&accion=indicadores">Indicadores</a></li>
              <li class="breadcrumb-item active">Nuevo</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="app-content">
      <div class="container-fluid">
        <div class="row justify-content-center">
          <div class="col-lg-7">

            <?php if (!empty($_SESSION['flash_error'])): ?>
              <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-1"></i><?= htmlspecialchars($_SESSION['flash_error']) ?>
              </div>
              <?php unset($_SESSION['flash_error']); ?>
            <?php endif; ?>

            <div class="card shadow-sm">
              <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-graph-up me-2 text-primary"></i>Datos del indicador</h5>
              </div>
              <form method="POST" action="index.php?modulo=configuracion&accion=crear-indicador">
                <div class="card-body">

                  <div class="mb-3">
                    <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control" required autofocus
                           value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>"
                           placeholder="Ej. Número de empleados directos">
                  </div>

                  <div class="mb-3">
                    <label class="form-label fw-semibold">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="3"
                              placeholder="Descripción opcional del indicador"><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
                  </div>

                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label class="form-label fw-semibold">Unidad de medida</label>
                      <input type="text" name="unidad" class="form-control"
                             value="<?= htmlspecialchars($_POST['unidad'] ?? '') ?>"
                             placeholder="Ej. empleados, m², USD, %">
                    </div>
                    <div class="col-md-6 mb-3">
                      <label class="form-label fw-semibold">Meta</label>
                      <input type="number" name="meta" class="form-control" step="0.01"
                             value="<?= htmlspecialchars($_POST['meta'] ?? '') ?>"
                             placeholder="Valor objetivo">
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label class="form-label fw-semibold">Periodicidad <span class="text-danger">*</span></label>
                      <select name="periodicidad" class="form-select" required>
                        <?php foreach (['mensual' => 'Mensual', 'trimestral' => 'Trimestral', 'semestral' => 'Semestral', 'anual' => 'Anual'] as $val => $lbl): ?>
                          <option value="<?= $val ?>" <?= ($_POST['periodicidad'] ?? 'anual') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label class="form-label fw-semibold">Tipo de gráfica</label>
                      <select name="tipo_grafico" class="form-select">
                        <?php foreach (['linea' => 'Línea', 'barra' => 'Barras', 'area' => 'Área', 'radar' => 'Radar', 'torta' => 'Torta / Dona', 'combo' => 'Combo (barras + línea)'] as $val => $lbl): ?>
                          <option value="<?= $val ?>" <?= ($_POST['tipo_grafico'] ?? 'linea') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>

                  <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="comparativo_anual" id="chkComparativo" value="1"
                           <?= isset($_POST['comparativo_anual']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="chkComparativo">
                      Mostrar comparativo por año <small class="text-muted">(agrupa los datos por año para comparar períodos)</small>
                    </label>
                  </div>

                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="activo" id="chkActivo" value="1"
                           <?= !isset($_POST['nombre']) || isset($_POST['activo']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="chkActivo">Activo</label>
                  </div>

                </div>
                <div class="card-footer d-flex gap-2">
                  <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>Guardar indicador
                  </button>
                  <a href="index.php?modulo=configuracion&accion=indicadores" class="btn btn-outline-secondary">Cancelar</a>
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
