<?php
/** Contenido del manual de Empresa (visión general del proceso). Incluido desde empresa.php o mi_manual.php. */
?>

<!-- Encabezado -->
<div class="card border-0 shadow-sm mb-4" style="background:linear-gradient(135deg,var(--zf-navy) 0%,var(--zf-teal) 100%);">
  <div class="card-body py-4 text-white">
    <div class="row align-items-center">
      <div class="col-md-8">
        <h4 class="fw-bold mb-1"><i class="bi bi-building me-2"></i>Manual de Empresa</h4>
        <p class="mb-1 opacity-75">Zona Franca Internacional de Pereira · Centro de Control Gerencial</p>
        <p class="mb-0 opacity-75 small">Guía sobre el proceso y el avance de tu empresa en el sistema.</p>
      </div>
      <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <i class="bi bi-building opacity-25" style="font-size:5rem;"></i>
      </div>
    </div>
  </div>
</div>

<!-- Navegación interna -->
<div class="card shadow-sm mb-4">
  <div class="card-body py-2">
    <div class="d-flex flex-wrap gap-2">
      <a href="#cronograma-emp"  class="btn btn-outline-secondary btn-sm"><i class="bi bi-calendar3 me-1"></i>Cronograma</a>
      <a href="#seguimiento-emp" class="btn btn-outline-secondary btn-sm"><i class="bi bi-clipboard-check me-1"></i>Estados del proceso</a>
      <a href="#entidades-emp"   class="btn btn-outline-secondary btn-sm"><i class="bi bi-bank me-1"></i>Entidades</a>
      <a href="#documentos-emp"  class="btn btn-outline-secondary btn-sm"><i class="bi bi-file-earmark me-1"></i>Documentos</a>
      <a href="#comites-emp"     class="btn btn-outline-secondary btn-sm"><i class="bi bi-people-fill me-1"></i>Comités</a>
      <a href="#reportes-emp"    class="btn btn-outline-secondary btn-sm"><i class="bi bi-bar-chart-fill me-1"></i>Reportes</a>
      <a href="#colores-emp"     class="btn btn-outline-secondary btn-sm"><i class="bi bi-palette me-1"></i>Indicadores</a>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════ -->
<!-- SECCIÓN: CRONOGRAMA                                   -->
<!-- ══════════════════════════════════════════════════════ -->
<div id="cronograma-emp" class="card shadow-sm mb-4">
  <div class="card-header bg-zf-teal text-white">
    <h5 class="mb-0"><i class="bi bi-calendar3 me-2"></i>Cronograma</h5>
  </div>
  <div class="card-body">
    <p class="mb-3">Es la vista principal del avance de tu empresa en el proceso de zona franca: <code>Cronograma</code> en el menú lateral.</p>
    <div class="row g-3">
      <div class="col-md-6">
        <h6 class="small fw-bold text-muted text-uppercase">Qué muestra</h6>
        <ul class="small">
          <li>Un <strong>stepper por fase</strong> (ej. Etapa Preoperativa, Etapa Operativa) con sus etapas y % de avance</li>
          <li>Barra de avance general de todo tu proyecto</li>
          <li>Por cada etapa: estado, fechas de inicio/finalización</li>
          <li>Por cada requisito: estado, entidad responsable, fecha de vencimiento</li>
          <li>Documentos que Operaciones ha subido para cada requisito, con botón de descarga</li>
        </ul>
      </div>
      <div class="col-md-6">
        <h6 class="small fw-bold text-muted text-uppercase">Cómo leerlo</h6>
        <ul class="small">
          <li>Cada fase tiene su propio stepper — las etapas se numeran desde 1 dentro de cada fase</li>
          <li>La etapa en la que estás trabajando ahora se resalta en <span class="text-zf-teal fw-semibold">teal</span> y aparece expandida</li>
          <li>Las fechas vencidas aparecen en <span class="badge text-bg-danger">rojo</span></li>
          <li>Haz clic en un requisito para ver sus documentos adjuntos</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════ -->
<!-- SECCIÓN: ESTADOS DEL PROCESO (seguimiento, solo lectura) -->
<!-- ══════════════════════════════════════════════════════ -->
<div id="seguimiento-emp" class="card shadow-sm mb-4">
  <div class="card-header bg-primary text-white">
    <h5 class="mb-0"><i class="bi bi-clipboard-check me-2"></i>Estados del proceso</h5>
  </div>
  <div class="card-body">
    <p class="mb-3">El equipo de Operaciones de Zona Franca actualiza el estado de cada requisito a medida que revisan la información y documentos de tu empresa. Tú puedes ver ese progreso en tiempo real desde el Cronograma, pero no puedes modificarlo directamente.</p>
    <div class="table-responsive">
      <table class="table table-bordered table-sm small">
        <thead class="table-primary">
          <tr><th>Estado</th><th>Qué significa</th></tr>
        </thead>
        <tbody>
          <tr><td><span class="badge text-bg-secondary">Pendiente</span></td><td>Aún no se ha comenzado a trabajar en el requisito</td></tr>
          <tr><td><span class="badge bg-zf-teal text-white">En progreso</span></td><td>Ya tiene algún avance, pero no está completo</td></tr>
          <tr><td><span class="badge text-bg-success">Cumplido</span></td><td>El requisito quedó completado — recibirás un correo cuando esto ocurra</td></tr>
          <tr><td><span class="badge text-bg-light border text-dark">No aplica</span></td><td>El requisito no le aplica a tu empresa en esta etapa</td></tr>
        </tbody>
      </table>
    </div>
    <div class="alert alert-info small mb-0">
      <i class="bi bi-envelope-check-fill me-2"></i>
      Cuando un requisito de tu empresa pasa a <strong>Cumplido</strong>, te llega un correo con el detalle (requisito, fecha y observaciones).
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════ -->
<!-- SECCIÓN: ENTIDADES                                    -->
<!-- ══════════════════════════════════════════════════════ -->
<div id="entidades-emp" class="card shadow-sm mb-4">
  <div class="card-header bg-zf-navy text-white">
    <h5 class="mb-0"><i class="bi bi-bank me-2"></i>Entidades</h5>
  </div>
  <div class="card-body">
    <p class="mb-3">Desde <code>Entidades</code> en el menú lateral puedes ver los organismos externos involucrados en tu proceso (ej. DIAN, Ministerio de Comercio, DIMAR) y, para cada uno, cuántos requisitos tienes cumplidos, en progreso, pendientes o no aplicables.</p>
    <p class="small text-muted mb-0">Es una forma rápida de saber, por ejemplo, "¿cómo voy con la DIAN?" sin tener que revisar etapa por etapa.</p>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════ -->
<!-- SECCIÓN: DOCUMENTOS                                   -->
<!-- ══════════════════════════════════════════════════════ -->
<div id="documentos-emp" class="card shadow-sm mb-4">
  <div class="card-header" style="background:#6f42c1;">
    <h5 class="mb-0 text-white"><i class="bi bi-file-earmark me-2"></i>Documentos</h5>
  </div>
  <div class="card-body">
    <p class="mb-3">Lista de todos los archivos que Operaciones ha subido para tu empresa, organizados por etapa y requisito: <code>Documentos</code> en el menú lateral.</p>
    <div class="row g-3">
      <div class="col-md-6">
        <h6 class="small fw-bold text-muted text-uppercase">Qué puedes hacer</h6>
        <ul class="small">
          <li><i class="bi bi-check-circle-fill text-success me-1"></i> Ver todos los documentos de tu empresa</li>
          <li><i class="bi bi-check-circle-fill text-success me-1"></i> Descargar cualquier archivo</li>
          <li><i class="bi bi-x-circle-fill text-danger me-1"></i> No puedes subir documentos</li>
          <li><i class="bi bi-x-circle-fill text-danger me-1"></i> No puedes eliminar documentos</li>
        </ul>
      </div>
      <div class="col-md-6">
        <h6 class="small fw-bold text-muted text-uppercase">Formatos soportados</h6>
        <div class="d-flex flex-wrap gap-2">
          <span class="badge text-bg-danger">PDF</span>
          <span class="badge text-bg-primary">Word</span>
          <span class="badge text-bg-success">Excel</span>
          <span class="badge text-bg-warning text-dark">JPG / PNG</span>
          <span class="badge text-bg-secondary">ZIP</span>
        </div>
        <p class="small text-muted mt-2 mb-0">Tamaño máximo por archivo: <strong>10 MB</strong></p>
      </div>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════ -->
<!-- SECCIÓN: COMITÉS                                      -->
<!-- ══════════════════════════════════════════════════════ -->
<div id="comites-emp" class="card shadow-sm mb-4">
  <div class="card-header bg-secondary text-white">
    <h5 class="mb-0"><i class="bi bi-people-fill me-2"></i>Comités</h5>
  </div>
  <div class="card-body">
    <p class="mb-3">Desde <code>Comités</code> puedes ver las reuniones formales programadas con tu empresa y los <strong>compromisos</strong> (tareas con fecha límite) que se acordaron en cada una.</p>
    <div class="alert alert-info small mb-2">
      <i class="bi bi-envelope-check-fill me-2"></i>
      Cuando se programa un nuevo comité para tu empresa, te llega un correo con el detalle (título, tipo, fecha, lugar y agenda).
    </div>
    <p class="small text-muted mb-0">Si un compromiso queda asignado a alguien de tu empresa, esa persona podrá gestionarlo desde <strong>Mis compromisos</strong> (ver Manual de Usuario).</p>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════ -->
<!-- SECCIÓN: REPORTES                                     -->
<!-- ══════════════════════════════════════════════════════ -->
<div id="reportes-emp" class="card shadow-sm mb-4">
  <div class="card-header bg-success text-white">
    <h5 class="mb-0"><i class="bi bi-bar-chart-fill me-2"></i>Reportes</h5>
  </div>
  <div class="card-body">
    <p class="mb-3">Resumen ejecutivo del estado de tu empresa en el proceso de zona franca: <code>Reportes</code> en el menú lateral.</p>
    <ul class="small mb-2">
      <li>% de avance general del proyecto</li>
      <li>Etapas completadas sobre el total</li>
      <li>Requisitos cumplidos, pendientes y vencidos</li>
      <li>Requisitos por vencer en los próximos 30 días</li>
      <li>Botón <strong>Imprimir</strong> para generar una versión en papel</li>
    </ul>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════ -->
<!-- SECCIÓN: INDICADORES DE COLOR                         -->
<!-- ══════════════════════════════════════════════════════ -->
<div id="colores-emp" class="card shadow-sm mb-4">
  <div class="card-header bg-body-secondary">
    <h5 class="mb-0"><i class="bi bi-palette me-2"></i>Indicadores de color</h5>
  </div>
  <div class="card-body">
    <div class="row g-3">
      <?php
      $colores = [
        ['badge'=>'text-bg-success',              'label'=>'Cumplido / Completo',          'desc'=>'Requisito o etapa finalizada al 100%'],
        ['badge'=>'bg-zf-teal text-white',        'label'=>'En progreso',                   'desc'=>'Tiene avance pero no está completo'],
        ['badge'=>'text-bg-secondary',            'label'=>'Pendiente / No iniciado',       'desc'=>'Sin ningún avance todavía'],
        ['badge'=>'text-bg-light border text-dark','label'=>'No aplica',                   'desc'=>'No se aplica a tu empresa'],
        ['badge'=>'text-bg-danger',               'label'=>'Vencido',                      'desc'=>'La fecha límite ya pasó sin cumplirse'],
        ['badge'=>'text-bg-warning text-dark',    'label'=>'Por vencer',                   'desc'=>'Vence en los próximos 30 días'],
      ];
      foreach ($colores as $c): ?>
      <div class="col-md-4">
        <div class="d-flex align-items-center gap-2 p-2 rounded border">
          <span class="badge <?= $c['badge'] ?> flex-shrink-0"><?= $c['label'] ?></span>
          <small class="text-muted"><?= $c['desc'] ?></small>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
