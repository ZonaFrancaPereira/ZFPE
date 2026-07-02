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
                $indModelo  = new IndicadoresModelo($this->db);
                $indicadoresResumen = $indModelo->obtenerResumenPorEmpresa((int) $empresa_id);

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
            }

            require_once __DIR__ . '/../vista/tablero/usuario.php';
        }
    }
}
