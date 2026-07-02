<?php

class CronogramaControlador {

    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function index(?int $id = null): void {
        require_once __DIR__ . '/../modelo/EmpresasModelo.php';

        $rol           = $_SESSION['usuario_rol'] ?? '';
        $esOperaciones = $rol === 'operaciones';
        $esAdmin       = $rol === 'admin';

        $empresa    = null;
        $etapas     = [];
        $avance     = 0;
        $documentosPorRequisito = [];
        $modelo     = new EmpresasModelo($this->db);

        if ($esOperaciones || $esAdmin) {
            if ($id) {
                $empresa = $modelo->obtenerPorId($id);
                if ($empresa) {
                    $etapas = $modelo->cronograma($id);
                    $totales = array_column($etapas, 'avance');
                    $avance  = count($totales) > 0 ? round(array_sum($totales) / count($totales), 1) : 0;
                    $documentosPorRequisito = $this->cargarDocumentos($id);
                }
            }
            $todasEmpresas = $id ? [] : $modelo->obtenerTodas();
        } else {
            $empresa_id = $_SESSION['usuario_empresa_id'] ?? null;
            if ($empresa_id) {
                $empresa = $modelo->obtenerPorId((int) $empresa_id);
                $etapas  = $modelo->cronograma((int) $empresa_id);
                $totales = array_column($etapas, 'avance');
                $avance  = count($totales) > 0 ? round(array_sum($totales) / count($totales), 1) : 0;
                $documentosPorRequisito = $this->cargarDocumentos((int) $empresa_id);
            }
            $todasEmpresas = [];
        }

        require_once __DIR__ . '/../vista/modulos/cronograma/index.php';
    }

    private function cargarDocumentos(int $empresa_id): array {
        $stmt = $this->db->prepare("
            SELECT d.id, d.requisito_id, d.nombre_original, d.tamano, d.tipo_mime, d.created_at,
                   u.nombre AS subido_por_nombre
            FROM documentos d
            LEFT JOIN usuarios u ON u.id = d.subido_por
            WHERE d.empresa_id = ?
            ORDER BY d.created_at DESC
        ");
        $stmt->execute([$empresa_id]);
        $docs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $agrupados = [];
        foreach ($docs as $doc) {
            $agrupados[$doc['requisito_id']][] = $doc;
        }
        return $agrupados;
    }
}
