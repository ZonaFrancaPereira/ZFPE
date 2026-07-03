<?php

require_once __DIR__ . '/ControladorBase.php';

class ReportesControlador extends ControladorBase {

    public function index(?int $id = null): void {
        $empresa            = null;
        $etapas             = [];
        $resumenEstados     = [];
        $vencidos           = [];
        $porVencer          = [];
        $totalDocs          = 0;
        $todasEmpresas      = [];
        $indicadoresReporte = [];
        $alertasEjecutivas  = [];
        $usuariosEmpresa    = [];
        $equipoInterno      = [];

        require_once __DIR__ . '/../modelo/EmpresasModelo.php';
        require_once __DIR__ . '/../modelo/AlertasModelo.php';
        require_once __DIR__ . '/../modelo/UsuariosModelo.php';
        $modeloEmpresas = new EmpresasModelo($this->db);
        $modeloAlertas  = new AlertasModelo($this->db);
        $modeloUsuarios = new UsuariosModelo($this->db);

        if ($this->esOp()) {
            if ($id) {
                $empresa = $modeloEmpresas->obtenerPorId($id);
                if ($empresa) {
                    $empresa_id = $id;
                    $this->cargarDatos($empresa_id, $etapas, $resumenEstados, $vencidos, $porVencer, $totalDocs, $indicadoresReporte);
                    $alertasEjecutivas = $modeloAlertas->listarPorEmpresa($empresa_id);
                    $usuariosEmpresa   = $modeloUsuarios->obtenerPorEmpresa($empresa_id);
                    $equipoInterno     = $modeloUsuarios->obtenerEquipoInterno();
                }
            } else {
                $todasEmpresas = $modeloEmpresas->obtenerTodas();
            }
        } else {
            $empresa_id = $this->empresaId();
            if ($empresa_id) {
                $empresa = $modeloEmpresas->obtenerPorId($empresa_id);
                $this->cargarDatos($empresa_id, $etapas, $resumenEstados, $vencidos, $porVencer, $totalDocs, $indicadoresReporte);
                $alertasEjecutivas = $modeloAlertas->listarPorEmpresa($empresa_id);
            }
        }

        require_once __DIR__ . '/../vista/modulos/reportes/index.php';
    }

    /** Solo Operaciones/Admin puede marcar una nueva alerta ejecutiva para una empresa. */
    public function crearAlerta(?int $id): void {
        $this->exigirPost('index.php?modulo=reportes');
        if (!$this->esOp() || !$id) {
            header('Location: index.php?modulo=reportes');
            exit;
        }

        $mensaje = trim($_POST['mensaje'] ?? '');
        if ($mensaje === '') {
            $_SESSION['flash_error'] = 'El mensaje de la alerta es obligatorio.';
            header('Location: index.php?modulo=reportes&id=' . $id);
            exit;
        }

        $destinatarios = array_map('intval', $_POST['destinatarios'] ?? []);

        require_once __DIR__ . '/../modelo/AlertasModelo.php';
        (new AlertasModelo($this->db))->crear(
            $id,
            $_POST['tipo'] ?? 'pendiente',
            $_POST['prioridad'] ?? 'media',
            $mensaje,
            $_POST['enlace_reunion'] ?? null,
            $destinatarios
        );
        $_SESSION['flash_success'] = 'Alerta ejecutiva registrada.';
        header('Location: index.php?modulo=reportes&id=' . $id);
        exit;
    }

    /** Marca una alerta como resuelta; vuelve al reporte de la misma empresa. */
    public function resolverAlerta(?int $id): void {
        $this->exigirPost('index.php?modulo=reportes');
        if (!$this->esOp() || !$id) {
            header('Location: index.php?modulo=reportes');
            exit;
        }

        require_once __DIR__ . '/../modelo/AlertasModelo.php';
        $modeloAlertas = new AlertasModelo($this->db);
        $alerta = $modeloAlertas->obtener($id);
        if (!$alerta) {
            header('Location: index.php?modulo=reportes');
            exit;
        }

        $modeloAlertas->resolver($id, $_POST['comentario'] ?? null);
        $_SESSION['flash_success'] = 'Alerta marcada como resuelta.';
        header('Location: index.php?modulo=reportes&id=' . $alerta['empresa_id']);
        exit;
    }

    private function cargarDatos(
        int $empresa_id,
        array &$etapas,
        array &$resumenEstados,
        array &$vencidos,
        array &$porVencer,
        int  &$totalDocs,
        array &$indicadoresReporte
    ): void {
        // Sincronizar etapas nuevas: insertar filas faltantes en empresa_etapa_progreso
        $this->db->prepare("
            INSERT IGNORE INTO empresa_etapa_progreso (empresa_id, etapa_id, porcentaje_avance, estado)
            SELECT ?, et.id, 0, 'pendiente'
            FROM etapas et
            WHERE et.activo = 1
              AND NOT EXISTS (
                  SELECT 1 FROM empresa_etapa_progreso ep2
                  WHERE ep2.empresa_id = ? AND ep2.etapa_id = et.id
              )
        ")->execute([$empresa_id, $empresa_id]);

        // Etapas con progreso y conteo de requisitos
        $stmt = $this->db->prepare("
            SELECT et.*,
                   f.id     AS fase_id,
                   f.nombre AS fase_nombre,
                   f.orden  AS fase_orden,
                   COALESCE(ep.porcentaje_avance, 0) AS avance,
                   COALESCE(ep.estado, 'pendiente')   AS estado_progreso,
                   ep.fecha_inicio,
                   ep.fecha_completado,
                   COUNT(DISTINCT r.id)                            AS total_req,
                   SUM(ere.estado = 'cumplido')                    AS req_cumplidos,
                   SUM(ere.estado = 'en_progreso')                 AS req_en_progreso,
                   SUM(ere.estado = 'no_aplica')                   AS req_no_aplica,
                   SUM(ere.estado = 'pendiente' OR ere.estado IS NULL) AS req_pendientes
            FROM etapas et
            LEFT JOIN fases f ON f.id = et.fase_id
            LEFT JOIN empresa_etapa_progreso ep ON ep.etapa_id = et.id AND ep.empresa_id = :e1
            LEFT JOIN requisitos r  ON r.etapa_id = et.id AND r.activo = 1
            LEFT JOIN empresa_requisito_estado ere ON ere.requisito_id = r.id AND ere.empresa_id = :e2
            WHERE et.activo = 1
            GROUP BY et.id, f.id, f.nombre, f.orden, ep.porcentaje_avance, ep.estado, ep.fecha_inicio, ep.fecha_completado
            ORDER BY COALESCE(f.orden, 999) ASC, et.orden ASC
        ");
        $stmt->execute([':e1' => $empresa_id, ':e2' => $empresa_id]);
        $etapas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Resumen global de estados de requisitos
        $stmt = $this->db->prepare("
            SELECT COALESCE(ere.estado, 'pendiente') AS estado, COUNT(*) AS total
            FROM requisitos r
            LEFT JOIN empresa_etapa_progreso ep ON ep.empresa_id = ? AND ep.etapa_id = r.etapa_id
            LEFT JOIN empresa_requisito_estado ere ON ere.requisito_id = r.id AND ere.empresa_id = ?
            WHERE r.activo = 1
            GROUP BY COALESCE(ere.estado, 'pendiente')
        ");
        $stmt->execute([$empresa_id, $empresa_id]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $resumenEstados[$row['estado']] = (int) $row['total'];
        }

        // Requisitos vencidos
        $stmt = $this->db->prepare("
            SELECT r.id AS requisito_id, r.nombre AS requisito, et.nombre AS etapa,
                   ere.fecha_vencimiento,
                   DATEDIFF(CURDATE(), ere.fecha_vencimiento) AS dias_vencido
            FROM empresa_requisito_estado ere
            JOIN requisitos r ON r.id = ere.requisito_id
            JOIN etapas et    ON et.id = r.etapa_id
            WHERE ere.empresa_id = ?
              AND ere.fecha_vencimiento < CURDATE()
              AND ere.estado NOT IN ('cumplido','no_aplica')
            ORDER BY ere.fecha_vencimiento ASC
        ");
        $stmt->execute([$empresa_id]);
        $vencidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Requisitos por vencer (próximos 30 días)
        $stmt = $this->db->prepare("
            SELECT r.id AS requisito_id, r.nombre AS requisito, et.nombre AS etapa,
                   ere.fecha_vencimiento,
                   DATEDIFF(ere.fecha_vencimiento, CURDATE()) AS dias_restantes
            FROM empresa_requisito_estado ere
            JOIN requisitos r ON r.id = ere.requisito_id
            JOIN etapas et    ON et.id = r.etapa_id
            WHERE ere.empresa_id = ?
              AND ere.fecha_vencimiento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
              AND ere.estado NOT IN ('cumplido','no_aplica')
            ORDER BY ere.fecha_vencimiento ASC
        ");
        $stmt->execute([$empresa_id]);
        $porVencer = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Total documentos
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM documentos WHERE empresa_id = ?");
        $stmt->execute([$empresa_id]);
        $totalDocs = (int) $stmt->fetchColumn();

        // Indicadores
        require_once __DIR__ . '/../modelo/IndicadoresModelo.php';
        $indicadoresReporte = (new IndicadoresModelo($this->db))->obtenerResumenPorEmpresa($empresa_id);
        // $indicadoresReporte is already passed by reference — assignment above writes to caller's variable
    }
}
