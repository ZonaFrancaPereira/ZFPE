<?php
/** @var array $comite */
/** @var array $empresas */
$pageTitle  = 'Editar comité — ZFPE';
$activePage = 'comites';
$pageStyles = ['vista/assets/css/componentes.css'];

$v = fn(string $k) => htmlspecialchars($_POST[$k] ?? $comite[$k] ?? '');
?>
<?php require_once __DIR__ . '/../../parciales/cabecera.php'; ?>

<div class="app-wrapper">
  <?php require_once __DIR__ . '/../../parciales/navegacion.php'; ?>
  <?php require_once __DIR__ . '/../../parciales/menu_lateral.php'; ?>

  <main class="app-main">
    <div class="app-content-header encabezado-zf">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6"><h3 class="mb-0 titulo-zf">Editar comité</h3></div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item"><a href="index.php?modulo=comites">Comités</a></li>
              <li class="breadcrumb-item"><a href="index.php?modulo=comites&accion=ver&id=<?= $comite['id'] ?>">Detalle</a></li>
              <li class="breadcrumb-item active">Editar</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="app-content">
      <div class="container-fluid">
        <div class="row justify-content-center">
          <div class="col-xl-7 col-lg-9">

            <form method="POST" action="index.php?modulo=comites&accion=editar&id=<?= $comite['id'] ?>">

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
                  <div>
                    <h5 class="card-title mb-0"><?= htmlspecialchars($comite['titulo']) ?></h5>
                    <small class="text-muted">ID #<?= $comite['id'] ?></small>
                  </div>
                </div>
                <div class="card-body">

                  <div class="mb-3">
                    <label for="titulo" class="form-label fw-semibold">
                      Título <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="titulo" name="titulo" class="form-control" required autofocus
                           value="<?= $v('titulo') ?>">
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
                        <?php foreach (['seguimiento' => 'Seguimiento', 'aprobacion' => 'Aprobación', 'revision' => 'Revisión', 'extraordinario' => 'Extraordinario'] as $val => $lbl): ?>
                          <option value="<?= $val ?>" <?= $v('tipo') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>

                  <div class="row g-3 mb-3">
                    <div class="col-md-6">
                      <label for="fecha" class="form-label fw-semibold">
                        Fecha <span class="text-danger">*</span>
                      </label>
                      <input type="datetime-local" id="fecha" name="fecha" class="form-control" required
                             value="<?= htmlspecialchars(str_replace(' ', 'T', $_POST['fecha'] ?? $comite['fecha'] ?? '')) ?>">
                    </div>
                    <div class="col-md-6">
                      <label for="estado" class="form-label fw-semibold">Estado</label>
                      <select id="estado" name="estado" class="form-select">
                        <?php foreach (['programado' => 'Programado', 'realizado' => 'Realizado', 'cancelado' => 'Cancelado'] as $val => $lbl): ?>
                          <option value="<?= $val ?>" <?= $v('estado') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label for="lugar" class="form-label fw-semibold">Lugar</label>
                    <input type="text" id="lugar" name="lugar" class="form-control" value="<?= $v('lugar') ?>">
                  </div>

                  <div>
                    <label for="descripcion" class="form-label fw-semibold">Descripción / Agenda</label>
                    <textarea id="descripcion" name="descripcion" class="form-control" rows="3"><?= $v('descripcion') ?></textarea>
                  </div>

                </div>
                <div class="card-footer d-flex gap-2">
                  <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Guardar cambios
                  </button>
                  <a href="index.php?modulo=comites&accion=ver&id=<?= $comite['id'] ?>" class="btn btn-outline-secondary">
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
