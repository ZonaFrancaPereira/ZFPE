<?php

class ComitesControlador {

    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function index(): void {
        require_once __DIR__ . '/../modelo/ComitesModelo.php';
        $comites = (new ComitesModelo($this->db))->listar();
        require_once __DIR__ . '/../vista/modulos/comites/index.php';
    }

    public function crear(): void {
        require_once __DIR__ . '/../modelo/ComitesModelo.php';
        require_once __DIR__ . '/../modelo/EmpresasModelo.php';
        $modelo   = new ComitesModelo($this->db);
        $empresas = (new EmpresasModelo($this->db))->obtenerTodas();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty(trim($_POST['titulo'] ?? '')) || empty($_POST['fecha'] ?? '')) {
                $_SESSION['flash_error'] = 'El título y la fecha son obligatorios.';
            } else {
                $id = $modelo->crear($_POST);
                $_SESSION['flash_success'] = 'Comité registrado correctamente.';
                header('Location: index.php?modulo=comites&accion=ver&id=' . $id);
                exit;
            }
        }

        require_once __DIR__ . '/../vista/modulos/comites/crear.php';
    }

    public function ver(?int $id): void {
        if (!$id) { header('Location: index.php?modulo=comites'); exit; }
        require_once __DIR__ . '/../modelo/ComitesModelo.php';
        $modelo      = new ComitesModelo($this->db);
        $comite      = $modelo->obtener($id);
        if (!$comite) { header('Location: index.php?modulo=comites'); exit; }
        $compromisos  = $modelo->compromisos($id);
        $responsables = $modelo->responsablesDisponibles($comite['empresa_id'] ?? null);
        $historialPorCompromiso = [];
        foreach ($compromisos as $comp) {
            $historialPorCompromiso[$comp['id']] = $modelo->historialCompromiso((int) $comp['id']);
        }
        require_once __DIR__ . '/../vista/modulos/comites/ver.php';
    }

    public function editar(?int $id): void {
        if (!$id) { header('Location: index.php?modulo=comites'); exit; }
        require_once __DIR__ . '/../modelo/ComitesModelo.php';
        require_once __DIR__ . '/../modelo/EmpresasModelo.php';
        $modelo   = new ComitesModelo($this->db);
        $comite   = $modelo->obtener($id);
        if (!$comite) { header('Location: index.php?modulo=comites'); exit; }
        $empresas = (new EmpresasModelo($this->db))->obtenerTodas();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty(trim($_POST['titulo'] ?? '')) || empty($_POST['fecha'] ?? '')) {
                $_SESSION['flash_error'] = 'El título y la fecha son obligatorios.';
            } else {
                $modelo->editar($id, $_POST);
                $_SESSION['flash_success'] = 'Comité actualizado correctamente.';
                header('Location: index.php?modulo=comites&accion=ver&id=' . $id);
                exit;
            }
        }

        require_once __DIR__ . '/../vista/modulos/comites/editar.php';
    }

    public function eliminar(?int $id): void {
        if ($id) {
            require_once __DIR__ . '/../modelo/ComitesModelo.php';
            (new ComitesModelo($this->db))->eliminar($id);
            $_SESSION['flash_success'] = 'Comité eliminado correctamente.';
        }
        header('Location: index.php?modulo=comites');
        exit;
    }

    public function guardarCompromiso(): void {
        $comite_id = (int) ($_POST['comite_id'] ?? 0);
        if ($comite_id && !empty(trim($_POST['descripcion'] ?? ''))) {
            require_once __DIR__ . '/../modelo/ComitesModelo.php';
            (new ComitesModelo($this->db))->guardarCompromiso($_POST);
            $_SESSION['flash_success'] = 'Compromiso registrado correctamente.';
        }
        header('Location: index.php?modulo=comites&accion=ver&id=' . $comite_id);
        exit;
    }

    public function actualizarCompromiso(?int $id): void {
        $comite_id = (int) ($_POST['comite_id'] ?? 0);
        if ($id && !empty($_POST['estado'] ?? '')) {
            require_once __DIR__ . '/../modelo/ComitesModelo.php';
            (new ComitesModelo($this->db))->actualizarCompromiso($id, $_POST, $_SESSION['usuario_id'] ?? null);
            $_SESSION['flash_success'] = 'Compromiso actualizado.';
        }
        header('Location: index.php?modulo=comites&accion=ver&id=' . $comite_id);
        exit;
    }

    public function eliminarCompromiso(?int $id): void {
        if ($id) {
            require_once __DIR__ . '/../modelo/ComitesModelo.php';
            $comite_id = (new ComitesModelo($this->db))->eliminarCompromiso($id);
            $_SESSION['flash_success'] = 'Compromiso eliminado.';
            header('Location: index.php?modulo=comites&accion=ver&id=' . $comite_id);
            exit;
        }
        header('Location: index.php?modulo=comites');
        exit;
    }
}
