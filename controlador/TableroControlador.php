<?php

require_once __DIR__ . '/ControladorBase.php';

class TableroControlador extends ControladorBase {

    public function index(): void {
        $rol = $this->rol();

        if ($rol === 'admin') {
            $totalEmpresas = (int) $this->db->query("SELECT COUNT(*) FROM empresas")->fetchColumn();
            $totalUsuarios = (int) $this->db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
            require_once __DIR__ . '/../vista/tablero/admin.php';

        } elseif ($rol === 'operaciones') {
            $totalEmpresas  = (int) $this->db->query("SELECT COUNT(*) FROM empresas")->fetchColumn();
            $totalEtapas    = (int) $this->db->query("SELECT COUNT(*) FROM etapas WHERE activo = 1")->fetchColumn();
            $totalRequisitos = (int) $this->db->query("SELECT COUNT(*) FROM requisitos WHERE activo = 1")->fetchColumn();
            $totalAlertas   = (int) $this->db->query("SELECT COUNT(*) FROM empresa_alertas WHERE resuelta = 0")->fetchColumn();
            require_once __DIR__ . '/../vista/tablero/operaciones.php';

        } else {
            $empresa_id = $this->empresaId() ?: null;

            if ($empresa_id) {
                require_once __DIR__ . '/../modelo/EmpresasModelo.php';
                require_once __DIR__ . '/../modelo/IndicadoresModelo.php';
                $modelo     = new EmpresasModelo($this->db);
                $empresa    = $modelo->obtenerDetalle((int) $empresa_id);
                $etapasCronograma = $modelo->cronograma((int) $empresa_id);
                $indModelo  = new IndicadoresModelo($this->db);
                $indicadoresResumen = $indModelo->obtenerResumenPorEmpresa((int) $empresa_id);

                // Resumen de entidades (DIAN, MINCIT, etc.) involucradas
                $stmtEnt = $this->db->prepare("
                    SELECT en.*,
                           COUNT(r.id)                                        AS total_requisitos,
                           SUM(ere.estado = 'cumplido')                       AS cumplidos,
                           SUM(ere.estado = 'en_progreso')                    AS en_progreso,
                           SUM(ere.estado = 'no_aplica')                      AS no_aplica,
                           SUM(ere.estado = 'pendiente' OR ere.estado IS NULL) AS pendientes
                    FROM entidades en
                    JOIN requisitos r ON r.entidad_id = en.id AND r.activo = 1
                    JOIN empresa_etapa_progreso ep ON ep.empresa_id = :emp1 AND ep.etapa_id = r.etapa_id
                    LEFT JOIN empresa_requisito_estado ere
                           ON ere.requisito_id = r.id AND ere.empresa_id = :emp2
                    GROUP BY en.id
                    ORDER BY en.nombre ASC
                ");
                $stmtEnt->execute([':emp1' => $empresa_id, ':emp2' => $empresa_id]);
                $entidadesResumen = $stmtEnt->fetchAll(PDO::FETCH_ASSOC);

                $stmtEntReq = $this->db->prepare("
                    SELECT r.nombre, COALESCE(ere.estado, 'pendiente') AS estado_req
                    FROM requisitos r
                    LEFT JOIN empresa_requisito_estado ere
                           ON ere.requisito_id = r.id AND ere.empresa_id = ?
                    WHERE r.entidad_id = ? AND r.activo = 1
                    ORDER BY r.nombre ASC
                ");
                foreach ($entidadesResumen as &$ent) {
                    $stmtEntReq->execute([$empresa_id, $ent['id']]);
                    $ent['requisitos'] = $stmtEntReq->fetchAll(PDO::FETCH_ASSOC);
                }
                unset($ent);

                // Documentos recientes
                $stmtDocs = $this->db->prepare("
                    SELECT d.*, r.nombre AS requisito_nombre, u.nombre AS subido_por_nombre
                    FROM documentos d
                    LEFT JOIN requisitos r ON r.id = d.requisito_id
                    LEFT JOIN usuarios u   ON u.id = d.subido_por
                    WHERE d.empresa_id = ?
                    ORDER BY d.created_at DESC
                    LIMIT 8
                ");
                $stmtDocs->execute([$empresa_id]);
                $documentosRecientes = $stmtDocs->fetchAll(PDO::FETCH_ASSOC);

                // Requisitos pendientes
                $stmtPend = $this->db->prepare("
                    SELECT r.nombre, r.obligatorio, en.nombre AS entidad_nombre,
                           et.nombre AS etapa_nombre, ere.estado, ere.fecha_vencimiento
                    FROM empresa_requisito_estado ere
                    JOIN requisitos r  ON r.id  = ere.requisito_id
                    JOIN etapas et     ON et.id = r.etapa_id
                    LEFT JOIN entidades en ON en.id = r.entidad_id
                    WHERE ere.empresa_id = ? AND ere.estado IN ('pendiente','en_progreso')
                    ORDER BY et.orden ASC, r.obligatorio DESC, r.nombre ASC
                    LIMIT 10
                ");
                $stmtPend->execute([$empresa_id]);
                $requisitos_pendientes = $stmtPend->fetchAll();

                // Alertas sin resolver
                $stmtAlertas = $this->db->prepare("
                    SELECT * FROM empresa_alertas
                    WHERE empresa_id = ? AND resuelta = 0
                    ORDER BY prioridad ASC, creado_en DESC
                    LIMIT 5
                ");
                $stmtAlertas->execute([$empresa_id]);
                $alertas = $stmtAlertas->fetchAll();
            } else {
                $empresa = null;
                $requisitos_pendientes = [];
                $alertas = [];
                $indicadoresResumen = [];
                $etapasCronograma = [];
                $entidadesResumen = [];
                $documentosRecientes = [];
            }

            require_once __DIR__ . '/../vista/tablero/usuario.php';
        }
    }
}
