-- Catálogo global de indicadores
CREATE TABLE IF NOT EXISTS indicadores (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    nombre        VARCHAR(200)  NOT NULL,
    descripcion   TEXT          NULL,
    unidad        VARCHAR(100)  NULL,
    meta          DECIMAL(15,2) NULL,
    periodicidad  ENUM('mensual','trimestral','semestral','anual') DEFAULT 'anual',
    activo        TINYINT(1)    DEFAULT 1,
    created_at    TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
);

-- Asignación y valores por empresa
CREATE TABLE IF NOT EXISTS empresa_indicador (
    id              INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    empresa_id      INT UNSIGNED  NOT NULL,
    indicador_id    INT           NOT NULL,
    valor_actual    DECIMAL(15,2) NULL,
    fecha_reporte   DATE          NULL,
    observaciones   TEXT          NULL,
    registrado_por  INT           NULL,
    updated_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_ei (empresa_id, indicador_id),
    FOREIGN KEY (empresa_id)   REFERENCES empresas(id)    ON DELETE CASCADE,
    FOREIGN KEY (indicador_id) REFERENCES indicadores(id) ON DELETE CASCADE
);
