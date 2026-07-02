<?php

class EmpresasModelo {

    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function obtenerTodas(): array {
        return $this->db->query("
            SELECT e.*,
                   (SELECT COUNT(*) FROM usuarios u WHERE u.empresa_id = e.id) AS total_usuarios,
                   (SELECT etapas.nombre FROM empresa_etapa_progreso ep
                    JOIN etapas ON etapas.id = ep.etapa_id
                    WHERE ep.empresa_id = e.id AND ep.estado IN ('pendiente','en_progreso')
                    ORDER BY etapas.orden ASC LIMIT 1) AS etapa_actual,
                   (SELECT ROUND(AVG(ep.porcentaje_avance),1) FROM empresa_etapa_progreso ep
                    WHERE ep.empresa_id = e.id) AS avance_general
            FROM empresas e
            ORDER BY e.creado_en DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM empresas WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerDetalle(int $id): array|false {
        $empresa = $this->obtenerPorId($id);
        if (!$empresa) return false;

        // Etapas con su progreso
        $empresa['etapas'] = $this->db->prepare("
            SELECT et.*,
                   COALESCE(ep.porcentaje_avance, 0) AS avance,
                   COALESCE(ep.estado, 'pendiente')   AS estado_progreso
            FROM etapas et
            LEFT JOIN empresa_etapa_progreso ep ON ep.etapa_id = et.id AND ep.empresa_id = ?
            WHERE et.activo = 1
            ORDER BY et.orden ASC
        ")->execute([$id]) ? $this->db->prepare("
            SELECT et.*,
                   COALESCE(ep.porcentaje_avance, 0) AS avance,
                   COALESCE(ep.estado, 'pendiente')   AS estado_progreso
            FROM etapas et
            LEFT JOIN empresa_etapa_progreso ep ON ep.etapa_id = et.id AND ep.empresa_id = ?
            WHERE et.activo = 1
            ORDER BY et.orden ASC
        ") : [];

        $stmt = $this->db->prepare("
            SELECT et.*,
                   f.id     AS fase_id,
                   f.nombre AS fase_nombre,
                   f.orden  AS fase_orden,
                   COALESCE(ep.porcentaje_avance, 0) AS avance,
                   COALESCE(ep.estado, 'pendiente')   AS estado_progreso
            FROM etapas et
            LEFT JOIN fases f ON f.id = et.fase_id
            LEFT JOIN empresa_etapa_progreso ep ON ep.etapa_id = et.id AND ep.empresa_id = ?
            WHERE et.activo = 1
            ORDER BY COALESCE(f.orden, 999) ASC, et.orden ASC
        ");
        $stmt->execute([$id]);
        $empresa['etapas'] = $stmt->fetchAll();

        // Requisitos con su estado
        $stmt = $this->db->prepare("
            SELECT r.*, en.nombre AS entidad_nombre,
                   COALESCE(ere.estado, 'pendiente')  AS estado_req,
                   COALESCE(ere.aprobado, 0)          AS aprobado,
                   (SELECT COUNT(*) FROM requisito_items ri WHERE ri.requisito_id = r.id AND ri.activo = 1)  AS total_items,
                   (SELECT COUNT(*) FROM empresa_requisito_item_estado ei
                    JOIN requisito_items ri ON ri.id = ei.requisito_item_id
                    WHERE ri.requisito_id = r.id AND ei.empresa_id = ? AND ei.cumplido = 1) AS items_cumplidos
            FROM requisitos r
            LEFT JOIN entidades en ON en.id = r.entidad_id
            LEFT JOIN empresa_requisito_estado ere ON ere.requisito_id = r.id AND ere.empresa_id = ?
            WHERE r.activo = 1
            ORDER BY r.etapa_id ASC, r.nombre ASC
        ");
        $stmt->execute([$id, $id]);
        $empresa['requisitos'] = $stmt->fetchAll();

        // Usuarios asignados
        $stmt = $this->db->prepare("
            SELECT id, nombre, correo, rol FROM usuarios WHERE empresa_id = ? ORDER BY nombre
        ");
        $stmt->execute([$id]);
        $empresa['usuarios'] = $stmt->fetchAll();

        // Avance general
        $stmt = $this->db->prepare("
            SELECT COALESCE(ROUND(AVG(porcentaje_avance), 1), 0) FROM empresa_etapa_progreso WHERE empresa_id = ?
        ");
        $stmt->execute([$id]);
        $empresa['avance_general'] = (float) $stmt->fetchColumn();

        return $empresa;
    }

    public function crear(array $datos): int {
        $stmt = $this->db->prepare("
            INSERT INTO empresas (nit, razon_social, representante, telefono, correo, contrasena, creado_en)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $datos['nit'],
            $datos['razon_social'],
            $datos['representante'] ?? null,
            $datos['telefono'] ?? null,
            $datos['correo'] ?? null,
            password_hash($datos['contrasena'], PASSWORD_DEFAULT),
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function aplicarMatriz(int $empresa_id, ?int $faseInicialId = null): void {
        // Si se indica una fase inicial, las etapas de fases anteriores a esa
        // (según su "orden") se dan por omitidas: quedan completas para la
        // etapa y sus requisitos se marcan como "no_aplica".
        $ordenInicial = null;
        if ($faseInicialId) {
            $stmt = $this->db->prepare("SELECT orden FROM fases WHERE id = ?");
            $stmt->execute([$faseInicialId]);
            $orden = $stmt->fetchColumn();
            $ordenInicial = $orden !== false ? (int) $orden : null;
        }

        // Etapas
        $etapas = $this->db->query("
            SELECT e.id, COALESCE(f.orden, 999) AS fase_orden
            FROM etapas e
            LEFT JOIN fases f ON f.id = e.fase_id
            WHERE e.activo = 1
        ")->fetchAll();

        $stmtEtapa = $this->db->prepare("
            INSERT IGNORE INTO empresa_etapa_progreso (empresa_id, etapa_id, porcentaje_avance, estado)
            VALUES (?, ?, ?, ?)
        ");
        $etapasOmitidas = [];
        foreach ($etapas as $e) {
            $omitida = $ordenInicial !== null && (int) $e['fase_orden'] < $ordenInicial;
            if ($omitida) {
                $etapasOmitidas[] = (int) $e['id'];
            }
            $stmtEtapa->execute([
                $empresa_id, $e['id'],
                $omitida ? 100 : 0,
                $omitida ? 'completa' : 'pendiente',
            ]);
        }

        // Requisitos
        $requisitos = $this->db->query("SELECT id, etapa_id FROM requisitos WHERE activo = 1")->fetchAll();
        $stmtReq = $this->db->prepare("
            INSERT IGNORE INTO empresa_requisito_estado (empresa_id, requisito_id, estado)
            VALUES (?, ?, ?)
        ");
        foreach ($requisitos as $r) {
            $estado = in_array((int) $r['etapa_id'], $etapasOmitidas, true) ? 'no_aplica' : 'pendiente';
            $stmtReq->execute([$empresa_id, $r['id'], $estado]);
        }

        // Ítems
        $items = $this->db->query("SELECT id FROM requisito_items WHERE activo = 1")->fetchAll();
        $stmtItem = $this->db->prepare("
            INSERT IGNORE INTO empresa_requisito_item_estado (empresa_id, requisito_item_id, cumplido)
            VALUES (?, ?, 0)
        ");
        foreach ($items as $item) {
            $stmtItem->execute([$empresa_id, $item['id']]);
        }
    }

    public function actualizar(int $id, array $datos): bool {
        if (!empty($datos['contrasena'])) {
            $stmt = $this->db->prepare("
                UPDATE empresas SET nit=?, razon_social=?, representante=?, telefono=?, correo=?, contrasena=? WHERE id=?
            ");
            return $stmt->execute([
                $datos['nit'], $datos['razon_social'], $datos['representante'],
                $datos['telefono'], $datos['correo'],
                password_hash($datos['contrasena'], PASSWORD_DEFAULT), $id,
            ]);
        }

        $stmt = $this->db->prepare("
            UPDATE empresas SET nit=?, razon_social=?, representante=?, telefono=?, correo=? WHERE id=?
        ");
        return $stmt->execute([
            $datos['nit'], $datos['razon_social'], $datos['representante'],
            $datos['telefono'], $datos['correo'], $id,
        ]);
    }

    public function eliminar(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM empresas WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function etapasSinAsignar(int $empresa_id): array {
        $stmt = $this->db->prepare("
            SELECT e.*,
                   f.id     AS fase_id,
                   f.nombre AS fase_nombre,
                   f.orden  AS fase_orden
            FROM etapas e
            LEFT JOIN fases f ON f.id = e.fase_id
            WHERE e.activo = 1
              AND e.id NOT IN (
                SELECT ep.etapa_id FROM empresa_etapa_progreso ep WHERE ep.empresa_id = ?
              )
            ORDER BY COALESCE(f.orden, 999) ASC, e.orden ASC
        ");
        $stmt->execute([$empresa_id]);
        return $stmt->fetchAll();
    }

    public function agregarEtapa(int $empresa_id, int $etapa_id): void {
        // Agregar la etapa
        $stmt = $this->db->prepare("
            INSERT IGNORE INTO empresa_etapa_progreso (empresa_id, etapa_id, porcentaje_avance, estado)
            VALUES (?, ?, 0, 'pendiente')
        ");
        $stmt->execute([$empresa_id, $etapa_id]);

        // Agregar los requisitos activos de esa etapa
        $requisitos = $this->db->prepare("SELECT id FROM requisitos WHERE etapa_id = ? AND activo = 1");
        $requisitos->execute([$etapa_id]);
        $requisitos = $requisitos->fetchAll();

        $stmtReq = $this->db->prepare("
            INSERT IGNORE INTO empresa_requisito_estado (empresa_id, requisito_id, estado)
            VALUES (?, ?, 'pendiente')
        ");

        $stmtItem = $this->db->prepare("
            INSERT IGNORE INTO empresa_requisito_item_estado (empresa_id, requisito_item_id, cumplido)
            VALUES (?, ?, 0)
        ");

        foreach ($requisitos as $r) {
            $stmtReq->execute([$empresa_id, $r['id']]);

            // Agregar los ítems activos de ese requisito
            $items = $this->db->prepare("SELECT id FROM requisito_items WHERE requisito_id = ? AND activo = 1");
            $items->execute([$r['id']]);
            foreach ($items->fetchAll() as $item) {
                $stmtItem->execute([$empresa_id, $item['id']]);
            }
        }
    }

    public function cronograma(int $empresa_id): array {
        $stmt = $this->db->prepare("
            SELECT et.*,
                   f.id     AS fase_id,
                   f.nombre AS fase_nombre,
                   f.orden  AS fase_orden,
                   COALESCE(ep.porcentaje_avance, 0)  AS avance,
                   COALESCE(ep.estado, 'pendiente')    AS estado_progreso,
                   ep.fecha_inicio,
                   ep.fecha_completado,
                   COUNT(DISTINCT r.id)                           AS total_requisitos,
                   SUM(ere.estado IN ('cumplido','no_aplica'))    AS requisitos_ok
            FROM etapas et
            LEFT JOIN fases f ON f.id = et.fase_id
            JOIN empresa_etapa_progreso ep ON ep.etapa_id = et.id AND ep.empresa_id = :emp1
            LEFT JOIN requisitos r   ON r.etapa_id = et.id AND r.activo = 1
            LEFT JOIN empresa_requisito_estado ere
                   ON ere.requisito_id = r.id AND ere.empresa_id = :emp2
            WHERE et.activo = 1
            GROUP BY et.id, f.id, f.nombre, f.orden, ep.porcentaje_avance, ep.estado, ep.fecha_inicio, ep.fecha_completado
            ORDER BY COALESCE(f.orden, 999) ASC, et.orden ASC
        ");
        $stmt->execute([':emp1' => $empresa_id, ':emp2' => $empresa_id]);
        $etapas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmtReq = $this->db->prepare("
            SELECT r.*, en.nombre AS entidad_nombre,
                   COALESCE(ere.estado, 'pendiente')   AS estado_req,
                   ere.fecha_vencimiento,
                   ere.observaciones
            FROM requisitos r
            LEFT JOIN entidades en ON en.id = r.entidad_id
            LEFT JOIN empresa_requisito_estado ere
                   ON ere.requisito_id = r.id AND ere.empresa_id = ?
            WHERE r.etapa_id = ? AND r.activo = 1
            ORDER BY r.nombre ASC
        ");

        foreach ($etapas as &$etapa) {
            $stmtReq->execute([$empresa_id, $etapa['id']]);
            $etapa['requisitos'] = $stmtReq->fetchAll(PDO::FETCH_ASSOC);
        }
        unset($etapa);

        return $etapas;
    }

    public function usuariosDisponibles(int $empresa_id): array {
        $stmt = $this->db->prepare("
            SELECT id, nombre, correo FROM usuarios
            WHERE rol = 'usuario' AND (empresa_id IS NULL OR empresa_id = ?)
            ORDER BY nombre
        ");
        $stmt->execute([$empresa_id]);
        return $stmt->fetchAll();
    }
}
