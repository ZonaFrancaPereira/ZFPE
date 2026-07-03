<?php
/** @var array $compromisos */
/** @var array $historialPorCompromiso */
$pageTitle  = 'Mis compromisos — ZFPE';
$activePage = 'mis-compromisos';
// componentes.css trae las clases compartidas (meta-pill, file-chip, ch-timeline);
// mis-compromisos.css trae los estilos propios de esta vista.
$pageStyles = ['vista/assets/css/componentes.css', 'vista/assets/css/mis-compromisos.css'];

// Mapa de presentación por estado: [clase badge, clase texto, ícono, etiqueta]
$estadoComp = [
    'pendiente'   => ['bg-warning text-dark', 'text-warning',  'bi-hourglass-split',  'Pendiente'],
    'en_progreso' => ['bg-primary',           'text-primary',  'bi-arrow-repeat',     'En progreso'],
    'cumplido'    => ['bg-success',           'text-success',  'bi-check-circle-fill','Cumplido'],
    'vencido'     => ['bg-danger',            'text-danger',   'bi-exclamation-circle-fill', 'Vencido'],
];

$totalComp = count($compromisos);
$cumplidosComp = count(array_filter($compromisos, fn($c) => $c['estado'] === 'cumplido'));
$vencidosComp  = count(array_filter($compromisos, fn($c) =>
    $c['estado'] !== 'cumplido' && !empty($c['fecha_limite']) && $c['fecha_limite'] < date('Y-m-d')
));
$pctComp = $totalComp > 0 ? round($cumplidosComp / $totalComp * 100) : 0;

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
          <div class="col-sm-6"><h3 class="mb-0 titulo-zf">Mis compromisos</h3></div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item active">Mis compromisos</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="app-content">
      <div class="container-fluid">

        <?php if (empty($compromisos)): ?>
          <div class="card shadow-sm">
            <div class="card-body text-center py-5 text-muted">
              <i class="bi bi-clipboard-check fs-1 opacity-25 d-block mb-3"></i>
              <h5>No tienes compromisos asignados</h5>
              <p class="mb-0">Cuando un comité te asigne un compromiso, aparecerá aquí.</p>
            </div>
          </div>
        <?php else: ?>

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
                <div class="fs-2 fw-bold text-success"><?= $cumplidosComp ?></div>
                <div class="text-muted small">Cumplidos</div>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="card text-center border-0 shadow-sm py-3">
                <div class="fs-2 fw-bold <?= $vencidosComp > 0 ? 'text-danger' : 'text-muted' ?>"><?= $vencidosComp ?></div>
                <div class="text-muted small">Vencidos</div>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="card text-center border-0 shadow-sm py-3">
                <div class="fs-2 fw-bold text-success"><?= $pctComp ?>%</div>
                <div class="text-muted small">Avance</div>
                <div class="px-4">
                  <div class="progress" style="height:3px;">
                    <div class="progress-bar bg-success" style="width:<?= $pctComp ?>%"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row g-4">
            <?php foreach ($compromisos as $comp): ?>
            <?php
              [$cbadge, $ctext, $cicon, $ctxt] = $estadoComp[$comp['estado']] ?? ['bg-secondary', 'text-secondary', 'bi-circle', $comp['estado']];
              $vencido = ($comp['estado'] !== 'cumplido' && !empty($comp['fecha_limite']) && $comp['fecha_limite'] < date('Y-m-d'));
              $historial = $historialPorCompromiso[$comp['id']] ?? [];
              $totalDocsComp = array_sum(array_map(fn($h) => count($h['documentos']), $historial));
              $cumplido = $comp['estado'] === 'cumplido';
            ?>
            <div class="col-lg-6">
              <div id="compromiso-<?= $comp['id'] ?>" class="card shadow-sm h-100 compromiso-card compromiso-card--<?= $cumplido ? 'done' : $comp['estado'] ?>">
                <div class="card-header d-flex align-items-center justify-content-between gap-2">
                  <div class="d-flex align-items-center gap-2 min-w-0">
                    <span class="comite-avatar flex-shrink-0"><i class="bi bi-people-fill"></i></span>
                    <span class="fw-semibold small text-truncate" title="<?= htmlspecialchars($comp['comite_titulo']) ?>">
                      <?= htmlspecialchars($comp['comite_titulo']) ?>
                    </span>
                  </div>
                  <div class="d-flex align-items-center gap-1 flex-shrink-0">
                    <?php if ($totalDocsComp > 0): ?>
                      <span class="badge text-bg-light border text-muted" title="Documentos adjuntos">
                        <i class="bi bi-paperclip"></i> <?= $totalDocsComp ?>
                      </span>
                    <?php endif; ?>
                    <span class="badge <?= $cbadge ?>"><i class="bi <?= $cicon ?> me-1"></i><?= $ctxt ?></span>
                  </div>
                </div>
                <div class="card-body">
                  <p class="fw-semibold mb-2 fs-6"><?= htmlspecialchars($comp['descripcion']) ?></p>
                  <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="meta-pill"><i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y', strtotime($comp['comite_fecha'])) ?></span>
                    <?php if ($comp['fecha_limite']): ?>
                      <span class="meta-pill <?= $vencido ? 'meta-pill--danger' : '' ?>">
                        <i class="bi bi-calendar-event me-1"></i><?= date('d/m/Y', strtotime($comp['fecha_limite'])) ?>
                        <?= $vencido ? ' · Vencido' : '' ?>
                      </span>
                    <?php endif; ?>
                  </div>

                  <?php if ($cumplido): ?>
                  <div class="locked-banner mb-3">
                    <i class="bi bi-check-circle-fill"></i>
                    <div>
                      <div class="fw-semibold">Compromiso cumplido</div>
                      <div class="small text-muted">No se pueden registrar más cambios. Revisa el historial completo abajo.</div>
                    </div>
                  </div>
                  <?php else: ?>
                  <form method="POST" action="index.php?modulo=mis-compromisos&accion=actualizar&id=<?= $comp['id'] ?>"
                        enctype="multipart/form-data" class="update-form mb-3">
                    <div class="row g-2 mb-2">
                      <div class="col-sm-6">
                        <label class="form-label fw-semibold small text-muted text-uppercase mb-1" style="letter-spacing:.04em;">Estado</label>
                        <select name="estado" class="form-select form-select-sm">
                          <option value="pendiente"   <?= $comp['estado'] === 'pendiente'   ? 'selected' : '' ?>>Pendiente</option>
                          <option value="en_progreso" <?= $comp['estado'] === 'en_progreso' ? 'selected' : '' ?>>En progreso</option>
                          <option value="cumplido"    <?= $comp['estado'] === 'cumplido'    ? 'selected' : '' ?>>Cumplido</option>
                        </select>
                      </div>
                      <div class="col-sm-6">
                        <label class="form-label fw-semibold small text-muted text-uppercase mb-1" style="letter-spacing:.04em;">Documento soporte</label>
                        <input type="file" name="archivo" class="form-control form-control-sm"
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip">
                      </div>
                    </div>
                    <div class="mb-2">
                      <label class="form-label fw-semibold small text-muted text-uppercase mb-1" style="letter-spacing:.04em;">Solución / Observaciones</label>
                      <textarea name="observaciones" class="form-control form-control-sm" rows="2"
                                placeholder="Describe cómo se resolvió el compromiso..."><?= htmlspecialchars($comp['observaciones'] ?? '') ?></textarea>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                      <div class="form-text mb-0">PDF, Word, Excel, JPG, PNG, ZIP · máx. 10 MB</div>
                      <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-check-lg me-1"></i> Guardar
                      </button>
                    </div>
                  </form>
                  <?php endif; ?>

                  <div class="historial-box">
                    <div class="fw-semibold small text-muted text-uppercase mb-2" style="letter-spacing:.04em;">
                      <i class="bi bi-clock-history me-1 text-primary"></i>Historial
                    </div>

                    <?php if (empty($historial)): ?>
                      <div class="text-muted small fst-italic">Aún no se han registrado actualizaciones.</div>
                    <?php else: ?>
                      <ul class="ch-timeline">
                        <?php foreach ($historial as $h): ?>
                        <?php [$hbadge, $htext, $hicon, $htxt] = $estadoComp[$h['estado']] ?? ['bg-secondary', 'text-secondary', 'bi-circle', $h['estado']]; ?>
                        <li class="ch-timeline-item">
                          <span class="ch-timeline-dot <?= $hbadge ?>"><i class="bi <?= $hicon ?>"></i></span>
                          <div class="ch-timeline-content">
                            <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                              <span class="small">
                                <span class="fw-semibold"><?= htmlspecialchars($h['usuario_nombre'] ?? 'Usuario') ?></span>
                                <span class="text-muted">· <?= date('d/m/Y H:i', strtotime($h['created_at'])) ?></span>
                              </span>
                              <span class="badge <?= $hbadge ?>" style="font-size:.65rem;"><?= $htxt ?></span>
                            </div>
                            <?php if (!empty($h['observaciones'])): ?>
                              <div class="small text-muted mb-1"><?= nl2br(htmlspecialchars($h['observaciones'])) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($h['documentos'])): ?>
                              <div class="d-flex flex-wrap gap-1 mt-1">
                                <?php foreach ($h['documentos'] as $doc): ?>
                                <a href="index.php?modulo=mis-compromisos&accion=descargar-documento&id=<?= $doc['id'] ?>"
                                   class="file-chip" title="Descargar <?= htmlspecialchars($doc['nombre_original']) ?>">
                                  <i class="bi <?= $iconoDoc($doc['nombre_original']) ?>"></i>
                                  <span class="text-truncate"><?= htmlspecialchars($doc['nombre_original']) ?></span>
                                  <i class="bi bi-download text-muted ms-1"></i>
                                </a>
                                <?php endforeach; ?>
                              </div>
                            <?php endif; ?>
                          </div>
                        </li>
                        <?php endforeach; ?>
                      </ul>
                    <?php endif; ?>
                  </div>

                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>

        <?php endif; ?>

      </div>
    </div>
  </main>

  <?php require_once __DIR__ . '/../../parciales/pie.php'; ?>
</div>
