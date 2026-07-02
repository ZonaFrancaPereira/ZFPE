<?php
/** @var array $comites */
$pageTitle  = 'Comités — ZFIP-E';
$activePage = 'comites';
$pageStyles = ['vista/assets/css/componentes.css'];

$estadoBadge = [
    'programado' => ['bg-info',    'Programado'],
    'realizado'  => ['bg-success', 'Realizado'],
    'cancelado'  => ['bg-secondary','Cancelado'],
];
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
          <div class="col-sm-6"><h3 class="mb-0 titulo-zf">Comités</h3></div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item active">Comités</li>
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
              <i class="bi bi-people-fill me-2 text-primary"></i>Comités registrados
            </h5>
            <a href="index.php?modulo=comites&accion=crear" class="btn btn-primary btn-sm">
              <i class="bi bi-plus-lg me-1"></i> Nuevo comité
            </a>
          </div>

          <div class="card-body p-0">
            <?php if (empty($comites)): ?>
              <div class="text-center text-muted py-5">
                <i class="bi bi-people fs-1 opacity-25 d-block mb-2"></i>
                No hay comités registrados aún.
                <a href="index.php?modulo=comites&accion=crear" class="d-block mt-2">Registrar primer comité</a>
              </div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Comité</th>
                      <th>Fecha</th>
                      <th>Tipo</th>
                      <th>Empresa</th>
                      <th class="text-center">Compromisos</th>
                      <th class="text-center">Estado</th>
                      <th class="text-end">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($comites as $c): ?>
                    <?php
                      [$badgeCls, $badgeTxt] = $estadoBadge[$c['estado']] ?? ['bg-secondary', $c['estado']];
                      $totalComp   = (int) $c['total_compromisos'];
                      $cumplidos   = (int) $c['compromisos_cumplidos'];
                      $pct         = $totalComp > 0 ? round($cumplidos / $totalComp * 100) : 0;
                    ?>
                    <tr>
                      <td>
                        <div class="fw-semibold"><?= htmlspecialchars($c['titulo']) ?></div>
                        <?php if ($c['lugar']): ?>
                          <small class="text-muted"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($c['lugar']) ?></small>
                        <?php endif; ?>
                      </td>
                      <td class="text-nowrap">
                        <i class="bi bi-calendar3 me-1 text-muted"></i>
                        <?= date('d/m/Y', strtotime($c['fecha'])) ?>
                      </td>
                      <td>
                        <span class="badge text-bg-light border text-capitalize"><?= htmlspecialchars($c['tipo']) ?></span>
                      </td>
                      <td>
                        <?php if (!empty($c['empresa_nombre'])): ?>
                          <span class="small"><i class="bi bi-building me-1 text-muted"></i><?= htmlspecialchars($c['empresa_nombre']) ?></span>
                        <?php else: ?>
                          <span class="text-muted small">—</span>
                        <?php endif; ?>
                      </td>
                      <td class="text-center" style="min-width:120px;">
                        <?php if ($totalComp > 0): ?>
                          <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height:5px;">
                              <div class="progress-bar bg-success" style="width:<?= $pct ?>%"></div>
                            </div>
                            <small class="text-muted"><?= $cumplidos ?>/<?= $totalComp ?></small>
                          </div>
                        <?php else: ?>
                          <span class="text-muted small">Sin compromisos</span>
                        <?php endif; ?>
                      </td>
                      <td class="text-center">
                        <span class="badge <?= $badgeCls ?>"><?= $badgeTxt ?></span>
                      </td>
                      <td class="text-end">
                        <a href="index.php?modulo=comites&accion=ver&id=<?= $c['id'] ?>"
                           class="btn btn-sm btn-outline-primary me-1" title="Ver detalle">
                          <i class="bi bi-eye"></i>
                        </a>
                        <a href="index.php?modulo=comites&accion=editar&id=<?= $c['id'] ?>"
                           class="btn btn-sm btn-outline-secondary me-1" title="Editar">
                          <i class="bi bi-pencil"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger"
                                data-bs-toggle="modal" data-bs-target="#modalEliminar"
                                data-id="<?= $c['id'] ?>" data-titulo="<?= htmlspecialchars($c['titulo']) ?>">
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

<!-- Modal eliminar -->
<div class="modal fade" id="modalEliminar" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Eliminar comité</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        ¿Estás seguro que deseas eliminar el comité <strong id="tituloComite"></strong>?
        Se eliminarán también todos sus compromisos registrados.
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
  document.getElementById('tituloComite').textContent = btn.dataset.titulo;
  document.getElementById('btnConfirmarEliminar').href =
    'index.php?modulo=comites&accion=eliminar&id=' + btn.dataset.id;
});
</script>
