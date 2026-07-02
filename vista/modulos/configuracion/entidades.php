<?php
$pageTitle  = 'Entidades — ZFIP-E';
$activePage = 'configuracion';
$pageStyles = ['vista/assets/css/componentes.css'];
?>
<?php require_once __DIR__ . '/../../parciales/cabecera.php'; ?>

<?php if (!empty($_SESSION['flash_success'])): ?>
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1100;">
  <div class="toast align-items-center text-bg-success border-0 show" role="alert">
    <div class="d-flex">
      <div class="toast-body"><i class="bi bi-check-circle me-1"></i><?= htmlspecialchars($_SESSION['flash_success']) ?></div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>
<?php unset($_SESSION['flash_success']); endif; ?>

<div class="app-wrapper">
  <?php require_once __DIR__ . '/../../parciales/navegacion.php'; ?>
  <?php require_once __DIR__ . '/../../parciales/menu_lateral.php'; ?>

  <main class="app-main">
    <div class="app-content-header encabezado-zf">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6"><h3 class="mb-0 titulo-zf">Entidades</h3></div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item"><a href="index.php?modulo=configuracion">Configuración</a></li>
              <li class="breadcrumb-item active">Entidades</li>
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
              <i class="bi bi-bank me-2 text-primary"></i>Entidades registradas
            </h5>
            <a href="index.php?modulo=configuracion&accion=crear-entidad" class="btn btn-primary btn-sm">
              <i class="bi bi-plus-lg me-1"></i> Nueva entidad
            </a>
          </div>
          <div class="card-body p-0">
            <?php if (empty($entidades)): ?>
              <div class="text-center text-muted py-5">
                <i class="bi bi-bank fs-1 opacity-25 d-block mb-2"></i>
                No hay entidades registradas aún.
                <a href="index.php?modulo=configuracion&accion=crear-entidad" class="d-block mt-2">Agregar primera entidad</a>
              </div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>#</th>
                      <th>Nombre</th>
                      <th>Descripción</th>
                      <th class="text-center">Estado</th>
                      <th class="text-end">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($entidades as $i => $e): ?>
                    <tr>
                      <td class="text-muted small"><?= $i + 1 ?></td>
                      <td class="fw-semibold"><?= htmlspecialchars($e['nombre']) ?></td>
                      <td class="text-muted small"><?= htmlspecialchars($e['descripcion'] ?? '—') ?></td>
                      <td class="text-center">
                        <?php if ($e['activo']): ?>
                          <span class="badge text-bg-success">Activa</span>
                        <?php else: ?>
                          <span class="badge text-bg-secondary">Inactiva</span>
                        <?php endif; ?>
                      </td>
                      <td class="text-end">
                        <a href="index.php?modulo=configuracion&accion=editar-entidad&id=<?= $e['id'] ?>"
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
        <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Eliminar entidad</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        ¿Estás seguro que deseas eliminar la entidad <strong id="nombreEntidad"></strong>?
        Esta acción no se puede deshacer.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <a id="btnConfirmarEliminar" href="#" class="btn btn-danger">
          <i class="bi bi-trash me-1"></i> Eliminar
        </a>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('modalEliminar').addEventListener('show.bs.modal', function (e) {
  const btn = e.relatedTarget;
  document.getElementById('nombreEntidad').textContent = btn.dataset.nombre;
  document.getElementById('btnConfirmarEliminar').href =
    'index.php?modulo=configuracion&accion=eliminar-entidad&id=' + btn.dataset.id;
});
</script>
