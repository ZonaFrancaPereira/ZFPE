<?php

require_once __DIR__ . '/ControladorBase.php';

class ComitesControlador extends ControladorBase {

    /** Corta la ejecución si un usuario de empresa intenta una acción de escritura. */
    private function exigirOperaciones(): void {
        if (!$this->esOp()) {
            header('Location: index.php?modulo=comites');
            exit;
        }
    }

    public function index(): void {
        require_once __DIR__ . '/../modelo/ComitesModelo.php';
        $modelo = new ComitesModelo($this->db);
        $comites = $this->esOp()
            ? $modelo->listar()
            : ($this->empresaId() ? $modelo->listarPorEmpresa($this->empresaId()) : []);
        require_once __DIR__ . '/../vista/modulos/comites/index.php';
    }

    public function crear(): void {
        $this->exigirOperaciones();

        require_once __DIR__ . '/../modelo/ComitesModelo.php';
        require_once __DIR__ . '/../modelo/EmpresasModelo.php';
        $modelo   = new ComitesModelo($this->db);
        $empresas = (new EmpresasModelo($this->db))->obtenerTodas();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty(trim($_POST['titulo'] ?? '')) || empty($_POST['fecha'] ?? '')) {
                $_SESSION['flash_error'] = 'El título y la fecha son obligatorios.';
            } else {
                $id = $modelo->crear($_POST);
                $this->notificarComiteCreado($_POST);
                $_SESSION['flash_success'] = 'Comité registrado correctamente.';
                header('Location: index.php?modulo=comites&accion=ver&id=' . $id);
                exit;
            }
        }

        require_once __DIR__ . '/../vista/modulos/comites/crear.php';
    }

    /** Notifica por correo a los usuarios de la empresa vinculada de que se programó un comité. No bloquea la creación si falla. */
    private function notificarComiteCreado(array $datos): void {
        $empresa_id = (int) ($datos['empresa_id'] ?? 0);
        if (!$empresa_id) {
            return;
        }

        require_once __DIR__ . '/../modelo/UsuariosModelo.php';
        $usuarios = (new UsuariosModelo($this->db))->obtenerPorEmpresa($empresa_id);
        if (!$usuarios) {
            return;
        }

        $stmt = $this->db->prepare("SELECT razon_social FROM empresas WHERE id = ?");
        $stmt->execute([$empresa_id]);
        $empresaNombre = $stmt->fetchColumn() ?: '';

        $tiposLabel = [
            'seguimiento'    => 'Seguimiento',
            'aprobacion'     => 'Aprobación',
            'revision'       => 'Revisión',
            'extraordinario' => 'Extraordinario',
        ];

        $titulo          = trim($datos['titulo'] ?? '');
        $tipoLabel       = $tiposLabel[$datos['tipo'] ?? ''] ?? 'Seguimiento';
        $fechaFormateada = !empty($datos['fecha']) ? date('d/m/Y h:i A', strtotime($datos['fecha'])) : '';
        $lugar           = trim($datos['lugar'] ?? '');
        $descripcion     = trim($datos['descripcion'] ?? '');
        $urlLogin        = APP_URL;

        require_once __DIR__ . '/../modelo/CorreoServicio.php';
        $correoServicio = new CorreoServicio($this->db);

        foreach ($usuarios as $usuario) {
            if (empty($usuario['correo'])) {
                continue;
            }

            $nombreDestinatario = $usuario['nombre'];

            ob_start();
            require __DIR__ . '/../vista/plantillas/correo_comite_programado.php';
            $cuerpoHtml = ob_get_clean();

            $correoServicio->enviar(
                $usuario['correo'],
                'Nuevo comité programado — Centro de Control Gerencial ZFPE',
                $cuerpoHtml,
                $nombreDestinatario
            );
        }
    }

    public function ver(?int $id): void {
        if (!$id) { header('Location: index.php?modulo=comites'); exit; }
        require_once __DIR__ . '/../modelo/ComitesModelo.php';
        $modelo      = new ComitesModelo($this->db);
        $comite      = $modelo->obtener($id);
        if (!$comite) { header('Location: index.php?modulo=comites'); exit; }

        // Un usuario de empresa solo puede ver comités de su propia empresa
        if (!$this->esOp() && (int) ($comite['empresa_id'] ?? 0) !== $this->empresaId()) {
            header('Location: index.php?modulo=comites');
            exit;
        }

        $compromisos  = $modelo->compromisos($id);
        $responsables = $modelo->responsablesDisponibles($comite['empresa_id'] ?? null);
        $historialPorCompromiso = [];
        foreach ($compromisos as $comp) {
            $historialPorCompromiso[$comp['id']] = $modelo->historialCompromiso((int) $comp['id']);
        }
        require_once __DIR__ . '/../vista/modulos/comites/ver.php';
    }

    public function editar(?int $id): void {
        $this->exigirOperaciones();

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
        $this->exigirOperaciones();
        $this->exigirPost('index.php?modulo=comites');

        if ($id) {
            require_once __DIR__ . '/../modelo/ComitesModelo.php';
            (new ComitesModelo($this->db))->eliminar($id);
            $_SESSION['flash_success'] = 'Comité eliminado correctamente.';
        }
        header('Location: index.php?modulo=comites');
        exit;
    }

    public function guardarCompromiso(): void {
        $this->exigirOperaciones();

        $comite_id = (int) ($_POST['comite_id'] ?? 0);
        if ($comite_id && !empty(trim($_POST['descripcion'] ?? ''))) {
            require_once __DIR__ . '/../modelo/ComitesModelo.php';
            (new ComitesModelo($this->db))->guardarCompromiso($_POST);
            $this->notificarCompromisoAsignado($comite_id, $_POST);
            $_SESSION['flash_success'] = 'Compromiso registrado correctamente.';
        }
        header('Location: index.php?modulo=comites&accion=ver&id=' . $comite_id);
        exit;
    }

    /** Notifica por correo al responsable asignado de un compromiso nuevo. No bloquea el registro si falla. */
    private function notificarCompromisoAsignado(int $comite_id, array $datos): void {
        $nombreResponsable = trim($datos['responsable'] ?? '');
        if ($nombreResponsable === '') {
            return;
        }

        require_once __DIR__ . '/../modelo/ComitesModelo.php';
        $comite = (new ComitesModelo($this->db))->obtener($comite_id);
        if (!$comite) {
            return;
        }

        $stmt = $this->db->prepare("
            SELECT correo, nombre FROM usuarios
            WHERE nombre = ? AND (rol = 'operaciones' OR empresa_id = ?)
            LIMIT 1
        ");
        $stmt->execute([$nombreResponsable, $comite['empresa_id']]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$usuario || empty($usuario['correo'])) {
            return;
        }

        $nombreDestinatario     = $usuario['nombre'];
        $comiteTitulo           = $comite['titulo'];
        $empresaNombre          = $comite['empresa_nombre'] ?? null;
        $descripcion            = trim($datos['descripcion'] ?? '');
        $fechaLimiteFormateada  = !empty($datos['fecha_limite']) ? date('d/m/Y', strtotime($datos['fecha_limite'])) : null;
        $urlLogin               = APP_URL;

        ob_start();
        require __DIR__ . '/../vista/plantillas/correo_compromiso_asignado.php';
        $cuerpoHtml = ob_get_clean();

        require_once __DIR__ . '/../modelo/CorreoServicio.php';
        (new CorreoServicio($this->db))->enviar(
            $usuario['correo'],
            'Nuevo compromiso asignado — Centro de Control Gerencial ZFPE',
            $cuerpoHtml,
            $nombreDestinatario
        );
    }

    public function actualizarCompromiso(?int $id): void {
        $this->exigirOperaciones();

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
        $this->exigirOperaciones();
        $this->exigirPost('index.php?modulo=comites');

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
