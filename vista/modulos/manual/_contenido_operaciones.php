<?php
/** Contenido del manual de Operaciones. Incluido desde operaciones.php. */
?>

<!-- Encabezado -->
<div class="card border-0 shadow-sm mb-4" style="background:linear-gradient(135deg,var(--zf-teal) 0%,var(--zf-navy) 100%);">
  <div class="card-body py-4 text-white">
    <div class="row align-items-center">
      <div class="col-md-8">
        <h4 class="fw-bold mb-1"><i class="bi bi-person-fill-gear me-2"></i>Manual de Operaciones</h4>
        <p class="mb-2 opacity-75">Zona Franca Internacional de Pereira · Centro de Control Gerencial</p>
        <div class="d-flex flex-wrap gap-2">
          <span class="badge bg-white text-zf-teal"><i class="bi bi-person-fill-gear me-1"></i>Operaciones</span>
        </div>
      </div>
      <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <i class="bi bi-person-fill-gear opacity-25" style="font-size:5rem;"></i>
      </div>
    </div>
  </div>
</div>

<!-- Navegación interna -->
<div class="card shadow-sm mb-4">
  <div class="card-body py-2">
    <div class="d-flex flex-wrap gap-2">
      <a href="#roles-op"          class="btn btn-outline-secondary btn-sm"><i class="bi bi-people me-1"></i>Roles</a>
      <a href="#configuracion-op"  class="btn btn-outline-secondary btn-sm"><i class="bi bi-sliders me-1"></i>Configuración</a>
      <a href="#empresas-op"       class="btn btn-outline-secondary btn-sm"><i class="bi bi-building me-1"></i>Empresas</a>
      <a href="#seguimiento-op"    class="btn btn-outline-secondary btn-sm"><i class="bi bi-clipboard-check me-1"></i>Seguimiento</a>
      <a href="#documentos-op"     class="btn btn-outline-secondary btn-sm"><i class="bi bi-folder2-open me-1"></i>Documentos</a>
      <a href="#comites-op"        class="btn btn-outline-secondary btn-sm"><i class="bi bi-people-fill me-1"></i>Comités</a>
      <a href="#cronograma-op"     class="btn btn-outline-secondary btn-sm"><i class="bi bi-calendar3 me-1"></i>Cronograma</a>
      <a href="#reportes-op"       class="btn btn-outline-secondary btn-sm"><i class="bi bi-bar-chart-fill me-1"></i>Reportes</a>
      <a href="#colores-op"        class="btn btn-outline-secondary btn-sm"><i class="bi bi-palette me-1"></i>Indicadores</a>
      <a href="#jerarquia-op"      class="btn btn-outline-danger btn-sm"><i class="bi bi-collection me-1"></i>Jerarquía</a>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════ -->
<!-- SECCIÓN: ROLES                                        -->
<!-- ══════════════════════════════════════════════════════ -->
<div id="roles-op" class="card shadow-sm mb-4">
  <div class="card-header bg-zf-teal text-white">
    <h5 class="mb-0"><i class="bi bi-people me-2"></i>Roles del sistema</h5>
  </div>
  <div class="card-body p-0">
    <div class="row g-0">

      <div class="col-md-4 border-end p-4">
        <div class="text-center mb-3">
          <div class="rounded-circle bg-zf-teal text-white d-inline-flex align-items-center justify-content-center mb-2"
               style="width:52px;height:52px;font-size:1.4rem;">
            <i class="bi bi-person-fill-gear"></i>
          </div>
          <h6 class="fw-bold mb-0">Operaciones</h6>
          <small class="text-muted">Equipo interno de Zona Franca</small>
        </div>
        <ul class="list-unstyled small">
          <li class="mb-1"><i class="bi bi-check-circle-fill text-success me-2"></i>Configurar fases, etapas, requisitos e ítems</li>
          <li class="mb-1"><i class="bi bi-check-circle-fill text-success me-2"></i>Gestionar empresas y sus usuarios</li>
          <li class="mb-1"><i class="bi bi-check-circle-fill text-success me-2"></i>Hacer seguimiento a requisitos</li>
          <li class="mb-1"><i class="bi bi-check-circle-fill text-success me-2"></i>Subir y eliminar documentos</li>
          <li class="mb-1"><i class="bi bi-check-circle-fill text-success me-2"></i>Gestionar comités y compromisos</li>
          <li class="mb-1"><i class="bi bi-check-circle-fill text-success me-2"></i>Ver cronogramas y reportes de empresas</li>
        </ul>
      </div>

      <div class="col-md-4 border-end p-4">
        <div class="text-center mb-3">
          <div class="rounded-circle bg-zf-navy text-white d-inline-flex align-items-center justify-content-center mb-2"
               style="width:52px;height:52px;font-size:1.4rem;">
            <i class="bi bi-shield-fill-check"></i>
          </div>
          <h6 class="fw-bold mb-0">Administrador</h6>
          <small class="text-muted">Acceso total al sistema</small>
        </div>
        <ul class="list-unstyled small">
          <li class="mb-1"><i class="bi bi-check-circle-fill text-success me-2"></i>Todo lo de Operaciones, y además:</li>
          <li class="mb-1"><i class="bi bi-shield-fill-check text-zf-navy me-2"></i>Gestionar usuarios del sistema</li>
          <li class="mb-1"><i class="bi bi-shield-fill-check text-zf-navy me-2"></i>Configurar la cuenta de correo (SMTP)</li>
        </ul>
        <p class="small text-muted mb-0">Tiene su propio manual — pídele acceso al administrador si necesitas alguna de estas funciones.</p>
      </div>

      <div class="col-md-4 p-4">
        <div class="text-center mb-3">
          <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-2"
               style="width:52px;height:52px;font-size:1.4rem;">
            <i class="bi bi-person-fill"></i>
          </div>
          <h6 class="fw-bold mb-0">Usuario empresa</h6>
          <small class="text-muted">Empleado de la empresa usuaria</small>
        </div>
        <ul class="list-unstyled small">
          <li class="mb-1"><i class="bi bi-check-circle-fill text-success me-2"></i>Ver cronograma de su empresa</li>
          <li class="mb-1"><i class="bi bi-check-circle-fill text-success me-2"></i>Descargar documentos</li>
          <li class="mb-1"><i class="bi bi-check-circle-fill text-success me-2"></i>Gestionar sus compromisos de comité</li>
          <li class="mb-1"><i class="bi bi-x-circle-fill text-danger me-2"></i>No puede modificar la configuración</li>
        </ul>
      </div>

    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════ -->
<!-- SECCIÓN: CONFIGURACIÓN INICIAL                        -->
<!-- ══════════════════════════════════════════════════════ -->
<div id="configuracion-op" class="card shadow-sm mb-4">
  <div class="card-header bg-warning text-dark">
    <h5 class="mb-0"><i class="bi bi-sliders me-2"></i>Paso 1 — Configuración inicial <small class="fw-normal">(solo se hace una vez)</small></h5>
  </div>
  <div class="card-body">

    <div class="alert alert-warning border-warning mb-4">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      <strong>Importante:</strong> Antes de crear empresas, configura la estructura del sistema en este orden exacto.
    </div>

    <!-- Stepper 5 pasos -->
    <div class="row g-3 mb-4">
      <?php
      $pasos = [
        ['num'=>'1','color'=>'danger', 'icon'=>'bi-collection',  'titulo'=>'Fases',      'desc'=>'Grandes grupos: Preoperativa, Operativa…',   'url'=>'index.php?modulo=configuracion&accion=fases'],
        ['num'=>'2','color'=>'primary','icon'=>'bi-bank',        'titulo'=>'Entidades',  'desc'=>'DIAN, Ministerio de Comercio, DIMAR…',        'url'=>'index.php?modulo=configuracion&accion=entidades'],
        ['num'=>'3','color'=>'success','icon'=>'bi-diagram-3',   'titulo'=>'Etapas',     'desc'=>'Grupos dentro de cada fase (con orden y peso)','url'=>'index.php?modulo=configuracion&accion=etapas'],
        ['num'=>'4','color'=>'warning','icon'=>'bi-list-check',  'titulo'=>'Requisitos', 'desc'=>'Documentos o acciones a cumplir por etapa',   'url'=>'index.php?modulo=configuracion&accion=requisitos'],
        ['num'=>'5','color'=>'info',   'icon'=>'bi-ui-checks',   'titulo'=>'Ítems',      'desc'=>'Checklist dentro de cada requisito',          'url'=>'index.php?modulo=configuracion&accion=items'],
      ];
      foreach ($pasos as $p): ?>
      <div class="col-6 col-md">
        <a href="<?= $p['url'] ?>" class="text-decoration-none">
          <div class="card h-100 border-<?= $p['color'] ?> border-2 text-center p-3">
            <div class="rounded-circle bg-<?= $p['color'] ?> text-white d-inline-flex align-items-center justify-content-center mx-auto mb-2"
                 style="width:38px;height:38px;font-size:1rem;">
              <?= $p['num'] ?>
            </div>
            <i class="bi <?= $p['icon'] ?> fs-3 text-<?= $p['color'] ?> mb-1"></i>
            <h6 class="fw-bold mb-1"><?= $p['titulo'] ?></h6>
            <small class="text-muted"><?= $p['desc'] ?></small>
          </div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="accordion" id="accConfigOp">

      <!-- Fases -->
      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#cfgFaseOp">
            <i class="bi bi-collection me-2 text-danger"></i>1. Fases
          </button>
        </h2>
        <div id="cfgFaseOp" class="accordion-collapse collapse" data-bs-parent="#accConfigOp">
          <div class="accordion-body">
            <p class="mb-2">Las <strong>fases</strong> son el nivel más alto de la jerarquía. Agrupan varias etapas bajo un mismo nombre.</p>
            <p class="mb-2"><strong>Ejemplos:</strong> Etapa Preoperativa · Etapa Operativa · Renovación</p>
            <div class="bg-body-secondary rounded p-3 small mb-3">
              <strong>Cómo crearlas:</strong><br>
              <code>Configuración → Fases → + Nueva fase</code><br><br>
              Campos: <strong>Nombre</strong> · Descripción · <strong>Orden</strong> (define en qué posición aparece) · Activo
            </div>
            <div class="alert alert-info small mb-0">
              <i class="bi bi-info-circle me-1"></i>
              Debes crear las fases <strong>antes</strong> de crear las etapas, porque al crear una etapa deberás asignarle su fase.
            </div>
          </div>
        </div>
      </div>

      <!-- Entidades -->
      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#cfgEntOp">
            <i class="bi bi-bank me-2 text-primary"></i>2. Entidades
          </button>
        </h2>
        <div id="cfgEntOp" class="accordion-collapse collapse" data-bs-parent="#accConfigOp">
          <div class="accordion-body">
            <p class="mb-2">Son los organismos externos que intervienen en los requisitos.</p>
            <p class="mb-2"><strong>Ejemplos:</strong> DIAN, Ministerio de Comercio, DIMAR, Supersociedades, Cámara de Comercio.</p>
            <div class="bg-body-secondary rounded p-3 small">
              <strong>Cómo crearlas:</strong><br>
              <code>Configuración → Entidades → + Nueva entidad</code><br><br>
              Campos: <strong>Nombre</strong> (requerido) · Descripción (opcional) · Activo
            </div>
          </div>
        </div>
      </div>

      <!-- Etapas -->
      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#cfgEtapaOp">
            <i class="bi bi-diagram-3 me-2 text-success"></i>3. Etapas
          </button>
        </h2>
        <div id="cfgEtapaOp" class="accordion-collapse collapse" data-bs-parent="#accConfigOp">
          <div class="accordion-body">
            <p class="mb-2">Son los pasos concretos del proceso dentro de cada fase.</p>
            <p class="mb-2"><strong>Ejemplos dentro de "Etapa Preoperativa":</strong> Garantía · Cerramiento Provisional · Control frentes de Obra · Manuales y Procedimientos</p>
            <div class="bg-body-secondary rounded p-3 small mb-3">
              <strong>Cómo crearlas:</strong><br>
              <code>Configuración → Etapas → + Nueva etapa</code><br><br>
              Campos importantes:
              <ul class="mb-0 mt-1">
                <li><strong>Fase:</strong> a qué fase pertenece esta etapa (ej: Etapa Preoperativa)</li>
                <li><strong>Orden:</strong> posición dentro de la fase (1, 2, 3…)</li>
                <li><strong>Peso porcentual:</strong> cuánto pesa esta etapa en el avance total</li>
              </ul>
            </div>
            <div class="alert alert-warning small mb-0">
              <i class="bi bi-exclamation-triangle me-1"></i>
              Cada etapa que crees aquí debe asignarse manualmente a cada empresa desde la ficha de la empresa.
            </div>
          </div>
        </div>
      </div>

      <!-- Requisitos -->
      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#cfgReqOp">
            <i class="bi bi-list-check me-2 text-warning"></i>4. Requisitos
          </button>
        </h2>
        <div id="cfgReqOp" class="accordion-collapse collapse" data-bs-parent="#accConfigOp">
          <div class="accordion-body">
            <p class="mb-2">Son los documentos o acciones concretas que debe cumplir cada empresa dentro de cada etapa.</p>
            <div class="bg-body-secondary rounded p-3 small mb-3">
              <strong>Cómo crearlos:</strong><br>
              <code>Configuración → Requisitos → + Nuevo requisito</code>
            </div>
            <table class="table table-sm table-bordered small">
              <thead class="table-light">
                <tr><th>Campo</th><th>Para qué sirve</th></tr>
              </thead>
              <tbody>
                <tr><td><strong>Etapa</strong></td><td>A qué etapa (y por ende fase) pertenece</td></tr>
                <tr><td><strong>Entidad</strong></td><td>Organismo que lo gestiona (opcional)</td></tr>
                <tr><td><strong>Obligatorio</strong></td><td>Si está activo, bloquea el avance si no se cumple</td></tr>
                <tr><td><strong>Requiere documento soporte</strong></td><td>Aparecerá campo de subida en el seguimiento</td></tr>
                <tr><td><strong>Requiere fecha de vencimiento</strong></td><td>Se registra una fecha límite</td></tr>
                <tr><td><strong>Requiere aprobación</strong></td><td>Debe ser validado por operaciones</td></tr>
                <tr><td><strong>Peso porcentual</strong></td><td>Cuánto pesa este requisito dentro de su etapa</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Ítems -->
      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#cfgItemOp">
            <i class="bi bi-ui-checks me-2 text-info"></i>5. Ítems
          </button>
        </h2>
        <div id="cfgItemOp" class="accordion-collapse collapse" data-bs-parent="#accConfigOp">
          <div class="accordion-body">
            <p class="mb-2">Sub-pasos dentro de un requisito. Funcionan como checklist. No son obligatorios, pero sirven para desglosar requisitos complejos.</p>
            <div class="bg-body-secondary rounded p-3 small mb-3">
              <strong>Cómo crearlos:</strong><br>
              <code>Configuración → Ítems → "Agregar ítem aquí"</code> (en el requisito correspondiente)
            </div>
            <div class="alert alert-info small mb-2">
              <i class="bi bi-info-circle me-1"></i>
              Si un ítem es <strong>obligatorio (*)</strong>, el requisito no puede marcarse como "Cumplido" hasta que esté marcado.
            </div>
            <div class="bg-body-secondary rounded p-3 small">
              <strong>Ejemplo de estructura completa:</strong><br>
              <code>
                Fase: Etapa Preoperativa<br>
                &nbsp;&nbsp;└── Etapa: Garantía<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;└── Requisito: Registro ante la DIAN<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;└── Ítem: RUT actualizado ✓<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;└── Ítem: Resolución de calificación ✓
              </code>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════ -->
<!-- SECCIÓN: EMPRESAS                                     -->
<!-- ══════════════════════════════════════════════════════ -->
<div id="empresas-op" class="card shadow-sm mb-4">
  <div class="card-header bg-success text-white">
    <h5 class="mb-0"><i class="bi bi-building me-2"></i>Paso 2 — Crear y configurar empresas</h5>
  </div>
  <div class="card-body">

    <div class="row g-4">
      <div class="col-md-6">
        <h6 class="fw-bold"><i class="bi bi-plus-circle text-success me-2"></i>2.1 Crear empresa</h6>
        <div class="bg-body-secondary rounded p-3 small mb-2">
          <code>Empresas → + Nueva empresa</code>
        </div>
        <ul class="small">
          <li>Razón social, NIT, representante legal, teléfono</li>
          <li>Correo de contacto — <strong>si se llena, se le crea automáticamente un usuario de acceso</strong> con ese correo y la contraseña indicada</li>
          <li>Fase inicial del proyecto</li>
        </ul>
        <div class="alert alert-info small py-2 mb-0">
          <i class="bi bi-envelope-check me-1"></i>
          Al crear la empresa (y al crear un usuario nuevo para ella) le llega automáticamente un <strong>correo de bienvenida</strong> con sus credenciales, y el sistema le exige <strong>cambiar la contraseña</strong> en su primer ingreso.
        </div>
      </div>
      <div class="col-md-6">
        <h6 class="fw-bold"><i class="bi bi-diagram-3 text-warning me-2"></i>2.2 Asignar etapas</h6>
        <p class="small mb-2">Desde la ficha de la empresa, en el bloque <strong>"Etapas del proyecto"</strong>, el desplegable muestra las etapas agrupadas por fase. Seleccionar la etapa y hacer clic en <strong>Agregar</strong>.</p>
        <div class="alert alert-info small py-2 mb-2">
          <i class="bi bi-info-circle me-1"></i>
          Si creas una fase/etapa nueva en Configuración <strong>después</strong> de crear la empresa, debes agregar las nuevas etapas manualmente desde la ficha de la empresa.
        </div>
        <div class="alert alert-warning small py-2 mb-0">
          <i class="bi bi-exclamation-triangle me-1"></i>
          Al asignar una etapa, <strong>todos sus requisitos quedan asociados automáticamente</strong> a esa empresa.
        </div>
      </div>
      <div class="col-md-6">
        <h6 class="fw-bold"><i class="bi bi-person-plus text-primary me-2"></i>2.3 Crear usuarios de la empresa</h6>
        <div class="bg-body-secondary rounded p-3 small mb-2">
          <code>Ficha empresa → Usuarios → + Nuevo</code><br>
          <em>o también:</em><br>
          <code>Empresas → Usuarios de empresa → + Nuevo</code>
        </div>
        <ul class="small mb-0">
          <li>Nombre completo</li>
          <li>Correo electrónico (sirve de usuario)</li>
          <li>Contraseña inicial temporal — se notifica por correo y se exige cambiarla al ingresar</li>
        </ul>
      </div>
      <div class="col-md-6">
        <h6 class="fw-bold"><i class="bi bi-eye text-info me-2"></i>2.4 Accesos rápidos desde la ficha</h6>
        <p class="small mb-2">Desde la ficha de cada empresa hay botones de acceso rápido a:</p>
        <div class="d-flex flex-wrap gap-2">
          <span class="badge text-bg-primary"><i class="bi bi-clipboard-check me-1"></i>Seguimiento</span>
          <span class="badge text-bg-info"><i class="bi bi-calendar3 me-1"></i>Cronograma</span>
          <span class="badge text-bg-success"><i class="bi bi-bar-chart-fill me-1"></i>Reporte</span>
          <span class="badge text-bg-warning text-dark"><i class="bi bi-folder2-open me-1"></i>Documentos</span>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- ══════════════════════════════════════════════════════ -->
<!-- SECCIÓN: SEGUIMIENTO                                  -->
<!-- ══════════════════════════════════════════════════════ -->
<div id="seguimiento-op" class="card shadow-sm mb-4">
  <div class="card-header bg-primary text-white">
    <h5 class="mb-0"><i class="bi bi-clipboard-check me-2"></i>Paso 3 — Seguimiento de requisitos</h5>
  </div>
  <div class="card-body">

    <div class="bg-body-secondary rounded p-3 small mb-4">
      <code>Empresas → [nombre empresa] → Seguimiento</code>
    </div>

    <p class="mb-3">Por cada requisito se puede actualizar la siguiente información:</p>

    <div class="table-responsive mb-4">
      <table class="table table-bordered table-sm small">
        <thead class="table-primary">
          <tr><th>Campo</th><th>Para qué sirve</th><th>Notas</th></tr>
        </thead>
        <tbody>
          <tr>
            <td><strong>Estado</strong></td>
            <td>Situación actual del requisito</td>
            <td>Pendiente / En progreso / Cumplido / No aplica</td>
          </tr>
          <tr>
            <td><strong>Fecha de vencimiento</strong></td>
            <td>Cuándo vence el requisito</td>
            <td>Aparece en rojo si ya venció</td>
          </tr>
          <tr>
            <td><strong>Observaciones</strong></td>
            <td>Notas internas del seguimiento</td>
            <td>Solo visible para operaciones</td>
          </tr>
          <tr>
            <td><strong>Ítems (checklist)</strong></td>
            <td>Marcar sub-pasos como cumplidos</td>
            <td>Los obligatorios (*) bloquean el cierre</td>
          </tr>
          <tr>
            <td><strong>Documento soporte</strong></td>
            <td>Subir el archivo requerido</td>
            <td>Solo aparece si el requisito lo exige</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="row g-3">
      <div class="col-md-6">
        <div class="alert alert-info mb-0 small">
          <i class="bi bi-info-circle-fill me-2"></i>
          <strong>Auto-cálculo de estado:</strong> Si el requisito tiene ítems, el estado se recalcula automáticamente. Cuando todos los ítems obligatorios están marcados, pasa a <span class="badge text-bg-success">Cumplido</span> solo.
        </div>
      </div>
      <div class="col-md-6">
        <div class="alert alert-success mb-0 small">
          <i class="bi bi-arrow-up-right-circle-fill me-2"></i>
          <strong>Auto-cálculo de etapa:</strong> Al guardar un requisito, el porcentaje de avance de la etapa se recalcula automáticamente.
        </div>
      </div>
      <div class="col-md-12">
        <div class="alert alert-warning mb-0 small">
          <i class="bi bi-envelope-check-fill me-2"></i>
          <strong>Notificación automática:</strong> cuando un requisito pasa a <strong>Cumplido</strong>, los usuarios de la empresa reciben un correo avisándoles. Guardar el mismo requisito de nuevo (ya cumplido) no vuelve a enviar el correo.
        </div>
      </div>
    </div>

  </div>
</div>

<!-- ══════════════════════════════════════════════════════ -->
<!-- SECCIÓN: DOCUMENTOS                                   -->
<!-- ══════════════════════════════════════════════════════ -->
<div id="documentos-op" class="card shadow-sm mb-4">
  <div class="card-header bg-warning text-dark">
    <h5 class="mb-0"><i class="bi bi-folder2-open me-2"></i>Paso 4 — Gestión de documentos</h5>
  </div>
  <div class="card-body">

    <div class="alert alert-warning small mb-4">
      <i class="bi bi-shield-lock-fill me-2"></i>
      <strong>Solo operaciones/admin puede subir y eliminar documentos.</strong> Los usuarios de empresa únicamente pueden descargarlos.
    </div>

    <div class="row g-4">
      <div class="col-md-6">
        <h6 class="fw-bold"><i class="bi bi-upload text-success me-2"></i>Opción A — Desde el Seguimiento</h6>
        <p class="small">Cuando un requisito tiene <strong>"Requiere documento soporte"</strong> activado, aparece directamente un panel de subida en el formulario de seguimiento.</p>
        <ul class="small">
          <li>El archivo queda vinculado automáticamente al requisito</li>
          <li>También aparece en el Cronograma y en Documentos</li>
        </ul>
      </div>
      <div class="col-md-6">
        <h6 class="fw-bold"><i class="bi bi-folder2-open text-primary me-2"></i>Opción B — Módulo Documentos</h6>
        <div class="bg-body-secondary rounded p-2 small mb-2">
          <code>Documentos → [seleccionar empresa] → Subir documento</code>
        </div>
        <ul class="small">
          <li>Seleccionar a qué requisito pertenece</li>
          <li>Seleccionar el archivo</li>
          <li>Formatos: PDF, Word, Excel, JPG, PNG, ZIP</li>
          <li>Tamaño máximo: <strong>10 MB</strong></li>
        </ul>
      </div>
    </div>

  </div>
</div>

<!-- ══════════════════════════════════════════════════════ -->
<!-- SECCIÓN: COMITÉS                                      -->
<!-- ══════════════════════════════════════════════════════ -->
<div id="comites-op" class="card shadow-sm mb-4">
  <div class="card-header bg-secondary text-white">
    <h5 class="mb-0"><i class="bi bi-people-fill me-2"></i>Comités</h5>
  </div>
  <div class="card-body">

    <p class="mb-3">Los comités son reuniones formales con la empresa. Cada comité puede tener <strong>compromisos</strong> (tareas acordadas con fecha límite).</p>

    <div class="row g-3">
      <div class="col-md-4 text-center">
        <div class="rounded-circle bg-zf-navy text-white d-inline-flex align-items-center justify-content-center mb-2" style="width:40px;height:40px;">1</div>
        <p class="small fw-semibold mb-1">Crear comité</p>
        <p class="small text-muted">Tipo, título, empresa, fecha, lugar</p>
      </div>
      <div class="col-md-4 text-center">
        <div class="rounded-circle bg-zf-teal text-white d-inline-flex align-items-center justify-content-center mb-2" style="width:40px;height:40px;">2</div>
        <p class="small fw-semibold mb-1">Agregar compromisos</p>
        <p class="small text-muted">Descripción, responsable, fecha límite</p>
      </div>
      <div class="col-md-4 text-center">
        <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-2" style="width:40px;height:40px;">3</div>
        <p class="small fw-semibold mb-1">Marcar cumplidos</p>
        <p class="small text-muted">El comité muestra % de avance de compromisos</p>
      </div>
    </div>

    <div class="alert alert-info small mt-3 mb-0">
      <i class="bi bi-envelope-check-fill me-2"></i>
      <strong>Notificaciones automáticas:</strong> al crear un comité vinculado a una empresa, se avisa por correo a los usuarios de esa empresa. Al asignar un compromiso a un responsable, esa persona recibe un correo con la descripción y la fecha límite.
    </div>

  </div>
</div>

<!-- ══════════════════════════════════════════════════════ -->
<!-- SECCIÓN: CRONOGRAMA                                   -->
<!-- ══════════════════════════════════════════════════════ -->
<div id="cronograma-op" class="card shadow-sm mb-4">
  <div class="card-header bg-zf-teal text-white">
    <h5 class="mb-0"><i class="bi bi-calendar3 me-2"></i>Cronograma</h5>
  </div>
  <div class="card-body">

    <p class="mb-3">Vista visual del avance del proyecto de una empresa, por fases, etapas y requisitos. Puedes ver el cronograma de <strong>cualquier</strong> empresa desde <code>Cronograma → Ver todas las empresas</code>.</p>

    <div class="row g-3">
      <div class="col-md-6">
        <h6 class="small fw-bold text-muted text-uppercase">Qué muestra</h6>
        <ul class="small">
          <li>Un <strong>stepper por fase</strong> con sus etapas y % de avance individual</li>
          <li>Barra de progreso de cada fase en la cabecera del stepper</li>
          <li>Barra de avance general del proyecto</li>
          <li>Por cada etapa: avance %, estado, fechas</li>
          <li>Por cada requisito: estado, entidad, fecha de vencimiento</li>
          <li>Documentos adjuntos a cada requisito (con descarga)</li>
        </ul>
      </div>
      <div class="col-md-6">
        <h6 class="small fw-bold text-muted text-uppercase">Comportamiento</h6>
        <ul class="small">
          <li>Las etapas se numeran <strong>desde 1 dentro de cada fase</strong> (independientemente)</li>
          <li>La etapa <strong>en progreso</strong> se resalta en <span class="text-zf-teal fw-semibold">teal corporativo</span> y aparece expandida automáticamente</li>
          <li>Las fechas vencidas aparecen en <span class="badge text-bg-danger">rojo</span></li>
          <li>Los documentos del requisito aparecen con botón de descarga</li>
        </ul>
      </div>
    </div>

  </div>
</div>

<!-- ══════════════════════════════════════════════════════ -->
<!-- SECCIÓN: REPORTES                                     -->
<!-- ══════════════════════════════════════════════════════ -->
<div id="reportes-op" class="card shadow-sm mb-4">
  <div class="card-header bg-success text-white">
    <h5 class="mb-0"><i class="bi bi-bar-chart-fill me-2"></i>Reportes</h5>
  </div>
  <div class="card-body">

    <p class="mb-3">Resumen ejecutivo del estado de la empresa en el proceso de zona franca.</p>

    <div class="row g-3 mb-3">
      <?php
      $kpis = [
        ['icon'=>'bi-percent','color'=>'primary','label'=>'Avance general','desc'=>'% del proyecto completado'],
        ['icon'=>'bi-layers-fill','color'=>'success','label'=>'Etapas completadas','desc'=>'Cuántas etapas están al 100%'],
        ['icon'=>'bi-check2-all','color'=>'info','label'=>'Requisitos cumplidos','desc'=>'Sobre el total asignado'],
        ['icon'=>'bi-exclamation-triangle-fill','color'=>'danger','label'=>'Requisitos vencidos','desc'=>'Vencidos sin estar cumplidos'],
      ];
      foreach ($kpis as $k): ?>
      <div class="col-md-3">
        <div class="card border-<?= $k['color'] ?> border-2 text-center p-2">
          <i class="bi <?= $k['icon'] ?> text-<?= $k['color'] ?> fs-3 mb-1"></i>
          <div class="small fw-bold"><?= $k['label'] ?></div>
          <div class="text-muted" style="font-size:.7rem;"><?= $k['desc'] ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <ul class="small mb-2">
      <li>Tabla de avance por etapa con barras de progreso</li>
      <li>Distribución de requisitos por estado</li>
      <li>Listado de requisitos vencidos (con días de retraso)</li>
      <li>Requisitos por vencer en los próximos 30 días</li>
      <li>Conteo total de documentos subidos</li>
      <li>Botón <strong>Imprimir</strong> para generar versión en papel</li>
    </ul>

  </div>
</div>

<!-- ══════════════════════════════════════════════════════ -->
<!-- SECCIÓN: INDICADORES DE COLOR                         -->
<!-- ══════════════════════════════════════════════════════ -->
<div id="colores-op" class="card shadow-sm mb-4">
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
        ['badge'=>'text-bg-light border text-dark','label'=>'No aplica',                   'desc'=>'No se aplica a esta empresa'],
        ['badge'=>'text-bg-danger',               'label'=>'Vencido',                      'desc'=>'La fecha límite ya pasó sin cumplirse'],
        ['badge'=>'text-bg-warning text-dark',    'label'=>'Por vencer',                   'desc'=>'Vence en los próximos 30 días'],
        ['badge'=>'bg-zf-navy text-white',        'label'=>'Etapa actual / En curso',      'desc'=>'Etapa en la que se está trabajando ahora'],
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

<!-- Flujo resumido -->
<div class="card shadow-sm mb-4 border-2" style="border-color:var(--zf-teal) !important;">
  <div class="card-header bg-zf-teal text-white">
    <h5 class="mb-0"><i class="bi bi-diagram-2 me-2"></i>Flujo completo resumido</h5>
  </div>
  <div class="card-body">
    <div class="row g-2">
      <?php
      $flujo = [
        ['n'=>'1','color'=>'danger',  'texto'=>'Crear Fases en Configuración (Preoperativa, Operativa…)','icon'=>'bi-collection'],
        ['n'=>'2','color'=>'warning', 'texto'=>'Configurar Entidades, Etapas (asignadas a su fase), Requisitos e Ítems','icon'=>'bi-sliders'],
        ['n'=>'3','color'=>'success', 'texto'=>'Crear empresa y asignarle las etapas que le aplican (el dropdown muestra por fase)','icon'=>'bi-building'],
        ['n'=>'4','color'=>'zf-teal', 'texto'=>'Crear usuario(s) de la empresa para que puedan ingresar','icon'=>'bi-person-plus'],
        ['n'=>'5','color'=>'info',    'texto'=>'Hacer seguimiento periódico a los requisitos (agrupados por fase)','icon'=>'bi-clipboard-check'],
        ['n'=>'6','color'=>'warning', 'texto'=>'Subir documentos soporte requeridos','icon'=>'bi-upload'],
        ['n'=>'7','color'=>'secondary','texto'=>'Registrar comités y compromisos si los hay','icon'=>'bi-people'],
        ['n'=>'8','color'=>'success', 'texto'=>'Revisar reportes y cronograma (separados por fase) para detectar vencidos','icon'=>'bi-bar-chart-fill'],
      ];
      foreach ($flujo as $f):
        $esZf = str_starts_with($f['color'], 'zf-');
      ?>
      <div class="col-12">
        <div class="d-flex align-items-center gap-3 p-2 rounded border">
          <div class="rounded-circle bg-<?= $f['color'] ?> text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
               style="width:32px;height:32px;font-size:.85rem;">
            <?= $f['n'] ?>
          </div>
          <i class="bi <?= $f['icon'] ?> text-<?= $f['color'] ?> flex-shrink-0 fs-5"></i>
          <span class="small"><?= $f['texto'] ?></span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════ -->
<!-- SECCIÓN: JERARQUÍA DEL SISTEMA                        -->
<!-- ══════════════════════════════════════════════════════ -->
<div id="jerarquia-op" class="card shadow-sm mb-4 border-danger border-2">
  <div class="card-header bg-danger text-white">
    <h5 class="mb-0"><i class="bi bi-collection me-2"></i>Jerarquía del sistema</h5>
  </div>
  <div class="card-body">
    <p class="text-muted small mb-4">El sistema organiza la información en 4 niveles jerárquicos. Cada nivel pertenece al anterior.</p>
    <div class="row g-3 align-items-center text-center">

      <div class="col-md">
        <div class="card border-danger border-2 p-3 h-100">
          <div class="rounded-circle bg-danger text-white d-inline-flex align-items-center justify-content-center mx-auto mb-2"
               style="width:46px;height:46px;font-size:1.1rem;">
            <i class="bi bi-collection"></i>
          </div>
          <h6 class="fw-bold text-danger mb-1">Fases</h6>
          <small class="text-muted">El nivel más alto.<br>Agrupan varias etapas.<br><em>Ej: "Etapa Preoperativa"</em></small>
        </div>
      </div>

      <div class="col-md-auto text-muted fs-4">→</div>

      <div class="col-md">
        <div class="card border-success border-2 p-3 h-100">
          <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mx-auto mb-2"
               style="width:46px;height:46px;font-size:1.1rem;">
            <i class="bi bi-diagram-3"></i>
          </div>
          <h6 class="fw-bold text-success mb-1">Etapas</h6>
          <small class="text-muted">Pasos dentro de una fase.<br>Tienen orden y peso %.<br><em>Ej: "Garantía"</em></small>
        </div>
      </div>

      <div class="col-md-auto text-muted fs-4">→</div>

      <div class="col-md">
        <div class="card border-warning border-2 p-3 h-100">
          <div class="rounded-circle bg-warning text-dark d-inline-flex align-items-center justify-content-center mx-auto mb-2"
               style="width:46px;height:46px;font-size:1.1rem;">
            <i class="bi bi-list-check"></i>
          </div>
          <h6 class="fw-bold text-warning mb-1">Requisitos</h6>
          <small class="text-muted">Tareas o documentos<br>dentro de cada etapa.<br><em>Ej: "Registro DIAN"</em></small>
        </div>
      </div>

      <div class="col-md-auto text-muted fs-4">→</div>

      <div class="col-md">
        <div class="card p-3 h-100" style="border:2px solid var(--zf-teal);">
          <div class="rounded-circle bg-zf-teal text-white d-inline-flex align-items-center justify-content-center mx-auto mb-2"
               style="width:46px;height:46px;font-size:1.1rem;">
            <i class="bi bi-ui-checks"></i>
          </div>
          <h6 class="fw-bold text-zf-teal mb-1">Ítems</h6>
          <small class="text-muted">Checklist dentro<br>de un requisito.<br><em>Ej: "RUT actualizado"</em></small>
        </div>
      </div>

    </div>

    <div class="mt-4 bg-body-secondary rounded p-3 small font-monospace">
      <span class="badge text-bg-danger me-1">Fase</span> Etapa Preoperativa<br>
      &nbsp;&nbsp;&nbsp;<span class="badge text-bg-success me-1">Etapa</span> Garantía<br>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="badge text-bg-warning text-dark me-1">Requisito</span> Póliza de garantía<br>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="badge bg-zf-teal text-white me-1">Ítem</span> Póliza vigente ✓<br>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="badge bg-zf-teal text-white me-1">Ítem</span> Valor cubierto ✓<br>
      &nbsp;&nbsp;&nbsp;<span class="badge text-bg-success me-1">Etapa</span> Control frentes de Obra<br>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="badge text-bg-warning text-dark me-1">Requisito</span> Permiso de construcción<br>
      <span class="badge text-bg-danger me-1">Fase</span> Etapa Operativa<br>
      &nbsp;&nbsp;&nbsp;<span class="badge text-bg-success me-1">Etapa</span> Sistema de control de inventarios<br>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="badge text-bg-warning text-dark me-1">Requisito</span> Software aprobado por DIAN
    </div>

  </div>
</div>
