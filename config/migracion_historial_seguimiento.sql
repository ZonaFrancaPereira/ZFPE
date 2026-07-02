-- Descripción opcional para saber a qué corresponde cada documento subido
-- (la columna `descripcion` ya existe en `documentos`; no se requiere ALTER)

-- Historial de cambios de estado/observaciones por requisito y empresa
CREATE TABLE IF NOT EXISTS empresa_requisito_historial (
    id                 INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    empresa_id         INT UNSIGNED NOT NULL,
    requisito_id       INT UNSIGNED NOT NULL,
    estado_anterior    VARCHAR(20)  NULL,
    estado_nuevo       VARCHAR(20)  NOT NULL,
    observaciones      TEXT         NULL,
    fecha_cumplimiento DATETIME     NULL,
    documento_id       INT UNSIGNED NULL,
    registrado_por     INT UNSIGNED NULL,
    created_at         TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (empresa_id)     REFERENCES empresas(id)    ON DELETE CASCADE,
    FOREIGN KEY (requisito_id)   REFERENCES requisitos(id)  ON DELETE CASCADE,
    FOREIGN KEY (documento_id)   REFERENCES documentos(id)  ON DELETE SET NULL,
    FOREIGN KEY (registrado_por) REFERENCES usuarios(id)    ON DELETE SET NULL,
    INDEX idx_erh_empresa_requisito (empresa_id, requisito_id)
);
