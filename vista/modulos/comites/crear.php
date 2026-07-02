<?php
/** @var array $empresas */
$pageTitle  = 'Nuevo comité — ZFIP-E';
$activePage = 'comites';
$pageStyles = ['vista/assets/css/componentes.css'];

$v = fn(string $k, string $def = '') => htmlspecialchars($_POST[$k] ?? $def);
?>
<?php require_once __DIR__ . '/../../parciales/cabecera.php'; ?>

<div class="app-wrapper">
  <?php require_once __DIR__ . '/../../parciales/navegacion.php'; ?>
  <?php require_once __DIR__ . '/../../parciales/menu_lateral.php'; ?>

  <main class="app-main">
    <div class="app-content-header encabezado-zf">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6"><h3 class="mb-0 titulo-zf">Nuevo comité</h3></div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item"><a href="index.php?modulo=comites">Comités</a></li>
              <li class="breadcrumb-item active">Nuevo</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="app-content">
      <div class="container-fluid">
        <div class="row justify-content-center">
          <div class="col-xl-7 col-lg-9">

            <form method="POST" action="index.php?modulo=comites&accion=crear">

              <?php if (!empty($_SESSION['flash_error'])): ?>
                <div class="alert alert-danger mb-3">
                  <i class="bi bi-exclamation-triangle me-1"></i>
                  <?= htmlspecialchars($_SESSION['flash_error']) ?>
                </div>
                <?php unset($_SESSION['flash_error']); ?>
              <?php endif; ?>

              <div class="card shadow-sm">
                <div class="card-header d-flex align-items-center gap-2">
                  <i class="bi bi-people-fill text-primary fs-5"></i>
                  <h5 class="card-title mb-0">Información del comité</h5>
                </div>
                <div class="card-body">

                  <div class="mb-3">
                    <label for="titulo" class="form-label fw-semibold">
                      Título <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="titulo" name="titulo" class="form-control" required autofocus
                           value="<?= $v('titulo') ?>" placeholder="Ej: Comité de seguimiento Q2 2026">
                  </div>

                  <div class="row g-3 mb-3">
                    <div class="col-md-6">
                      <label for="empresa_id" class="form-label fw-semibold">Empresa</label>
                      <select id="empresa_id" name="empresa_id" class="form-select">
                        <option value="">— Sin empresa específica —</option>
                        <?php foreach ($empresas as $e): ?>
                          <option value="<?= $e['id'] ?>" <?= $v('empresa_id') == $e['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($e['razon_social']) ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="col-md-6">
                      <label for="tipo" class="form-label fw-semibold">Tipo</label>
                      <select id="tipo" name="tipo" class="form-select">
                        <option value="seguimiento"    <?= $v('tipo', 'seguimiento') === 'seguimiento'    ? 'selected' : '' ?>>Seguimiento</option>
                        <option value="aprobacion"     <?= $v('tipo') === 'aprobacion'     ? 'selected' : '' ?>>Aprobación</option>
                        <option value="revision"       <?= $v('tipo') === 'revision'       ? 'selected' : '' ?>>Revisión</option>
                        <option value="extraordinario" <?= $v('tipo') === 'extraordinario' ? 'selected' : '' ?>>Extraordinario</option>
                      </select>
                    </div>
                  </div>

                  <div class="row g-3 mb-3">
                    <div class="col-md-6">
                      <label for="fecha" class="form-label fw-semibold">
                        Fecha <span class="text-danger">*</span>
                      </label>
                      <input type="datetime-local" id="fecha" name="fecha" class="form-control" required
                             value="<?= $v('fecha') ?>">
                    </div>
                    <div class="col-md-6">
                      <label for="estado" class="form-label fw-semibold">Estado</label>
                      <select id="estado" name="estado" class="form-select">
                        <option value="programado" <?= $v('estado', 'programado') === 'programado' ? 'selected' : '' ?>>Programado</option>
                        <option value="realizado"  <?= $v('estado') === 'realizado'  ? 'selected' : '' ?>>Realizado</option>
                        <option value="cancelado"  <?= $v('estado') === 'cancelado'  ? 'selected' : '' ?>>Cancelado</option>
                      </select>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label for="lugar" class="form-label fw-semibold">Lugar</label>
                    <input type="text" id="lugar" name="lugar" class="form-control"
                           value="<?= $v('lugar') ?>" placeholder="Sala de reuniones, virtual, etc.">
                  </div>

                  <div>
                    <label for="descripcion" class="form-label fw-semibold">Descripción / Agenda</label>
                    <textarea id="descripcion" name="descripcion" class="form-control" rows="3"
                              placeholder="Temas a tratar en el comité..."><?= $v('descripcion') ?></textarea>
                  </div>

                </div>
                <div class="card-footer d-flex gap-2">
                  <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Registrar comité
                  </button>
                  <a href="index.php?modulo=comites" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg me-1"></i> Cancelar
                  </a>
                </div>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>
  </main>

  <?php require_once __DIR__ . '/../../parciales/pie.php'; ?>
</div>
