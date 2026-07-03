<?php
$pageTitle  = 'Configuración SMTP — ZFPE';
$activePage = 'smtp';
?>
<?php require_once __DIR__ . '/../../parciales/cabecera.php'; ?>

<div class="app-wrapper">

  <?php require_once __DIR__ . '/../../parciales/navegacion.php'; ?>
  <?php require_once __DIR__ . '/../../parciales/menu_lateral.php'; ?>

  <main class="app-main">
    <div class="app-content-header encabezado-zf">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h3 class="mb-0 titulo-zf">Configuración SMTP</h3>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item active">Configuración SMTP</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="app-content">
      <div class="container-fluid">
        <div class="row justify-content-center">
          <div class="col-lg-7">

            <div class="card shadow-sm mb-4">
              <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-envelope-gear me-2 text-primary"></i>Cuenta de correo (Hostinger)</h5>
              </div>
              <div class="card-body">
                <form method="POST" action="index.php?modulo=smtp&accion=guardar">

                  <div class="row">
                    <div class="col-md-8 mb-3">
                      <label class="form-label fw-semibold">Servidor SMTP (host) <span class="text-danger">*</span></label>
                      <input type="text" name="host" class="form-control"
                             value="<?= htmlspecialchars($config['host'] ?? '') ?>"
                             placeholder="smtp.hostinger.com" required maxlength="255">
                    </div>
                    <div class="col-md-4 mb-3">
                      <label class="form-label fw-semibold">Puerto</label>
                      <input type="number" name="puerto" class="form-control"
                             value="<?= htmlspecialchars((string) ($config['puerto'] ?? 587)) ?>" min="1" max="65535">
                      <div class="form-text">465 (SSL) o 587 (TLS)</div>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label fw-semibold">Usuario / correo de autenticación <span class="text-danger">*</span></label>
                    <input type="text" name="usuario" class="form-control"
                           value="<?= htmlspecialchars($config['usuario'] ?? '') ?>"
                           placeholder="notificaciones@midominio.com" required maxlength="255">
                  </div>

                  <div class="mb-3">
                    <label class="form-label fw-semibold">Contraseña</label>
                    <input type="password" name="clave" class="form-control" autocomplete="new-password"
                           placeholder="<?= !empty($config) ? 'Dejar en blanco para no cambiarla' : '' ?>">
                    <div class="form-text">Se guarda cifrada en la base de datos.</div>
                  </div>

                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label class="form-label fw-semibold">Cifrado</label>
                      <?php $cifradoActual = $config['cifrado'] ?? 'tls'; ?>
                      <select name="cifrado" class="form-select">
                        <option value="tls" <?= $cifradoActual === 'tls' ? 'selected' : '' ?>>TLS (STARTTLS)</option>
                        <option value="ssl" <?= $cifradoActual === 'ssl' ? 'selected' : '' ?>>SSL</option>
                      </select>
                    </div>
                    <div class="col-md-6 mb-3 d-flex align-items-end">
                      <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="activo" id="chkActivo" value="1"
                               <?= (!isset($config) || !empty($config['activo'])) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="chkActivo">Configuración activa</label>
                      </div>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label fw-semibold">Correo remitente <span class="text-danger">*</span></label>
                    <input type="email" name="correo_remitente" class="form-control"
                           value="<?= htmlspecialchars($config['correo_remitente'] ?? '') ?>"
                           placeholder="notificaciones@midominio.com" required maxlength="255">
                  </div>

                  <div class="mb-4">
                    <label class="form-label fw-semibold">Nombre remitente</label>
                    <input type="text" name="nombre_remitente" class="form-control"
                           value="<?= htmlspecialchars($config['nombre_remitente'] ?? '') ?>"
                           placeholder="ZFPE — Notificaciones" maxlength="255">
                  </div>

                  <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Guardar configuración
                  </button>
                </form>
              </div>
            </div>

            <div class="card shadow-sm">
              <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-send-check me-2 text-primary"></i>Enviar correo de prueba</h5>
              </div>
              <div class="card-body">
                <form method="POST" action="index.php?modulo=smtp&accion=probar" class="d-flex gap-2">
                  <input type="email" name="correo_prueba" class="form-control" placeholder="destino@correo.com" required>
                  <button type="submit" class="btn btn-outline-primary text-nowrap">
                    <i class="bi bi-send me-1"></i> Enviar prueba
                  </button>
                </form>
                <div class="form-text mt-2">Guarda la configuración antes de probar.</div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </main>

  <?php require_once __DIR__ . '/../../parciales/pie.php'; ?>

</div>
