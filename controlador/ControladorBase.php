<?php

/** Acceso común a la conexión PDO y a los datos de sesión del usuario autenticado. */
abstract class ControladorBase {

    protected PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    protected function rol(): string {
        return $_SESSION['usuario_rol'] ?? '';
    }

    protected function esOp(): bool {
        return in_array($this->rol(), ['operaciones', 'admin'], true);
    }

    protected function empresaId(): int {
        return (int) ($_SESSION['usuario_empresa_id'] ?? 0);
    }

    protected function usuarioId(): int {
        return (int) ($_SESSION['usuario_id'] ?? 0);
    }

    protected function nombreUsuario(): string {
        return $_SESSION['usuario_nombre'] ?? '';
    }

    /** Corta la ejecución si la acción (normalmente destructiva) no llegó por POST. */
    protected function exigirPost(string $redirigirA): void {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . $redirigirA);
            exit;
        }
    }
}
