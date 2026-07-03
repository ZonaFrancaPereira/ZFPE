<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/SmtpConfigModelo.php';
require_once __DIR__ . '/../config/cifrado.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

/** Envía correos usando la configuración SMTP guardada en la base de datos (módulo smtp). */
class CorreoServicio {

    private SmtpConfigModelo $configModelo;

    public function __construct(PDO $db) {
        $this->configModelo = new SmtpConfigModelo($db);
    }

    public function enviar(string $destinatario, string $asunto, string $cuerpoHtml, ?string $nombreDestinatario = null): array {
        $config = $this->configModelo->obtener();
        if (!$config || !$config['activo']) {
            return ['exito' => false, 'mensaje' => 'No hay una configuración SMTP activa.'];
        }

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = $config['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $config['usuario'];
            $mail->Password   = descifrar($config['clave_cifrada']);
            $mail->Port       = (int) $config['puerto'];
            $mail->SMTPSecure = $config['cifrado'] === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom($config['correo_remitente'], $config['nombre_remitente'] ?: $config['correo_remitente']);
            $mail->addAddress($destinatario, $nombreDestinatario ?? '');

            $logo = __DIR__ . '/../vista/img/logo2-recortado.png';
            if (is_file($logo)) {
                $mail->addEmbeddedImage($logo, 'logo_zfpe', 'logo2-recortado.png');
            }

            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body    = $cuerpoHtml;

            $mail->send();
            return ['exito' => true, 'mensaje' => 'Correo enviado correctamente.'];
        } catch (PHPMailerException|\Exception $e) {
            return ['exito' => false, 'mensaje' => $mail->ErrorInfo ?: $e->getMessage()];
        }
    }
}
