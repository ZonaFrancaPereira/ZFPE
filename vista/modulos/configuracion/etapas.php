<?php
$pageTitle  = 'Etapas — ZFIP-E';
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
          <div class="col-sm-6"><h3 class="mb-0 titulo-zf">Etapas</h3></div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item"><a href="index.php?modulo=configuracion">Configuración</a></li>
              <li class="breadcrumb-item active">Etapas</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="app-content">
      <div class="container-fluid">

        <div class="card shadow-sm">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0">
              <i class="bi bi-diagram-3 me-2 text-success"></i>Etapas configuradas
            </h5>
            <a href="index.php?modulo=configuracion&accion=crear-etapa" class="btn btn-primary btn-sm">
              <i class="bi bi-plus-lg me-1"></i> Nueva etapa
            </a>
          </div>
          <div class="card-body p-0">
            <?php if (empty($etapas)): ?>
              <div class="text-center text-muted py-5">
                <i class="bi bi-diagram-3 fs-1 opacity-25 d-block mb-2"></i>
                No hay etapas configuradas aún.
                <a href="index.php?modulo=configuracion&accion=crear-etapa" class="d-block mt-2">Crear primera etapa</a>
              </div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th style="width:60px;" class="text-center">Orden</th>
                      <th>Fase</th>
                      <th>Nombre</th>
                      <th>Descripción</th>
                      <th class="text-center">Peso %</th>
                      <th class="text-center">Estado</th>
                      <th class="text-end">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($etapas as $e): ?>
                    <tr>
                      <td class="text-center">
                        <span class="badge text-bg-light border fw-bold text-muted"><?= $e['orden'] ?></span>
                      </td>
                      <td>
                        <?php if ($e['fase_nombre']): ?>
                          <span class="badge text-bg-danger bg-opacity-75"><?= htmlspecialchars($e['fase_nombre']) ?></span>
                        <?php else: ?>
                          <span class="text-muted small">—</span>
                        <?php endif; ?>
                      </td>
                      <td class="fw-semibold"><?= htmlspecialchars($e['nombre']) ?></td>
                      <td class="text-muted small"><?= htmlspecialchars($e['descripcion'] ?? '—') ?></td>
                      <td class="text-center">
                        <span class="badge text-bg-info"><?= number_format($e['peso_porcentual'], 1) ?>%</span>
                      </td>
                      <td class="text-center">
                        <?php if ($e['activo']): ?>
                          <span class="badge text-bg-success">Activa</span>
                        <?php else: ?>
                          <span class="badge text-bg-secondary">Inactiva</span>
                        <?php endif; ?>
                      </td>
                      <td class="text-end">
                        <a href="index.php?modulo=configuracion&accion=editar-etapa&id=<?= $e['id'] ?>"
                           class="btn btn-sm btn-outline-primary me-1">
                          <i class="bi bi-pencil"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger"
                                data-bs-toggle="modal" data-bs-target="#modalEliminar"
                                data-id="<?= $e['id'] ?>" data-nombre="<?= htmlspecialchars($e['nombre']) ?>">
                          <i class="bi bi-trash"></i>
                        </button>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </div>
        </div>

      </div>
    </div>
  </main>

  <?php require_once __DIR__ . '/../../parciales/pie.php'; ?>
</div>

<!-- Modal confirmar eliminación -->
<div class="modal fade" id="modalEliminar" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Eliminar etapa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        ¿Estás seguro que deseas eliminar la etapa <strong id="nombreEtapa"></strong>?
        Se eliminarán también todos los requisitos e ítems asociados a ella.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <form method="POST" id="formEliminar" class="m-0">
          <button type="submit" class="btn btn-danger">
            <i class="bi bi-trash me-1"></i> Eliminar
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('modalEliminar').addEventListener('show.bs.modal', function (e) {
  const btn = e.relatedTarget;
  document.getElementById('nombreEtapa').textContent = btn.dataset.nombre;
  document.getElementById('formEliminar').action =
    'index.php?modulo=configuracion&accion=eliminar-etapa&id=' + btn.dataset.id;
});
</script>
