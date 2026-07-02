<?php
$pageTitle  = 'Nuevo requisito — ZFIP-E';
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
          <div class="col-sm-6"><h3 class="mb-0 titulo-zf">Nuevo requisito</h3></div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item"><a href="index.php?modulo=configuracion">Configuración</a></li>
              <li class="breadcrumb-item"><a href="index.php?modulo=configuracion&accion=requisitos">Requisitos</a></li>
              <li class="breadcrumb-item active">Nuevo</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="app-content">
      <div class="container-fluid">
        <div class="row justify-content-center">
          <div class="col-lg-9 col-xl-8">

            <form method="POST" action="index.php?modulo=configuracion&accion=crear-requisito">
              <?php if (!empty($_SESSION['flash_error'])): ?>
                <div class="alert alert-danger">
                  <i class="bi bi-exclamation-triangle me-1"></i>
                  <?= htmlspecialchars($_SESSION['flash_error']) ?>
                </div>
                <?php unset($_SESSION['flash_error']); ?>
              <?php endif; ?>

              <!-- Información básica -->
              <div class="card shadow-sm mb-3">
                <div class="card-header d-flex align-items-center gap-2">
                  <i class="bi bi-list-check text-warning fs-5"></i>
                  <h5 class="card-title mb-0">Información básica</h5>
                </div>
                <div class="card-body">

                  <div class="mb-3">
                    <label for="nombre" class="form-label fw-semibold">
                      Nombre del requisito <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required autofocus
                           placeholder="Ej. Garantía, Evaluación DIAN, Manual operativo"
                           value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
                  </div>

                  <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                      <label for="etapa_id" class="form-label fw-semibold">
                        Etapa <span class="text-danger">*</span>
                      </label>
                      <select id="etapa_id" name="etapa_id" class="form-select" required>
                        <option value="">— Selecciona una etapa —</option>
                        <?php foreach ($etapas as $e): ?>
                          <option value="<?= $e['id'] ?>" <?= ($_POST['etapa_id'] ?? '') == $e['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($e['nombre']) ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="col-sm-6">
                      <label for="entidad_id" class="form-label fw-semibold">Entidad responsable</label>
                      <select id="entidad_id" name="entidad_id" class="form-select">
                        <option value="">— Sin entidad específica —</option>
                        <?php foreach ($entidades as $en): ?>
                          <option value="<?= $en['id'] ?>" <?= ($_POST['entidad_id'] ?? '') == $en['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($en['nombre']) ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>

                  <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                      <label for="responsable" class="form-label fw-semibold">Responsable</label>
                      <input type="text" id="responsable" name="responsable" class="form-control"
                             placeholder="Persona o área responsable"
                             value="<?= htmlspecialchars($_POST['responsable'] ?? '') ?>">
                    </div>
                    <div class="col-sm-6">
                      <label for="peso_porcentual" class="form-label fw-semibold">Peso porcentual</label>
                      <div class="input-group">
                        <input type="number" id="peso_porcentual" name="peso_porcentual"
                               class="form-control" min="0" max="100" step="0.01"
                               value="<?= htmlspecialchars($_POST['peso_porcentual'] ?? '0') ?>">
                        <span class="input-group-text">%</span>
                      </div>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label for="descripcion" class="form-label fw-semibold">Descripción</label>
                    <textarea id="descripcion" name="descripcion" class="form-control" rows="3"
                              placeholder="Descripción detallada del requisito"><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
                  </div>

                </div>
              </div>

              <!-- Configuración del requisito -->
              <div class="card shadow-sm mb-3">
                <div class="card-header d-flex align-items-center gap-2">
                  <i class="bi bi-toggles text-primary fs-5"></i>
                  <h5 class="card-title mb-0">Configuración</h5>
                </div>
                <div class="card-body">
                  <div class="row g-3">
                    <div class="col-sm-6">
                      <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="obligatorio" name="obligatorio" value="1"
                               <?= !isset($_POST['obligatorio']) || $_POST['obligatorio'] ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="obligatorio">Obligatorio</label>
                        <div class="text-muted small">Bloquea el avance si no se cumple.</div>
                      </div>
                      <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="requiere_documento" name="requiere_documento" value="1"
                               <?= isset($_POST['requiere_documento']) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="requiere_documento">Requiere documento soporte</label>
                        <div class="text-muted small">Se debe adjuntar un archivo.</div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="requiere_fecha_vencimiento" name="requiere_fecha_vencimiento" value="1"
                               <?= isset($_POST['requiere_fecha_vencimiento']) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="requiere_fecha_vencimiento">Requiere fecha de vencimiento</label>
                        <div class="text-muted small">Se registra una fecha límite.</div>
                      </div>
                      <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="requiere_aprobacion" name="requiere_aprobacion" value="1"
                               <?= isset($_POST['requiere_aprobacion']) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="requiere_aprobacion">Requiere aprobación</label>
                        <div class="text-muted small">Debe ser validado por operaciones.</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Alertas y acciones -->
              <div class="card shadow-sm mb-3">
                <div class="card-header d-flex align-items-center gap-2">
                  <i class="bi bi-bell text-danger fs-5"></i>
                  <h5 class="card-title mb-0">Alertas y acciones recomendadas</h5>
                </div>
                <div class="card-body">
                  <div class="mb-3">
                    <label for="alerta_asociada" class="form-label fw-semibold">Alerta asociada</label>
                    <input type="text" id="alerta_asociada" name="alerta_asociada" class="form-control"
                           placeholder="Ej. Vencimiento próximo, Documento faltante"
                           value="<?= htmlspecialchars($_POST['alerta_asociada'] ?? '') ?>">
                  </div>
                  <div class="mb-3">
                    <label for="accion_recomendada" class="form-label fw-semibold">Acción recomendada</label>
                    <textarea id="accion_recomendada" name="accion_recomendada" class="form-control" rows="2"
                              placeholder="Ej. Contactar a la DIAN para radicación"><?= htmlspecialchars($_POST['accion_recomendada'] ?? '') ?></textarea>
                  </div>
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1"
                           <?= !isset($_POST['activo']) || $_POST['activo'] ? 'checked' : '' ?>>
                    <label class="form-check-label fw-semibold" for="activo">Requisito activo</label>
                  </div>
                </div>
              </div>

              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-check-lg me-1"></i> Guardar requisito
                </button>
                <a href="index.php?modulo=configuracion&accion=requisitos" class="btn btn-outline-secondary">
                  <i class="bi bi-x-lg me-1"></i> Cancelar
                </a>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>
  </main>

  <?php require_once __DIR__ . '/../../parciales/pie.php'; ?>
</div>
