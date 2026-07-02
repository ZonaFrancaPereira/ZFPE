<?php
/** @var array      $empresa */
/** @var array      $asignados */
/** @var array      $disponibles */
$pageTitle   = 'Indicadores — ZFPE';
$activePage  = 'indicadores';
$pageStyles  = ['vista/assets/css/componentes.css', 'vista/assets/css/indicadores.css'];
$pageScripts = [
    'vista/assets/vendor/chartjs/chart.umd.min.js',
    'vista/assets/js/indicadores.js',
];

$esOperaciones = ($_SESSION['usuario_rol'] ?? '') === 'operaciones';
$esAdmin       = ($_SESSION['usuario_rol'] ?? '') === 'admin';
$puedeEditar   = $esOperaciones || $esAdmin;

$periodoLabel = ['mensual' => 'Mensual', 'trimestral' => 'Trimestral', 'semestral' => 'Semestral', 'anual' => 'Anual'];
?>
<?php require_once __DIR__ . '/../../parciales/cabecera.php'; ?>

<div class="app-wrapper">
  <?php require_once __DIR__ . '/../../parciales/navegacion.php'; ?>
  <?php require_once __DIR__ . '/../../parciales/menu_lateral.php'; ?>

  <main class="app-main">
    <div class="app-content-header encabezado-zf">
      <div class="container-fluid">
        <div class="row align-items-center">
          <div class="col-sm-6">
            <h3 class="mb-0 titulo-zf"><i class="bi bi-graph-up me-2"></i>Indicadores</h3>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <?php if ($puedeEditar): ?>
                <li class="breadcrumb-item"><a href="index.php?modulo=indicadores">Indicadores</a></li>
              <?php endif; ?>
              <li class="breadcrumb-item active"
                  title="<?= htmlspecialchars($empresa['razon_social']) ?>"
                  style="max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                <?= htmlspecialchars($empresa['razon_social']) ?>
              </li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="app-content">
      <div class="container-fluid">


        <!-- Encabezado empresa -->
        <div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
          <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
               style="width:48px;height:48px;font-size:1.2rem;">
            <?= mb_strtoupper(mb_substr($empresa['razon_social'], 0, 1)) ?>
          </div>
          <div class="flex-grow-1">
            <h5 class="mb-0"><?= htmlspecialchars($empresa['razon_social']) ?></h5>
            <small class="text-muted">NIT: <?= htmlspecialchars($empresa['nit']) ?></small>
          </div>
          <?php if ($puedeEditar && !empty($disponibles)): ?>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAsignar">
              <i class="bi bi-plus-lg me-1"></i>Asignar indicador
            </button>
          <?php endif; ?>
          <?php if ($puedeEditar): ?>
            <a href="index.php?modulo=empresas&accion=ver&id=<?= $empresa['id'] ?>"
               class="btn btn-outline-secondary btn-sm">
              <i class="bi bi-arrow-left me-1"></i>Volver a empresa
            </a>
          <?php endif; ?>
        </div>

        <?php if (empty($asignados)): ?>
          <div class="card shadow-sm">
            <div class="card-body text-center py-5 text-muted">
              <i class="bi bi-graph-up fs-1 opacity-25 d-block mb-3"></i>
              <h5>Sin indicadores asignados</h5>
              <?php if ($puedeEditar && !empty($disponibles)): ?>
                <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#modalAsignar">
                  <i class="bi bi-plus-lg me-1"></i>Asignar primer indicador
                </button>
              <?php elseif ($puedeEditar): ?>
                <a href="index.php?modulo=configuracion&accion=indicadores" class="btn btn-outline-primary mt-2">
                  <i class="bi bi-sliders me-1"></i>Crear indicadores en Configuración
                </a>
              <?php endif; ?>
            </div>
          </div>

        <?php else: ?>

          <?php foreach ($asignados as $ind):
            $valores  = $ind['valores'] ?? [];
            $labels   = array_column($valores, 'periodo');
            $vals     = array_map(fn($v) => $v['valor'] !== null ? (float)$v['valor'] : null, $valores);
            $meta     = $ind['meta'] !== null ? (float) $ind['meta'] : null;
            $ultimoVal = !empty($vals) ? end($vals) : null;
            $pct      = ($meta && $meta > 0 && $ultimoVal !== null) ? min(round($ultimoVal / $meta * 100, 1), 100) : null;
            $colorBar = ($pct === null) ? 'secondary' : ($pct >= 100 ? 'success' : ($pct >= 50 ? 'primary' : 'warning'));
            $tipoGraf = $ind['tipo_grafico'] ?? 'linea';
          ?>
          <div class="card shadow-sm mb-4 indicador-card">
            <div class="card-header d-flex align-items-start justify-content-between gap-2">
              <div>
                <div class="fw-semibold fs-6"><?= htmlspecialchars($ind['nombre']) ?></div>
                <div class="d-flex gap-1 flex-wrap mt-1">
                  <span class="badge text-bg-light border"><?= $periodoLabel[$ind['periodicidad']] ?? $ind['periodicidad'] ?></span>
                  <?php if ($ind['unidad']): ?>
                    <span class="badge text-bg-light border"><?= htmlspecialchars($ind['unidad']) ?></span>
                  <?php endif; ?>
                  <?php
                    $iconoGraf = ['barra' => 'bar-chart', 'area' => 'graph-up', 'radar' => 'broadcast', 'torta' => 'pie-chart', 'combo' => 'bar-chart-line', 'linea' => 'graph-up-arrow'];
                    $labelGraf = ['barra' => 'Barras', 'area' => 'Área', 'radar' => 'Radar', 'torta' => 'Torta', 'combo' => 'Combo', 'linea' => 'Línea'];
                  ?>
                  <span class="badge text-bg-light border">
                    <i class="bi bi-<?= $iconoGraf[$tipoGraf] ?? 'graph-up-arrow' ?> me-1"></i>
                    <?= $labelGraf[$tipoGraf] ?? ucfirst($tipoGraf) ?>
                  </span>
                  <?php if (!empty($ind['comparativo_anual'])): ?>
                    <span class="badge text-bg-info border"><i class="bi bi-bar-chart-steps me-1"></i>Comparativo anual</span>
                  <?php endif; ?>
                </div>
              </div>
              <?php if ($puedeEditar): ?>
                <div class="d-flex gap-1 flex-shrink-0">
                  <button class="btn btn-sm btn-primary btn-agregar-valor"
                          data-id="<?= $ind['id'] ?>"
                          data-nombre="<?= htmlspecialchars($ind['nombre'], ENT_QUOTES) ?>"
                          data-periodicidad="<?= $ind['periodicidad'] ?>"
                          title="Agregar período">
                    <i class="bi bi-plus-lg"></i>
                  </button>
                  <button class="btn btn-sm btn-outline-danger btn-desasignar-ind"
                          data-id="<?= $ind['id'] ?>"
                          data-nombre="<?= htmlspecialchars($ind['nombre'], ENT_QUOTES) ?>"
                          title="Quitar indicador">
                    <i class="bi bi-x-lg"></i>
                  </button>
                </div>
              <?php endif; ?>
            </div>

            <div class="card-body">
              <div class="row g-3">

                <!-- KPIs del indicador -->
                <div class="col-md-3">
                  <div class="row g-2 mb-3">
                    <div class="col-6">
                      <div class="indicador-stat-label">Último valor</div>
                      <div class="indicador-stat-value text-<?= $colorBar ?>">
                        <?= $ultimoVal !== null ? number_format($ultimoVal, 2, ',', '.') : '—' ?>
                        <?php if ($ultimoVal !== null && $ind['unidad']): ?>
                          <small class="text-muted fw-normal"><?= htmlspecialchars($ind['unidad']) ?></small>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="col-6">
                      <div class="indicador-stat-label">Meta</div>
                      <div class="indicador-stat-value">
                        <?= $meta !== null ? number_format($meta, 2, ',', '.') : '—' ?>
                        <?php if ($meta !== null && $ind['unidad']): ?>
                          <small class="text-muted fw-normal"><?= htmlspecialchars($ind['unidad']) ?></small>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                  <?php if ($pct !== null): ?>
                    <div class="mb-3">
                      <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Avance vs meta</span>
                        <span class="fw-semibold text-<?= $colorBar ?>"><?= $pct ?>%</span>
                      </div>
                      <div class="progress" style="height:8px;">
                        <div class="progress-bar bg-<?= $colorBar ?>" style="width:<?= $pct ?>%;"></div>
                      </div>
                    </div>
                  <?php endif; ?>

                  <!-- Tabla de períodos -->
                  <?php if (!empty($valores)): ?>
                    <div class="small">
                      <div class="text-muted fw-semibold mb-1" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.06em;">Períodos registrados</div>
                      <div class="indicador-periodos-lista">
                        <?php foreach (array_reverse($valores) as $v): ?>
                          <div class="d-flex justify-content-between align-items-center py-1 border-bottom gap-2">
                            <span class="text-muted"><?= htmlspecialchars($v['periodo']) ?></span>
                            <span class="fw-semibold">
                              <?= $v['valor'] !== null ? number_format((float)$v['valor'], 2, ',', '.') : '—' ?>
                            </span>
                            <?php if ($puedeEditar): ?>
                              <form method="POST"
                                    action="index.php?modulo=indicadores&accion=eliminar-valor&id=<?= $empresa['id'] ?>"
                                    onsubmit="return confirm('¿Eliminar el período <?= htmlspecialchars($v['periodo']) ?>?')">
                                <input type="hidden" name="indicador_id" value="<?= $ind['id'] ?>">
                                <input type="hidden" name="periodo" value="<?= htmlspecialchars($v['periodo']) ?>">
                                <button type="submit" class="btn btn-link btn-sm text-danger p-0" title="Eliminar período">
                                  <i class="bi bi-trash" style="font-size:.75rem;"></i>
                                </button>
                              </form>
                            <?php endif; ?>
                          </div>
                        <?php endforeach; ?>
                      </div>
                    </div>
                  <?php else: ?>
                    <p class="text-muted small">Sin datos registrados aún.</p>
                  <?php endif; ?>
                </div>

                <!-- Gráfica -->
                <div class="col-md-9">
                  <?php if (!empty($valores)): ?>
                    <canvas class="indicador-chart"
                            data-tipo="<?= htmlspecialchars($tipoGraf) ?>"
                            data-comparativo="<?= (int)($ind['comparativo_anual'] ?? 0) ?>"
                            data-periodicidad="<?= htmlspecialchars($ind['periodicidad'] ?? '') ?>"
                            data-meta="<?= $meta !== null ? $meta : '' ?>"
                            data-unidad="<?= htmlspecialchars($ind['unidad'] ?? '') ?>"
                            data-labels="<?= htmlspecialchars(json_encode($labels, JSON_UNESCAPED_UNICODE)) ?>"
                            data-values="<?= htmlspecialchars(json_encode($vals)) ?>"
                            height="140"></canvas>
                  <?php else: ?>
                    <div class="d-flex align-items-center justify-content-center h-100 text-muted"
                         style="min-height:120px;border:2px dashed #dee2e6;border-radius:.5rem;">
                      <div class="text-center">
                        <i class="bi bi-bar-chart fs-2 opacity-25 d-block mb-1"></i>
                        <small>Agrega períodos para ver la gráfica</small>
                      </div>
                    </div>
                  <?php endif; ?>
                </div>

              </div>
            </div>
          </div>
          <?php endforeach; ?>

        <?php endif; ?>

      </div>
    </div>
  </main>

  <?php require_once __DIR__ . '/../../parciales/pie.php'; ?>
</div>

<?php if ($puedeEditar): ?>

<!-- Modal: asignar indicador -->
<?php if (!empty($disponibles)): ?>
<div class="modal fade" id="modalAsignar" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-plus-circle me-2 text-primary"></i>Asignar indicador</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="index.php?modulo=indicadores&accion=asignar&id=<?= $empresa['id'] ?>">
        <div class="modal-body">
          <label class="form-label fw-semibold">Indicador <span class="text-danger">*</span></label>
          <select name="indicador_id" class="form-select" required>
            <option value="">— Seleccione —</option>
            <?php foreach ($disponibles as $d): ?>
              <option value="<?= $d['id'] ?>">
                <?= htmlspecialchars($d['nombre']) ?>
                <?php if ($d['unidad']): ?>(<?= htmlspecialchars($d['unidad']) ?>)<?php endif; ?>
                — <?= $periodoLabel[$d['periodicidad']] ?? $d['periodicidad'] ?>
                <?php if ($d['meta'] !== null): ?> | Meta: <?= number_format((float)$d['meta'], 2, ',', '.') ?><?php endif; ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Asignar</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Modal: agregar/editar valor por período -->
<div class="modal fade" id="modalAgregarValor" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-plus-circle me-2 text-primary"></i>Registrar valor</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="index.php?modulo=indicadores&accion=actualizar&id=<?= $empresa['id'] ?>">
        <input type="hidden" name="indicador_id" id="inputIndicadorId">
        <div class="modal-body">
          <p class="fw-semibold mb-3" id="labelNombreIndicador"></p>
          <div class="mb-3">
            <label class="form-label fw-semibold">Período <span class="text-danger">*</span></label>
            <select name="periodo" id="selectPeriodo" class="form-select" required></select>
            <div class="form-text" id="periodoHelp"></div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Valor</label>
            <input type="number" name="valor" id="inputValor" class="form-control"
                   step="0.01" placeholder="Ingrese el valor">
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Observaciones</label>
            <textarea name="observaciones" id="inputObservaciones" class="form-control" rows="2"
                      placeholder="Comentarios opcionales..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal: confirmar desasignar -->
<div class="modal fade" id="modalDesasignar" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Quitar indicador</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        ¿Quitar el indicador <strong id="labelNombreDesasignar"></strong> de esta empresa? Se perderán todos los valores registrados.
      </div>
      <form method="POST" action="index.php?modulo=indicadores&accion=desasignar&id=<?= $empresa['id'] ?>">
        <input type="hidden" name="indicador_id" id="inputDesasignarId">
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i>Quitar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php endif; ?>
