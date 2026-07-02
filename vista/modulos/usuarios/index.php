<?php
$pageTitle   = 'Usuarios — ZFIP-E';
$activePage  = 'usuarios';
$pageStyles = ['vista/assets/css/componentes.css'];
$pageScripts = ['vista/assets/js/usuarios-index.js'];
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
            <h3 class="mb-0 titulo-zf">Usuarios</h3>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item active">Usuarios</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="app-content">
      <div class="container-fluid">
        <div class="card shadow-sm">
          <div class="card-header d-flex flex-wrap gap-2 align-items-center">
            <h3 class="card-title me-auto mb-0">
              Usuarios
              <?php if (!empty($usuarios)): ?>
                <span class="badge text-bg-secondary ms-1"><?= count($usuarios) ?></span>
              <?php endif; ?>
            </h3>
            <div class="input-group input-group-sm" style="width:220px;">
              <span class="input-group-text"><i class="bi bi-search"></i></span>
              <input type="text" id="buscarUsuario" class="form-control" placeholder="Buscar usuario…">
            </div>
            <a href="index.php?modulo=usuarios&accion=crear" class="btn btn-primary btn-sm">
              <i class="bi bi-person-plus-fill me-1"></i> Nuevo usuario
            </a>
          </div>

          <div class="card-body p-0">
            <table class="table table-hover mb-0" id="tablaUsuarios">
              <thead class="table-light">
                <tr>
                  <th style="width:48px;">#</th>
                  <th>Usuario</th>
                  <th>Correo</th>
                  <th>Rol</th>
                  <th>Registrado</th>
                  <th class="text-end" style="width:110px;">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($usuarios)): ?>
                  <tr>
                    <td colspan="6" class="text-center py-5">
                      <i class="bi bi-people fs-1 text-muted d-block mb-2"></i>
                      <span class="text-muted">No hay usuarios registrados.</span>
                    </td>
                  </tr>
                <?php else: ?>
                  <?php
                  $paleta    = ['primary', 'success', 'danger', 'warning', 'info'];
                  $rol_color = [
                    'admin'         => 'danger',
                    'administrador' => 'danger',
                    'operador'      => 'warning',
                    'visualizador'  => 'info',
                    'viewer'        => 'info',
                  ];
                  ?>
                  <?php foreach ($usuarios as $u):
                    $inicial  = mb_strtoupper(mb_substr($u['nombre'], 0, 1));
                    $color    = $paleta[abs(crc32($u['nombre'])) % 5];
                    $rolColor = $rol_color[strtolower($u['rol'])] ?? 'secondary';
                    $fecha    = !empty($u['creado_en']) ? date('d/m/Y', strtotime($u['creado_en'])) : '—';
                  ?>
                    <tr>
                      <td class="align-middle text-muted small"><?= $u['id'] ?></td>
                      <td class="align-middle">
                        <div class="d-flex align-items-center gap-2">
                          <span class="rounded-circle bg-<?= $color ?> text-white d-flex align-items-center justify-content-center fw-semibold flex-shrink-0"
                                style="width:36px;height:36px;font-size:.85rem;">
                            <?= $inicial ?>
                          </span>
                          <?= htmlspecialchars($u['nombre']) ?>
                        </div>
                      </td>
                      <td class="align-middle"><?= htmlspecialchars($u['correo']) ?></td>
                      <td class="align-middle">
                        <span class="badge text-bg-<?= $rolColor ?>"><?= htmlspecialchars($u['rol']) ?></span>
                      </td>
                      <td class="align-middle text-muted small"><?= $fecha ?></td>
                      <td class="align-middle text-end">
                        <a href="index.php?modulo=usuarios&accion=editar&id=<?= $u['id'] ?>"
                           class="btn btn-sm btn-outline-warning" title="Editar">
                          <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="index.php?modulo=usuarios&accion=eliminar&id=<?= $u['id'] ?>"
                              class="d-inline" onsubmit="return confirm('¿Eliminar este usuario?')">
                          <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                            <i class="bi bi-trash"></i>
                          </button>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </main>

  <?php require_once __DIR__ . '/../../parciales/pie.php'; ?>

</div>
