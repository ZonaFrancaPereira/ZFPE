<?php

class EntidadesUsuarioControlador {

    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function index(): void {
        $empresa_id = $_SESSION['usuario_empresa_id'] ?? null;
        $entidades  = [];
        $empresa    = null;

        if ($empresa_id) {
            // Datos básicos de la empresa
            $stmt = $this->db->prepare("SELECT razon_social, nit FROM empresas WHERE id = ?");
            $stmt->execute([$empresa_id]);
            $empresa = $stmt->fetch(PDO::FETCH_ASSOC);

            // Entidades con sus requisitos para esta empresa
            $stmt = $this->db->prepare("
                SELECT en.*,
                       COUNT(r.id)                                   AS total_requisitos,
                       SUM(ere.estado = 'cumplido')                  AS cumplidos,
                       SUM(ere.estado = 'en_progreso')               AS en_progreso,
                       SUM(ere.estado = 'no_aplica')                 AS no_aplica,
                       SUM(ere.estado = 'pendiente' OR ere.estado IS NULL) AS pendientes
                FROM entidades en
                JOIN requisitos r ON r.entidad_id = en.id AND r.activo = 1
                JOIN empresa_etapa_progreso ep ON ep.empresa_id = :emp1 AND ep.etapa_id = r.etapa_id
                LEFT JOIN empresa_requisito_estado ere
                       ON ere.requisito_id = r.id AND ere.empresa_id = :emp2
                GROUP BY en.id
                ORDER BY en.nombre ASC
            ");
            $stmt->execute([':emp1' => $empresa_id, ':emp2' => $empresa_id]);
            $entidades = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Cargar los requisitos de cada entidad
            $stmtReq = $this->db->prepare("
                SELECT r.*, et.nombre AS etapa_nombre,
                       COALESCE(ere.estado, 'pendiente')  AS estado_req,
                       ere.fecha_vencimiento,
                       ere.observaciones
                FROM requisitos r
                JOIN etapas et ON et.id = r.etapa_id
                LEFT JOIN empresa_requisito_estado ere
                       ON ere.requisito_id = r.id AND ere.empresa_id = ?
                WHERE r.entidad_id = ? AND r.activo = 1
                ORDER BY et.orden ASC, r.nombre ASC
            ");

            foreach ($entidades as &$entidad) {
                $stmtReq->execute([$empresa_id, $entidad['id']]);
                $entidad['requisitos'] = $stmtReq->fetchAll(PDO::FETCH_ASSOC);
            }
            unset($entidad);
        }

        require_once __DIR__ . '/../vista/modulos/entidades/index.php';
    }
}
