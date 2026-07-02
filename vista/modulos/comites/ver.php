<?php
/** @var array $comite */
/** @var array $compromisos */
/** @var array $responsables */
/** @var array $historialPorCompromiso */
$pageTitle  = 'Detalle del comité — ZFPE';
$activePage = 'comites';
$pageStyles = ['vista/assets/css/componentes.css'];
$esOp       = in_array($_SESSION['usuario_rol'] ?? '', ['operaciones', 'admin'], true);
$miNombre   = $_SESSION['usuario_nombre'] ?? '';

$estadoBadge = [
    'programado' => ['bg-info text-dark', 'Programado'],
    'realizado'  => ['bg-success',        'Realizado'],
    'cancelado'  => ['bg-secondary',      'Cancelado'],
];
$estadoComp = [
    'pendiente'   => ['bg-warning text-dark', 'Pendiente'],
    'en_progreso' => ['bg-primary',           'En progreso'],
    'cumplido'    => ['bg-success',           'Cumplido'],
    'vencido'     => ['bg-danger',            'Vencido'],
];
[$badgeCls, $badgeTxt] = $estadoBadge[$comite['estado']] ?? ['bg-secondary', $comite['estado']];

$totalComp = count($compromisos);
$cumplidos = count(array_filter($compromisos, fn($c) => $c['estado'] === 'cumplido'));
$vencidos  = count(array_filter($compromisos, fn($c) => $c['estado'] === 'vencido'));
$pct       = $totalComp > 0 ? round($cumplidos / $totalComp * 100) : 0;
?>
<?php require_once __DIR__ . '/../../parciales/cabecera.php'; ?>


<div class="app-wrapper">
  <?php require_once __DIR__ . '/../../parciales/navegacion.php'; ?>
  <?php require_once __DIR__ . '/../../parciales/menu_lateral.php'; ?>

  <main class="app-main">
    <div class="app-content-header encabezado-zf">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6"><h3 class="mb-0 titulo-zf">Detalle del comité</h3></div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item"><a href="index.php?modulo=comites">Comités</a></li>
              <li class="breadcrumb-item active">Detalle</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="app-content">
      <div class="container-fluid">

        <!-- Encabezado -->
        <div class="card shadow-sm mb-4">
          <div class="card-body">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
              <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                  <span class="badge <?= $badgeCls ?>"><?= $badgeTxt ?></span>
                  <span class="badge text-bg-light border text-capitalize"><?= htmlspecialchars($comite['tipo'] ?? 'seguimiento') ?></span>
                </div>
                <h4 class="mb-1"><?= htmlspecialchars($comite['titulo']) ?></h4>
                <div class="text-muted small d-flex flex-wrap gap-3 mb-2">
                  <span><i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y H:i', strtotime($comite['fecha'])) ?></span>
                  <?php if (!empty($comite['lugar'])): ?>
                    <span><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($comite['lugar']) ?></span>
                  <?php endif; ?>
                  <?php if (!empty($comite['empresa_nombre'])): ?>
                    <span><i class="bi bi-building me-1"></i><?= htmlspecialchars($comite['empresa_nombre']) ?></span>
                  <?php endif; ?>
                </div>
                <?php if (!empty($comite['descripcion'])): ?>
                  <p class="mb-0 text-muted"><?= nl2br(htmlspecialchars($comite['descripcion'])) ?></p>
                <?php endif; ?>
              </div>
              <?php if ($esOp): ?>
              <a href="index.php?modulo=comites&accion=editar&id=<?= $comite['id'] ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-pencil me-1"></i>Editar
              </a>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- KPIs -->
        <div class="row g-3 mb-4">
          <div class="col-6 col-md-3">
            <div class="card text-center border-0 shadow-sm py-3">
              <div class="fs-2 fw-bold text-primary"><?= $totalComp ?></div>
              <div class="text-muted small">Total compromisos</div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="card text-center border-0 shadow-sm py-3">
              <div class="fs-2 fw-bold text-success"><?= $cumplidos ?></div>
              <div class="text-muted small">Cumplidos</div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="card text-center border-0 shadow-sm py-3">
              <div class="fs-2 fw-bold <?= $vencidos > 0 ? 'text-danger' : 'text-muted' ?>"><?= $vencidos ?></div>
              <div class="text-muted small">Vencidos</div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm px-3 py-3 d-flex flex-column justify-content-center">
              <div class="d-flex justify-content-between mb-1">
                <small class="fw-semibold">Avance</small>
                <small class="fw-bold"><?= $pct ?>%</small>
              </div>
              <div class="progress" style="height:8px;">
                <div class="progress-bar bg-success" style="width:<?= $pct ?>%"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="row g-4">
          <?php if ($esOp): ?>
          <!-- Agregar compromiso -->
          <div class="col-lg-5">
            <div class="card shadow-sm">
              <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle text-success fs-5"></i>
                <h6 class="card-title mb-0">Registrar compromiso</h6>
              </div>
              <form method="POST" action="index.php?modulo=comites&accion=guardar-compromiso">
                <input type="hidden" name="comite_id" value="<?= $comite['id'] ?>">
                <div class="card-body">
                  <div class="mb-3">
                    <label class="form-label fw-semibold">
                      Descripción <span class="text-danger">*</span>
                    </label>
                    <textarea name="descripcion" class="form-control" rows="3" required
                              placeholder="¿Qué se debe hacer?"></textarea>
                  </div>
                  <div class="mb-3">
                    <label class="form-label fw-semibold">Responsable</label>
                    <select name="responsable" class="form-select">
                      <option value="">Seleccione un responsable...</option>
                      <?php
                        $empresaResp    = array_filter($responsables, fn($r) => $r['grupo'] === 'Empresa');
                        $operacionesResp = array_filter($responsables, fn($r) => $r['grupo'] === 'Operaciones');
                      ?>
                      <?php if ($empresaResp): ?>
                        <optgroup label="Personas de la empresa">
                          <?php foreach ($empresaResp as $r): ?>
                            <option value="<?= htmlspecialchars($r['nombre']) ?>"><?= htmlspecialchars($r['nombre']) ?></option>
                          <?php endforeach; ?>
                        </optgroup>
                      <?php endif; ?>
                      <?php if ($operacionesResp): ?>
                        <optgroup label="Equipo de Operaciones">
                          <?php foreach ($operacionesResp as $r): ?>
                            <option value="<?= htmlspecialchars($r['nombre']) ?>"><?= htmlspecialchars($r['nombre']) ?></option>
                          <?php endforeach; ?>
                        </optgroup>
                      <?php endif; ?>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label class="form-label fw-semibold">Fecha límite</label>
                    <input type="date" name="fecha_limite" class="form-control">
                  </div>
                  <div>
                    <label class="form-label fw-semibold">Estado inicial</label>
                    <select name="estado" class="form-select">
                      <option value="pendiente">Pendiente</option>
                      <option value="en_progreso">En progreso</option>
                    </select>
                  </div>
                </div>
                <div class="card-footer">
                  <button type="submit" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> Agregar
                  </button>
                </div>
              </form>
            </div>
          </div>
          <?php endif; ?>

          <!-- Lista de compromisos -->
          <div class="<?= $esOp ? 'col-lg-7' : 'col-12' ?>">
            <div class="card shadow-sm">
              <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-list-check text-primary fs-5"></i>
                <h6 class="card-title mb-0">Compromisos</h6>
              </div>
              <div class="card-body p-0">
                <?php if (empty($compromisos)): ?>
                  <div class="text-center text-muted py-4">
                    <i class="bi bi-clipboard-check fs-2 opacity-25 d-block mb-2"></i>
                    No hay compromisos registrados aún.
                  </div>
                <?php else: ?>
                  <div class="list-group list-group-flush">
                    <?php foreach ($compromisos as $comp): ?>
                    <?php
                      [$cbadge, $ctxt] = $estadoComp[$comp['estado']] ?? ['bg-secondary', $comp['estado']];
                      $vencido = ($comp['estado'] !== 'cumplido' && !empty($comp['fecha_limite']) && $comp['fecha_limite'] < date('Y-m-d'));
                      $historial = $historialPorCompromiso[$comp['id']] ?? [];
                      $totalDocsComp = array_sum(array_map(fn($h) => count($h['documentos']), $historial));
                      $collapseId = 'historial-' . $comp['id'];
                    ?>
                    <div id="compromiso-<?= $comp['id'] ?>" class="list-group-item py-3">
                      <div class="d-flex align-items-start justify-content-between gap-2">
                        <div class="flex-grow-1">
                          <div class="fw-semibold mb-1"><?= htmlspecialchars($comp['descripcion']) ?></div>
                          <div class="d-flex flex-wrap gap-3 text-muted small">
                            <?php if ($comp['responsable']): ?>
                              <span><i class="bi bi-person me-1"></i><?= htmlspecialchars($comp['responsable']) ?></span>
                            <?php endif; ?>
                            <?php if ($comp['fecha_limite']): ?>
                              <span class="<?= $vencido ? 'text-danger fw-semibold' : '' ?>">
                                <i class="bi bi-calendar-event me-1"></i><?= date('d/m/Y', strtotime($comp['fecha_limite'])) ?>
                                <?= $vencido ? '<span class="badge bg-danger ms-1">Vencido</span>' : '' ?>
                              </span>
                            <?php endif; ?>
                          </div>
                          <?php if ($comp['observaciones']): ?>
                            <div class="text-muted small mt-1 fst-italic">
                              <?= htmlspecialchars($comp['observaciones']) ?>
                            </div>
                          <?php endif; ?>

                          <?php if (!empty($historial)): ?>
                          <button type="button" class="btn btn-sm btn-link px-0 mt-1" data-bs-toggle="collapse" data-bs-target="#<?= $collapseId ?>">
                            <i class="bi bi-clock-history me-1"></i>Historial (<?= count($historial) ?>)
                            <?php if ($totalDocsComp > 0): ?>
                              <span class="badge text-bg-light border text-muted ms-1"><i class="bi bi-paperclip"></i> <?= $totalDocsComp ?></span>
                            <?php endif; ?>
                          </button>
                          <div class="collapse" id="<?= $collapseId ?>">
                            <ul class="list-unstyled small border rounded p-2 mt-1">
                              <?php foreach ($historial as $h): ?>
                              <?php [$hbadge, $htxt] = $estadoComp[$h['estado']] ?? ['bg-secondary', $h['estado']]; ?>
                              <li class="py-1 border-bottom border-opacity-25">
                                <div class="d-flex align-items-center justify-content-between gap-2">
                                  <span>
                                    <span class="fw-semibold"><?= htmlspecialchars($h['usuario_nombre'] ?? 'Usuario') ?></span>
                                    <span class="text-muted"><?= date('d/m/Y H:i', strtotime($h['created_at'])) ?></span>
                                  </span>
                                  <span class="badge <?= $hbadge ?>" style="font-size:.6rem;"><?= $htxt ?></span>
                                </div>
                                <?php if (!empty($h['observaciones'])): ?>
                                  <div class="text-muted"><?= nl2br(htmlspecialchars($h['observaciones'])) ?></div>
                                <?php endif; ?>
                                <?php if ($esOp || $comp['responsable'] === $miNombre): ?>
                                <?php foreach ($h['documentos'] as $doc): ?>
                                <div class="d-flex align-items-center gap-1">
                                  <i class="bi bi-paperclip text-primary"></i>
                                  <a href="index.php?modulo=mis-compromisos&accion=descargar-documento&id=<?= $doc['id'] ?>"
                                     title="Descargar"><?= htmlspecialchars($doc['nombre_original']) ?></a>
                                </div>
                                <?php endforeach; ?>
                                <?php endif; ?>
                              </li>
                              <?php endforeach; ?>
                            </ul>
                          </div>
                          <?php endif; ?>
                        </div>
                        <div class="d-flex align-items-center gap-1 flex-shrink-0">
                          <span class="badge <?= $cbadge ?>"><?= $ctxt ?></span>
                          <?php if ($esOp): ?>
                          <button type="button" class="btn btn-sm btn-outline-secondary ms-1"
                                  data-bs-toggle="modal" data-bs-target="#modalActualizar"
                                  data-id="<?= $comp['id'] ?>"
                                  data-estado="<?= $comp['estado'] ?>"
                                  data-obs="<?= htmlspecialchars($comp['observaciones'] ?? '') ?>">
                            <i class="bi bi-pencil-square"></i>
                          </button>
                          <button type="button" class="btn btn-sm btn-outline-danger"
                                  data-bs-toggle="modal" data-bs-target="#modalEliminarComp"
                                  data-id="<?= $comp['id'] ?>"
                                  data-desc="<?= htmlspecialchars(mb_substr($comp['descripcion'], 0, 60)) ?>">
                            <i class="bi bi-trash"></i>
                          </button>
                          <?php elseif ($comp['responsable'] === $miNombre && $comp['estado'] !== 'cumplido'): ?>
                          <a href="index.php?modulo=mis-compromisos#compromiso-<?= $comp['id'] ?>"
                             class="btn btn-sm btn-outline-primary ms-1" title="Completar este compromiso">
                            <i class="bi bi-pencil-square"></i>
                          </a>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                    <?php endforeach; ?>
                  </div>
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

<!-- Modal actualizar compromiso -->
<div class="modal fade" id="modalActualizar" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Actualizar compromiso</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" id="formActualizar">
        <input type="hidden" name="comite_id" value="<?= $comite['id'] ?>">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Estado</label>
            <select name="estado" id="modalEstado" class="form-select" required>
              <option value="pendiente">Pendiente</option>
              <option value="en_progreso">En progreso</option>
              <option value="cumplido">Cumplido</option>
              <option value="vencido">Vencido</option>
            </select>
          </div>
          <div>
            <label class="form-label fw-semibold">Observaciones</label>
            <textarea name="observaciones" id="modalObs" class="form-control" rows="3"
                      placeholder="Notas adicionales..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal eliminar compromiso -->
<div class="modal fade" id="modalEliminarComp" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Eliminar compromiso</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        ¿Eliminar el compromiso "<strong id="descComp"></strong>"?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <form method="POST" id="formEliminarComp" class="m-0">
          <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i>Eliminar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('modalActualizar').addEventListener('show.bs.modal', function(e) {
  const btn = e.relatedTarget;
  document.getElementById('formActualizar').action =
    'index.php?modulo=comites&accion=actualizar-compromiso&id=' + btn.dataset.id;
  document.getElementById('modalEstado').value = btn.dataset.estado;
  document.getElementById('modalObs').value    = btn.dataset.obs;
});

document.getElementById('modalEliminarComp').addEventListener('show.bs.modal', function(e) {
  const btn = e.relatedTarget;
  document.getElementById('descComp').textContent = btn.dataset.desc;
  document.getElementById('formEliminarComp').action =
    'index.php?modulo=comites&accion=eliminar-compromiso&id=' + btn.dataset.id;
});
</script>
