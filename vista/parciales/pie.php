<footer class="app-footer">
  <div class="float-end d-none d-sm-inline-block">
    <b>Versión</b> 1.0.0
  </div>
  <strong>
    &copy; <?= date('Y') ?> <a href="#" class="text-decoration-none">ZFPE</a>.
  </strong>
  Todos los derechos reservados.
</footer>

<!-- Bootstrap Bundle (Popper incluido) (local) -->
<script src="vista/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE 4 JS (local) -->
<script src="vista/assets/vendor/adminlte/js/adminlte.min.js"></script>
<!-- JS específico de la página -->
<?php foreach ($pageScripts ?? [] as $js): ?>
  <?php $ruta = __DIR__ . '/../../' . $js; ?>
  <script src="<?= $js ?>?v=<?= file_exists($ruta) ? filemtime($ruta) : '1' ?>"></script>
<?php endforeach; ?>

<!-- Toasts de notificación (éxito/error): autocierre a los 5s -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.toast-container .toast').forEach(function (el) {
      new bootstrap.Toast(el, { delay: 5000 }).show();
    });
  });
</script>

<!-- Evitar doble envío: deshabilita el botón y muestra spinner al enviar un formulario -->
<script>
  document.addEventListener('submit', function (e) {
    const form = e.target;
    if (!(form instanceof HTMLFormElement) || form.dataset.sinCarga !== undefined || e.defaultPrevented) return;

    const btn = e.submitter || form.querySelector('button[type="submit"]');
    if (!btn || btn.tagName !== 'BUTTON' || btn.disabled) return;

    const textoCarga = btn.dataset.textoCarga || 'Guardando...';
    setTimeout(function () {
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>' + textoCarga;
    }, 0);
  });
</script>

<!-- Selector de tema -->
<script>
  const iconosPorTema = { light: 'bi-sun-fill', dark: 'bi-moon-stars-fill', auto: 'bi-circle-half' };

  function temaResuelto(tema) {
    return (tema === 'auto' || !tema)
      ? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
      : tema;
  }

  function aplicarTema(tema) {
    document.documentElement.setAttribute('data-bs-theme', temaResuelto(tema));
    localStorage.setItem('tema', tema);

    // Actualizar ícono del botón
    const icono = document.querySelector('.tema-icono-activo');
    if (icono) {
      icono.className = 'bi ' + (iconosPorTema[tema] ?? iconosPorTema.auto) + ' tema-icono-activo';
    }

    // Marcar la opción activa con el check
    document.querySelectorAll('[data-tema]').forEach(btn => {
      const check = btn.querySelector('.bi-check2');
      if (check) check.classList.toggle('d-none', btn.dataset.tema !== tema);
    });
  }

  // Inicializar con el tema guardado (o auto por defecto)
  document.addEventListener('DOMContentLoaded', function () {
    const temaGuardado = localStorage.getItem('tema') ?? 'auto';
    aplicarTema(temaGuardado);

    document.querySelectorAll('[data-tema]').forEach(btn => {
      btn.addEventListener('click', () => aplicarTema(btn.dataset.tema));
    });

    // Reaccionar a cambios del SO cuando el tema es auto
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
      if ((localStorage.getItem('tema') ?? 'auto') === 'auto') {
        document.documentElement.setAttribute('data-bs-theme', temaResuelto('auto'));
      }
    });
  });
</script>

<!-- Campana de notificaciones: se marcan como leídas al hacer clic en cada una (o todas, con el botón) -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    function marcarLeidas(claves) {
      if (!claves.length) return;
      const payload = JSON.stringify({ claves });
      // sendBeacon sobrevive a la navegación que dispara el propio clic en la notificación.
      if (navigator.sendBeacon) {
        navigator.sendBeacon(
          'index.php?modulo=notificaciones&accion=marcar-leidas',
          new Blob([payload], { type: 'application/json' })
        );
      } else {
        fetch('index.php?modulo=notificaciones&accion=marcar-leidas', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: payload,
          keepalive: true,
        }).catch(() => {});
      }
    }

    function actualizarBadge() {
      const restantes = document.querySelectorAll('.notif-item--no-leida').length;
      const badge = document.getElementById('badgeNotificaciones');
      if (badge) {
        badge.style.display = restantes === 0 ? 'none' : '';
        badge.textContent   = restantes > 9 ? '9+' : restantes;
      }
      const btnTodas = document.getElementById('btnMarcarTodasLeidas');
      if (btnTodas) btnTodas.style.display = restantes === 0 ? 'none' : '';
    }

    function marcarComoLeidaEnPantalla(el) {
      el.classList.remove('notif-item--no-leida');
      const dot = el.querySelector('.notif-dot');
      if (dot) dot.style.visibility = 'hidden';
      actualizarBadge();
    }

    document.querySelectorAll('.notif-item--no-leida').forEach(function (el) {
      el.addEventListener('click', function () {
        marcarLeidas([el.dataset.clave]);
        marcarComoLeidaEnPantalla(el);
      });
    });

    const btnTodas = document.getElementById('btnMarcarTodasLeidas');
    if (btnTodas) {
      btnTodas.addEventListener('click', function () {
        const noLeidas = document.querySelectorAll('.notif-item--no-leida');
        marcarLeidas(Array.from(noLeidas).map(el => el.dataset.clave));
        noLeidas.forEach(marcarComoLeidaEnPantalla);
      });
    }
  });
</script>
</body>
</html>
