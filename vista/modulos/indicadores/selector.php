<?php
/** @var array $todasEmpresas */
$pageTitle  = 'Indicadores — ZFPE';
$activePage = 'indicadores';
$pageStyles = ['vista/assets/css/componentes.css'];
?>
<?php require_once __DIR__ . '/../../parciales/cabecera.php'; ?>

<div class="app-wrapper">
  <?php require_once __DIR__ . '/../../parciales/navegacion.php'; ?>
  <?php require_once __DIR__ . '/../../parciales/menu_lateral.php'; ?>

  <main class="app-main">
    <div class="app-content-header encabezado-zf">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6"><h3 class="mb-0 titulo-zf"><i class="bi bi-graph-up me-2"></i>Indicadores</h3></div>
          <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-zf float-sm-end">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item active">Indicadores</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="app-content">
      <div class="container-fluid">
        <div class="card shadow-sm">
          <div class="card-header">
            <h5 class="card-title mb-0"><i class="bi bi-building me-2 text-primary"></i>Seleccionar empresa</h5>
          </div>
          <div class="card-body p-0">
            <?php if (empty($todasEmpresas)): ?>
              <div class="text-center text-muted py-5">
                <i class="bi bi-building-x fs-1 opacity-25 d-block mb-2"></i>
                No hay empresas registradas.
              </div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Empresa</th>
                      <th>NIT</th>
                      <th class="text-end">Indicadores</th>
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
                      <td class="text-end">
                        <a href="index.php?modulo=indicadores&id=<?= $e['id'] ?>"
                           class="btn btn-sm btn-outline-primary">
                          <i class="bi bi-graph-up me-1"></i>Ver indicadores
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
