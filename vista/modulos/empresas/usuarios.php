<?php
/** @var array $usuarios */
$pageTitle  = 'Usuarios de empresa — ZFIP-E';
$activePage = 'usuarios-empresa';
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
          <div class="col-sm-6"><h3 class="mb-0 titulo-zf">Usuarios de empresa</h3></div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item"><a href="index.php?modulo=empresas">Empresas</a></li>
              <li class="breadcrumb-item active">Usuarios</li>
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
              <i class="bi bi-people-fill me-2 text-primary"></i>Todos los usuarios de empresa
            </h5>
          </div>
          <div class="card-body p-0">
            <?php if (empty($usuarios)): ?>
              <div class="text-center text-muted py-5">
                <i class="bi bi-person-x fs-1 opacity-25 d-block mb-2"></i>
                No hay usuarios de empresa registrados.
              </div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Usuario</th>
                      <th>Correo</th>
                      <th>Empresa</th>
                      <th class="text-end">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($usuarios as $u): ?>
                    <tr>
                      <td>
                        <div class="d-flex align-items-center gap-2">
                          <span class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-semibold flex-shrink-0"
                                style="width:34px;height:34px;font-size:.85rem;">
                            <?= mb_strtoupper(mb_substr($u['nombre'], 0, 1)) ?>
                          </span>
                          <span class="fw-semibold"><?= htmlspecialchars($u['nombre']) ?></span>
                        </div>
                      </td>
                      <td class="text-muted small"><?= htmlspecialchars($u['correo']) ?></td>
                      <td>
                        <?php if ($u['empresa_nombre']): ?>
                          <a href="index.php?modulo=empresas&accion=ver&id=<?= $u['empresa_id'] ?>" class="text-decoration-none">
                            <span class="badge text-bg-light border">
                              <i class="bi bi-building me-1"></i><?= htmlspecialchars($u['empresa_nombre']) ?>
                            </span>
                          </a>
                        <?php else: ?>
                          <span class="text-muted small">Sin empresa</span>
                        <?php endif; ?>
                      </td>
                      <td class="text-end">
                        <a href="index.php?modulo=empresas&accion=editar-usuario&id=<?= $u['id'] ?>"
                           class="btn btn-sm btn-outline-secondary me-1" title="Editar">
                          <i class="bi bi-pencil"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger"
                                data-bs-toggle="modal" data-bs-target="#modalEliminar"
                                data-id="<?= $u['id'] ?>" data-nombre="<?= htmlspecialchars($u['nombre']) ?>">
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
        <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Eliminar usuario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        ¿Eliminar al usuario <strong id="nombreUsuario"></strong>? Esta acción no se puede deshacer.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <form method="POST" id="formEliminar" class="m-0">
          <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i>Eliminar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('modalEliminar').addEventListener('show.bs.modal', function(e) {
  const btn = e.relatedTarget;
  document.getElementById('nombreUsuario').textContent = btn.dataset.nombre;
  document.getElementById('formEliminar').action =
    'index.php?modulo=empresas&accion=eliminar-usuario&id=' + btn.dataset.id;
});
</script>
