-- Tipo de gráfica en el catálogo
ALTER TABLE indicadores
    ADD COLUMN IF NOT EXISTS tipo_grafico ENUM('linea','barra','area') DEFAULT 'linea' AFTER periodicidad;

-- Historial de valores por período (reemplaza el campo único valor_actual)
CREATE TABLE IF NOT EXISTS empresa_indicador_valor (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id     INT UNSIGNED NOT NULL,
    indicador_id   INT          NOT NULL,
    periodo        VARCHAR(10)  NOT NULL,   -- '2026-01' / '2026-T1' / '2026-S1' / '2026'
    valor          DECIMAL(15,2) NULL,
    observaciones  TEXT         NULL,
    registrado_por INT          NULL,
    created_at     TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_eiv (empresa_id, indicador_id, periodo),
    FOREIGN KEY (empresa_id)   REFERENCES empresas(id)    ON DELETE CASCADE,
    FOREIGN KEY (indicador_id) REFERENCES indicadores(id) ON DELETE CASCADE
);

-- Migrar valores existentes a la nueva tabla
INSERT IGNORE INTO empresa_indicador_valor (empresa_id, indicador_id, periodo, valor, observaciones, registrado_por)
SELECT empresa_id, indicador_id,
       DATE_FORMAT(COALESCE(fecha_reporte, CURDATE()), '%Y-%m'),
       valor_actual, observaciones, registrado_por
FROM empresa_indicador
WHERE valor_actual IS NOT NULL;
