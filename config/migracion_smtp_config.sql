-- Configuración SMTP para el envío de correos de notificación.
-- Tabla de una sola fila vigente: el módulo (solo administrador) siempre lee/actualiza
-- la primera fila existente; si no hay ninguna, la primera vez que se guarde se inserta.
CREATE TABLE IF NOT EXISTS smtp_config (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    host             VARCHAR(255) NOT NULL,
    puerto           SMALLINT UNSIGNED NOT NULL DEFAULT 587,
    usuario          VARCHAR(255) NOT NULL,
    clave_cifrada    VARCHAR(500) NOT NULL,
    cifrado          ENUM('tls','ssl') NOT NULL DEFAULT 'tls',
    correo_remitente VARCHAR(255) NOT NULL,
    nombre_remitente VARCHAR(255) NOT NULL DEFAULT '',
    activo           TINYINT(1) NOT NULL DEFAULT 1,
    actualizado_por  INT UNSIGNED NULL,
    actualizado_en   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (actualizado_por) REFERENCES usuarios(id) ON DELETE SET NULL
);
