<?php

/**
 * Alertas ejecutivas por empresa (tabla empresa_alertas, ya existente en la
 * base de datos). Se crean a mano desde Operaciones — regla_alerta_id queda
 * en NULL porque el motor de reglas automáticas (reglas_alerta/reglas_decision)
 * todavía no está conectado a esto.
 *
 * Una alerta puede tener destinatarios específicos (empresa_alertas_destinatarios).
 * Si no tiene ninguno, se considera "difusión por defecto": la ven todo
 * Operaciones/Admin, y del lado de la empresa solo quien tenga es_gerente=1.
 */
class AlertasModelo {

    private PDO $db;

    private const TIPOS       = ['vencimiento', 'bloqueo', 'pendiente', 'documento', 'decision', 'reunion'];
    private const PRIORIDADES = ['alta', 'media', 'baja'];

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function crear(
        int $empresaId,
        string $tipo,
        string $prioridad,
        string $mensaje,
        ?string $enlaceReunion,
        array $destinatarioIds = []
    ): int {
        $tipo      = in_array($tipo, self::TIPOS, true) ? $tipo : 'pendiente';
        $prioridad = in_array($prioridad, self::PRIORIDADES, true) ? $prioridad : 'media';
        $enlace    = $enlaceReunion !== null ? trim($enlaceReunion) : null;
        $enlace    = $enlace === '' ? null : $enlace;

        $stmt = $this->db->prepare("
            INSERT INTO empresa_alertas (empresa_id, tipo, mensaje, enlace_reunion, prioridad)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$empresaId, $tipo, trim($mensaje), $enlace, $prioridad]);
        $alertaId = (int) $this->db->lastInsertId();

        $destinatarioIds = array_values(array_unique(array_filter(array_map('intval', $destinatarioIds))));
        if (!empty($destinatarioIds)) {
            $stmtDest = $this->db->prepare("
                INSERT IGNORE INTO empresa_alertas_destinatarios (alerta_id, usuario_id) VALUES (?, ?)
            ");
            foreach ($destinatarioIds as $uid) {
                $stmtDest->execute([$alertaId, $uid]);
            }
        }

        return $alertaId;
    }

    public function listarPorEmpresa(int $empresaId, bool $soloActivas = true): array {
        $sql = "
            SELECT a.*,
                   GROUP_CONCAT(DISTINCT d.usuario_id)             AS destinatario_ids,
                   GROUP_CONCAT(DISTINCT u.nombre SEPARATOR ', ')   AS destinatario_nombres
            FROM empresa_alertas a
            LEFT JOIN empresa_alertas_destinatarios d ON d.alerta_id = a.id
            LEFT JOIN usuarios u ON u.id = d.usuario_id
            WHERE a.empresa_id = ?" . ($soloActivas ? ' AND a.resuelta = 0' : '') . "
            GROUP BY a.id
            ORDER BY FIELD(a.prioridad, 'alta', 'media', 'baja'), a.creado_en DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$empresaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarGlobal(bool $soloActivas = true): array {
        $sql = "
            SELECT a.*, e.razon_social AS empresa_nombre,
                   GROUP_CONCAT(DISTINCT d.usuario_id) AS destinatario_ids
            FROM empresa_alertas a
            JOIN empresas e ON e.id = a.empresa_id
            LEFT JOIN empresa_alertas_destinatarios d ON d.alerta_id = a.id
            WHERE 1=1" . ($soloActivas ? ' AND a.resuelta = 0' : '') . "
            GROUP BY a.id
            ORDER BY FIELD(a.prioridad, 'alta', 'media', 'baja'), a.creado_en DESC
        ";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtener(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM empresa_alertas WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function destinatariosDe(int $alertaId): array {
        $stmt = $this->db->prepare("SELECT usuario_id FROM empresa_alertas_destinatarios WHERE alerta_id = ?");
        $stmt->execute([$alertaId]);
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    public function resolver(int $id, ?string $comentario = null): void {
        $comentario = $comentario !== null ? trim($comentario) : null;
        $comentario = $comentario === '' ? null : $comentario;

        $this->db->prepare("
            UPDATE empresa_alertas
            SET resuelta = 1, fecha_resolucion = NOW(), comentario_resolucion = ?
            WHERE id = ?
        ")->execute([$comentario, $id]);
    }

    /** Convierte el "destinatario_ids" (string "3,7,9" de GROUP_CONCAT) en array de int. */
    public static function idsDesdeConcat(?string $concat): array {
        if (!$concat) return [];
        return array_map('intval', explode(',', $concat));
    }
}
