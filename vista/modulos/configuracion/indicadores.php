<?php
/** @var array $indicadores */
$pageTitle  = 'Indicadores — Configuración';
$activePage = 'configuracion';
$pageStyles = ['vista/assets/css/componentes.css'];

$periodoLabel = ['mensual' => 'Mensual', 'trimestral' => 'Trimestral', 'semestral' => 'Semestral', 'anual' => 'Anual'];
?>
<?php require_once __DIR__ . '/../../parciales/cabecera.php'; ?>

<div class="app-wrapper">
  <?php require_once __DIR__ . '/../../parciales/navegacion.php'; ?>
  <?php require_once __DIR__ . '/../../parciales/menu_lateral.php'; ?>

  <main class="app-main">
    <div class="app-content-header encabezado-zf">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6"><h3 class="mb-0 titulo-zf">Indicadores</h3></div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item"><a href="index.php?modulo=configuracion">Configuración</a></li>
              <li class="breadcrumb-item active">Indicadores</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="app-content">
      <div class="container-fluid">

        <?php if (!empty($_SESSION['flash_success'])): ?>
          <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-1"></i><?= htmlspecialchars($_SESSION['flash_success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
          <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>

        <div class="card shadow-sm">
          <div class="card-header d-flex align-items-center justify-content-between gap-2">
            <h5 class="card-title mb-0">
              <i class="bi bi-graph-up me-2 text-primary"></i>Catálogo de indicadores
              <?php if (!empty($indicadores)): ?>
                <span class="badge text-bg-secondary ms-1"><?= count($indicadores) ?></span>
              <?php endif; ?>
            </h5>
            <a href="index.php?modulo=configuracion&accion=crear-indicador" class="btn btn-primary btn-sm">
              <i class="bi bi-plus-lg me-1"></i>Nuevo indicador
            </a>
          </div>
          <div class="card-body p-0">
            <?php if (empty($indicadores)): ?>
              <div class="text-center text-muted py-5">
                <i class="bi bi-graph-up fs-1 opacity-25 d-block mb-2"></i>
                No hay indicadores creados. <a href="index.php?modulo=configuracion&accion=crear-indicador">Crear el primero</a>.
              </div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Nombre</th>
                      <th>Unidad</th>
                      <th>Meta</th>
                      <th>Periodicidad</th>
                      <th class="text-center">Empresas</th>
                      <th class="text-center">Estado</th>
                      <th class="text-end" style="width:110px;">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($indicadores as $ind): ?>
                    <tr>
                      <td>
                        <div class="fw-semibold"><?= htmlspecialchars($ind['nombre']) ?></div>
                        <?php if ($ind['descripcion']): ?>
                          <small class="text-muted"><?= htmlspecialchars(mb_substr($ind['descripcion'], 0, 80)) ?><?= mb_strlen($ind['descripcion']) > 80 ? '…' : '' ?></small>
                        <?php endif; ?>
                      </td>
                      <td class="text-muted small"><?= htmlspecialchars($ind['unidad'] ?? '—') ?></td>
                      <td class="text-muted small">
                        <?= $ind['meta'] !== null ? number_format((float)$ind['meta'], 2, ',', '.') : '—' ?>
                      </td>
                      <td>
                        <span class="badge text-bg-light border">
                          <?= $periodoLabel[$ind['periodicidad']] ?? $ind['periodicidad'] ?>
                        </span>
                      </td>
                      <td class="text-center">
                        <span class="badge text-bg-secondary"><?= (int) $ind['total_asignados'] ?></span>
                      </td>
                      <td class="text-center">
                        <?php if ($ind['activo']): ?>
                          <span class="badge text-bg-success">Activo</span>
                        <?php else: ?>
                          <span class="badge text-bg-secondary">Inactivo</span>
                        <?php endif; ?>
                      </td>
                      <td class="text-end">
                        <a href="index.php?modulo=configuracion&accion=editar-indicador&id=<?= $ind['id'] ?>"
                           class="btn btn-sm btn-outline-warning" title="Editar">
                          <i class="bi bi-pencil"></i>
                        </a>
                        <a href="index.php?modulo=configuracion&accion=eliminar-indicador&id=<?= $ind['id'] ?>"
                           class="btn btn-sm btn-outline-danger" title="Eliminar"
                           onclick="return confirm('¿Eliminar este indicador? Se quitará de todas las empresas asignadas.')">
                          <i class="bi bi-trash"></i>
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

      </div>
    </div>
  </main>

  <?php require_once __DIR__ . '/../../parciales/pie.php'; ?>
</div>
