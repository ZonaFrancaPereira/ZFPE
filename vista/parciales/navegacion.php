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
        <a class="nav-link" data-bs-toggle="dropdown" href="#">
          <i class="bi bi-bell-fill"></i>
          <span class="navbar-badge badge text-bg-warning">3</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
          <span class="dropdown-item dropdown-header">3 Notificaciones</span>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="bi bi-envelope me-2"></i> 4 mensajes nuevos
            <span class="float-end text-secondary fs-7">3 min</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">Ver todas</a>
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
          <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Perfil</a></li>
          <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Configuración</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="index.php?accion=logout"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</a></li>
        </ul>
      </li>
    </ul>
  </div>
</nav>
