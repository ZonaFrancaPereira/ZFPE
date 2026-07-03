<?php

require_once __DIR__ . '/../modelo/EmpresasModelo.php';
require_once __DIR__ . '/ControladorBase.php';

class EmpresasControlador extends ControladorBase {

    private EmpresasModelo $modelo;

    public function __construct(PDO $db) {
        parent::__construct($db);
        $this->modelo = new EmpresasModelo($db);
    }

    public function index(): void {
        $empresas = $this->modelo->obtenerTodas();
        require_once __DIR__ . '/../vista/modulos/empresas/index.php';
    }

    public function crear(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nit        = trim($_POST['nit'] ?? '');
            $razon      = trim($_POST['razon_social'] ?? '');
            $contrasena = $_POST['contrasena'] ?? '';

            if ($nit === '' || $razon === '' || strlen($contrasena) < 8) {
                $_SESSION['flash_error'] = 'NIT, razón social y contraseña (mín. 8 caracteres) son obligatorios.';
                header('Location: index.php?modulo=empresas&accion=crear');
                exit;
            }

            $faseInicialId = (int) ($_POST['fase_inicial_id'] ?? 0) ?: null;

            $empresa_id = $this->modelo->crear($_POST);
            $this->modelo->aplicarMatriz($empresa_id, $faseInicialId);
            $this->notificarCreacion($_POST, $empresa_id, $faseInicialId);

            $_SESSION['flash_success'] = 'Empresa creada y matriz aplicada correctamente.';
            header('Location: index.php?modulo=empresas&accion=ver&id=' . $empresa_id);
            exit;
        }

        $fases = $this->db->query("SELECT id, nombre FROM fases WHERE activo = 1 ORDER BY orden ASC")->fetchAll(PDO::FETCH_ASSOC);
        require_once __DIR__ . '/../vista/modulos/empresas/crear.php';
    }

    /** Crea (si hace falta) el usuario de acceso de la empresa y envía el correo de bienvenida. No bloquea la creación si el correo falla. */
    private function notificarCreacion(array $datos, int $empresa_id, ?int $faseInicialId): void {
        $correoEmpresa = trim($datos['correo'] ?? '');
        if ($correoEmpresa === '') {
            return;
        }

        require_once __DIR__ . '/../modelo/UsuariosModelo.php';
        $usuariosModelo = new UsuariosModelo($this->db);

        $accesoCreado = false;
        if (!$usuariosModelo->correoExiste($correoEmpresa)) {
            $accesoCreado = $usuariosModelo->crearParaEmpresa($datos, $empresa_id, true);
        }

        $faseNombre = null;
        if ($faseInicialId) {
            $stmt = $this->db->prepare("SELECT nombre FROM fases WHERE id = ?");
            $stmt->execute([$faseInicialId]);
            $faseNombre = $stmt->fetchColumn() ?: null;
        }

        $razonSocial   = trim($datos['razon_social'] ?? '');
        $nit           = trim($datos['nit'] ?? '');
        $representante = trim($datos['representante'] ?? '');
        $telefono      = trim($datos['telefono'] ?? '');
        $correo        = $correoEmpresa;
        $contrasena    = $accesoCreado ? $datos['contrasena'] : null;
        $urlLogin      = APP_URL;

        ob_start();
        require __DIR__ . '/../vista/plantillas/correo_bienvenida_empresa.php';
        $cuerpoHtml = ob_get_clean();

        require_once __DIR__ . '/../modelo/CorreoServicio.php';
        (new CorreoServicio($this->db))->enviar(
            $correoEmpresa,
            'Registro de tu empresa — Centro de Control Gerencial ZFPE',
            $cuerpoHtml,
            $razonSocial
        );
    }

    /** Envía al usuario recién creado el aviso de su acceso al Centro de Control Gerencial, vinculado a la empresa. No bloquea la creación si el correo falla. */
    private function notificarUsuarioCreado(array $datos, array $empresa): void {
        $correo = trim($datos['correo'] ?? '');
        if ($correo === '') {
            return;
        }

        $nombreUsuario = trim($datos['nombre'] ?? '');
        $contrasena    = $datos['contrasena'] ?? '';
        $empresaNombre = $empresa['razon_social'] ?? '';
        $urlLogin      = APP_URL;

        ob_start();
        require __DIR__ . '/../vista/plantillas/correo_bienvenida_usuario.php';
        $cuerpoHtml = ob_get_clean();

        require_once __DIR__ . '/../modelo/CorreoServicio.php';
        (new CorreoServicio($this->db))->enviar(
            $correo,
            'Tu usuario de acceso — Centro de Control Gerencial ZFPE',
            $cuerpoHtml,
            $nombreUsuario
        );
    }

    public function editar(?int $id): void {
        if (!$id) { header('Location: index.php?modulo=empresas'); exit; }
        $empresa = $this->modelo->obtenerPorId($id);
        if (!$empresa) { header('Location: index.php?modulo=empresas'); exit; }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->modelo->actualizar($id, $_POST);
            $_SESSION['flash_success'] = 'Empresa actualizada correctamente.';
            header('Location: index.php?modulo=empresas&accion=ver&id=' . $id);
            exit;
        }

        require_once __DIR__ . '/../vista/modulos/empresas/editar.php';
    }

    public function ver(?int $id): void {
        if (!$id) { header('Location: index.php?modulo=empresas'); exit; }
        $empresa = $this->modelo->obtenerDetalle($id);
        if (!$empresa) { header('Location: index.php?modulo=empresas'); exit; }

        $usuariosDisponibles = $this->modelo->usuariosDisponibles($id);
        $etapasSinAsignar    = $this->modelo->etapasSinAsignar($id);
        require_once __DIR__ . '/../vista/modulos/empresas/ver.php';
    }

    public function asignarUsuario(?int $empresa_id): void {
        if (!$empresa_id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?modulo=empresas');
            exit;
        }

        $usuario_id = (int) ($_POST['usuario_id'] ?? 0);
        if ($usuario_id) {
            $stmt = $this->db->prepare("UPDATE usuarios SET empresa_id = ? WHERE id = ? AND rol = 'usuario'");
            $stmt->execute([$empresa_id, $usuario_id]);
        }

        header('Location: index.php?modulo=empresas&accion=ver&id=' . $empresa_id);
        exit;
    }

    public function agregarEtapa(?int $empresa_id): void {
        if (!$empresa_id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?modulo=empresas');
            exit;
        }

        $etapa_id = (int) ($_POST['etapa_id'] ?? 0);
        if ($etapa_id) {
            $this->modelo->agregarEtapa($empresa_id, $etapa_id);
            $_SESSION['flash_success'] = 'Etapa agregada correctamente.';
        }

        header('Location: index.php?modulo=empresas&accion=ver&id=' . $empresa_id);
        exit;
    }

    public function quitarUsuario(?int $empresa_id): void {
        if (!$empresa_id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?modulo=empresas');
            exit;
        }

        $usuario_id = (int) ($_POST['usuario_id'] ?? 0);
        if ($usuario_id) {
            $stmt = $this->db->prepare("UPDATE usuarios SET empresa_id = NULL WHERE id = ? AND empresa_id = ?");
            $stmt->execute([$usuario_id, $empresa_id]);
        }

        header('Location: index.php?modulo=empresas&accion=ver&id=' . $empresa_id);
        exit;
    }

    public function eliminar(?int $id): void {
        $this->exigirPost('index.php?modulo=empresas');
        if (!$id) { header('Location: index.php?modulo=empresas'); exit; }
        $this->modelo->eliminar($id);
        $_SESSION['flash_success'] = 'Empresa eliminada correctamente.';
        header('Location: index.php?modulo=empresas');
        exit;
    }

    // --- Gestión de usuarios de empresa ---

    public function listarUsuarios(): void {
        require_once __DIR__ . '/../modelo/UsuariosModelo.php';
        $usuarios = (new UsuariosModelo($this->db))->obtenerTodosDeEmpresas();
        require_once __DIR__ . '/../vista/modulos/empresas/usuarios.php';
    }

    public function crearUsuario(?int $empresa_id): void {
        if (!$empresa_id) { header('Location: index.php?modulo=empresas'); exit; }
        $empresa = $this->modelo->obtenerPorId($empresa_id);
        if (!$empresa) { header('Location: index.php?modulo=empresas'); exit; }

        require_once __DIR__ . '/../modelo/UsuariosModelo.php';
        $usuariosModelo = new UsuariosModelo($this->db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre     = trim($_POST['nombre'] ?? '');
            $correo     = trim($_POST['correo'] ?? '');
            $contrasena = $_POST['contrasena'] ?? '';

            if ($nombre === '' || $correo === '' || strlen($contrasena) < 8) {
                $_SESSION['flash_error'] = 'Nombre, correo y contraseña (mín. 8 caracteres) son obligatorios.';
            } elseif ($usuariosModelo->correoExiste($correo)) {
                $_SESSION['flash_error'] = 'Ya existe un usuario con ese correo electrónico.';
            } else {
                $usuariosModelo->crearParaEmpresa($_POST, $empresa_id, true);
                $this->notificarUsuarioCreado($_POST, $empresa);
                $_SESSION['flash_success'] = 'Usuario creado correctamente.';
                header('Location: index.php?modulo=empresas&accion=ver&id=' . $empresa_id);
                exit;
            }
        }

        require_once __DIR__ . '/../vista/modulos/empresas/crear_usuario.php';
    }

    public function editarUsuario(?int $usuario_id): void {
        if (!$usuario_id) { header('Location: index.php?modulo=empresas'); exit; }

        require_once __DIR__ . '/../modelo/UsuariosModelo.php';
        $usuariosModelo = new UsuariosModelo($this->db);
        $usuario        = $usuariosModelo->obtenerPorId($usuario_id);
        if (!$usuario || $usuario['rol'] !== 'usuario') {
            header('Location: index.php?modulo=empresas');
            exit;
        }

        $empresa  = $usuario['empresa_id'] ? $this->modelo->obtenerPorId((int) $usuario['empresa_id']) : null;
        $empresas = $this->modelo->obtenerTodas();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre     = trim($_POST['nombre'] ?? '');
            $correo     = trim($_POST['correo'] ?? '');
            $empresa_id = $_POST['empresa_id'] !== '' ? (int) $_POST['empresa_id'] : null;

            if ($nombre === '' || $correo === '') {
                $_SESSION['flash_error'] = 'Nombre y correo son obligatorios.';
            } elseif ($usuariosModelo->correoExiste($correo, $usuario_id)) {
                $_SESSION['flash_error'] = 'Ya existe otro usuario con ese correo.';
            } else {
                $usuariosModelo->actualizarUsuarioEmpresa($usuario_id, array_merge($_POST, ['empresa_id' => $empresa_id]));
                $_SESSION['flash_success'] = 'Usuario actualizado correctamente.';
                $back = $empresa_id
                    ? 'index.php?modulo=empresas&accion=ver&id=' . $empresa_id
                    : 'index.php?modulo=empresas&accion=usuarios';
                header('Location: ' . $back);
                exit;
            }
        }

        require_once __DIR__ . '/../vista/modulos/empresas/editar_usuario.php';
    }

    public function eliminarUsuario(?int $usuario_id): void {
        $this->exigirPost('index.php?modulo=empresas&accion=usuarios');
        if ($usuario_id) {
            require_once __DIR__ . '/../modelo/UsuariosModelo.php';
            $usuariosModelo = new UsuariosModelo($this->db);
            $usuario        = $usuariosModelo->obtenerPorId($usuario_id);
            if ($usuario && $usuario['rol'] === 'usuario') {
                $empresa_id = $usuario['empresa_id'];
                $usuariosModelo->eliminar($usuario_id);
                $_SESSION['flash_success'] = 'Usuario eliminado correctamente.';
                if ($empresa_id) {
                    header('Location: index.php?modulo=empresas&accion=ver&id=' . $empresa_id);
                    exit;
                }
            }
        }
        header('Location: index.php?modulo=empresas&accion=usuarios');
        exit;
    }
}
