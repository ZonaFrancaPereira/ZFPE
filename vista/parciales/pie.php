<footer class="app-footer">
  <div class="float-end d-none d-sm-inline-block">
    <b>Versión</b> 1.0.0
  </div>
  <strong>
    &copy; <?= date('Y') ?> <a href="#" class="text-decoration-none">ZFIP-E</a>.
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
</body>
</html>
