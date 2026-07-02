<?php
$pageTitle  = 'Empresas — ZFIP-E';
$activePage = 'empresas';
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
          <div class="col-sm-6"><h3 class="mb-0 titulo-zf">Empresas</h3></div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item active">Empresas</li>
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
              <i class="bi bi-building-fill me-2 text-primary"></i>Empresas en seguimiento
            </h5>
            <a href="index.php?modulo=empresas&accion=crear" class="btn btn-primary btn-sm">
              <i class="bi bi-plus-lg me-1"></i> Nueva empresa
            </a>
          </div>
          <div class="card-body p-0">
            <?php if (empty($empresas)): ?>
              <div class="text-center text-muted py-5">
                <i class="bi bi-building fs-1 opacity-25 d-block mb-2"></i>
                No hay empresas registradas aún.
                <a href="index.php?modulo=empresas&accion=crear" class="d-block mt-2">Registrar primera empresa</a>
              </div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Empresa</th>
                      <th>NIT</th>
                      <th>Representante</th>
                      <th class="text-center">Usuarios</th>
                      <th>Etapa actual</th>
                      <th style="min-width:140px;">Avance general</th>
                      <th class="text-end">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($empresas as $e): ?>
                    <tr>
                      <td>
                        <div class="fw-semibold"><?= htmlspecialchars($e['razon_social']) ?></div>
                        <small class="text-muted"><?= htmlspecialchars($e['correo'] ?? '') ?></small>
                      </td>
                      <td class="text-muted small"><?= htmlspecialchars($e['nit']) ?></td>
                      <td class="small"><?= htmlspecialchars($e['representante'] ?? '—') ?></td>
                      <td class="text-center">
                        <span class="badge text-bg-light border"><?= $e['total_usuarios'] ?></span>
                      </td>
                      <td class="small">
                        <?php if ($e['etapa_actual']): ?>
                          <span class="badge text-bg-primary"><?= htmlspecialchars($e['etapa_actual']) ?></span>
                        <?php else: ?>
                          <span class="text-muted">Sin iniciar</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php $avance = (float) ($e['avance_general'] ?? 0); ?>
                        <div class="d-flex align-items-center gap-2">
                          <div class="progress flex-grow-1" style="height:6px;">
                            <div class="progress-bar bg-success" style="width:<?= $avance ?>%"></div>
                          </div>
                          <small class="text-muted fw-semibold" style="min-width:36px;"><?= $avance ?>%</small>
                        </div>
                      </td>
                      <td class="text-end">
                        <a href="index.php?modulo=empresas&accion=ver&id=<?= $e['id'] ?>"
                           class="btn btn-sm btn-outline-primary me-1" title="Ver detalle">
                          <i class="bi bi-eye"></i>
                        </a>
                        <a href="index.php?modulo=empresas&accion=editar&id=<?= $e['id'] ?>"
                           class="btn btn-sm btn-outline-secondary me-1" title="Editar">
                          <i class="bi bi-pencil"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger"
                                data-bs-toggle="modal" data-bs-target="#modalEliminar"
                                data-id="<?= $e['id'] ?>" data-nombre="<?= htmlspecialchars($e['razon_social']) ?>">
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
        <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Eliminar empresa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        ¿Estás seguro que deseas eliminar la empresa <strong id="nombreEmpresa"></strong>?
        Se eliminará todo su seguimiento, documentos y progreso registrado.
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
document.getElementById('modalEliminar').addEventListener('show.bs.modal', function(e) {
  const btn = e.relatedTarget;
  document.getElementById('nombreEmpresa').textContent = btn.dataset.nombre;
  document.getElementById('btnConfirmarEliminar').href =
    'index.php?modulo=empresas&accion=eliminar&id=' + btn.dataset.id;
});
</script>
