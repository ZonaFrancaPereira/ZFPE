<?php
/** @var array|null $empresa */
/** @var array      $etapas */
/** @var float      $avance */
/** @var array      $todasEmpresas */
/** @var array      $documentosPorRequisito */
$pageTitle     = 'Cronograma — ZFPE';
$activePage    = 'cronograma';
// componentes.css trae las clases compartidas (meta-pill, file-chip);
// cronograma.css trae los estilos propios de esta vista (timeline, acentos por estado).
$pageStyles    = ['vista/assets/css/componentes.css', 'vista/assets/css/cronograma.css'];
$esOperaciones = ($_SESSION['usuario_rol'] ?? '') === 'operaciones';
$esAdmin       = ($_SESSION['usuario_rol'] ?? '') === 'admin';

// Mapas de presentación: estado interno -> [color bootstrap, ícono bootstrap-icons]
$colorEstado = [
    'pendiente'   => ['secondary', 'bi-hourglass-split'],
    'en_progreso' => ['zf-teal',   'bi-arrow-repeat'],
    'completa'    => ['success',   'bi-check-circle-fill'],
];
$labelEstado = [
    'pendiente'   => 'Pendiente',
    'en_progreso' => 'En progreso',
    'completa'    => 'Completada',
];
$colorReq = [
    'pendiente'   => ['secondary', 'bi-hourglass-split'],
    'en_progreso' => ['zf-teal',   'bi-arrow-repeat'],
    'cumplido'    => ['success',   'bi-check-circle-fill'],
    'no_aplica'   => ['light',     'bi-dash-circle'],
];
$iconoDoc = fn(string $nombre) => match (strtolower(pathinfo($nombre, PATHINFO_EXTENSION))) {
    'pdf'              => 'bi-file-earmark-pdf text-danger',
    'doc','docx'       => 'bi-file-earmark-word text-primary',
    'xls','xlsx'       => 'bi-file-earmark-excel text-success',
    'jpg','jpeg','png' => 'bi-file-earmark-image text-warning',
    'zip'              => 'bi-file-earmark-zip text-secondary',
    default            => 'bi-file-earmark text-muted',
};
?>
<?php require_once __DIR__ . '/../../parciales/cabecera.php'; ?>

<div class="app-wrapper">
  <?php require_once __DIR__ . '/../../parciales/navegacion.php'; ?>
  <?php require_once __DIR__ . '/../../parciales/menu_lateral.php'; ?>

  <main class="app-main">
    <div class="app-content-header encabezado-zf">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6"><h3 class="mb-0 titulo-zf">Cronograma</h3></div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item active">Cronograma</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="app-content">
      <div class="container-fluid">

        <?php if (!$empresa && ($esOperaciones || $esAdmin) && !empty($todasEmpresas)): ?>
          <!-- Operaciones sin empresa seleccionada: mostrar selector -->
          <div class="card shadow-sm">
            <div class="card-header">
              <h5 class="card-title mb-0"><i class="bi bi-building me-2 text-primary"></i>Seleccionar empresa</h5>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Empresa</th>
                      <th>NIT</th>
                      <th style="min-width:140px;">Avance general</th>
                      <th class="text-end">Cronograma</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($todasEmpresas as $e): ?>
                    <tr>
                      <td>
                        <div class="fw-semibold"><?= htmlspecialchars($e['razon_social']) ?></div>
                        <small class="text-muted"><?= htmlspecialchars($e['representante'] ?? '') ?></small>
                      </td>
                      <td class="text-muted small"><?= htmlspecialchars($e['nit']) ?></td>
                      <td>
                        <?php $av = (float)($e['avance_general'] ?? 0); ?>
                        <div class="d-flex align-items-center gap-2">
                          <div class="progress flex-grow-1" style="height:6px;">
                            <div class="progress-bar bg-success" style="width:<?= $av ?>%"></div>
                          </div>
                          <small class="text-muted fw-semibold" style="min-width:34px;"><?= $av ?>%</small>
                        </div>
                      </td>
                      <td class="text-end">
                        <a href="index.php?modulo=cronograma&id=<?= $e['id'] ?>"
                           class="btn btn-sm btn-outline-primary">
                          <i class="bi bi-calendar3 me-1"></i>Ver
                        </a>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        <?php elseif (!$empresa): ?>
          <div class="card shadow-sm">
            <div class="card-body text-center py-5 text-muted">
              <i class="bi bi-building-x fs-1 opacity-25 d-block mb-3"></i>
              <h5>Sin empresa asignada</h5>
              <p class="mb-0">Tu cuenta aún no tiene una empresa vinculada. Contacta al equipo de operaciones.</p>
            </div>
          </div>
        <?php elseif (empty($etapas)): ?>
          <div class="card shadow-sm">
            <div class="card-body text-center py-5 text-muted">
              <i class="bi bi-calendar-x fs-1 opacity-25 d-block mb-3"></i>
              <h5>Sin etapas asignadas</h5>
              <p class="mb-0">Aún no se han configurado etapas para tu empresa.</p>
            </div>
          </div>
        <?php else: ?>

          <?php if ($esOperaciones || $esAdmin): ?>
          <div class="mb-3 d-flex gap-2">
            <a href="index.php?modulo=empresas&accion=ver&id=<?= $empresa['id'] ?>"
               class="btn btn-outline-secondary btn-sm">
              <i class="bi bi-arrow-left me-1"></i> Volver a la empresa
            </a>
            <a href="index.php?modulo=cronograma" class="btn btn-outline-secondary btn-sm">
              <i class="bi bi-list me-1"></i> Ver todas las empresas
            </a>
          </div>
          <?php endif; ?>

          <!-- Encabezado empresa + avance global -->
          <div class="card shadow-sm mb-4 border-0 bg-body-secondary">
            <div class="card-body py-3">
              <div class="row align-items-center g-3">
                <div class="col-md-6">
                  <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                         style="width:50px;height:50px;font-size:1.2rem;">
                      <?= mb_strtoupper(mb_substr($empresa['razon_social'], 0, 1)) ?>
                    </div>
                    <div>
                      <h5 class="mb-0"><?= htmlspecialchars($empresa['razon_social']) ?></h5>
                      <span class="meta-pill mt-1"><i class="bi bi-upc-scan me-1"></i>NIT <?= htmlspecialchars($empresa['nit']) ?></span>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="d-flex align-items-center gap-3">
                    <div class="flex-grow-1">
                      <div class="d-flex justify-content-between mb-1">
                        <small class="fw-semibold text-muted">Avance general del proyecto</small>
                        <small class="fw-bold text-success"><?= $avance ?>%</small>
                      </div>
                      <div class="progress" style="height:10px;">
                        <div class="progress-bar bg-success" style="width:<?= $avance ?>%"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <?php
          // Agrupar etapas por fase (usado tanto en stepper como en timeline)
          $cronFaseGrupos = [];
          foreach ($etapas as $et) {
              $fid = $et['fase_id'] ?? 0;
              if (!isset($cronFaseGrupos[$fid])) {
                  $cronFaseGrupos[$fid] = [
                      'nombre' => $et['fase_nombre'] ?? null,
                      'orden'  => $et['fase_orden']  ?? 999,
                      'etapas' => [],
                  ];
              }
              $cronFaseGrupos[$fid]['etapas'][] = $et;
          }
          ?>

          <!-- Steppers horizontales por fase -->
          <?php foreach ($cronFaseGrupos as $cFase): ?>
          <?php
            $faseAvances   = array_column($cFase['etapas'], 'avance');
            $faseAvancePct = count($faseAvances) > 0
                ? round(array_sum($faseAvances) / count($faseAvances), 1)
                : 0;
            $faseColor = $faseAvancePct >= 100 ? 'success' : ($faseAvancePct > 0 ? 'zf-teal' : 'secondary');
          ?>
          <div class="card shadow-sm mb-3 fase-card">
            <!-- Cabecera de fase -->
            <div class="card-header py-2 d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center gap-2">
                <?php if ($cFase['nombre']): ?>
                  <span class="badge text-white bg-zf-navy py-1 px-2">
                    <i class="bi bi-collection me-1"></i><?= htmlspecialchars($cFase['nombre']) ?>
                  </span>
                <?php else: ?>
                  <span class="text-muted small fw-semibold">Etapas sin fase asignada</span>
                <?php endif; ?>
              </div>
              <div class="d-flex align-items-center gap-2">
                <div class="progress" style="width:120px;height:7px;">
                  <div class="progress-bar bg-<?= $faseColor ?>" style="width:<?= $faseAvancePct ?>%"></div>
                </div>
                <small class="fw-bold text-<?= $faseColor ?>" style="min-width:38px;"><?= $faseAvancePct ?>%</small>
              </div>
            </div>
            <!-- Stepper horizontal de las etapas de esta fase -->
            <div class="card-body py-3 px-4 overflow-auto">
              <div class="d-flex align-items-center gap-0">
                <?php foreach ($cFase['etapas'] as $si => $etapa): ?>
                <?php [$color] = $colorEstado[$etapa['estado_progreso']] ?? ['secondary', 'bi-circle']; ?>
                <div class="d-flex align-items-center <?= $si > 0 ? 'flex-grow-1' : '' ?>">
                  <?php if ($si > 0): ?>
                    <div class="flex-grow-1 border-top border-2
                                <?= $etapa['estado_progreso'] === 'completa' ? 'border-success' : ($etapa['estado_progreso'] === 'en_progreso' ? 'border-zf-teal' : 'border-secondary') ?>"
                         style="min-width:24px;"></div>
                  <?php endif; ?>
                  <a href="#etapa-<?= $etapa['id'] ?>" class="text-decoration-none stepper-node">
                    <div class="d-flex flex-column align-items-center text-center flex-shrink-0" style="min-width:72px;">
                      <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold mb-1 text-white bg-<?= $color ?>"
                           style="width:38px;height:38px;font-size:.85rem;
                                  <?= $etapa['estado_progreso'] === 'en_progreso' ? 'box-shadow:0 0 0 4px rgba(var(--zf-teal-rgb),.25)' : '' ?>">
                        <?php if ($etapa['estado_progreso'] === 'completa'): ?>
                          <i class="bi bi-check-lg"></i>
                        <?php else: ?>
                          <?= $si + 1 ?>
                        <?php endif; ?>
                      </div>
                      <small class="text-muted" style="font-size:.65rem;max-width:72px;line-height:1.2;">
                        <?= htmlspecialchars($etapa['nombre']) ?>
                      </small>
                      <small class="fw-semibold text-<?= $color ?>" style="font-size:.6rem;">
                        <?= (float)$etapa['avance'] ?>%
                      </small>
                    </div>
                  </a>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
          <?php endforeach; ?>

          <!-- Detalle de cada etapa (timeline vertical) -->
          <div class="timeline-container">
            <?php $globalIdx = 0; foreach ($cronFaseGrupos as $cFase): ?>

            <?php if ($cFase['nombre']): ?>
            <div class="d-flex align-items-center gap-2 mb-3 mt-1 ms-4">
              <span class="badge text-white bg-zf-navy fs-6 py-1 px-3">
                <i class="bi bi-collection me-1"></i><?= htmlspecialchars($cFase['nombre']) ?>
              </span>
            </div>
            <?php endif; ?>

            <?php foreach ($cFase['etapas'] as $localIdx => $etapa):
                  $i = $globalIdx++;
            ?>
            <?php
              [$color, $icon] = $colorEstado[$etapa['estado_progreso']] ?? ['secondary', 'bi-circle'];
              $label   = $labelEstado[$etapa['estado_progreso']] ?? $etapa['estado_progreso'];
              $avanceEtapa = (float) $etapa['avance'];
              $totalReq = (int) $etapa['total_requisitos'];
              $okReq    = (int) $etapa['requisitos_ok'];
              $esActual = $etapa['estado_progreso'] === 'en_progreso';
              $collapseId = 'etapa_' . $etapa['id'];
            ?>
            <div id="etapa-<?= $etapa['id'] ?>" class="timeline-item mb-0 <?= $esActual ? 'timeline-active' : '' ?>">
              <div class="timeline-marker bg-<?= $color ?>">
                <?php if ($etapa['estado_progreso'] === 'completa'): ?>
                  <i class="bi bi-check-lg text-white"></i>
                <?php else: ?>
                  <span class="text-white fw-bold" style="font-size:.8rem;"><?= $localIdx + 1 ?></span>
                <?php endif; ?>
              </div>
              <div class="timeline-content ms-4 mb-4">
                <div class="card etapa-card etapa-card--<?= $etapa['estado_progreso'] ?> <?= $esActual ? 'border-zf-teal border-2' : '' ?>">
                  <div class="card-header d-flex align-items-center justify-content-between gap-2 py-2">
                    <div class="d-flex align-items-center gap-2">
                      <span class="fw-semibold"><?= htmlspecialchars($etapa['nombre']) ?></span>
                      <span class="badge bg-<?= $color ?> small"><i class="bi <?= $icon ?> me-1"></i><?= $label ?></span>
                      <?php if ($esActual): ?>
                        <span class="badge bg-zf-teal bg-opacity-25 text-zf-teal small">
                          <i class="bi bi-arrow-right-circle me-1"></i>Etapa actual
                        </span>
                      <?php endif; ?>
                    </div>
                    <?php if (!empty($etapa['requisitos'])): ?>
                    <button class="btn btn-sm btn-outline-secondary py-0 collapse-toggle" type="button"
                            data-bs-toggle="collapse" data-bs-target="#<?= $collapseId ?>">
                      <i class="bi bi-chevron-down"></i>
                    </button>
                    <?php endif; ?>
                  </div>
                  <div class="card-body py-2">
                    <div class="row align-items-center g-3">
                      <div class="col-md-5">
                        <div class="d-flex align-items-center gap-2">
                          <div class="progress flex-grow-1" style="height:8px;">
                            <div class="progress-bar bg-<?= $color ?>" style="width:<?= $avanceEtapa ?>%"></div>
                          </div>
                          <small class="fw-bold text-<?= $color ?>"><?= $avanceEtapa ?>%</small>
                        </div>
                        <?php if ($totalReq > 0): ?>
                          <small class="text-muted"><?= $okReq ?>/<?= $totalReq ?> requisitos completados</small>
                        <?php endif; ?>
                      </div>
                      <div class="col-md-7">
                        <div class="d-flex flex-wrap gap-2">
                          <?php if ($etapa['fecha_inicio']): ?>
                            <span class="meta-pill">
                              <i class="bi bi-play-circle me-1 text-primary"></i>
                              Inicio: <strong class="ms-1"><?= date('d/m/Y', strtotime($etapa['fecha_inicio'])) ?></strong>
                            </span>
                          <?php endif; ?>
                          <?php if ($etapa['fecha_completado']): ?>
                            <span class="meta-pill meta-pill--success">
                              <i class="bi bi-check-circle me-1"></i>
                              Completada: <strong class="ms-1"><?= date('d/m/Y', strtotime($etapa['fecha_completado'])) ?></strong>
                            </span>
                          <?php elseif ($etapa['estado_progreso'] !== 'pendiente'): ?>
                            <span class="meta-pill"><i class="bi bi-hourglass-split me-1"></i>En curso</span>
                          <?php else: ?>
                            <span class="meta-pill"><i class="bi bi-clock me-1"></i>No iniciada</span>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>

                  <?php if (!empty($etapa['requisitos'])): ?>
                  <div class="collapse <?= $esActual ? 'show' : '' ?>" id="<?= $collapseId ?>">
                    <div class="card-body border-top py-2 px-3">
                      <div class="row g-2">
                        <?php foreach ($etapa['requisitos'] as $req): ?>
                        <?php
                          [$rColor, $rIcon] = $colorReq[$req['estado_req']] ?? ['secondary', 'bi-circle'];
                          $docsReq = $documentosPorRequisito[$req['id']] ?? [];
                          $reqVencido = $req['fecha_vencimiento'] && $req['fecha_vencimiento'] < date('Y-m-d') && $req['estado_req'] !== 'cumplido';
                        ?>
                        <div class="col-md-6">
                          <div id="req-<?= $req['id'] ?>" class="requisito-mini-card requisito-mini-card--<?= $req['estado_req'] ?>">
                            <!-- Cabecera del requisito -->
                            <div class="d-flex align-items-start gap-2 p-2">
                              <i class="bi <?= $rIcon ?> text-<?= $rColor ?> mt-1 flex-shrink-0"></i>
                              <div class="min-w-0 flex-grow-1">
                                <div class="small fw-semibold text-truncate"><?= htmlspecialchars($req['nombre']) ?></div>
                                <div class="d-flex flex-wrap gap-1 mt-1">
                                  <?php if ($req['entidad_nombre']): ?>
                                    <span class="meta-pill" style="font-size:.6rem;padding:.1rem .4rem;">
                                      <?= htmlspecialchars($req['entidad_nombre']) ?>
                                    </span>
                                  <?php endif; ?>
                                  <span class="badge bg-<?= $rColor ?>" style="font-size:.6rem;">
                                    <?= ucfirst(str_replace('_', ' ', $req['estado_req'])) ?>
                                  </span>
                                  <?php if ($req['fecha_vencimiento']): ?>
                                    <span class="meta-pill <?= $reqVencido ? 'meta-pill--danger' : '' ?>" style="font-size:.6rem;padding:.1rem .4rem;">
                                      <i class="bi bi-calendar-event me-1"></i><?= date('d/m/Y', strtotime($req['fecha_vencimiento'])) ?>
                                    </span>
                                  <?php endif; ?>
                                </div>
                              </div>
                              <?php if (!empty($docsReq)): ?>
                              <span class="badge text-bg-primary flex-shrink-0" title="Documentos">
                                <i class="bi bi-paperclip"></i> <?= count($docsReq) ?>
                              </span>
                              <?php endif; ?>
                            </div>
                            <!-- Documentos del requisito -->
                            <?php if (!empty($docsReq)): ?>
                            <div class="border-top px-2 py-2 d-flex flex-wrap gap-1" style="background:rgba(0,0,0,.03);">
                              <?php foreach ($docsReq as $doc): ?>
                              <a href="index.php?modulo=documentos&accion=descargar&id=<?= $doc['id'] ?>"
                                 class="file-chip" title="Descargar <?= htmlspecialchars($doc['nombre_original']) ?>">
                                <i class="bi <?= $iconoDoc($doc['nombre_original']) ?>"></i>
                                <span class="text-truncate"><?= htmlspecialchars($doc['nombre_original']) ?></span>
                                <i class="bi bi-download text-muted ms-1"></i>
                              </a>
                              <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                          </div>
                        </div>
                        <?php endforeach; ?>
                      </div>
                    </div>
                  </div>
                  <?php endif; ?>

                </div>
              </div>
            </div>
            <?php endforeach; // etapas del grupo ?>
            <?php endforeach; // grupos de fase ?>
          </div>

        <?php endif; ?>

      </div>
    </div>
  </main>

  <?php require_once __DIR__ . '/../../parciales/pie.php'; ?>
</div>
