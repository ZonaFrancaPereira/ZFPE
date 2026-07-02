<?php
/** @var array $empresa */
/** @var array $usuariosDisponibles */
/** @var array $etapasSinAsignar */
$pageTitle  = htmlspecialchars($empresa['razon_social']) . ' — ZFIP-E';
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

<?php
$estadoColor = [
    'pendiente'   => 'secondary',
    'en_progreso' => 'primary',
    'completa'    => 'success',
];
$estadoLabel = [
    'pendiente'   => 'Pendiente',
    'en_progreso' => 'En progreso',
    'completa'    => 'Completa',
];
$reqColor = [
    'pendiente'   => 'secondary',
    'en_progreso' => 'primary',
    'cumplido'    => 'success',
    'no_aplica'   => 'light',
];

// Agrupar requisitos por etapa_id
$reqPorEtapa = [];
foreach ($empresa['requisitos'] as $r) {
    $reqPorEtapa[$r['etapa_id']][] = $r;
}

// Agrupar etapas por fase
$faseGrupos = [];
foreach ($empresa['etapas'] as $et) {
    $fid = $et['fase_id'] ?? 0;
    if (!isset($faseGrupos[$fid])) {
        $faseGrupos[$fid] = [
            'nombre' => $et['fase_nombre'] ?? null,
            'orden'  => $et['fase_orden']  ?? 999,
            'etapas' => [],
        ];
    }
    $faseGrupos[$fid]['etapas'][] = $et;
}
usort($faseGrupos, fn($a, $b) => $a['orden'] <=> $b['orden']);
?>

<div class="app-wrapper">
  <?php require_once __DIR__ . '/../../parciales/navegacion.php'; ?>
  <?php require_once __DIR__ . '/../../parciales/menu_lateral.php'; ?>

  <main class="app-main">
    <div class="app-content-header encabezado-zf">
      <div class="container-fluid">
        <div class="row align-items-center">
          <div class="col-sm-6">
            <h3 class="mb-0 titulo-zf"><?= htmlspecialchars($empresa['razon_social']) ?></h3>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item"><a href="index.php?modulo=empresas">Empresas</a></li>
              <li class="breadcrumb-item active"><?= htmlspecialchars($empresa['razon_social']) ?></li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="app-content">
      <div class="container-fluid">

        <!-- Encabezado empresa -->
        <div class="card shadow-sm mb-4 border-0 bg-body-secondary">
          <div class="card-body py-3">
            <div class="row align-items-center g-3">
              <div class="col-md-6">
                <div class="d-flex align-items-center gap-3">
                  <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                       style="width:52px;height:52px;font-size:1.3rem;">
                    <?= mb_strtoupper(mb_substr($empresa['razon_social'], 0, 1)) ?>
                  </div>
                  <div>
                    <h5 class="mb-0"><?= htmlspecialchars($empresa['razon_social']) ?></h5>
                    <small class="text-muted">
                      NIT: <?= htmlspecialchars($empresa['nit']) ?> ·
                      <?= htmlspecialchars($empresa['representante'] ?? '') ?>
                    </small>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="d-flex align-items-center gap-2">
                  <div class="progress flex-grow-1" style="height:10px;">
                    <div class="progress-bar bg-success" style="width:<?= $empresa['avance_general'] ?>%"></div>
                  </div>
                  <span class="fw-bold text-success"><?= $empresa['avance_general'] ?>%</span>
                </div>
                <small class="text-muted">Avance general del proyecto</small>
              </div>
              <div class="col-md-2 text-md-end d-flex flex-column gap-2 align-items-end">
                <a href="index.php?modulo=seguimiento&id=<?= $empresa['id'] ?>"
                   class="btn btn-primary btn-sm">
                  <i class="bi bi-clipboard-check me-1"></i> Seguimiento
                </a>
                <a href="index.php?modulo=cronograma&id=<?= $empresa['id'] ?>"
                   class="btn btn-outline-info btn-sm">
                  <i class="bi bi-calendar3 me-1"></i> Cronograma
                </a>
                <a href="index.php?modulo=reportes&id=<?= $empresa['id'] ?>"
                   class="btn btn-outline-success btn-sm">
                  <i class="bi bi-bar-chart-fill me-1"></i> Reporte
                </a>
                <a href="index.php?modulo=documentos&accion=ver&id=<?= $empresa['id'] ?>"
                   class="btn btn-outline-warning btn-sm">
                  <i class="bi bi-folder2-open me-1"></i> Documentos
                </a>
                <div class="d-flex gap-1">
                  <a href="index.php?modulo=informes&accion=excel&id=<?= $empresa['id'] ?>"
                     class="btn btn-outline-success btn-sm" title="Descargar informe en Excel">
                    <i class="bi bi-file-earmark-excel"></i>
                  </a>
                  <a href="index.php?modulo=informes&accion=pdf&id=<?= $empresa['id'] ?>"
                     class="btn btn-outline-danger btn-sm" title="Descargar informe en PDF">
                    <i class="bi bi-file-earmark-pdf"></i>
                  </a>
                </div>
                <a href="index.php?modulo=empresas&accion=editar&id=<?= $empresa['id'] ?>"
                   class="btn btn-outline-secondary btn-sm">
                  <i class="bi bi-pencil me-1"></i> Editar
                </a>
              </div>
            </div>
          </div>
        </div>

        <div class="row g-4">

          <!-- Columna principal: etapas y requisitos -->
          <div class="col-lg-8">

            <!-- Etapas -->
            <div class="card shadow-sm mb-4">
              <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-diagram-3 me-2 text-success"></i>Avance por etapa</h5>
              </div>
              <div class="card-body">
                <?php if (empty($faseGrupos)): ?>
                  <p class="text-muted mb-0">No hay etapas configuradas en la matriz.</p>
                <?php else: ?>
                  <?php foreach ($faseGrupos as $grupo): ?>

                  <?php if ($grupo['nombre']): ?>
                  <div class="d-flex align-items-center gap-2 mb-2 mt-3">
                    <span class="badge bg-info text-white" style="font-size:.75rem;letter-spacing:.03em;">
                      <i class="bi bi-collection me-1"></i><?= htmlspecialchars($grupo['nombre']) ?>
                    </span>
                    <hr class="flex-grow-1 my-0">
                  </div>
                  <?php endif; ?>

                  <?php foreach ($grupo['etapas'] as $etapa): ?>
                  <div class="mb-3 <?= $grupo['nombre'] ? 'ps-2' : '' ?>">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                      <div class="d-flex align-items-center gap-2">
                        <span class="fw-semibold small"><?= htmlspecialchars($etapa['nombre']) ?></span>
                        <span class="badge text-bg-<?= $estadoColor[$etapa['estado_progreso']] ?? 'secondary' ?> small">
                          <?= $estadoLabel[$etapa['estado_progreso']] ?? $etapa['estado_progreso'] ?>
                        </span>
                      </div>
                      <small class="text-muted fw-semibold"><?= number_format($etapa['avance'], 1) ?>%</small>
                    </div>
                    <div class="progress" style="height:8px;">
                      <div class="progress-bar bg-<?= $etapa['avance'] >= 100 ? 'success' : ($etapa['avance'] > 0 ? 'primary' : 'secondary') ?>"
                           style="width:<?= $etapa['avance'] ?>%"></div>
                    </div>
                    <?php if (!empty($reqPorEtapa[$etapa['id']])): ?>
                    <div class="mt-2 ps-2">
                      <?php foreach ($reqPorEtapa[$etapa['id']] as $req): ?>
                      <div class="d-flex align-items-center justify-content-between py-1 border-bottom border-opacity-25">
                        <div class="small">
                          <i class="bi bi-dot text-muted"></i>
                          <?= htmlspecialchars($req['nombre']) ?>
                          <?php if ($req['entidad_nombre']): ?>
                            <span class="badge text-bg-primary border text-white ms-1" style="font-size:.65rem;"><?= htmlspecialchars($req['entidad_nombre']) ?></span>
                          <?php endif; ?>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                          <?php if ($req['total_items'] > 0): ?>
                            <small class="text-muted"><?= $req['items_cumplidos'] ?>/<?= $req['total_items'] ?> ítems</small>
                          <?php endif; ?>
                          <span class="badge text-bg-<?= $reqColor[$req['estado_req']] ?? 'secondary' ?>" style="font-size:.65rem;">
                            <?= ucfirst(str_replace('_', ' ', $req['estado_req'])) ?>
                          </span>
                        </div>
                      </div>
                      <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                  </div>
                  <?php endforeach; ?>

                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
            </div>

          </div>

          <!-- Columna lateral: usuarios asignados -->
          <div class="col-lg-4">

            <!-- Agregar etapa -->
            <div class="card shadow-sm mb-4">
              <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                  <i class="bi bi-diagram-3 me-2 text-warning"></i>Etapas del proyecto
                </h5>
                <a href="index.php?modulo=configuracion&accion=crear-etapa"
                   class="btn btn-outline-warning btn-sm" title="Crear nueva etapa en configuración">
                  <i class="bi bi-plus-lg"></i>
                </a>
              </div>
              <div class="card-body">
                <?php if (!empty($etapasSinAsignar)): ?>
                  <p class="text-muted small mb-2">
                    Selecciona una etapa para asignarla a esta empresa:
                  </p>
                  <?php
                  // Agrupar etapas sin asignar por fase para el <optgroup>
                  $etapasPorFase = [];
                  foreach ($etapasSinAsignar as $et) {
                      $key = $et['fase_nombre'] ?? '';
                      $etapasPorFase[$key][] = $et;
                  }
                  ?>
                  <form method="POST" action="index.php?modulo=empresas&accion=agregar-etapa&id=<?= $empresa['id'] ?>">
                    <div class="input-group input-group-sm mb-0">
                      <select name="etapa_id" class="form-select" required>
                        <option value="">— Seleccionar etapa —</option>
                        <?php foreach ($etapasPorFase as $faseNombre => $ets): ?>
                          <?php if ($faseNombre): ?>
                            <optgroup label="📁 <?= htmlspecialchars($faseNombre) ?>">
                          <?php endif; ?>
                          <?php foreach ($ets as $et): ?>
                            <option value="<?= $et['id'] ?>"><?= htmlspecialchars($et['nombre']) ?></option>
                          <?php endforeach; ?>
                          <?php if ($faseNombre): ?>
                            </optgroup>
                          <?php endif; ?>
                        <?php endforeach; ?>
                      </select>
                      <button type="submit" class="btn btn-warning">
                        <i class="bi bi-plus-lg me-1"></i> Agregar
                      </button>
                    </div>
                  </form>
                <?php else: ?>
                  <div class="text-center py-2">
                    <i class="bi bi-check-circle text-success fs-4 d-block mb-1"></i>
                    <p class="small text-muted mb-2">
                      Todas las etapas configuradas ya están asignadas.
                    </p>
                    <p class="small text-muted mb-3">
                      Para agregar una nueva etapa (ej. <em>Etapa operativa</em>), primero créala en Configuración y luego regresa aquí para asignarla.
                    </p>
                    <a href="index.php?modulo=configuracion&accion=crear-etapa"
                       class="btn btn-warning btn-sm w-100">
                      <i class="bi bi-plus-lg me-1"></i> Crear nueva etapa
                    </a>
                    <a href="index.php?modulo=configuracion&accion=etapas"
                       class="btn btn-outline-secondary btn-sm w-100 mt-1">
                      <i class="bi bi-sliders me-1"></i> Ver etapas configuradas
                    </a>
                  </div>
                <?php endif; ?>
              </div>
            </div>

            <div class="card shadow-sm">
              <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                  <i class="bi bi-people me-2 text-primary"></i>Usuarios
                </h5>
                <a href="index.php?modulo=empresas&accion=crear-usuario&id=<?= $empresa['id'] ?>"
                   class="btn btn-primary btn-sm">
                  <i class="bi bi-person-plus me-1"></i> Nuevo
                </a>
              </div>
              <div class="card-body p-0">
                <?php if (empty($empresa['usuarios'])): ?>
                  <div class="text-center text-muted py-4 px-3">
                    <i class="bi bi-person-x fs-2 opacity-25 d-block mb-1"></i>
                    Sin usuarios registrados
                    <a href="index.php?modulo=empresas&accion=crear-usuario&id=<?= $empresa['id'] ?>"
                       class="d-block mt-1 small">Crear primer usuario</a>
                  </div>
                <?php else: ?>
                  <ul class="list-group list-group-flush">
                    <?php foreach ($empresa['usuarios'] as $u): ?>
                    <li class="list-group-item px-3 py-2">
                      <div class="d-flex align-items-center justify-content-between gap-2">
                        <div class="d-flex align-items-center gap-2 min-w-0">
                          <span class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-semibold flex-shrink-0"
                                style="width:30px;height:30px;font-size:.75rem;">
                            <?= mb_strtoupper(mb_substr($u['nombre'], 0, 1)) ?>
                          </span>
                          <div class="min-w-0">
                            <div class="fw-semibold small text-truncate"><?= htmlspecialchars($u['nombre']) ?></div>
                            <small class="text-muted text-truncate d-block"><?= htmlspecialchars($u['correo']) ?></small>
                          </div>
                        </div>
                        <div class="d-flex gap-1 flex-shrink-0">
                          <a href="index.php?modulo=empresas&accion=editar-usuario&id=<?= $u['id'] ?>"
                             class="btn btn-sm btn-outline-secondary py-0 px-1" title="Editar">
                            <i class="bi bi-pencil"></i>
                          </a>
                          <button type="button" class="btn btn-sm btn-outline-danger py-0 px-1"
                                  title="Eliminar"
                                  data-bs-toggle="modal" data-bs-target="#modalEliminarUsuario"
                                  data-id="<?= $u['id'] ?>" data-nombre="<?= htmlspecialchars($u['nombre']) ?>">
                            <i class="bi bi-trash"></i>
                          </button>
                        </div>
                      </div>
                    </li>
                    <?php endforeach; ?>
                  </ul>
                <?php endif; ?>
              </div>
            </div>

          </div>

        </div>

      </div>
    </div>
  </main>

  <?php require_once __DIR__ . '/../../parciales/pie.php'; ?>
</div>

<!-- Modal eliminar usuario -->
<div class="modal fade" id="modalEliminarUsuario" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Eliminar usuario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        ¿Eliminar al usuario <strong id="nombreUsuarioEliminar"></strong>? Esta acción no se puede deshacer.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <a id="btnEliminarUsuario" href="#" class="btn btn-danger">
          <i class="bi bi-trash me-1"></i> Eliminar
        </a>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('modalEliminarUsuario').addEventListener('show.bs.modal', function(e) {
  const btn = e.relatedTarget;
  document.getElementById('nombreUsuarioEliminar').textContent = btn.dataset.nombre;
  document.getElementById('btnEliminarUsuario').href =
    'index.php?modulo=empresas&accion=eliminar-usuario&id=' + btn.dataset.id;
});
</script>
