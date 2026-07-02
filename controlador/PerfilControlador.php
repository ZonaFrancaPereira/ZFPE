<?php

require_once __DIR__ . '/../modelo/UsuariosModelo.php';

class PerfilControlador {

    private PDO $db;
    private UsuariosModelo $modelo;

    public function __construct(PDO $db) {
        $this->db     = $db;
        $this->modelo = new UsuariosModelo($db);
    }

    public function index(): void {
        $usuario = $this->modelo->obtenerPorId((int) $_SESSION['usuario_id']);
        if (!$usuario) { header('Location: index.php?accion=logout'); exit; }

        $empresa = null;
        if (!empty($usuario['empresa_id'])) {
            $stmt = $this->db->prepare("SELECT razon_social, nit FROM empresas WHERE id = ?");
            $stmt->execute([$usuario['empresa_id']]);
            $empresa = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        require_once __DIR__ . '/../vista/modulos/perfil/index.php';
    }

    public function actualizar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?modulo=perfil');
            exit;
        }

        $usuarioId = (int) $_SESSION['usuario_id'];
        $usuario   = $this->modelo->obtenerPorId($usuarioId);
        if (!$usuario) { header('Location: index.php?accion=logout'); exit; }

        $nombre               = trim($_POST['nombre'] ?? '');
        $correo               = trim($_POST['correo'] ?? '');
        $contrasenaActual     = $_POST['contrasena_actual'] ?? '';
        $nuevaContrasena      = $_POST['nueva_contrasena'] ?? '';
        $confirmarContrasena  = $_POST['confirmar_contrasena'] ?? '';

        if ($nombre === '' || $correo === '') {
            $_SESSION['flash_error'] = 'El nombre y el correo son obligatorios.';
            header('Location: index.php?modulo=perfil');
            exit;
        }

        if ($this->modelo->correoExiste($correo, $usuarioId)) {
            $_SESSION['flash_error'] = 'Ese correo ya está en uso por otro usuario.';
            header('Location: index.php?modulo=perfil');
            exit;
        }

        $cambiandoContrasena = $contrasenaActual !== '' || $nuevaContrasena !== '' || $confirmarContrasena !== '';
        if ($cambiandoContrasena) {
            if (!password_verify($contrasenaActual, $usuario['contrasena'])) {
                $_SESSION['flash_error'] = 'La contraseña actual no es correcta.';
                header('Location: index.php?modulo=perfil');
                exit;
            }
            if (strlen($nuevaContrasena) < 6) {
                $_SESSION['flash_error'] = 'La nueva contraseña debe tener al menos 6 caracteres.';
                header('Location: index.php?modulo=perfil');
                exit;
            }
            if ($nuevaContrasena !== $confirmarContrasena) {
                $_SESSION['flash_error'] = 'Las contraseñas nuevas no coinciden.';
                header('Location: index.php?modulo=perfil');
                exit;
            }
            $this->modelo->actualizarContrasena($usuarioId, $nuevaContrasena);
        }

        $this->modelo->actualizarPerfil($usuarioId, $nombre, $correo);

        $_SESSION['usuario_nombre'] = $nombre;
        $_SESSION['flash_success']  = $cambiandoContrasena
            ? 'Perfil y contraseña actualizados correctamente.'
            : 'Perfil actualizado correctamente.';
        header('Location: index.php?modulo=perfil');
        exit;
    }
}
