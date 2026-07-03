<?php
/** Contenido del manual de Usuario (acciones individuales de la cuenta). Incluido desde usuario.php o mi_manual.php. */
?>

<!-- Encabezado -->
<div class="card border-0 shadow-sm mb-4" style="background:linear-gradient(135deg,var(--zf-teal) 0%,var(--zf-navy) 100%);">
  <div class="card-body py-4 text-white">
    <div class="row align-items-center">
      <div class="col-md-8">
        <h4 class="fw-bold mb-1"><i class="bi bi-person-fill me-2"></i>Manual de Usuario</h4>
        <p class="mb-1 opacity-75">Zona Franca Internacional de Pereira · Centro de Control Gerencial</p>
        <p class="mb-0 opacity-75 small">Guía sobre tu cuenta y tus acciones dentro del sistema.</p>
      </div>
      <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <i class="bi bi-person-fill opacity-25" style="font-size:5rem;"></i>
      </div>
    </div>
  </div>
</div>

<!-- Navegación interna -->
<div class="card shadow-sm mb-4">
  <div class="card-body py-2">
    <div class="d-flex flex-wrap gap-2">
      <a href="#primer-ingreso" class="btn btn-outline-secondary btn-sm"><i class="bi bi-shield-lock me-1"></i>Primer ingreso</a>
      <a href="#perfil-usr"     class="btn btn-outline-secondary btn-sm"><i class="bi bi-person-circle me-1"></i>Mi perfil</a>
      <a href="#compromisos-usr" class="btn btn-outline-secondary btn-sm"><i class="bi bi-clipboard-check me-1"></i>Mis compromisos</a>
      <a href="#notificaciones-usr" class="btn btn-outline-secondary btn-sm"><i class="bi bi-bell me-1"></i>Notificaciones</a>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════ -->
<!-- SECCIÓN: PRIMER INGRESO / CONTRASEÑA TEMPORAL         -->
<!-- ══════════════════════════════════════════════════════ -->
<div id="primer-ingreso" class="card shadow-sm mb-4">
  <div class="card-header bg-zf-navy text-white">
    <h5 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Primer ingreso con contraseña temporal</h5>
  </div>
  <div class="card-body">
    <p class="mb-3">Cuando tu cuenta se crea por primera vez (al registrarse tu empresa, o al crearte como usuario), recibes un <strong>correo de bienvenida</strong> con tu usuario (correo) y una <strong>contraseña temporal</strong>.</p>
    <div class="alert alert-warning small mb-0">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      La primera vez que ingreses con esa contraseña, el sistema <strong>te pedirá definir una nueva contraseña</strong> antes de dejarte usar cualquier otra parte del sistema. Una vez la cambies, no te lo volverá a pedir.
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════ -->
<!-- SECCIÓN: MI PERFIL                                    -->
<!-- ══════════════════════════════════════════════════════ -->
<div id="perfil-usr" class="card shadow-sm mb-4">
  <div class="card-header bg-zf-teal text-white">
    <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Mi perfil</h5>
  </div>
  <div class="card-body">
    <p class="mb-3">Desde el ícono de usuario en la esquina superior derecha, o <code>Perfil</code>, puedes:</p>
    <ul class="small mb-3">
      <li>Actualizar tu <strong>nombre completo</strong> y <strong>correo electrónico</strong></li>
      <li>Cambiar tu <strong>contraseña</strong> en cualquier momento (dejando los campos en blanco si no quieres cambiarla)</li>
    </ul>
    <div class="alert alert-info small mb-0">
      <i class="bi bi-info-circle me-1"></i>
      Para cambiar la contraseña desde aquí necesitas escribir tu <strong>contraseña actual</strong> y la nueva contraseña dos veces (mínimo 6 caracteres).
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════ -->
<!-- SECCIÓN: MIS COMPROMISOS                              -->
<!-- ══════════════════════════════════════════════════════ -->
<div id="compromisos-usr" class="card shadow-sm mb-4">
  <div class="card-header bg-primary text-white">
    <h5 class="mb-0"><i class="bi bi-clipboard-check me-2"></i>Mis compromisos</h5>
  </div>
  <div class="card-body">
    <p class="mb-3">Si en un <strong>comité</strong> te asignaron como responsable de un compromiso, lo verás en <code>Mis compromisos</code> del menú lateral.</p>
    <div class="row g-4">
      <div class="col-md-6">
        <h6 class="fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i>Actualizar un compromiso</h6>
        <ul class="small mb-0">
          <li>Cambiar su <strong>estado</strong> (pendiente, en progreso, cumplido)</li>
          <li>Agregar <strong>observaciones</strong> sobre el avance</li>
          <li>Adjuntar un <strong>documento de soporte</strong> (PDF, Word, Excel, JPG, PNG o ZIP, máx. 10 MB)</li>
        </ul>
      </div>
      <div class="col-md-6">
        <h6 class="fw-bold"><i class="bi bi-clock-history text-info me-2"></i>Historial</h6>
        <p class="small mb-2">Cada actualización queda registrada con fecha, quién la hizo y los documentos que se adjuntaron en ese momento — puedes revisar todo el historial de un compromiso.</p>
        <div class="alert alert-warning small py-2 mb-0">
          <i class="bi bi-lock-fill me-1"></i>
          Una vez un compromiso queda marcado como <strong>Cumplido</strong>, ya no se puede modificar.
        </div>
      </div>
    </div>
    <div class="alert alert-info small mt-3 mb-0">
      <i class="bi bi-envelope-check-fill me-2"></i>
      Cuando te asignan un compromiso nuevo, te llega un correo con la descripción, el comité al que pertenece y la fecha límite.
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════ -->
<!-- SECCIÓN: NOTIFICACIONES                               -->
<!-- ══════════════════════════════════════════════════════ -->
<div id="notificaciones-usr" class="card shadow-sm mb-4">
  <div class="card-header bg-secondary text-white">
    <h5 class="mb-0"><i class="bi bi-bell me-2"></i>Notificaciones</h5>
  </div>
  <div class="card-body">
    <p class="mb-3">El ícono de campana en la barra superior muestra avisos dentro del sistema, como:</p>
    <ul class="small mb-3">
      <li>Compromisos de comité que aún no has cumplido</li>
      <li>Requisitos por vencer en los próximos 30 días</li>
      <li>Cambios de estado hechos por Operaciones en tus requisitos</li>
      <li>Documentos nuevos subidos a tu empresa</li>
    </ul>
    <p class="small text-muted mb-0">Además, para los avisos más importantes (nuevo comité, compromiso asignado, requisito completado) también te llega un <strong>correo electrónico</strong>.</p>
  </div>
</div>
