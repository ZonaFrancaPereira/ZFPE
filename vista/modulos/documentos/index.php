<?php
/** @var array|false $empresa */
/** @var array $requisitos */
/** @var array $documentos */
/** @var array $todasEmpresas */

$esOp = in_array($_SESSION['usuario_rol'] ?? '', ['operaciones', 'admin']);

$pageTitle  = $empresa ? htmlspecialchars($empresa['razon_social']) . ' — Documentos' : 'Documentos';
$activePage = 'documentos';
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

<?php if (!empty($_SESSION['flash_error'])): ?>
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1100;">
  <div class="toast align-items-center text-bg-danger border-0 show" role="alert">
    <div class="d-flex">
      <div class="toast-body"><i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>
<?php unset($_SESSION['flash_error']); endif; ?>

<div class="app-wrapper">
  <?php require_once __DIR__ . '/../../parciales/navegacion.php'; ?>
  <?php require_once __DIR__ . '/../../parciales/menu_lateral.php'; ?>

  <main class="app-main">
    <div class="app-content-header encabezado-zf">
      <div class="container-fluid">
        <div class="row align-items-center">
          <div class="col-sm-6">
            <h3 class="mb-0 titulo-zf">
              <i class="bi bi-folder2-open me-2"></i>Documentos
              <?php if ($empresa): ?>
                <span class="fw-normal fs-5 text-truncate d-inline-block align-bottom"
                      style="max-width:380px; color:#5c7a85;"
                      title="<?= htmlspecialchars($empresa['razon_social']) ?>">
                  — <?= htmlspecialchars($empresa['razon_social']) ?>
                </span>
              <?php endif; ?>
            </h3>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <?php if ($esOp && $empresa): ?>
                <li class="breadcrumb-item"><a href="index.php?modulo=documentos">Documentos</a></li>
                <li class="breadcrumb-item active text-truncate d-inline-block align-bottom" style="max-width:260px;"
                    title="<?= htmlspecialchars($empresa['razon_social']) ?>">
                  <?= htmlspecialchars($empresa['razon_social']) ?>
                </li>
              <?php else: ?>
                <li class="breadcrumb-item active">Documentos</li>
              <?php endif; ?>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="app-content">
      <div class="container-fluid">

        <?php if ($esOp && empty($empresa)): ?>
        <!-- ===== OPERACIONES: selector de empresa ===== -->
        <div class="card shadow-sm">
          <div class="card-header">
            <h5 class="card-title mb-0"><i class="bi bi-building me-2 text-primary"></i>Seleccionar empresa</h5>
          </div>
          <div class="card-body p-0">
            <?php if (empty($todasEmpresas)): ?>
              <div class="text-center text-muted py-5">
                <i class="bi bi-building-x fs-2 d-block mb-2 opacity-25"></i>
                No hay empresas registradas.
              </div>
            <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                  <tr>
                    <th>Empresa</th>
                    <th>NIT</th>
                    <th class="text-center">Documentos</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($todasEmpresas as $emp): ?>
                  <tr>
                    <td class="fw-semibold"><?= htmlspecialchars($emp['razon_social']) ?></td>
                    <td class="text-muted"><?= htmlspecialchars($emp['nit']) ?></td>
                    <td class="text-center">
                      <span class="badge text-bg-<?= $emp['total_docs'] > 0 ? 'primary' : 'secondary' ?>">
                        <?= $emp['total_docs'] ?>
                      </span>
                    </td>
                    <td class="text-end">
                      <a href="index.php?modulo=documentos&accion=ver&id=<?= $emp['id'] ?>"
                         class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-folder2-open me-1"></i> Ver documentos
                      </a>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            <?php endif; ?>
          </div>
        </div>

        <?php else: ?>
        <!-- ===== VISTA CON EMPRESA SELECCIONADA ===== -->

        <?php if ($esOp): ?>
        <div class="d-flex align-items-center gap-2 mb-3">
          <a href="index.php?modulo=documentos" class="btn btn-outline-zf btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Todas las empresas
          </a>
          <a href="index.php?modulo=empresas&accion=ver&id=<?= $empresa['id'] ?>" class="btn btn-outline-zf-teal btn-sm">
            <i class="bi bi-building me-1"></i> Ver empresa
          </a>
        </div>
        <?php endif; ?>

        <div class="row g-4">

          <?php if ($esOp): ?>
          <!-- ===== FORMULARIO DE SUBIDA (solo operaciones) ===== -->
          <div class="col-lg-4">
            <div class="card shadow-sm">
              <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-upload me-2 text-success"></i>Subir documento</h5>
              </div>
              <div class="card-body">
                <form method="POST"
                      action="index.php?modulo=documentos&accion=subir&id=<?= $empresa['id'] ?>"
                      enctype="multipart/form-data">

                  <div class="mb-3">
                    <label class="form-label fw-semibold">Requisito <span class="text-danger">*</span></label>
                    <?php if (empty($requisitos)): ?>
                      <div class="alert alert-warning small mb-0">
                        Esta empresa no tiene requisitos asignados.
                      </div>
                    <?php else: ?>
                    <select name="requisito_id" class="form-select" required>
                      <option value="">— Seleccionar requisito —</option>
                      <?php
                      $etapaActual = '';
                      foreach ($requisitos as $req):
                          if ($req['etapa_nombre'] !== $etapaActual):
                              if ($etapaActual !== '') echo '</optgroup>';
                              echo '<optgroup label="' . htmlspecialchars($req['etapa_nombre']) . '">';
                              $etapaActual = $req['etapa_nombre'];
                          endif;
                      ?>
                        <option value="<?= $req['id'] ?>"><?= htmlspecialchars($req['nombre']) ?></option>
                      <?php endforeach; ?>
                      <?php if ($etapaActual !== '') echo '</optgroup>'; ?>
                    </select>
                    <?php endif; ?>
                  </div>

                  <div class="mb-3">
                    <label class="form-label fw-semibold">Archivo <span class="text-danger">*</span></label>
                    <input type="file" name="archivo" id="inputArchivo" class="form-control"
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip" required>
                    <div class="form-text">PDF, Word, Excel, JPG, PNG, ZIP · máx. 10 MB</div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label fw-semibold">Descripción</label>
                    <input type="text" name="descripcion" class="form-control"
                           placeholder="¿A qué corresponde este archivo? (opcional)" maxlength="255">
                  </div>

                  <div id="previewImg" class="mb-3 d-none">
                    <img id="imgPreview" src="#" alt="Vista previa" class="img-fluid rounded border" style="max-height:160px;">
                  </div>

                  <button type="submit" class="btn btn-success w-100" <?= empty($requisitos) ? 'disabled' : '' ?>>
                    <i class="bi bi-upload me-1"></i> Subir
                  </button>
                </form>
              </div>
            </div>
          </div>
          <div class="col-lg-8">
          <?php else: ?>
          <div class="col-12">
          <?php endif; ?>

            <!-- ===== LISTA DE DOCUMENTOS ===== -->
            <?php if (empty($documentos)): ?>
              <div class="card shadow-sm">
                <div class="card-body text-center text-muted py-5">
                  <i class="bi bi-folder-x fs-2 d-block mb-2 opacity-25"></i>
                  No hay documentos cargados aún.
                </div>
              </div>
            <?php else: ?>
              <?php foreach ($documentos as $etapaNombre => $requisitosGrupo): ?>
              <div class="card shadow-sm mb-3">
                <div class="card-header bg-body-secondary">
                  <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-layers me-2 text-primary"></i><?= htmlspecialchars($etapaNombre) ?>
                  </h6>
                </div>
                <div class="card-body p-0">
                  <?php foreach ($requisitosGrupo as $reqNombre => $docs): ?>
                  <div class="border-bottom">
                    <div class="px-3 pt-2 pb-1">
                      <p class="fw-semibold small text-muted mb-2">
                        <i class="bi bi-check2-square me-1"></i><?= htmlspecialchars($reqNombre) ?>
                      </p>
                    </div>
                    <ul class="list-group list-group-flush">
                      <?php foreach ($docs as $doc): ?>
                      <?php
                        $ext  = strtolower(pathinfo($doc['nombre_original'], PATHINFO_EXTENSION));
                        $icon = match($ext) {
                            'pdf'              => 'bi-file-earmark-pdf text-danger',
                            'doc','docx'       => 'bi-file-earmark-word text-primary',
                            'xls','xlsx'       => 'bi-file-earmark-excel text-success',
                            'jpg','jpeg','png' => 'bi-file-earmark-image text-warning',
                            'zip'              => 'bi-file-earmark-zip text-secondary',
                            default            => 'bi-file-earmark text-muted',
                        };
                        $kb    = round(($doc['tamano'] ?? 0) / 1024, 1);
                        $fecha = $doc['created_at'] ? date('d/m/Y', strtotime($doc['created_at'])) : '';
                      ?>
                      <li class="list-group-item px-3 py-2">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                          <div class="d-flex align-items-center gap-2 min-w-0">
                            <i class="bi <?= $icon ?> fs-5 flex-shrink-0"></i>
                            <div class="min-w-0">
                              <div class="small fw-semibold text-truncate" title="<?= htmlspecialchars($doc['nombre_original']) ?>">
                                <?= htmlspecialchars($doc['nombre_original']) ?>
                              </div>
                              <?php if (!empty($doc['descripcion'])): ?>
                              <div class="text-muted fst-italic text-truncate" style="font-size:.72rem;" title="<?= htmlspecialchars($doc['descripcion']) ?>">
                                <?= htmlspecialchars($doc['descripcion']) ?>
                              </div>
                              <?php endif; ?>
                              <div class="text-muted" style="font-size:.72rem;">
                                <?= $kb ?> KB
                                <?php if ($fecha): ?> · <?= $fecha ?><?php endif; ?>
                                <?php if ($doc['subido_por_nombre']): ?> · <?= htmlspecialchars($doc['subido_por_nombre']) ?><?php endif; ?>
                              </div>
                            </div>
                          </div>
                          <div class="d-flex gap-1 flex-shrink-0">
                            <a href="index.php?modulo=documentos&accion=descargar&id=<?= $doc['id'] ?>"
                               class="btn btn-sm btn-outline-primary py-0 px-2" title="Descargar">
                              <i class="bi bi-download"></i>
                            </a>
                            <?php if ($esOp): ?>
                            <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2"
                                    title="Eliminar"
                                    data-bs-toggle="modal" data-bs-target="#modalEliminar"
                                    data-id="<?= $doc['id'] ?>"
                                    data-nombre="<?= htmlspecialchars($doc['nombre_original']) ?>">
                              <i class="bi bi-trash"></i>
                            </button>
                            <?php endif; ?>
                          </div>
                        </div>
                      </li>
                      <?php endforeach; ?>
                    </ul>
                  </div>
                  <?php endforeach; ?>
                </div>
              </div>
              <?php endforeach; ?>
            <?php endif; ?>

          </div><!-- /col -->
        </div><!-- /row -->

        <?php endif; // fin empresa vs. selector ?>

      </div>
    </div>
  </main>

  <?php require_once __DIR__ . '/../../parciales/pie.php'; ?>
</div>

<?php if ($esOp): ?>
<!-- Modal eliminar documento -->
<div class="modal fade" id="modalEliminar" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Eliminar documento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        ¿Eliminar <strong id="nombreDocEliminar"></strong>? Esta acción no se puede deshacer.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <a id="btnEliminar" href="#" class="btn btn-danger">
          <i class="bi bi-trash me-1"></i> Eliminar
        </a>
      </div>
    </div>
  </div>
</div>
<script>
document.getElementById('modalEliminar').addEventListener('show.bs.modal', function(e) {
  const btn = e.relatedTarget;
  document.getElementById('nombreDocEliminar').textContent = btn.dataset.nombre;
  document.getElementById('btnEliminar').href =
    'index.php?modulo=documentos&accion=eliminar&id=' + btn.dataset.id;
});
</script>
<?php endif; ?>

<script>
(function() {
  const input   = document.getElementById('inputArchivo');
  const preview = document.getElementById('previewImg');
  const img     = document.getElementById('imgPreview');
  if (!input) return;
  input.addEventListener('change', function() {
    const file = this.files[0];
    if (file && file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = e => { img.src = e.target.result; preview.classList.remove('d-none'); };
      reader.readAsDataURL(file);
    } else {
      preview.classList.add('d-none');
      img.src = '#';
    }
  });
})();
</script>
