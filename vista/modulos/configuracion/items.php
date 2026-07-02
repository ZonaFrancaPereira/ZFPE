<?php
/** @var array $etapas */
/** @var array $requisitosPorEtapa */
/** @var array $itemsPorRequisito */
$pageTitle  = 'Ítems — ZFPE';
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
          <div class="col-sm-6"><h3 class="mb-0 titulo-zf">Ítems de requisitos</h3></div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item"><a href="index.php?modulo=configuracion">Configuración</a></li>
              <li class="breadcrumb-item active">Ítems</li>
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
              <i class="bi bi-ui-checks me-2 text-info"></i>Ítems configurados
            </h5>
            <a href="index.php?modulo=configuracion&accion=crear-item" class="btn btn-primary btn-sm">
              <i class="bi bi-plus-lg me-1"></i> Nuevo ítem
            </a>
          </div>
          <div class="card-body p-0">
            <?php if (empty($etapas)): ?>
              <div class="text-center text-muted py-5">
                <i class="bi bi-diagram-3 fs-1 opacity-25 d-block mb-2"></i>
                No hay etapas configuradas.
                <a href="index.php?modulo=configuracion&accion=crear-etapa" class="d-block mt-2">Crear una etapa</a>
              </div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Etapa / Requisito</th>
                      <th>Ítem</th>
                      <th class="text-center">Orden</th>
                      <th class="text-center">Obligatorio</th>
                      <th class="text-center">Estado</th>
                      <th class="text-end">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($etapas as $etapa): ?>
                    <?php $reqs = $requisitosPorEtapa[$etapa['id']] ?? []; ?>

                    <!-- Fila de etapa -->
                    <tr style="background:#e9f0fb;">
                      <td colspan="6" class="py-2 ps-3">
                        <i class="bi bi-diagram-3 me-1 text-primary"></i>
                        <strong class="text-primary"><?= htmlspecialchars($etapa['nombre']) ?></strong>
                        <?php if (empty($reqs)): ?>
                          <span class="badge text-bg-secondary ms-2" style="font-size:.65rem;">Sin requisitos</span>
                          <a href="index.php?modulo=configuracion&accion=crear-requisito"
                             class="btn btn-outline-secondary btn-sm ms-2 py-0" style="font-size:.7rem;">
                            <i class="bi bi-plus"></i> Agregar requisito
                          </a>
                        <?php endif; ?>
                      </td>
                    </tr>

                    <?php foreach ($reqs as $req): ?>
                    <?php $itsReq = $itemsPorRequisito[$req['id']] ?? []; ?>

                    <!-- Fila cabecera del requisito -->
                    <tr class="table-light">
                      <td colspan="6" class="py-2 ps-4">
                        <span class="text-muted small">
                          <i class="bi bi-list-check me-1 text-warning"></i>
                          <span class="fw-semibold text-dark"><?= htmlspecialchars($req['nombre']) ?></span>
                        </span>
                        <a href="index.php?modulo=configuracion&accion=crear-item&requisito_id=<?= $req['id'] ?>"
                           class="btn btn-outline-primary btn-sm ms-2 py-0" style="font-size:.7rem;">
                          <i class="bi bi-plus"></i> Agregar ítem aquí
                        </a>
                        <?php if (empty($itsReq)): ?>
                          <span class="badge text-bg-secondary ms-1" style="font-size:.65rem;">Sin ítems</span>
                        <?php else: ?>
                          <span class="badge text-bg-info ms-1" style="font-size:.65rem;">
                            <?= count($itsReq) ?> ítem<?= count($itsReq) !== 1 ? 's' : '' ?>
                          </span>
                        <?php endif; ?>
                      </td>
                    </tr>

                    <?php foreach ($itsReq as $item): ?>
                    <tr>
                      <td></td>
                      <td class="fw-semibold">
                        <?= htmlspecialchars($item['nombre']) ?>
                        <?php if ($item['descripcion']): ?>
                          <div class="text-muted small fw-normal"><?= htmlspecialchars($item['descripcion']) ?></div>
                        <?php endif; ?>
                      </td>
                      <td class="text-center">
                        <span class="badge text-bg-light border text-muted fw-bold"><?= $item['orden'] ?></span>
                      </td>
                      <td class="text-center">
                        <?= $item['obligatorio']
                          ? '<i class="bi bi-check-circle-fill text-success"></i>'
                          : '<i class="bi bi-dash text-muted"></i>' ?>
                      </td>
                      <td class="text-center">
                        <?= $item['activo']
                          ? '<span class="badge text-bg-success">Activo</span>'
                          : '<span class="badge text-bg-secondary">Inactivo</span>' ?>
                      </td>
                      <td class="text-end">
                        <a href="index.php?modulo=configuracion&accion=editar-item&id=<?= $item['id'] ?>"
                           class="btn btn-sm btn-outline-primary me-1">
                          <i class="bi bi-pencil"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger"
                                data-bs-toggle="modal" data-bs-target="#modalEliminar"
                                data-id="<?= $item['id'] ?>" data-nombre="<?= htmlspecialchars($item['nombre']) ?>">
                          <i class="bi bi-trash"></i>
                        </button>
                      </td>
                    </tr>
                    <?php endforeach; // ítems ?>

                    <?php endforeach; // requisitos ?>

                    <?php endforeach; // etapas ?>
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
        <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Eliminar ítem</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        ¿Estás seguro que deseas eliminar el ítem <strong id="nombreItem"></strong>?
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
  document.getElementById('nombreItem').textContent = btn.dataset.nombre;
  document.getElementById('formEliminar').action =
    'index.php?modulo=configuracion&accion=eliminar-item&id=' + btn.dataset.id;
});
</script>
