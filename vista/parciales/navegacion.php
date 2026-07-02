<?php
require_once __DIR__ . '/../../modelo/NotificacionesModelo.php';
$notificaciones = [];
if (!empty($_SESSION['usuario_id'])) {
    $notifModelo = new NotificacionesModelo(conectar());
    $notificaciones = $notifModelo->paraUsuario(
        (int) $_SESSION['usuario_id'],
        $_SESSION['usuario_rol'] ?? '',
        isset($_SESSION['usuario_empresa_id']) ? (int) $_SESSION['usuario_empresa_id'] : null,
        $_SESSION['usuario_nombre'] ?? ''
    );
}
$notifIcono = [
    'vencido'             => ['bi-exclamation-circle-fill', 'text-danger'],
    'compromiso_vencido'  => ['bi-exclamation-circle-fill', 'text-danger'],
    'por_vencer'          => ['bi-clock-fill', 'text-warning'],
    'compromiso'          => ['bi-people-fill', 'text-warning'],
    'cambio'              => ['bi-arrow-repeat', 'text-primary'],
    'documento'           => ['bi-file-earmark-fill', 'text-info'],
];
$noLeidas = count(array_filter($notificaciones, fn($n) => !$n['leido']));
?>
<nav class="app-header navbar navbar-expand bg-body">
  <div class="container-fluid">
    <!-- Sidebar toggle -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
          <i class="bi bi-list"></i>
        </a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ms-auto">
      <!-- Notifications -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-bs-toggle="dropdown" href="#" id="campanaNotificaciones">
          <i class="bi bi-bell-fill"></i>
          <span class="navbar-badge badge text-bg-warning" id="badgeNotificaciones" <?= $noLeidas === 0 ? 'style="display:none;"' : '' ?>>
            <?= $noLeidas > 9 ? '9+' : $noLeidas ?>
          </span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" style="max-height:420px;overflow-y:auto;">
          <span class="dropdown-item dropdown-header d-flex align-items-center justify-content-between gap-2">
            <span><?= count($notificaciones) ?> notificaci<?= count($notificaciones) === 1 ? 'ón' : 'ones' ?></span>
            <button type="button" class="btn btn-link btn-sm p-0 small" id="btnMarcarTodasLeidas" <?= $noLeidas === 0 ? 'style="display:none;"' : '' ?>>
              Marcar todas leídas
            </button>
          </span>
          <div class="dropdown-divider m-0"></div>
          <?php if (empty($notificaciones)): ?>
            <div class="text-center text-muted py-4 px-3 small">
              <i class="bi bi-check-circle fs-3 d-block mb-1 opacity-50"></i>
              Sin novedades por ahora.
            </div>
          <?php else: ?>
            <?php foreach ($notificaciones as $n): [$icono, $color] = $notifIcono[$n['tipo']] ?? ['bi-bell', 'text-secondary']; ?>
            <a href="<?= htmlspecialchars($n['url']) ?>" class="dropdown-item white-space-normal py-2 notif-item <?= $n['leido'] ? '' : 'notif-item--no-leida' ?>"
               data-clave="<?= htmlspecialchars($n['clave']) ?>">
              <div class="d-flex align-items-start gap-2">
                <span class="notif-dot flex-shrink-0" <?= $n['leido'] ? 'style="visibility:hidden;"' : '' ?>></span>
                <i class="bi <?= $icono ?> <?= $color ?> mt-1 flex-shrink-0"></i>
                <div class="min-w-0">
                  <div class="small" style="white-space:normal;"><?= htmlspecialchars($n['texto']) ?></div>
                  <div class="text-muted" style="font-size:.72rem;"><?= htmlspecialchars($n['meta']) ?></div>
                </div>
              </div>
            </a>
            <div class="dropdown-divider m-0"></div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </li>

      <!-- Selector de tema -->
      <li class="nav-item dropdown">
        <button class="btn btn-link nav-link"
                type="button"
                id="selectorTema"
                data-bs-toggle="dropdown"
                aria-expanded="false">
          <i class="bi bi-sun-fill tema-icono-activo"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="selectorTema">
          <li>
            <button type="button" class="dropdown-item d-flex align-items-center gap-2" data-tema="light">
              <i class="bi bi-sun-fill"></i> Claro
              <i class="bi bi-check2 ms-auto d-none"></i>
            </button>
          </li>
          <li>
            <button type="button" class="dropdown-item d-flex align-items-center gap-2" data-tema="dark">
              <i class="bi bi-moon-stars-fill"></i> Oscuro
              <i class="bi bi-check2 ms-auto d-none"></i>
            </button>
          </li>
          <li>
            <button type="button" class="dropdown-item d-flex align-items-center gap-2" data-tema="auto">
              <i class="bi bi-circle-half"></i> Auto
              <i class="bi bi-check2 ms-auto d-none"></i>
            </button>
          </li>
        </ul>
      </li>

      <!-- User menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-bs-toggle="dropdown" href="#">
          <i class="bi bi-person-circle fs-5"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="index.php?modulo=perfil"><i class="bi bi-person me-2"></i>Perfil</a></li>
        
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="index.php?accion=logout"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</a></li>
        </ul>
      </li>
    </ul>
  </div>
</nav>

<style>
  .notif-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--zf-teal, #1993b8); margin-top: 6px; }
  .notif-item--no-leida .small { font-weight: 600; }
</style>
