-- Lo que la compañera ya agregó en su base de datos y a la nuestra le falta:
-- configuración SMTP para envío de correos, y la marca para forzar el
-- cambio de contraseña en el próximo login. No incluye su fila real de
-- smtp_config (tiene credenciales de correo) — esa la debe configurar
-- quien vaya a activar el envío de correos, desde el módulo correspondiente.

CREATE TABLE IF NOT EXISTS smtp_config (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    host              VARCHAR(255) NOT NULL,
    puerto            SMALLINT UNSIGNED NOT NULL DEFAULT 587,
    usuario           VARCHAR(255) NOT NULL,
    clave_cifrada     VARCHAR(500) NOT NULL,
    cifrado           ENUM('tls','ssl') NOT NULL DEFAULT 'tls',
    correo_remitente  VARCHAR(255) NOT NULL,
    nombre_remitente  VARCHAR(255) NOT NULL DEFAULT '',
    activo            TINYINT(1) NOT NULL DEFAULT 1,
    actualizado_por   INT UNSIGNED DEFAULT NULL,
    actualizado_en    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY actualizado_por (actualizado_por),
    CONSTRAINT smtp_config_ibfk_1 FOREIGN KEY (actualizado_por) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE usuarios
    ADD COLUMN IF NOT EXISTS debe_cambiar_contrasena TINYINT(1) NOT NULL DEFAULT 0 AFTER rol;
