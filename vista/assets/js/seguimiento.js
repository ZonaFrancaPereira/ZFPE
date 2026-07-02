document.addEventListener('DOMContentLoaded', function () {
  (function () {
    var modalEl = document.getElementById('modalDesbloquearReq');
    var btnConfirmar = document.getElementById('btnConfirmarDesbloqueo');
    var triggerBtn = null;

    if (!modalEl || !btnConfirmar) return;

    modalEl.addEventListener('show.bs.modal', function (e) {
      triggerBtn = e.relatedTarget;
      document.getElementById('nombreReqDesbloquear').textContent = triggerBtn.dataset.nombre;
    });

    btnConfirmar.addEventListener('click', function () {
      if (!triggerBtn) return;
      var target = document.getElementById(triggerBtn.dataset.target);
      if (target) {
        target.querySelectorAll('input:not([data-permanent]), select, textarea, button[type="submit"]').forEach(function (el) {
          el.disabled = false;
        });
      }
      triggerBtn.remove();
      triggerBtn = null;
      bootstrap.Modal.getInstance(modalEl).hide();
    });
  })();

  (function () {
    var colapsables = function () {
      return document.querySelectorAll('.etapa-collapse');
    };

    var btnExpandir = document.getElementById('btnExpandirTodas');
    var btnColapsar = document.getElementById('btnColapsarTodas');

    if (btnExpandir) {
      btnExpandir.addEventListener('click', function () {
        colapsables().forEach(function (el) {
          bootstrap.Collapse.getOrCreateInstance(el, { toggle: false }).show();
        });
      });
    }

    if (btnColapsar) {
      btnColapsar.addEventListener('click', function () {
        colapsables().forEach(function (el) {
          bootstrap.Collapse.getOrCreateInstance(el, { toggle: false }).hide();
        });
      });
    }

    document.querySelectorAll('.etapa-resumen-link').forEach(function (link) {
      link.addEventListener('click', function () {
        var el = document.getElementById(this.dataset.collapseTarget);
        if (el) bootstrap.Collapse.getOrCreateInstance(el, { toggle: false }).show();
      });
    });
  })();

  // Si la URL trae #req-<id> o #etapa-<id> (p. ej. tras guardar un seguimiento),
  // mantener abierta la etapa correspondiente en vez de dejarla colapsada.
  (function () {
    if (!window.location.hash) return;

    var hashTarget = document.querySelector(window.location.hash);
    if (!hashTarget) return;

    var etapaCollapse = hashTarget.closest('.etapa-collapse') || hashTarget.querySelector('.etapa-collapse');
    if (!etapaCollapse) return;

    etapaCollapse.addEventListener('shown.bs.collapse', function () {
      hashTarget.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }, { once: true });

    bootstrap.Collapse.getOrCreateInstance(etapaCollapse, { toggle: false }).show();
  })();
});
