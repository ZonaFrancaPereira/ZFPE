<?php

class IndicadoresModelo {

    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // ── CATÁLOGO ──────────────────────────────────────────────

    public function obtenerTodos(): array {
        return $this->db->query("
            SELECT i.*,
                   (SELECT COUNT(*) FROM empresa_indicador ei WHERE ei.indicador_id = i.id) AS total_asignados
            FROM indicadores i
            ORDER BY i.nombre ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerActivos(): array {
        return $this->db->query("
            SELECT * FROM indicadores WHERE activo = 1 ORDER BY nombre ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM indicadores WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear(array $datos): int {
        $stmt = $this->db->prepare("
            INSERT INTO indicadores (nombre, descripcion, unidad, meta, periodicidad, tipo_grafico, comparativo_anual, activo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $datos['nombre'],
            $datos['descripcion']    ?: null,
            $datos['unidad']         ?: null,
            $datos['meta']           !== '' ? $datos['meta'] : null,
            $datos['periodicidad']   ?? 'anual',
            $datos['tipo_grafico']   ?? 'linea',
            isset($datos['comparativo_anual']) ? 1 : 0,
            isset($datos['activo'])  ? 1 : 0,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): void {
        $this->db->prepare("
            UPDATE indicadores
            SET nombre=?, descripcion=?, unidad=?, meta=?, periodicidad=?, tipo_grafico=?, comparativo_anual=?, activo=?
            WHERE id=?
        ")->execute([
            $datos['nombre'],
            $datos['descripcion']    ?: null,
            $datos['unidad']         ?: null,
            $datos['meta']           !== '' ? $datos['meta'] : null,
            $datos['periodicidad']   ?? 'anual',
            $datos['tipo_grafico']   ?? 'linea',
            isset($datos['comparativo_anual']) ? 1 : 0,
            isset($datos['activo'])  ? 1 : 0,
            $id,
        ]);
    }

    public function eliminar(int $id): void {
        $this->db->prepare("DELETE FROM indicadores WHERE id = ?")->execute([$id]);
    }

    // ── ASIGNACIONES POR EMPRESA ──────────────────────────────

    public function obtenerPorEmpresa(int $empresa_id): array {
        $stmt = $this->db->prepare("
            SELECT i.*, ei.valor_actual, ei.fecha_reporte, ei.observaciones, ei.updated_at AS fecha_actualizacion,
                   u.nombre AS registrado_por_nombre
            FROM empresa_indicador ei
            JOIN indicadores i ON i.id = ei.indicador_id
            LEFT JOIN usuarios u ON u.id = ei.registrado_por
            WHERE ei.empresa_id = ?
            ORDER BY i.nombre ASC
        ");
        $stmt->execute([$empresa_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerNoAsignados(int $empresa_id): array {
        $stmt = $this->db->prepare("
            SELECT * FROM indicadores
            WHERE activo = 1
              AND id NOT IN (
                  SELECT indicador_id FROM empresa_indicador WHERE empresa_id = ?
              )
            ORDER BY nombre ASC
        ");
        $stmt->execute([$empresa_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function asignar(int $empresa_id, int $indicador_id): void {
        $this->db->prepare("
            INSERT IGNORE INTO empresa_indicador (empresa_id, indicador_id)
            VALUES (?, ?)
        ")->execute([$empresa_id, $indicador_id]);
    }

    public function actualizarValor(int $empresa_id, int $indicador_id, array $datos, ?int $usuario_id): void {
        $periodo = trim($datos['periodo'] ?? '');
        if ($periodo === '') return;
        $this->db->prepare("
            INSERT INTO empresa_indicador_valor (empresa_id, indicador_id, periodo, valor, observaciones, registrado_por)
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                valor          = VALUES(valor),
                observaciones  = VALUES(observaciones),
                registrado_por = VALUES(registrado_por)
        ")->execute([
            $empresa_id,
            $indicador_id,
            $periodo,
            $datos['valor'] !== '' ? $datos['valor'] : null,
            $datos['observaciones'] ?: null,
            $usuario_id,
        ]);
    }

    public function eliminarValor(int $empresa_id, int $indicador_id, string $periodo): void {
        $this->db->prepare("
            DELETE FROM empresa_indicador_valor
            WHERE empresa_id = ? AND indicador_id = ? AND periodo = ?
        ")->execute([$empresa_id, $indicador_id, $periodo]);
    }

    public function obtenerValoresPorIndicador(int $empresa_id, int $indicador_id): array {
        $stmt = $this->db->prepare("
            SELECT v.periodo, v.valor, v.observaciones, u.nombre AS registrado_por_nombre, v.created_at
            FROM empresa_indicador_valor v
            LEFT JOIN usuarios u ON u.id = v.registrado_por
            WHERE v.empresa_id = ? AND v.indicador_id = ?
            ORDER BY v.periodo ASC
        ");
        $stmt->execute([$empresa_id, $indicador_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerResumenPorEmpresa(int $empresa_id): array {
        $stmt = $this->db->prepare("
            SELECT i.*,
                   GROUP_CONCAT(v.periodo ORDER BY v.periodo ASC SEPARATOR ',')  AS periodos_json,
                   GROUP_CONCAT(v.valor   ORDER BY v.periodo ASC SEPARATOR ',')  AS valores_json,
                   (SELECT v2.valor FROM empresa_indicador_valor v2
                    WHERE v2.empresa_id = ei.empresa_id AND v2.indicador_id = i.id
                    ORDER BY v2.periodo DESC LIMIT 1) AS ultimo_valor,
                   (SELECT v2.periodo FROM empresa_indicador_valor v2
                    WHERE v2.empresa_id = ei.empresa_id AND v2.indicador_id = i.id
                    ORDER BY v2.periodo DESC LIMIT 1) AS ultimo_periodo
            FROM empresa_indicador ei
            JOIN indicadores i ON i.id = ei.indicador_id
            LEFT JOIN empresa_indicador_valor v ON v.empresa_id = ei.empresa_id AND v.indicador_id = i.id
            WHERE ei.empresa_id = ? AND i.activo = 1
            GROUP BY i.id, ei.empresa_id
            ORDER BY i.nombre ASC
        ");
        $stmt->execute([$empresa_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function desasignar(int $empresa_id, int $indicador_id): void {
        $this->db->prepare("
            DELETE FROM empresa_indicador WHERE empresa_id = ? AND indicador_id = ?
        ")->execute([$empresa_id, $indicador_id]);
    }
}
