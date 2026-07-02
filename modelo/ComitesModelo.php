<?php

class ComitesModelo {

    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function listar(): array {
        return $this->db->query("
            SELECT c.*,
                   e.razon_social          AS empresa_nombre,
                   COUNT(DISTINCT cc.id)   AS total_compromisos,
                   SUM(cc.estado = 'cumplido') AS compromisos_cumplidos
            FROM comites c
            LEFT JOIN empresas e             ON e.id = c.empresa_id
            LEFT JOIN comite_compromisos cc  ON cc.comite_id = c.id
            GROUP BY c.id
            ORDER BY c.fecha DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarPorEmpresa(int $empresa_id): array {
        $stmt = $this->db->prepare("
            SELECT c.*,
                   e.razon_social          AS empresa_nombre,
                   COUNT(DISTINCT cc.id)   AS total_compromisos,
                   SUM(cc.estado = 'cumplido') AS compromisos_cumplidos
            FROM comites c
            LEFT JOIN empresas e             ON e.id = c.empresa_id
            LEFT JOIN comite_compromisos cc  ON cc.comite_id = c.id
            WHERE c.empresa_id = ?
            GROUP BY c.id
            ORDER BY c.fecha DESC
        ");
        $stmt->execute([$empresa_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtener(int $id): array|false {
        $stmt = $this->db->prepare("
            SELECT c.*, e.razon_social AS empresa_nombre
            FROM comites c
            LEFT JOIN empresas e ON e.id = c.empresa_id
            WHERE c.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function responsablesDisponibles(?int $empresa_id): array {
        $stmt = $this->db->prepare("
            SELECT id, nombre, correo, rol,
                   CASE WHEN rol = 'operaciones' THEN 'Operaciones' ELSE 'Empresa' END AS grupo
            FROM usuarios
            WHERE rol = 'operaciones' OR empresa_id = :empresa_id
            ORDER BY grupo DESC, nombre ASC
        ");
        $stmt->execute([':empresa_id' => $empresa_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function compromisos(int $comite_id): array {
        $stmt = $this->db->prepare("
            SELECT *
            FROM comite_compromisos
            WHERE comite_id = ?
            ORDER BY fecha_limite IS NULL ASC, fecha_limite ASC, id ASC
        ");
        $stmt->execute([$comite_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO comites (empresa_id, tipo, titulo, descripcion, fecha, lugar, estado, creado_por)
            VALUES (:empresa_id, :tipo, :titulo, :descripcion, :fecha, :lugar, :estado, :creado_por)
        ");
        $stmt->execute([
            ':empresa_id'  => ($data['empresa_id'] ?? '') ?: null,
            ':tipo'        => $data['tipo']        ?? 'seguimiento',
            ':titulo'      => $data['titulo'],
            ':descripcion' => $data['descripcion'] ?: null,
            ':fecha'       => $data['fecha'],
            ':lugar'       => $data['lugar']       ?: null,
            ':estado'      => $data['estado']      ?? 'programado',
            ':creado_por'  => $_SESSION['usuario_id'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function editar(int $id, array $data): void {
        $stmt = $this->db->prepare("
            UPDATE comites
            SET empresa_id=:empresa_id, tipo=:tipo, titulo=:titulo,
                descripcion=:descripcion, fecha=:fecha, lugar=:lugar, estado=:estado
            WHERE id=:id
        ");
        $stmt->execute([
            ':empresa_id'  => ($data['empresa_id'] ?? '') ?: null,
            ':tipo'        => $data['tipo']        ?? 'seguimiento',
            ':titulo'      => $data['titulo'],
            ':descripcion' => $data['descripcion'] ?: null,
            ':fecha'       => $data['fecha'],
            ':lugar'       => $data['lugar']       ?: null,
            ':estado'      => $data['estado']      ?? 'programado',
            ':id'          => $id,
        ]);
    }

    public function eliminar(int $id): void {
        $this->db->prepare("DELETE FROM comites WHERE id = ?")->execute([$id]);
    }

    public function guardarCompromiso(array $data): void {
        $stmt = $this->db->prepare("
            INSERT INTO comite_compromisos
                (comite_id, descripcion, responsable, fecha_limite, estado, observaciones)
            VALUES
                (:comite_id, :descripcion, :responsable, :fecha_limite, :estado, :observaciones)
        ");
        $stmt->execute([
            ':comite_id'    => $data['comite_id'],
            ':descripcion'  => $data['descripcion'],
            ':responsable'  => $data['responsable']  ?: null,
            ':fecha_limite' => $data['fecha_limite']  ?: null,
            ':estado'       => $data['estado']        ?? 'pendiente',
            ':observaciones'=> $data['observaciones'] ?: null,
        ]);
    }

    /**
     * Actualiza el estado/observaciones "actuales" del compromiso (lo que
     * se muestra en los badges y listados) y, además, deja un registro
     * permanente en compromiso_actualizaciones para no perder el historial.
     * Devuelve el id de esa entrada de historial, para que el llamador
     * pueda ligarle un documento subido en la misma operación.
     */
    public function actualizarCompromiso(int $id, array $data, ?int $usuario_id = null): int {
        $estado        = $data['estado'] ?? 'pendiente';
        $observaciones = $data['observaciones'] ?? null;

        $stmt = $this->db->prepare("
            UPDATE comite_compromisos
            SET estado=:estado, observaciones=:observaciones
            WHERE id=:id
        ");
        $stmt->execute([
            ':estado'        => $estado,
            ':observaciones' => $observaciones ?: null,
            ':id'            => $id,
        ]);

        // Registrar el cambio en el historial del compromiso
        $stmtHist = $this->db->prepare("
            INSERT INTO compromiso_actualizaciones (compromiso_id, estado, observaciones, usuario_id)
            VALUES (:compromiso_id, :estado, :observaciones, :usuario_id)
        ");
        $stmtHist->execute([
            ':compromiso_id' => $id,
            ':estado'        => $estado,
            ':observaciones' => $observaciones ?: null,
            ':usuario_id'    => $usuario_id,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Línea de tiempo completa de un compromiso: cada actualización con
     * su autor y los documentos que se adjuntaron en ese mismo momento
     * (más reciente primero).
     */
    public function historialCompromiso(int $compromiso_id): array {
        $stmt = $this->db->prepare("
            SELECT ca.*, u.nombre AS usuario_nombre
            FROM compromiso_actualizaciones ca
            LEFT JOIN usuarios u ON u.id = ca.usuario_id
            WHERE ca.compromiso_id = ?
            ORDER BY ca.created_at DESC, ca.id DESC
        ");
        $stmt->execute([$compromiso_id]);
        $historial = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmtDocs = $this->db->prepare("
            SELECT * FROM compromiso_documentos WHERE actualizacion_id = ? ORDER BY created_at ASC
        ");
        foreach ($historial as &$h) {
            $stmtDocs->execute([$h['id']]);
            $h['documentos'] = $stmtDocs->fetchAll(PDO::FETCH_ASSOC);
        }
        unset($h);

        return $historial;
    }

    public function eliminarCompromiso(int $id): int {
        $stmt = $this->db->prepare("SELECT comite_id FROM comite_compromisos WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->db->prepare("DELETE FROM comite_compromisos WHERE id = ?")->execute([$id]);
        return $row ? (int) $row['comite_id'] : 0;
    }

    // --- Compromisos asignados a un usuario de empresa ---
    // comite_compromisos.responsable es texto libre (el nombre elegido al
    // crear el compromiso), no una FK a usuarios. Por eso estas consultas
    // identifican "lo mío" cruzando nombre + empresa de la sesión.

    /** Compromisos donde el usuario logueado figura como responsable. */
    public function misCompromisos(string $nombreResponsable, int $empresa_id): array {
        $stmt = $this->db->prepare("
            SELECT cc.*,
                   c.titulo AS comite_titulo, c.fecha AS comite_fecha
            FROM comite_compromisos cc
            JOIN comites c ON c.id = cc.comite_id
            WHERE cc.responsable = :responsable AND c.empresa_id = :empresa_id
            ORDER BY cc.fecha_limite IS NULL ASC, cc.fecha_limite ASC, cc.id DESC
        ");
        $stmt->execute([':responsable' => $nombreResponsable, ':empresa_id' => $empresa_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Compromisos donde el usuario de operaciones/admin logueado figura como
     * responsable, sin restringir por empresa (a diferencia de misCompromisos(),
     * pensada para un usuario de empresa atado a un solo empresa_id).
     * Usada solo para lectura (notificaciones) — no para autorizar ediciones.
     */
    public function misCompromisosGlobal(string $nombreResponsable): array {
        $stmt = $this->db->prepare("
            SELECT cc.*,
                   c.titulo AS comite_titulo, c.fecha AS comite_fecha, c.empresa_id,
                   e.razon_social AS empresa_nombre
            FROM comite_compromisos cc
            JOIN comites c ON c.id = cc.comite_id
            LEFT JOIN empresas e ON e.id = c.empresa_id
            WHERE cc.responsable = :responsable
            ORDER BY cc.fecha_limite IS NULL ASC, cc.fecha_limite ASC, cc.id DESC
        ");
        $stmt->execute([':responsable' => $nombreResponsable]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Trae el compromiso solo si pertenece a este usuario (mismo nombre +
     * misma empresa). Usar siempre antes de editar/subir documentos desde
     * MisCompromisosControlador, para que nadie modifique un compromiso ajeno.
     */
    public function obtenerCompromisoDeUsuario(int $id, string $nombreResponsable, int $empresa_id): array|false {
        $stmt = $this->db->prepare("
            SELECT cc.*, c.titulo AS comite_titulo, c.empresa_id
            FROM comite_compromisos cc
            JOIN comites c ON c.id = cc.comite_id
            WHERE cc.id = :id AND cc.responsable = :responsable AND c.empresa_id = :empresa_id
        ");
        $stmt->execute([':id' => $id, ':responsable' => $nombreResponsable, ':empresa_id' => $empresa_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function agregarDocumentoCompromiso(int $compromiso_id, array $data): void {
        $stmt = $this->db->prepare("
            INSERT INTO compromiso_documentos
                (compromiso_id, actualizacion_id, nombre_original, nombre_guardado, tipo_mime, tamano, subido_por)
            VALUES
                (:compromiso_id, :actualizacion_id, :nombre_original, :nombre_guardado, :tipo_mime, :tamano, :subido_por)
        ");
        $stmt->execute([
            ':compromiso_id'    => $compromiso_id,
            ':actualizacion_id' => $data['actualizacion_id'] ?? null,
            ':nombre_original'  => $data['nombre_original'],
            ':nombre_guardado'  => $data['nombre_guardado'],
            ':tipo_mime'        => $data['tipo_mime'] ?? null,
            ':tamano'           => $data['tamano'] ?? null,
            ':subido_por'       => $data['subido_por'] ?? null,
        ]);
    }

    public function obtenerDocumentoCompromiso(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM compromiso_documentos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
