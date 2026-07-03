<?php
$esAdmin       = ($_SESSION['usuario_rol'] ?? '') === 'admin';
$esOperaciones = ($_SESSION['usuario_rol'] ?? '') === 'operaciones';

// En la barra lateral: el personal de operaciones/admin ve "ZFPE";
// un usuario de empresa ve el nombre de su propia empresa.
$marcaTexto = 'ZFPE';
if (!$esAdmin && !$esOperaciones && !empty($_SESSION['usuario_empresa_id'])) {
    $stmt = conectar()->prepare('SELECT razon_social FROM empresas WHERE id = ?');
    $stmt->execute([(int) $_SESSION['usuario_empresa_id']]);
    $nombreEmpresa = $stmt->fetchColumn();
    if ($nombreEmpresa) {
        $marcaTexto = $nombreEmpresa;
    }
}
?>

<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
  <div class="sidebar-brand">
    <a href="index.php" class="brand-link" title="<?= htmlspecialchars($marcaTexto) ?>">
      <img src="vista/img/logo.png" alt="Logo" class="brand-image-zf flex-shrink-0">
      <span class="brand-text fw-light text-truncate d-inline-block" style="max-width:150px; vertical-align:middle;">
        <?= htmlspecialchars($marcaTexto) ?>
      </span>
    </a>
  </div>

  <style>
    .brand-image-zf {
      height: 2rem;
      width: auto;
      margin-right: .5rem;
      object-fit: contain;
    }
  </style>

  <div class="sidebar-wrapper">
    <nav class="mt-2">
      <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">

        <li class="nav-item">
          <a href="index.php" class="nav-link <?= ($activePage ?? '') === 'tablero' ? 'active' : '' ?>">
            <i class="nav-icon bi bi-speedometer2"></i>
            <p>Tablero</p>
          </a>
        </li>

        <?php if (!$esAdmin && !$esOperaciones): ?>
          <li class="nav-item">
            <a href="index.php?modulo=cronograma" class="nav-link <?= ($activePage ?? '') === 'cronograma' ? 'active' : '' ?>">
              <i class="nav-icon bi bi-calendar3"></i>
              <p>Cronograma</p>
            </a>
          </li>
           <li class="nav-item">
            <a href="index.php?modulo=comites" class="nav-link <?= ($activePage ?? '') === 'comites' ? 'active' : '' ?>">
              <i class="nav-icon bi bi-people"></i>
              <p>Comités</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="index.php?modulo=mis-compromisos" class="nav-link <?= ($activePage ?? '') === 'mis-compromisos' ? 'active' : '' ?>">
              <i class="nav-icon bi bi-clipboard-check"></i>
              <p>Mis compromisos</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="index.php?modulo=entidades" class="nav-link <?= ($activePage ?? '') === 'entidades' ? 'active' : '' ?>">
              <i class="nav-icon bi bi-bank"></i>
              <p>Entidades</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="index.php?modulo=documentos" class="nav-link <?= ($activePage ?? '') === 'documentos' ? 'active' : '' ?>">
              <i class="nav-icon bi bi-folder2-open"></i>
              <p>Documentos</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="index.php?modulo=reportes" class="nav-link <?= ($activePage ?? '') === 'reportes' ? 'active' : '' ?>">
              <i class="nav-icon bi bi-bar-chart-fill"></i>
              <p>Reportes</p>
            </a>
          </li>
         
        <?php endif; ?>

        <?php if ($esAdmin): ?>
          <li class="nav-item">
            <a href="index.php?modulo=usuarios" class="nav-link <?= ($activePage ?? '') === 'usuarios' ? 'active' : '' ?>">
              <i class="nav-icon bi bi-people-fill"></i>
              <p>Usuarios</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="index.php?modulo=empresas" class="nav-link <?= ($activePage ?? '') === 'empresas' ? 'active' : '' ?>">
              <i class="nav-icon bi bi-building-fill"></i>
              <p>Empresas</p>
            </a>
          </li>
        <?php endif; ?>

        <?php if ($esOperaciones): ?>
          <li class="nav-item">
            <a href="index.php?modulo=empresas" class="nav-link <?= ($activePage ?? '') === 'empresas' ? 'active' : '' ?>">
              <i class="nav-icon bi bi-building-fill"></i>
              <p>Empresas</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="index.php?modulo=empresas&accion=usuarios" class="nav-link <?= ($activePage ?? '') === 'usuarios-empresa' ? 'active' : '' ?>">
              <i class="nav-icon bi bi-people-fill"></i>
              <p>Usuarios de empresa</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="index.php?modulo=usuarios" class="nav-link <?= ($activePage ?? '') === 'usuarios' ? 'active' : '' ?>">
              <i class="nav-icon bi bi-person-badge-fill"></i>
              <p>Usuarios internos</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="index.php?modulo=configuracion" class="nav-link <?= ($activePage ?? '') === 'configuracion' ? 'active' : '' ?>">
              <i class="nav-icon bi bi-sliders"></i>
              <p>Configuración</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="index.php?modulo=indicadores" class="nav-link <?= ($activePage ?? '') === 'indicadores' ? 'active' : '' ?>">
              <i class="nav-icon bi bi-graph-up"></i>
              <p>Indicadores</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="index.php?modulo=comites" class="nav-link <?= ($activePage ?? '') === 'comites' ? 'active' : '' ?>">
              <i class="nav-icon bi bi-people"></i>
              <p>Comités</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="index.php?modulo=documentos" class="nav-link <?= ($activePage ?? '') === 'documentos' ? 'active' : '' ?>">
              <i class="nav-icon bi bi-folder2-open"></i>
              <p>Documentos</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="index.php?modulo=cronograma" class="nav-link <?= ($activePage ?? '') === 'cronograma' ? 'active' : '' ?>">
              <i class="nav-icon bi bi-calendar3"></i>
              <p>Cronograma</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="index.php?modulo=reportes" class="nav-link <?= ($activePage ?? '') === 'reportes' ? 'active' : '' ?>">
              <i class="nav-icon bi bi-bar-chart-fill"></i>
              <p>Reportes</p>
            </a>
          </li>
        <?php endif; ?>

        <li class="nav-item mt-2">
          <a href="index.php?modulo=manual" class="nav-link <?= ($activePage ?? '') === 'manual' ? 'active' : '' ?>">
            <i class="nav-icon bi bi-book-half"></i>
            <p>Manual de uso</p>
          </a>
        </li>

      </ul>
    </nav>
  </div>
</aside>
