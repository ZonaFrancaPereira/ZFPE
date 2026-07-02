-- Estado de lectura por usuario de las notificaciones de la campana.
-- Las notificaciones en sí no se guardan (se calculan al vuelo); esta tabla
-- solo recuerda qué "clave" (identificador estable del hecho: vencimiento,
-- historial, documento o compromiso) ya vio cada usuario.
CREATE TABLE IF NOT EXISTS notificaciones_leidas (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    clave      VARCHAR(64)  NOT NULL,
    leido_en   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_usuario_clave (usuario_id, clave),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);
