<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use Dompdf\Dompdf;
use Dompdf\Options;

class InformesControlador {

    private PDO $db;

    public const ESTADO_LABEL = [
        'pendiente'   => 'Pendiente',
        'en_progreso' => 'En progreso',
        'cumplido'    => 'Cumplido',
        'no_aplica'   => 'No aplica',
    ];

    public const ESTADO_COLOR = [
        'pendiente'   => '6c757d',
        'en_progreso' => '1993b8',
        'cumplido'    => '198754',
        'no_aplica'   => 'adb5bd',
    ];

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function excel(?int $empresa_id): void {
        $empresa_id = $this->resolverEmpresaId($empresa_id);
        if (!$empresa_id) { header('Location: index.php'); exit; }

        $datos = $this->obtenerDatos($empresa_id);
        $this->generarExcel($datos);
    }

    public function pdf(?int $empresa_id): void {
        $empresa_id = $this->resolverEmpresaId($empresa_id);
        if (!$empresa_id) { header('Location: index.php'); exit; }

        $datos = $this->obtenerDatos($empresa_id);
        $this->generarPdf($datos);
    }

    private function resolverEmpresaId(?int $empresa_id): ?int {
        $rol = $_SESSION['usuario_rol'] ?? '';
        if (in_array($rol, ['admin', 'operaciones'], true)) {
            return $empresa_id ?: null;
        }
        $propia = (int) ($_SESSION['usuario_empresa_id'] ?? 0);
        return $propia ?: null;
    }

    private function baseUrl(): string {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $dir    = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php')), '/');
        return $scheme . '://' . $host . $dir;
    }

    private function urlSeguimiento(int $empresa_id, int $requisito_id): string {
        return $this->baseUrl() . '/index.php?modulo=seguimiento&id=' . $empresa_id . '#req-' . $requisito_id;
    }

    private function urlDocumento(int $doc_id): string {
        return $this->baseUrl() . '/index.php?modulo=documentos&accion=descargar&id=' . $doc_id;
    }

    private function urlIndicadores(int $empresa_id): string {
        return $this->baseUrl() . '/index.php?modulo=indicadores&id=' . $empresa_id;
    }

    private function obtenerDatos(int $empresa_id): array {
        $stmt = $this->db->prepare("SELECT * FROM empresas WHERE id = ?");
        $stmt->execute([$empresa_id]);
        $empresa = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$empresa) { header('Location: index.php'); exit; }

        // Etapas con fase y avance
        $stmt = $this->db->prepare("
            SELECT et.id AS etapa_id, et.nombre AS etapa_nombre, et.orden AS etapa_orden,
                   f.nombre AS fase_nombre, COALESCE(f.orden, 999) AS fase_orden,
                   COALESCE(ep.porcentaje_avance, 0) AS avance,
                   COALESCE(ep.estado, 'pendiente') AS estado_progreso,
                   ep.fecha_inicio, ep.fecha_completado
            FROM etapas et
            LEFT JOIN fases f ON f.id = et.fase_id
            LEFT JOIN empresa_etapa_progreso ep ON ep.etapa_id = et.id AND ep.empresa_id = ?
            WHERE et.activo = 1
            ORDER BY fase_orden ASC, et.orden ASC
        ");
        $stmt->execute([$empresa_id]);
        $etapasRaw = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Requisitos con estado
        $stmt = $this->db->prepare("
            SELECT r.id AS requisito_id, r.etapa_id, r.nombre AS requisito_nombre, r.obligatorio,
                   r.responsable, en.nombre AS entidad_nombre,
                   COALESCE(ere.estado, 'pendiente') AS estado_req,
                   ere.fecha_vencimiento, ere.fecha_cumplimiento, ere.observaciones
            FROM requisitos r
            LEFT JOIN entidades en ON en.id = r.entidad_id
            LEFT JOIN empresa_requisito_estado ere ON ere.requisito_id = r.id AND ere.empresa_id = ?
            WHERE r.activo = 1
            ORDER BY r.etapa_id ASC, r.nombre ASC
        ");
        $stmt->execute([$empresa_id]);
        $requisitosRaw = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ítems (checklist) con estado
        $stmt = $this->db->prepare("
            SELECT ri.id AS item_id, ri.requisito_id, ri.nombre AS item_nombre, ri.obligatorio,
                   COALESCE(ei.cumplido, 0) AS cumplido, ei.fecha_cumplimiento
            FROM requisito_items ri
            LEFT JOIN empresa_requisito_item_estado ei ON ei.requisito_item_id = ri.id AND ei.empresa_id = ?
            WHERE ri.activo = 1
            ORDER BY ri.requisito_id ASC, ri.orden ASC
        ");
        $stmt->execute([$empresa_id]);
        $itemsPorRequisito = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $it) {
            $itemsPorRequisito[$it['requisito_id']][] = $it;
        }

        // Historial de cambios por requisito
        $stmt = $this->db->prepare("
            SELECT h.*, r.nombre AS requisito_nombre, et.nombre AS etapa_nombre,
                   u.nombre AS usuario_nombre, d.id AS documento_id_valido, d.nombre_original AS documento_nombre
            FROM empresa_requisito_historial h
            JOIN requisitos r ON r.id = h.requisito_id
            JOIN etapas et    ON et.id = r.etapa_id
            LEFT JOIN usuarios u ON u.id = h.registrado_por
            LEFT JOIN documentos d ON d.id = h.documento_id
            WHERE h.empresa_id = ?
            ORDER BY h.created_at DESC
        ");
        $stmt->execute([$empresa_id]);
        $historialRaw = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $historialPorRequisito = [];
        foreach ($historialRaw as $h) {
            $historialPorRequisito[$h['requisito_id']][] = $h;
        }

        // Documentos
        $stmt = $this->db->prepare("
            SELECT d.*, r.nombre AS requisito_nombre, et.nombre AS etapa_nombre, u.nombre AS subido_por_nombre
            FROM documentos d
            JOIN requisitos r ON r.id = d.requisito_id
            JOIN etapas et    ON et.id = r.etapa_id
            LEFT JOIN usuarios u ON u.id = d.subido_por
            WHERE d.empresa_id = ?
            ORDER BY et.orden ASC, r.nombre ASC, d.created_at DESC
        ");
        $stmt->execute([$empresa_id]);
        $documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $documentosPorRequisito = [];
        foreach ($documentos as $d) {
            $documentosPorRequisito[$d['requisito_id']][] = $d;
        }

        // Requisitos anidados (items + historial + documentos), agrupados por etapa
        $requisitosPorEtapa = [];
        $conteoEstados = ['pendiente' => 0, 'en_progreso' => 0, 'cumplido' => 0, 'no_aplica' => 0];
        foreach ($requisitosRaw as $r) {
            $r['items']      = $itemsPorRequisito[$r['requisito_id']] ?? [];
            $r['historial']  = $historialPorRequisito[$r['requisito_id']] ?? [];
            $r['documentos'] = $documentosPorRequisito[$r['requisito_id']] ?? [];
            $requisitosPorEtapa[$r['etapa_id']][] = $r;
            $conteoEstados[$r['estado_req']] = ($conteoEstados[$r['estado_req']] ?? 0) + 1;
        }

        // Fase > etapas (con requisitos anidados)
        $fases = [];
        foreach ($etapasRaw as $et) {
            $et['requisitos'] = $requisitosPorEtapa[$et['etapa_id']] ?? [];
            $faseKey = $et['fase_nombre'] ?: 'General';
            if (!isset($fases[$faseKey])) {
                $fases[$faseKey] = ['nombre' => $faseKey, 'orden' => $et['fase_orden'], 'etapas' => []];
            }
            $fases[$faseKey]['etapas'][] = $et;
        }
        uasort($fases, fn($a, $b) => $a['orden'] <=> $b['orden']);

        $avances = array_column($etapasRaw, 'avance');
        $avanceGeneral = $avances ? round(array_sum($avances) / count($avances), 1) : 0.0;

        $avancePorFase = [];
        foreach ($fases as $fase) {
            $vals = array_column($fase['etapas'], 'avance');
            $avancePorFase[] = [
                'fase'   => $fase['nombre'],
                'avance' => $vals ? round(array_sum($vals) / count($vals), 1) : 0.0,
            ];
        }

        // Indicadores (resumen con último valor)
        require_once __DIR__ . '/../modelo/IndicadoresModelo.php';
        $indicadores = (new IndicadoresModelo($this->db))->obtenerResumenPorEmpresa($empresa_id);

        // Histórico completo de valores por indicador
        $stmt = $this->db->prepare("
            SELECT v.indicador_id, v.periodo, v.valor, v.observaciones, v.created_at,
                   u.nombre AS registrado_por_nombre
            FROM empresa_indicador_valor v
            LEFT JOIN usuarios u ON u.id = v.registrado_por
            WHERE v.empresa_id = ?
            ORDER BY v.indicador_id ASC, v.periodo ASC
        ");
        $stmt->execute([$empresa_id]);
        $indicadorHistorial = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $v) {
            $indicadorHistorial[$v['indicador_id']][] = $v;
        }

        return [
            'empresa'            => $empresa,
            'empresaId'          => $empresa_id,
            'baseUrl'            => $this->baseUrl(),
            'fases'              => $fases,
            'conteoEstados'      => $conteoEstados,
            'avanceGeneral'      => $avanceGeneral,
            'avancePorFase'      => $avancePorFase,
            'documentos'         => $documentos,
            'historial'          => $historialRaw,
            'indicadores'        => $indicadores,
            'indicadorHistorial' => $indicadorHistorial,
        ];
    }

    private function nombreArchivo(array $empresa, string $ext): string {
        $slug = preg_replace('/[^A-Za-z0-9_]+/', '_', $empresa['razon_social']);
        return 'Informe_ZFPE_' . trim($slug, '_') . '_' . date('Ymd') . '.' . $ext;
    }

    private function estiloEncabezado($sheet, string $rango): void {
        $sheet->getStyle($rango)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle($rango)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1993B8');
    }

    private function celdaLink($sheet, string $celda, string $texto, string $url): void {
        $sheet->setCellValue($celda, $texto);
        $sheet->getCell($celda)->getHyperlink()->setUrl($url);
        $sheet->getStyle($celda)->getFont()->setUnderline(true)->getColor()->setRGB('0563C1');
    }

    private function generarExcel(array $datos): void {
        $empresa            = $datos['empresa'];
        $empresaId          = $datos['empresaId'];
        $fases               = $datos['fases'];
        $documentos          = $datos['documentos'];
        $historial           = $datos['historial'];
        $indicadores         = $datos['indicadores'];
        $indicadorHistorial  = $datos['indicadorHistorial'];
        $conteoEstados       = $datos['conteoEstados'];
        $avancePorFase       = $datos['avancePorFase'];

        $spreadsheet = new Spreadsheet();

        // ================= Hoja Resumen =================
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Resumen');
        $sheet->setCellValue('A1', 'Informe ZFPE — ' . $empresa['razon_social']);
        $sheet->mergeCells('A1:C1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $sheet->fromArray([
            ['NIT', $empresa['nit']],
            ['Representante', $empresa['representante'] ?? '—'],
            ['Correo', $empresa['correo'] ?? '—'],
            ['Fecha del informe', date('d/m/Y H:i')],
            ['Avance general', $datos['avanceGeneral'] . '%'],
        ], null, 'A3');
        $this->celdaLink($sheet, 'A9', 'Abrir empresa en el sistema →', $this->baseUrl() . '/index.php?modulo=empresas&accion=ver&id=' . $empresaId);
        $sheet->getColumnDimension('A')->setWidth(22);
        $sheet->getColumnDimension('B')->setWidth(28);

        // Datos fuente para el gráfico de distribución de estados
        $sheet->setCellValue('E2', 'Estado');
        $sheet->setCellValue('F2', 'Cantidad');
        $sheet->getStyle('E2:F2')->getFont()->setBold(true);
        $r = 3;
        foreach (self::ESTADO_LABEL as $key => $label) {
            $sheet->setCellValue("E$r", $label);
            $sheet->setCellValue("F$r", $conteoEstados[$key] ?? 0);
            $r++;
        }
        $ultimaFilaEstados = $r - 1;

        // Datos fuente para el gráfico de avance por fase
        $sheet->setCellValue('H2', 'Fase');
        $sheet->setCellValue('I2', 'Avance %');
        $sheet->getStyle('H2:I2')->getFont()->setBold(true);
        $r = 3;
        foreach ($avancePorFase as $af) {
            $sheet->setCellValue("H$r", $af['fase']);
            $sheet->setCellValue("I$r", $af['avance']);
            $r++;
        }
        $ultimaFilaFases = max($r - 1, 3);

        // Gráfico de dona: distribución de requisitos por estado
        $labelsEstado = [new DataSeriesValues('String', "Resumen!\$E\$3:\$E\$$ultimaFilaEstados", null, $ultimaFilaEstados - 2)];
        $valoresEstado = [new DataSeriesValues('Number', "Resumen!\$F\$3:\$F\$$ultimaFilaEstados", null, $ultimaFilaEstados - 2)];
        $seriesEstado = new DataSeries(DataSeries::TYPE_DONUTCHART, null, range(0, count($valoresEstado) - 1), $labelsEstado, $labelsEstado, $valoresEstado);
        $chartEstado = new Chart('distribucionEstados', new Title('Distribución de requisitos'), new Legend(Legend::POSITION_RIGHT, null, false), new PlotArea(null, [$seriesEstado]));
        $chartEstado->setTopLeftPosition('A12');
        $chartEstado->setBottomRightPosition('F30');
        $sheet->addChart($chartEstado);

        // Gráfico de barras: avance por fase
        if (!empty($avancePorFase)) {
            $labelsFase = [new DataSeriesValues('String', "Resumen!\$H\$3:\$H\$$ultimaFilaFases", null, $ultimaFilaFases - 2)];
            $valoresFase = [new DataSeriesValues('Number', "Resumen!\$I\$3:\$I\$$ultimaFilaFases", null, $ultimaFilaFases - 2)];
            $seriesFase = new DataSeries(DataSeries::TYPE_BARCHART, DataSeries::GROUPING_CLUSTERED, range(0, count($valoresFase) - 1), $labelsFase, $labelsFase, $valoresFase);
            $seriesFase->setPlotDirection(DataSeries::DIRECTION_BAR);
            $chartFase = new Chart('avancePorFase', new Title('Avance por fase (%)'), null, new PlotArea(null, [$seriesFase]));
            $chartFase->setTopLeftPosition('G12');
            $chartFase->setBottomRightPosition('M30');
            $sheet->addChart($chartFase);
        }

        // ================= Hoja Requisitos (matriz estructurada) =================
        $sheetReq = $spreadsheet->createSheet();
        $sheetReq->setTitle('Requisitos');
        $headers = ['Fase', 'Etapa', '% Avance etapa', 'Requisito', 'Entidad', 'Responsable', 'Obligatorio', 'Estado', 'Fecha vencimiento', 'Fecha cumplimiento', 'Ítems', 'Documentos', 'Observaciones', 'Abrir'];
        $sheetReq->fromArray($headers, null, 'A1');
        $this->estiloEncabezado($sheetReq, 'A1:N1');

        $row = 2;
        foreach ($fases as $fase) {
            foreach ($fase['etapas'] as $etapa) {
                foreach ($etapa['requisitos'] as $req) {
                    $totalItems = count($req['items']);
                    $itemsCumplidos = count(array_filter($req['items'], fn($i) => (int) $i['cumplido'] === 1));
                    $sheetReq->fromArray([
                        $fase['nombre'],
                        $etapa['etapa_nombre'],
                        $etapa['avance'] . '%',
                        $req['requisito_nombre'],
                        $req['entidad_nombre'] ?: '—',
                        $req['responsable'] ?: '—',
                        $req['obligatorio'] ? 'Sí' : 'No',
                        self::ESTADO_LABEL[$req['estado_req']] ?? $req['estado_req'],
                        $req['fecha_vencimiento'] ? date('d/m/Y', strtotime($req['fecha_vencimiento'])) : '',
                        $req['fecha_cumplimiento'] ? date('d/m/Y', strtotime($req['fecha_cumplimiento'])) : '',
                        $totalItems ? "$itemsCumplidos/$totalItems" : '—',
                        count($req['documentos']),
                        $req['observaciones'] ?: '',
                    ], null, "A$row");
                    $this->celdaLink($sheetReq, "N$row", 'Ver en Seguimiento', $this->urlSeguimiento($empresaId, (int) $req['requisito_id']));
                    $row++;
                }
            }
        }
        if ($row > 2) {
            $sheetReq->setAutoFilter('A1:N' . ($row - 1));
        }
        foreach (range('A', 'N') as $col) {
            $sheetReq->getColumnDimension($col)->setAutoSize(true);
        }
        $sheetReq->freezePane('A2');

        // ================= Hoja Ítems (checklist detallado) =================
        $sheetItems = $spreadsheet->createSheet();
        $sheetItems->setTitle('Items');
        $sheetItems->fromArray(['Fase', 'Etapa', 'Requisito', 'Ítem', 'Obligatorio', 'Cumplido', 'Fecha cumplimiento'], null, 'A1');
        $this->estiloEncabezado($sheetItems, 'A1:G1');
        $row = 2;
        foreach ($fases as $fase) {
            foreach ($fase['etapas'] as $etapa) {
                foreach ($etapa['requisitos'] as $req) {
                    foreach ($req['items'] as $item) {
                        $sheetItems->fromArray([
                            $fase['nombre'], $etapa['etapa_nombre'], $req['requisito_nombre'],
                            $item['item_nombre'], $item['obligatorio'] ? 'Sí' : 'No',
                            $item['cumplido'] ? 'Sí' : 'No',
                            $item['fecha_cumplimiento'] ? date('d/m/Y', strtotime($item['fecha_cumplimiento'])) : '',
                        ], null, "A$row");
                        $row++;
                    }
                }
            }
        }
        if ($row > 2) {
            $sheetItems->setAutoFilter('A1:G' . ($row - 1));
        }
        foreach (range('A', 'G') as $col) {
            $sheetItems->getColumnDimension($col)->setAutoSize(true);
        }

        // ================= Hoja Documentos =================
        $sheetDoc = $spreadsheet->createSheet();
        $sheetDoc->setTitle('Documentos');
        $sheetDoc->fromArray(['Etapa', 'Requisito', 'Archivo', 'Descripción', 'Tamaño (KB)', 'Subido por', 'Fecha', 'Descargar'], null, 'A1');
        $this->estiloEncabezado($sheetDoc, 'A1:H1');
        $row = 2;
        foreach ($documentos as $d) {
            $sheetDoc->fromArray([
                $d['etapa_nombre'], $d['requisito_nombre'], $d['nombre_original'],
                $d['descripcion'] ?: '', round(($d['tamano'] ?? 0) / 1024, 1),
                $d['subido_por_nombre'] ?: '', $d['created_at'] ? date('d/m/Y H:i', strtotime($d['created_at'])) : '',
            ], null, "A$row");
            $this->celdaLink($sheetDoc, "H$row", 'Descargar', $this->urlDocumento((int) $d['id']));
            $row++;
        }
        foreach (range('A', 'H') as $col) {
            $sheetDoc->getColumnDimension($col)->setAutoSize(true);
        }

        // ================= Hoja Historial =================
        $sheetHist = $spreadsheet->createSheet();
        $sheetHist->setTitle('Historial');
        $sheetHist->fromArray(['Fecha', 'Etapa', 'Requisito', 'Estado anterior', 'Estado nuevo', 'Observaciones', 'Documento', 'Usuario'], null, 'A1');
        $this->estiloEncabezado($sheetHist, 'A1:H1');
        $row = 2;
        foreach ($historial as $h) {
            $sheetHist->fromArray([
                date('d/m/Y H:i', strtotime($h['created_at'])),
                $h['etapa_nombre'], $h['requisito_nombre'],
                $h['estado_anterior'] ? (self::ESTADO_LABEL[$h['estado_anterior']] ?? $h['estado_anterior']) : '',
                self::ESTADO_LABEL[$h['estado_nuevo']] ?? $h['estado_nuevo'],
                $h['observaciones'] ?: '', '', $h['usuario_nombre'] ?: '',
            ], null, "A$row");
            if (!empty($h['documento_id_valido'])) {
                $this->celdaLink($sheetHist, "G$row", $h['documento_nombre'], $this->urlDocumento((int) $h['documento_id_valido']));
            }
            $row++;
        }
        foreach (range('A', 'H') as $col) {
            $sheetHist->getColumnDimension($col)->setAutoSize(true);
        }

        // ================= Hoja Indicadores =================
        if (!empty($indicadores)) {
            $sheetInd = $spreadsheet->createSheet();
            $sheetInd->setTitle('Indicadores');
            $sheetInd->fromArray(['Indicador', 'Unidad', 'Meta', 'Último valor', 'Último período', 'Periodicidad', 'Ver'], null, 'A1');
            $this->estiloEncabezado($sheetInd, 'A1:G1');
            $row = 2;
            foreach ($indicadores as $i) {
                $sheetInd->fromArray([
                    $i['nombre'], $i['unidad'] ?: '', $i['meta'], $i['ultimo_valor'], $i['ultimo_periodo'], $i['periodicidad'] ?: '',
                ], null, "A$row");
                $this->celdaLink($sheetInd, "G$row", 'Ver en el sistema', $this->urlIndicadores($empresaId));
                $row++;
            }
            foreach (range('A', 'G') as $col) {
                $sheetInd->getColumnDimension($col)->setAutoSize(true);
            }

            // Histórico completo de valores
            $sheetIndHist = $spreadsheet->createSheet();
            $sheetIndHist->setTitle('Indicadores - Histórico');
            $sheetIndHist->fromArray(['Indicador', 'Período', 'Valor', 'Observaciones', 'Registrado por', 'Fecha'], null, 'A1');
            $this->estiloEncabezado($sheetIndHist, 'A1:F1');
            $row = 2;
            $nombresPorId = array_column($indicadores, 'nombre', 'id');
            foreach ($indicadorHistorial as $indId => $valores) {
                foreach ($valores as $v) {
                    $sheetIndHist->fromArray([
                        $nombresPorId[$indId] ?? ('Indicador #' . $indId),
                        $v['periodo'], $v['valor'], $v['observaciones'] ?: '',
                        $v['registrado_por_nombre'] ?: '', $v['created_at'] ? date('d/m/Y', strtotime($v['created_at'])) : '',
                    ], null, "A$row");
                    $row++;
                }
            }
            foreach (range('A', 'F') as $col) {
                $sheetIndHist->getColumnDimension($col)->setAutoSize(true);
            }
        }

        $spreadsheet->setActiveSheetIndex(0);

        while (ob_get_level() > 0) { ob_end_clean(); }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $this->nombreArchivo($empresa, 'xlsx') . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);
        $writer->save('php://output');
        exit;
    }

    private function generarPdf(array $datos): void {
        $empresa = $datos['empresa'];

        ob_start();
        require __DIR__ . '/../vista/modulos/informes/pdf.php';
        $html = ob_get_clean();

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'Helvetica');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        while (ob_get_level() > 0) { ob_end_clean(); }
        $dompdf->stream($this->nombreArchivo($empresa, 'pdf'), ['Attachment' => true]);
        exit;
    }
}
