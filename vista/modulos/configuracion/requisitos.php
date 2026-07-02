<?php
$pageTitle  = 'Requisitos — ZFIP-E';
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
          <div class="col-sm-6"><h3 class="mb-0 titulo-zf">Requisitos</h3></div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item"><a href="index.php?modulo=configuracion">Configuración</a></li>
              <li class="breadcrumb-item active">Requisitos</li>
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
              <i class="bi bi-list-check me-2 text-warning"></i>Requisitos configurados
            </h5>
            <a href="index.php?modulo=configuracion&accion=crear-requisito" class="btn btn-primary btn-sm">
              <i class="bi bi-plus-lg me-1"></i> Nuevo requisito
            </a>
          </div>
          <div class="card-body p-0">
            <?php if (empty($requisitos)): ?>
              <div class="text-center text-muted py-5">
                <i class="bi bi-list-check fs-1 opacity-25 d-block mb-2"></i>
                No hay requisitos configurados aún.
                <a href="index.php?modulo=configuracion&accion=crear-requisito" class="d-block mt-2">Crear primer requisito</a>
              </div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Etapa</th>
                      <th>Entidad</th>
                      <th>Nombre</th>
                      <th class="text-center">Obligatorio</th>
                      <th class="text-center">Doc.</th>
                      <th class="text-center">Aprob.</th>
                      <th class="text-center">Peso %</th>
                      <th class="text-center">Estado</th>
                      <th class="text-end">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $etapaActual = null;
                    foreach ($requisitos as $r):
                      if ($etapaActual !== $r['etapa_nombre']):
                        $etapaActual = $r['etapa_nombre'];
                    ?>
                    <tr class="table-light">
                      <td colspan="9" class="fw-semibold text-muted small py-2 ps-3">
                        <i class="bi bi-diagram-3 me-1"></i><?= htmlspecialchars($etapaActual) ?>
                      </td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                      <td></td>
                      <td class="small">
                        <?php if ($r['entidad_nombre']): ?>
                          <span class="badge text-bg-light border text-muted"><?= htmlspecialchars($r['entidad_nombre']) ?></span>
                        <?php else: ?>
                          <span class="text-muted">—</span>
                        <?php endif; ?>
                      </td>
                      <td class="fw-semibold"><?= htmlspecialchars($r['nombre']) ?></td>
                      <td class="text-center">
                        <?= $r['obligatorio'] ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-dash text-muted"></i>' ?>
                      </td>
                      <td class="text-center">
                        <?= $r['requiere_documento'] ? '<i class="bi bi-check-circle-fill text-primary"></i>' : '<i class="bi bi-dash text-muted"></i>' ?>
                      </td>
                      <td class="text-center">
                        <?= $r['requiere_aprobacion'] ? '<i class="bi bi-check-circle-fill text-warning"></i>' : '<i class="bi bi-dash text-muted"></i>' ?>
                      </td>
                      <td class="text-center">
                        <span class="badge text-bg-info"><?= number_format($r['peso_porcentual'], 1) ?>%</span>
                      </td>
                      <td class="text-center">
                        <?= $r['activo'] ? '<span class="badge text-bg-success">Activo</span>' : '<span class="badge text-bg-secondary">Inactivo</span>' ?>
                      </td>
                      <td class="text-end">
                        <a href="index.php?modulo=configuracion&accion=editar-requisito&id=<?= $r['id'] ?>"
                           class="btn btn-sm btn-outline-primary me-1">
                          <i class="bi bi-pencil"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger"
                                data-bs-toggle="modal" data-bs-target="#modalEliminar"
                                data-id="<?= $r['id'] ?>" data-nombre="<?= htmlspecialchars($r['nombre']) ?>">
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

<div class="modal fade" id="modalEliminar" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Eliminar requisito</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        ¿Estás seguro que deseas eliminar el requisito <strong id="nombreRequisito"></strong>?
        Se eliminarán también todos sus ítems asociados.
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
  document.getElementById('nombreRequisito').textContent = btn.dataset.nombre;
  document.getElementById('formEliminar').action =
    'index.php?modulo=configuracion&accion=eliminar-requisito&id=' + btn.dataset.id;
});
</script>
