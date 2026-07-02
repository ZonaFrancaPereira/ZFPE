<?php
/** @var array $indicador */
$pageTitle  = 'Editar indicador — ZFPE';
$activePage = 'configuracion';
$pageStyles = ['vista/assets/css/componentes.css'];
$v = fn(string $k) => htmlspecialchars($_POST[$k] ?? $indicador[$k] ?? '');
?>
<?php require_once __DIR__ . '/../../parciales/cabecera.php'; ?>

<div class="app-wrapper">
  <?php require_once __DIR__ . '/../../parciales/navegacion.php'; ?>
  <?php require_once __DIR__ . '/../../parciales/menu_lateral.php'; ?>

  <main class="app-main">
    <div class="app-content-header encabezado-zf">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6"><h3 class="mb-0 titulo-zf">Editar indicador</h3></div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item"><a href="index.php?modulo=configuracion">Configuración</a></li>
              <li class="breadcrumb-item"><a href="index.php?modulo=configuracion&accion=indicadores">Indicadores</a></li>
              <li class="breadcrumb-item active">Editar</li>
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
                <h5 class="card-title mb-0"><i class="bi bi-graph-up me-2 text-primary"></i><?= $v('nombre') ?></h5>
              </div>
              <form method="POST" action="index.php?modulo=configuracion&accion=editar-indicador&id=<?= $indicador['id'] ?>">
                <div class="card-body">

                  <div class="mb-3">
                    <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control" required
                           value="<?= $v('nombre') ?>">
                  </div>

                  <div class="mb-3">
                    <label class="form-label fw-semibold">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="3"><?= $v('descripcion') ?></textarea>
                  </div>

                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label class="form-label fw-semibold">Unidad de medida</label>
                      <input type="text" name="unidad" class="form-control"
                             value="<?= $v('unidad') ?>"
                             placeholder="Ej. empleados, m², USD, %">
                    </div>
                    <div class="col-md-6 mb-3">
                      <label class="form-label fw-semibold">Meta</label>
                      <input type="number" name="meta" class="form-control" step="0.01"
                             value="<?= $v('meta') ?>">
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label class="form-label fw-semibold">Periodicidad <span class="text-danger">*</span></label>
                      <select name="periodicidad" class="form-select" required>
                        <?php $periodoActual = $_POST['periodicidad'] ?? $indicador['periodicidad'] ?? 'anual';
                        foreach (['mensual' => 'Mensual', 'trimestral' => 'Trimestral', 'semestral' => 'Semestral', 'anual' => 'Anual'] as $val => $lbl): ?>
                          <option value="<?= $val ?>" <?= $periodoActual === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label class="form-label fw-semibold">Tipo de gráfica</label>
                      <select name="tipo_grafico" class="form-select">
                        <?php $graficoActual = $_POST['tipo_grafico'] ?? $indicador['tipo_grafico'] ?? 'linea';
                        foreach (['linea' => 'Línea', 'barra' => 'Barras', 'area' => 'Área', 'radar' => 'Radar', 'torta' => 'Torta / Dona', 'combo' => 'Combo (barras + línea)'] as $val => $lbl): ?>
                          <option value="<?= $val ?>" <?= $graficoActual === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>

                  <?php $compActual = isset($_POST['comparativo_anual']) ? (bool)$_POST['comparativo_anual'] : (bool)($indicador['comparativo_anual'] ?? 0); ?>
                  <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="comparativo_anual" id="chkComparativo" value="1"
                           <?= $compActual ? 'checked' : '' ?>>
                    <label class="form-check-label" for="chkComparativo">
                      Mostrar comparativo por año <small class="text-muted">(agrupa los datos por año para comparar períodos)</small>
                    </label>
                  </div>

                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="activo" id="chkActivo" value="1"
                           <?= (int)($indicador['activo'] ?? 1) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="chkActivo">Activo</label>
                  </div>

                </div>
                <div class="card-footer d-flex gap-2">
                  <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>Actualizar indicador
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
