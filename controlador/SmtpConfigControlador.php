<?php

require_once __DIR__ . '/../modelo/SmtpConfigModelo.php';
require_once __DIR__ . '/../modelo/CorreoServicio.php';
require_once __DIR__ . '/ControladorBase.php';

class SmtpConfigControlador extends ControladorBase {

    private SmtpConfigModelo $modelo;

    public function __construct(PDO $db) {
        parent::__construct($db);
        $this->modelo = new SmtpConfigModelo($db);
    }

    public function index(): void {
        $config = $this->modelo->obtener();
        require_once __DIR__ . '/../vista/modulos/smtp/index.php';
    }

    public function guardar(): void {
        $this->exigirPost('index.php?modulo=smtp');

        if (trim($_POST['host'] ?? '') === '' || trim($_POST['usuario'] ?? '') === '' || trim($_POST['correo_remitente'] ?? '') === '') {
            $_SESSION['flash_error'] = 'Host, usuario y correo remitente son obligatorios.';
            header('Location: index.php?modulo=smtp');
            exit;
        }

        $this->modelo->guardar($_POST, $this->usuarioId());
        $_SESSION['flash_success'] = 'Configuración SMTP guardada correctamente.';
        header('Location: index.php?modulo=smtp');
        exit;
    }

    public function probar(): void {
        $this->exigirPost('index.php?modulo=smtp');

        $destino = trim($_POST['correo_prueba'] ?? '');
        if ($destino === '' || !filter_var($destino, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = 'Indica un correo de destino válido para la prueba.';
            header('Location: index.php?modulo=smtp');
            exit;
        }

        $correo    = new CorreoServicio($this->db);
        $resultado = $correo->enviar(
            $destino,
            'Correo de prueba — ZFPE',
            '<p>Este es un correo de prueba enviado desde la configuración SMTP del panel de ZFPE.</p>'
        );

        if ($resultado['exito']) {
            $_SESSION['flash_success'] = 'Correo de prueba enviado correctamente a ' . htmlspecialchars($destino) . '.';
        } else {
            $_SESSION['flash_error'] = 'No se pudo enviar el correo de prueba: ' . $resultado['mensaje'];
        }
        header('Location: index.php?modulo=smtp');
        exit;
    }
}
