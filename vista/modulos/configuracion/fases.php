<?php
/** @var array $fases */
$pageTitle  = 'Fases — ZFIP-E';
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
          <div class="col-sm-6"><h3 class="mb-0 titulo-zf">Fases del proyecto</h3></div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item"><a href="index.php?modulo=configuracion">Configuración</a></li>
              <li class="breadcrumb-item active">Fases</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="app-content">
      <div class="container-fluid">

        <div class="alert alert-info small">
          <i class="bi bi-info-circle-fill me-2"></i>
          Las <strong>fases</strong> agrupan las etapas. Ejemplo: la fase <em>"Etapa Preoperativa"</em> puede contener las etapas <em>Garantía</em>, <em>Cerramiento</em>, <em>Control de obra</em>, etc.
        </div>

        <div class="card shadow-sm">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0">
              <i class="bi bi-collection me-2 text-primary"></i>Fases configuradas
            </h5>
            <a href="index.php?modulo=configuracion&accion=crear-fase" class="btn btn-primary btn-sm">
              <i class="bi bi-plus-lg me-1"></i> Nueva fase
            </a>
          </div>
          <div class="card-body p-0">
            <?php if (empty($fases)): ?>
              <div class="text-center text-muted py-5">
                <i class="bi bi-collection fs-1 opacity-25 d-block mb-2"></i>
                No hay fases configuradas aún.<br>
                <a href="index.php?modulo=configuracion&accion=crear-fase" class="mt-2 d-inline-block">Crear primera fase</a>
              </div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th class="text-center" style="width:60px;">Orden</th>
                      <th>Nombre de la fase</th>
                      <th>Descripción</th>
                      <th class="text-center">Etapas</th>
                      <th class="text-center">Estado</th>
                      <th class="text-end">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($fases as $f): ?>
                    <tr>
                      <td class="text-center">
                        <span class="badge text-bg-light border fw-bold text-muted"><?= $f['orden'] ?></span>
                      </td>
                      <td>
                        <div class="fw-semibold"><?= htmlspecialchars($f['nombre']) ?></div>
                      </td>
                      <td class="text-muted small"><?= htmlspecialchars($f['descripcion'] ?? '—') ?></td>
                      <td class="text-center">
                        <a href="index.php?modulo=configuracion&accion=etapas"
                           class="badge text-bg-primary text-decoration-none">
                          <?= (int)$f['total_etapas'] ?> etapa<?= $f['total_etapas'] != 1 ? 's' : '' ?>
                        </a>
                      </td>
                      <td class="text-center">
                        <?= $f['activo']
                          ? '<span class="badge text-bg-success">Activa</span>'
                          : '<span class="badge text-bg-secondary">Inactiva</span>' ?>
                      </td>
                      <td class="text-end">
                        <a href="index.php?modulo=configuracion&accion=editar-fase&id=<?= $f['id'] ?>"
                           class="btn btn-sm btn-outline-primary me-1">
                          <i class="bi bi-pencil"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger"
                                data-bs-toggle="modal" data-bs-target="#modalEliminar"
                                data-id="<?= $f['id'] ?>" data-nombre="<?= htmlspecialchars($f['nombre']) ?>">
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
        <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Eliminar fase</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        ¿Eliminar la fase <strong id="nombreFase"></strong>?
        Las etapas que pertenecían a esta fase quedarán sin fase asignada.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <form method="POST" id="formEliminar" class="m-0">
          <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i> Eliminar</button>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
document.getElementById('modalEliminar').addEventListener('show.bs.modal', function(e) {
  const btn = e.relatedTarget;
  document.getElementById('nombreFase').textContent = btn.dataset.nombre;
  document.getElementById('formEliminar').action = 'index.php?modulo=configuracion&accion=eliminar-fase&id=' + btn.dataset.id;
});
</script>
