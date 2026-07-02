<?php
/** @var array $empresa */
/** @var array $etapas */
/** @var array $requisitosPorEtapa */
/** @var array $itemsPorRequisito */
/** @var array $documentosPorRequisito */
/** @var array $historialPorRequisito */
$pageTitle   = 'Seguimiento — ' . htmlspecialchars($empresa['razon_social']) . ' — ZFIP-E';
$activePage  = 'empresas';
$pageStyles  = ['vista/assets/css/componentes.css', 'vista/assets/css/seguimiento.css'];
$pageScripts = ['vista/assets/js/seguimiento.js'];
?>
<?php require_once __DIR__ . '/../../parciales/cabecera.php'; ?>


<?php
$estadoBadge = [
    'pendiente'   => 'secondary',
    'en_progreso' => 'primary',
    'cumplido'    => 'success',
    'no_aplica'   => 'light',
    'completa'    => 'success',
];
$avanceTotal = count($etapas) > 0
    ? round(array_sum(array_column($etapas, 'porcentaje_avance')) / count($etapas), 1)
    : 0;
?>

<div class="app-wrapper">
  <?php require_once __DIR__ . '/../../parciales/navegacion.php'; ?>
  <?php require_once __DIR__ . '/../../parciales/menu_lateral.php'; ?>

  <main class="app-main">
    <div class="app-content-header encabezado-zf">
      <div class="container-fluid">
        <div class="row align-items-center">
          <div class="col-sm-6">
            <h3 class="mb-0 titulo-zf">
              <i class="bi bi-clipboard-check-fill me-2"></i>Seguimiento
            </h3>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item"><a href="index.php?modulo=empresas">Empresas</a></li>
              <li class="breadcrumb-item">
                <a href="index.php?modulo=empresas&accion=ver&id=<?= $empresa['id'] ?>"
                   class="text-truncate d-inline-block align-bottom" style="max-width:260px;"
                   title="<?= htmlspecialchars($empresa['razon_social']) ?>">
                  <?= htmlspecialchars($empresa['razon_social']) ?>
                </a>
              </li>
              <li class="breadcrumb-item active">Seguimiento</li>
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
              <div class="col-md-5 d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                     style="width:46px;height:46px;font-size:1.2rem;">
                  <?= mb_strtoupper(mb_substr($empresa['razon_social'], 0, 1)) ?>
                </div>
                <div>
                  <h5 class="mb-0"><?= htmlspecialchars($empresa['razon_social']) ?></h5>
                  <small class="text-muted">NIT: <?= htmlspecialchars($empresa['nit']) ?></small>
                </div>
              </div>
              <div class="col-md-5">
                <div class="d-flex align-items-center gap-2 mb-1">
                  <div class="progress flex-grow-1" style="height:10px;">
                    <div class="progress-bar bg-success" style="width:<?= $avanceTotal ?>%"></div>
                  </div>
                  <span class="fw-bold text-success"><?= $avanceTotal ?>%</span>
                </div>
                <small class="text-muted">Avance general del proyecto</small>
              </div>
              <div class="col-md-2 text-md-end">
                <a href="index.php?modulo=empresas&accion=ver&id=<?= $empresa['id'] ?>"
                   class="btn btn-outline-secondary btn-sm">
                  <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Resumen de etapas -->
        <div class="d-flex justify-content-end gap-2 mb-2">
          <button type="button" id="btnExpandirTodas" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrows-expand me-1"></i> Expandir todas
          </button>
          <button type="button" id="btnColapsarTodas" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrows-collapse me-1"></i> Colapsar todas
          </button>
        </div>
        <div class="row g-2 mb-4">
          <?php foreach ($etapas as $etapa): ?>
          <div class="col">
            <a href="#etapa-<?= $etapa['id'] ?>" class="text-decoration-none etapa-resumen-link"
               data-collapse-target="etapa-body-<?= $etapa['id'] ?>">
              <div class="card shadow-sm h-100 text-center py-2 px-2 etapa-resumen-card etapa-resumen-card--<?= $etapa['estado_progreso'] ?>">
                <div class="small fw-semibold text-truncate mb-1 etapa-titulo"><?= htmlspecialchars($etapa['nombre']) ?></div>
                <div class="progress mb-1" style="height:5px;">
                  <div class="progress-bar" style="width:<?= $etapa['porcentaje_avance'] ?>%; background-color:<?= $etapa['porcentaje_avance'] >= 100 ? '#198754' : ($etapa['porcentaje_avance'] > 0 ? '#1993b8' : '#adb5bd') ?>;"></div>
                </div>
                <small class="text-muted"><?= number_format($etapa['porcentaje_avance'], 0) ?>%</small>
              </div>
            </a>
          </div>
          <?php endforeach; ?>
        </div>

        <!-- Etapas y requisitos agrupadas por fase -->
        <?php
        $segFaseGrupos = [];
        foreach ($etapas as $et) {
            $fid = $et['fase_id'] ?? 0;
            if (!isset($segFaseGrupos[$fid])) {
                $segFaseGrupos[$fid] = ['nombre' => $et['fase_nombre'] ?? null, 'etapas' => []];
            }
            $segFaseGrupos[$fid]['etapas'][] = $et;
        }
        ?>
        <?php foreach ($segFaseGrupos as $segFase): ?>

        <?php if ($segFase['nombre']): ?>
        <div class="d-flex align-items-center gap-2 mb-3 mt-2">
          <span class="badge text-bg-danger py-2 px-3" style="font-size:.85rem;">
            <i class="bi bi-collection me-1"></i><?= htmlspecialchars($segFase['nombre']) ?>
          </span>
          <hr class="flex-grow-1 my-0">
        </div>
        <?php endif; ?>

        <?php foreach ($segFase['etapas'] as $etapa): ?>
        <div id="etapa-<?= $etapa['id'] ?>" class="card shadow-sm mb-3 etapa-card etapa-card--<?= $etapa['estado_progreso'] ?>">
          <div class="card-header etapa-header d-flex align-items-center justify-content-between py-2"
               role="button"
               data-bs-toggle="collapse" data-bs-target="#etapa-body-<?= $etapa['id'] ?>"
               aria-expanded="false" aria-controls="etapa-body-<?= $etapa['id'] ?>">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-chevron-down etapa-chevron"></i>
              <i class="bi bi-diagram-3 etapa-icono"></i>
              <span class="fw-semibold etapa-titulo"><?= htmlspecialchars($etapa['nombre']) ?></span>
              <span class="badge text-bg-<?= $estadoBadge[$etapa['estado_progreso']] ?? 'secondary' ?>">
                <?= ucfirst(str_replace('_', ' ', $etapa['estado_progreso'])) ?>
              </span>
            </div>
            <div class="d-flex align-items-center gap-2">
              <div class="progress" style="width:100px;height:6px;">
                <div class="progress-bar" style="width:<?= $etapa['porcentaje_avance'] ?>%; background-color:<?= $etapa['porcentaje_avance'] >= 100 ? '#198754' : '#1993b8' ?>;"></div>
              </div>
              <small class="fw-semibold text-muted"><?= number_format($etapa['porcentaje_avance'], 1) ?>%</small>
            </div>
          </div>

          <div class="card-body p-0 collapse etapa-collapse" id="etapa-body-<?= $etapa['id'] ?>">
            <?php
            $reqsDeEtapa = $requisitosPorEtapa[$etapa['id']] ?? [];
            if (empty($reqsDeEtapa)):
            ?>
              <div class="text-center text-muted py-4 small">
                No hay requisitos configurados para esta etapa.
              </div>
            <?php else: ?>
              <?php foreach ($reqsDeEtapa as $i => $req): ?>
              <?php
                $itemsDeReq = $itemsPorRequisito[$req['id']] ?? [];
                $bloqueado  = $req['estado_req'] === 'cumplido';
                $histReq    = $historialPorRequisito[$req['id']] ?? [];
              ?>

              <div id="req-<?= $req['id'] ?>"
                   class="requisito-card requisito-card--<?= $req['estado_req'] ?> <?= $i % 2 === 1 ? 'requisito-card--alt' : '' ?>">
                <!-- Encabezado del requisito -->
                <div class="d-flex align-items-center justify-content-between px-3 py-2 requisito-header">
                  <div class="d-flex align-items-center gap-2">
                    <span class="requisito-numero"><?= $i + 1 ?></span>
                    <i class="bi bi-list-check text-warning"></i>
                    <span class="fw-semibold small"><?= htmlspecialchars($req['nombre']) ?></span>
                    <?php if ($req['entidad_nombre']): ?>
                      <span class="badge text-bg-light border text-muted"><?= htmlspecialchars($req['entidad_nombre']) ?></span>
                    <?php endif; ?>
                    <?php if ($req['obligatorio']): ?>
                      <span class="badge text-bg-danger" style="font-size:.6rem;">Obligatorio</span>
                    <?php endif; ?>
                  </div>
                  <div class="d-flex align-items-center gap-2">
                    <?php if (!empty($histReq)): ?>
                    <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2"
                            data-bs-toggle="collapse" data-bs-target="#historial-<?= $req['id'] ?>">
                      <i class="bi bi-clock-history me-1"></i>Historial (<?= count($histReq) ?>)
                    </button>
                    <?php endif; ?>
                    <span class="badge text-bg-<?= $estadoBadge[$req['estado_req']] ?? 'secondary' ?>">
                      <?= ucfirst(str_replace('_', ' ', $req['estado_req'])) ?>
                    </span>
                    <?php if ($bloqueado): ?>
                    <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 btn-desbloquear-req"
                            data-bs-toggle="modal" data-bs-target="#modalDesbloquearReq"
                            data-target="req-fields-<?= $req['id'] ?>"
                            data-nombre="<?= htmlspecialchars($req['nombre']) ?>"
                            title="Habilitar edición">
                      <i class="bi bi-unlock"></i>
                    </button>
                    <?php endif; ?>
                  </div>
                </div>

                <!-- Historial de cambios -->
                <?php if (!empty($histReq)): ?>
                <div class="collapse" id="historial-<?= $req['id'] ?>">
                  <div class="px-3 pt-2 pb-1 border-bottom">
                    <div class="border rounded p-2 bg-body-tertiary">
                      <div class="fw-semibold small text-muted text-uppercase mb-2" style="letter-spacing:.04em;">
                        <i class="bi bi-clock-history me-1"></i>Historial de cambios
                      </div>
                      <ul class="list-unstyled mb-0 small">
                        <?php foreach ($histReq as $h): ?>
                        <li class="d-flex gap-2 py-2 border-bottom border-opacity-25">
                          <div class="text-muted flex-shrink-0" style="width:120px;">
                            <?= date('d/m/Y H:i', strtotime($h['created_at'])) ?>
                          </div>
                          <div class="min-w-0">
                            <span class="badge text-bg-<?= $estadoBadge[$h['estado_nuevo']] ?? 'secondary' ?>">
                              <?= ucfirst(str_replace('_', ' ', $h['estado_nuevo'])) ?>
                            </span>
                            <?php if ($h['estado_anterior'] && $h['estado_anterior'] !== $h['estado_nuevo']): ?>
                              <span class="text-muted">(antes: <?= ucfirst(str_replace('_', ' ', $h['estado_anterior'])) ?>)</span>
                            <?php endif; ?>
                            <?php if ($h['observaciones']): ?>
                              <div class="mt-1"><?= nl2br(htmlspecialchars($h['observaciones'])) ?></div>
                            <?php endif; ?>
                            <?php if ($h['documento_nombre']): ?>
                              <div class="text-primary mt-1">
                                <i class="bi bi-paperclip me-1"></i><?= htmlspecialchars($h['documento_nombre']) ?>
                              </div>
                            <?php endif; ?>
                            <div class="text-muted mt-1" style="font-size:.7rem;">
                              <?= $h['registrado_por_nombre'] ? 'por ' . htmlspecialchars($h['registrado_por_nombre']) : '' ?>
                            </div>
                          </div>
                        </li>
                        <?php endforeach; ?>
                      </ul>
                    </div>
                  </div>
                </div>
                <?php endif; ?>

                <!-- Formulario del requisito -->
                <?php
                  $docsReq   = $documentosPorRequisito[$req['id']] ?? [];
                  $reqDoc    = (bool) ($req['requiere_documento'] ?? false);
                  $tieneDoc  = !empty($docsReq);
                  $sinDocOblig = $reqDoc && !$tieneDoc;
                ?>
                <form method="POST"
                      action="index.php?modulo=seguimiento&accion=guardar&id=<?= $empresa['id'] ?>"
                      enctype="multipart/form-data">
                  <input type="hidden" name="requisito_id" value="<?= $req['id'] ?>">

                  <div class="px-3 py-3" id="req-fields-<?= $req['id'] ?>">
                    <?php if ($bloqueado): ?>
                    <div class="alert alert-success py-2 px-3 small mb-3">
                      <i class="bi bi-lock-fill me-1"></i> Este requisito está cumplido y bloqueado para evitar cambios accidentales. Usa <i class="bi bi-unlock"></i> para habilitar la edición.
                    </div>
                    <?php endif; ?>
                    <div class="row g-3">

                      <!-- Ítems -->
                      <?php if (!empty($itemsDeReq)): ?>
                      <div class="col-md-6">
                        <label class="form-label fw-semibold small text-muted text-uppercase mb-2" style="letter-spacing:.04em;">
                          Ítems
                        </label>
                        <?php foreach ($itemsDeReq as $item): ?>
                        <div class="form-check mb-1">
                          <input class="form-check-input" type="checkbox"
                                 name="items[<?= $item['id'] ?>]" value="1"
                                 id="item-<?= $item['id'] ?>"
                                 <?= $item['cumplido'] ? 'checked' : '' ?>
                                 <?= $bloqueado ? 'disabled' : '' ?>>
                          <label class="form-check-label small" for="item-<?= $item['id'] ?>">
                            <?= htmlspecialchars($item['nombre']) ?>
                            <?php if ($item['obligatorio']): ?>
                              <span class="text-danger">*</span>
                            <?php endif; ?>
                          </label>
                        </div>
                        <?php endforeach; ?>
                        <small class="text-muted d-block mt-1">
                          <span class="text-danger">*</span> Obligatorio — bloquea el cierre del requisito.
                        </small>
                      </div>
                      <?php endif; ?>

                      <!-- Estado y notas -->
                      <div class="col-md-<?= !empty($itemsDeReq) ? '6' : '12' ?>">
                        <div class="row g-2">
                          <div class="col-sm-6">
                            <label class="form-label fw-semibold small text-muted text-uppercase mb-1" style="letter-spacing:.04em;">Estado</label>
                            <select name="estado_req" class="form-select form-select-sm" <?= $bloqueado ? 'disabled' : '' ?>>
                              <option value="pendiente"   <?= $req['estado_req'] === 'pendiente'   ? 'selected' : '' ?>>Pendiente</option>
                              <option value="en_progreso" <?= $req['estado_req'] === 'en_progreso' ? 'selected' : '' ?>>En progreso</option>
                              <option value="cumplido"    <?= $req['estado_req'] === 'cumplido'    ? 'selected' : '' ?>>Cumplido</option>
                              <option value="no_aplica"   <?= $req['estado_req'] === 'no_aplica'   ? 'selected' : '' ?>>No aplica</option>
                            </select>
                            <small class="text-muted">El estado se recalcula automáticamente según los ítems obligatorios.</small>
                          </div>
                          <?php if ($req['requiere_fecha_vencimiento']): ?>
                          <div class="col-sm-6">
                            <label class="form-label fw-semibold small text-muted text-uppercase mb-1" style="letter-spacing:.04em;">Vencimiento</label>
                            <?php if ($req['fecha_vencimiento']): ?>
                              <input type="text" class="form-control form-control-sm" disabled data-permanent="1"
                                     value="<?= date('d/m/Y', strtotime($req['fecha_vencimiento'])) ?>">
                              <small class="text-muted">Fecha fijada — no se puede modificar.</small>
                            <?php else: ?>
                              <input type="date" name="fecha_vencimiento" class="form-control form-control-sm"
                                     <?= $bloqueado ? 'disabled' : '' ?>>
                              <small class="text-muted">Se fija una sola vez al guardar.</small>
                            <?php endif; ?>
                          </div>
                          <?php endif; ?>
                          <div class="col-sm-6">
                            <label class="form-label fw-semibold small text-muted text-uppercase mb-1" style="letter-spacing:.04em;">Fecha de cumplimiento</label>
                            <input type="text" class="form-control form-control-sm" disabled data-permanent="1"
                                   value="<?= $req['fecha_cumplimiento'] ? date('d/m/Y', strtotime($req['fecha_cumplimiento'])) : '—' ?>">
                            <small class="text-muted">Se registra automáticamente al marcar "Cumplido".</small>
                          </div>
                          <div class="col-12">
                            <label class="form-label fw-semibold small text-muted text-uppercase mb-1" style="letter-spacing:.04em;">Observaciones</label>
                            <textarea name="observaciones" class="form-control form-control-sm" rows="2"
                                      placeholder="Notas del seguimiento..." <?= $bloqueado ? 'disabled' : '' ?>><?= htmlspecialchars($req['observaciones'] ?? '') ?></textarea>
                          </div>
                        </div>
                      </div>

                      <!-- SECCIÓN DOCUMENTOS (cuando requiere_documento = 1) -->
                      <?php if ($reqDoc): ?>
                      <div class="col-12">
                        <div class="border rounded p-2 <?= $sinDocOblig ? 'border-warning' : 'border-secondary border-opacity-25' ?>"
                             style="background:<?= $sinDocOblig ? 'rgba(255,193,7,.06)' : 'rgba(0,0,0,.02)' ?>;">

                          <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-semibold small text-muted text-uppercase" style="letter-spacing:.04em;">
                              <i class="bi bi-paperclip me-1 text-primary"></i>Documento soporte
                              <span class="badge text-bg-danger ms-1" style="font-size:.6rem;">Requerido</span>
                            </span>
                            <?php if ($tieneDoc): ?>
                              <span class="badge text-bg-success" style="font-size:.65rem;">
                                <i class="bi bi-check-lg me-1"></i><?= count($docsReq) ?> archivo<?= count($docsReq) !== 1 ? 's' : '' ?>
                              </span>
                            <?php else: ?>
                              <span class="badge text-bg-warning text-dark" style="font-size:.65rem;">
                                <i class="bi bi-exclamation-triangle me-1"></i>Sin documentos
                              </span>
                            <?php endif; ?>
                          </div>

                          <!-- Archivos ya subidos -->
                          <?php if ($tieneDoc): ?>
                          <ul class="list-unstyled mb-2">
                            <?php foreach ($docsReq as $doc): ?>
                            <?php
                              $ext2  = strtolower(pathinfo($doc['nombre_original'], PATHINFO_EXTENSION));
                              $icon2 = match($ext2) {
                                'pdf'              => 'bi-file-earmark-pdf text-danger',
                                'doc','docx'       => 'bi-file-earmark-word text-primary',
                                'xls','xlsx'       => 'bi-file-earmark-excel text-success',
                                'jpg','jpeg','png' => 'bi-file-earmark-image text-warning',
                                'zip'              => 'bi-file-earmark-zip text-secondary',
                                default            => 'bi-file-earmark text-muted',
                              };
                              $kb2 = round(($doc['tamano'] ?? 0) / 1024, 1);
                            ?>
                            <li class="d-flex align-items-center justify-content-between py-1 border-bottom border-opacity-25">
                              <div class="d-flex align-items-center gap-1 min-w-0">
                                <i class="bi <?= $icon2 ?>" style="font-size:.9rem;"></i>
                                <span class="small text-truncate" title="<?= htmlspecialchars($doc['nombre_original']) ?>">
                                  <?= htmlspecialchars($doc['nombre_original']) ?>
                                </span>
                                <?php if (!empty($doc['descripcion'])): ?>
                                  <span class="text-muted fst-italic small text-truncate" title="<?= htmlspecialchars($doc['descripcion']) ?>">
                                    — <?= htmlspecialchars($doc['descripcion']) ?>
                                  </span>
                                <?php endif; ?>
                                <span class="text-muted" style="font-size:.7rem;">(<?= $kb2 ?> KB)</span>
                              </div>
                              <a href="index.php?modulo=documentos&accion=descargar&id=<?= $doc['id'] ?>"
                                 class="btn btn-sm btn-outline-primary py-0 px-1 flex-shrink-0 ms-2" title="Descargar">
                                <i class="bi bi-download"></i>
                              </a>
                            </li>
                            <?php endforeach; ?>
                          </ul>
                          <?php endif; ?>

                          <!-- Input para subir nuevo archivo -->
                          <div class="mt-1">
                            <label class="form-label small mb-1">
                              <?= $tieneDoc ? 'Agregar otro archivo:' : 'Subir documento:' ?>
                            </label>
                            <input type="file" name="documento_soporte" class="form-control form-control-sm"
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip"
                                   <?= $bloqueado ? 'disabled' : '' ?>>
                            <div class="form-text">PDF, Word, Excel, JPG, PNG, ZIP · máx. 10 MB</div>
                            <input type="text" name="documento_descripcion" class="form-control form-control-sm mt-1"
                                   placeholder="¿A qué corresponde este archivo? (opcional)" maxlength="255"
                                   <?= $bloqueado ? 'disabled' : '' ?>>
                          </div>

                        </div>
                      </div>
                      <?php endif; ?>

                    </div>

                    <div class="mt-3 d-flex justify-content-end">
                      <button type="submit" class="btn btn-primary btn-sm" <?= $bloqueado ? 'disabled' : '' ?>>
                        <i class="bi bi-check-lg me-1"></i> Guardar seguimiento
                      </button>
                    </div>
                  </div>
                </form>
              </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; // etapas del grupo ?>
        <?php endforeach; // grupos de fase ?>

        <?php if (empty($etapas)): ?>
        <div class="card shadow-sm">
          <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-diagram-3 fs-1 opacity-25 d-block mb-3"></i>
            Esta empresa no tiene etapas asignadas todavía.
            <a href="index.php?modulo=empresas&accion=ver&id=<?= $empresa['id'] ?>" class="d-block mt-2">
              Ir a la empresa para agregar etapas
            </a>
          </div>
        </div>
        <?php endif; ?>

      </div>
    </div>
  </main>

  <?php require_once __DIR__ . '/../../parciales/pie.php'; ?>
</div>

<!-- Modal habilitar edición de requisito cumplido -->
<div class="modal fade" id="modalDesbloquearReq" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-warning"><i class="bi bi-unlock me-2"></i>Habilitar edición</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        El requisito <strong id="nombreReqDesbloquear"></strong> ya está marcado como <strong>cumplido</strong> y
        está bloqueado para evitar cambios accidentales. ¿Deseas habilitar su edición?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" id="btnConfirmarDesbloqueo" class="btn btn-warning">
          <i class="bi bi-unlock me-1"></i> Habilitar edición
        </button>
      </div>
    </div>
  </div>
</div>
