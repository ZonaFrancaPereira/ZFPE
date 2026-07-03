-- Alertas ejecutivas: quién es "gerente" de una empresa, tipo "reunión" con
-- enlace de conexión, y destinatarios específicos por alerta (en vez de un
-- solo destinatario fijo). Las tablas empresa_alertas / reglas_alerta /
-- reglas_decision ya existían en la base de datos antes de esta migración.

ALTER TABLE usuarios
    ADD COLUMN IF NOT EXISTS es_gerente TINYINT(1) NOT NULL DEFAULT 0 AFTER empresa_id;

ALTER TABLE empresa_alertas
    MODIFY COLUMN tipo ENUM('vencimiento','bloqueo','pendiente','documento','decision','reunion') NOT NULL;

ALTER TABLE empresa_alertas
    ADD COLUMN IF NOT EXISTS enlace_reunion VARCHAR(500) NULL AFTER mensaje;

ALTER TABLE empresa_alertas
    ADD COLUMN IF NOT EXISTS comentario_resolucion TEXT NULL AFTER fecha_resolucion;

-- Si una alerta no tiene filas aquí, se asume "todos los gerentes de la
-- empresa" (o todo Operaciones/Admin, según el contexto) como destinatario
-- por defecto. Si tiene filas, solo esos usuarios ven la notificación.
CREATE TABLE IF NOT EXISTS empresa_alertas_destinatarios (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    alerta_id  INT UNSIGNED NOT NULL,
    usuario_id INT UNSIGNED NOT NULL,
    UNIQUE KEY uk_alerta_usuario (alerta_id, usuario_id),
    FOREIGN KEY (alerta_id)  REFERENCES empresa_alertas(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);
