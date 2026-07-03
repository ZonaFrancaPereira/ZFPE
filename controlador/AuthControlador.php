<?php

require_once __DIR__ . '/../modelo/UsuariosModelo.php';

class AuthControlador {

    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $correo    = trim($_POST['correo'] ?? '');
            $contrasena = $_POST['contrasena'] ?? '';

            $db   = conectar();
            $stmt = $db->prepare("SELECT * FROM usuarios WHERE correo = ? LIMIT 1");
            $stmt->execute([$correo]);
            $usuario = $stmt->fetch();

            if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
                session_regenerate_id(true);
                $_SESSION['usuario_id']         = $usuario['id'];
                $_SESSION['usuario_nombre']     = $usuario['nombre'];
                $_SESSION['usuario_rol']        = $usuario['rol'];
                $_SESSION['usuario_empresa_id'] = $usuario['empresa_id'] ?? null;
                $_SESSION['usuario_debe_cambiar_contrasena'] = (bool) $usuario['debe_cambiar_contrasena'];
                header('Location: index.php');
                exit;
            }

            $_SESSION['error_login'] = 'Correo o contraseña incorrectos.';
            header('Location: index.php?accion=login');
            exit;
        }

        require_once __DIR__ . '/../vista/login.php';
    }

    public function logout(): void {
        session_destroy();
        header('Location: index.php?accion=login');
        exit;
    }

    public function cambiarContrasenaObligatoria(): void {
        if (empty($_SESSION['usuario_id'])) {
            header('Location: index.php?accion=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nueva     = $_POST['nueva_contrasena'] ?? '';
            $confirmar = $_POST['confirmar_contrasena'] ?? '';

            if (strlen($nueva) < 8) {
                $_SESSION['error_cambio'] = 'La nueva contraseña debe tener al menos 8 caracteres.';
                header('Location: index.php?accion=cambiar-contrasena');
                exit;
            }
            if ($nueva !== $confirmar) {
                $_SESSION['error_cambio'] = 'Las contraseñas no coinciden.';
                header('Location: index.php?accion=cambiar-contrasena');
                exit;
            }

            (new UsuariosModelo(conectar()))->actualizarContrasena((int) $_SESSION['usuario_id'], $nueva);
            unset($_SESSION['usuario_debe_cambiar_contrasena']);
            $_SESSION['flash_success'] = 'Contraseña actualizada correctamente.';
            header('Location: index.php');
            exit;
        }

        require_once __DIR__ . '/../vista/cambiar_contrasena.php';
    }
}
