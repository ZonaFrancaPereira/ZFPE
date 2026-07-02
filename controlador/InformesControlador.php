<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/ControladorBase.php';

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

class InformesControlador extends ControladorBase {

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
        if ($this->esOp()) {
            return $empresa_id ?: null;
        }
        return $this->empresaId() ?: null;
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

    private const FUENTE_REGULAR = __DIR__ . '/../vendor/dompdf/dompdf/lib/fonts/DejaVuSans.ttf';
    private const FUENTE_NEGRITA = __DIR__ . '/../vendor/dompdf/dompdf/lib/fonts/DejaVuSans-Bold.ttf';

    /**
     * Dibuja con GD la misma gráfica que ve el usuario en el módulo de Indicadores
     * (Chart.js no se puede ejecutar dentro del PDF, así que se genera como imagen).
     * Soporta línea, área y barras tal cual se configuraron; radar/combo caen a línea
     * por ser mucho más costosos de reproducir con GD y muy poco usados.
     *
     * Se dibuja al doble de tamaño final (@2x) y se deja que el PDF la escale hacia
     * abajo por CSS: es la forma más simple de lograr que las líneas y el texto se
     * vean suaves, ya que GD no antialiasa bien los trazos a resolución nativa.
     */
    private function generarGraficoIndicador(array $labels, array $valores, ?float $meta, string $tipo, string $unidad): string {
        $n = count($valores);
        if ($n === 0) return '';

        $tipo = in_array($tipo, ['linea', 'area', 'barra', 'torta'], true) ? $tipo : 'linea';

        $ancho = 1240; $alto = 440; // @2x — se muestra a 620x220
        $img = imagecreatetruecolor($ancho, $alto);
        imageantialias($img, true);

        $blanco     = imagecolorallocate($img, 255, 255, 255);
        $gris       = imagecolorallocate($img, 92, 102, 112);
        $grisClaro  = imagecolorallocate($img, 233, 236, 239);
        $texto      = imagecolorallocate($img, 73, 80, 87);
        $teal       = imagecolorallocate($img, 0x19, 0x93, 0xb8);
        $tealOscuro = imagecolorallocate($img, 0x10, 0x6b, 0x87);
        $tealClaro  = imagecolorallocatealpha($img, 0x19, 0x93, 0xb8, 100);
        $verde      = imagecolorallocate($img, 25, 135, 84);

        imagefilledrectangle($img, 0, 0, $ancho, $alto, $blanco);

        if ($tipo === 'torta') {
            $this->dibujarTorta($img, $ancho, $alto, $labels, $valores, $texto, $gris);
            return $this->exportarPng($img);
        }

        $padL = 90; $padR = 30; $padT = 30; $padB = 60;
        $plotW = $ancho - $padL - $padR;
        $plotH = $alto - $padT - $padB;

        $tope = max(array_merge($valores, [$meta ?? 0]));
        $tope = $tope > 0 ? $tope * 1.18 : 1;

        // Cuadrícula horizontal con etiquetas de escala (estilo Chart.js)
        $pasos = 4;
        for ($p = 0; $p <= $pasos; $p++) {
            $val = $tope / $pasos * $p;
            $y   = (int) ($padT + $plotH - ($val / $tope) * $plotH);
            imageline($img, $padL, $y, $padL + $plotW, $y, $grisClaro);
            $this->texto($img, self::FUENTE_REGULAR, 15, $padL - 14, $y, number_format($val, 0), $gris, 'derecha');
        }
        imageline($img, $padL, $padT, $padL, $padT + $plotH, $gris);

        // Línea de meta (verde punteada) por encima de la cuadrícula
        if ($meta !== null && $meta > 0) {
            $yMeta = (int) ($padT + $plotH - ($meta / $tope) * $plotH);
            for ($x = $padL; $x < $padL + $plotW; $x += 14) {
                imagesetthickness($img, 3);
                imageline($img, $x, $yMeta, (int) min($x + 7, $padL + $plotW), $yMeta, $verde);
            }
            $this->texto($img, self::FUENTE_NEGRITA, 15, $padL + $plotW, $yMeta - 20, 'Meta: ' . number_format($meta, 1), $verde, 'derecha');
        }

        if ($tipo === 'barra') {
            $espacio    = $plotW / $n;
            $anchoBarra = $espacio * 0.55;
            foreach ($valores as $i => $v) {
                $x = $padL + $i * $espacio + ($espacio - $anchoBarra) / 2;
                $h = ($v / $tope) * $plotH;
                $y = $padT + $plotH - $h;
                // Sombra sutil + relleno degradado (más claro arriba, teal sólido abajo)
                imagefilledrectangle($img, (int) $x + 3, (int) $y + 3, (int) ($x + $anchoBarra) + 3, $padT + $plotH, $grisClaro);
                $this->rectanguloDegradado($img, (int) $x, (int) $y, (int) ($x + $anchoBarra), $padT + $plotH, $teal, $tealOscuro);
                $this->texto($img, self::FUENTE_NEGRITA, 16, (int) ($x + $anchoBarra / 2), (int) $y - 12, number_format($v, 1), $tealOscuro, 'centro');
            }
        } else {
            $espacio = $n > 1 ? $plotW / ($n - 1) : 0;
            $puntos  = [];
            foreach ($valores as $i => $v) {
                $x = $padL + $i * $espacio;
                $y = $padT + $plotH - ($v / $tope) * $plotH;
                $puntos[] = [$x, $y];
            }
            if ($tipo === 'area') {
                $poly = [$padL, $padT + $plotH];
                foreach ($puntos as $p) { $poly[] = $p[0]; $poly[] = $p[1]; }
                $poly[] = $padL + $plotW; $poly[] = $padT + $plotH;
                imagefilledpolygon($img, $poly, (int) (count($poly) / 2), $tealClaro);
            }
            imagesetthickness($img, 5);
            for ($i = 0; $i < count($puntos) - 1; $i++) {
                imageline($img, (int) $puntos[$i][0], (int) $puntos[$i][1], (int) $puntos[$i + 1][0], (int) $puntos[$i + 1][1], $teal);
            }
            imagesetthickness($img, 1);
            $ultimo = count($puntos) - 1;
            foreach ($puntos as $i => $p) {
                // Punto con halo blanco (look de Chart.js): círculo blanco + anillo de color
                imagefilledellipse($img, (int) $p[0], (int) $p[1], 20, 20, $blanco);
                imagefilledellipse($img, (int) $p[0], (int) $p[1], 14, 14, $teal);
                $arriba = $i % 2 === 0;
                // El primero y el último se alinean hacia adentro para no salirse del lienzo
                // ni encimarse con la etiqueta del eje Y.
                $alinear = $i === 0 ? 'izquierda' : ($i === $ultimo ? 'derecha' : 'centro');
                $xEtiq   = $i === 0 ? (int) $p[0] + 10 : ($i === $ultimo ? (int) $p[0] - 10 : (int) $p[0]);
                $this->texto($img, self::FUENTE_NEGRITA, 16, $xEtiq, (int) $p[1] + ($arriba ? -28 : 16), number_format($valores[$i], 1), $tealOscuro, $alinear);
            }
        }

        // Etiquetas del eje X (períodos): la primera y la última se alinean hacia
        // adentro para que no queden cortadas por el borde del lienzo.
        $ultimoLabel = count($labels) - 1;
        foreach ($labels as $i => $lab) {
            $x = $tipo === 'barra' ? $padL + $i * ($plotW / $n) + ($plotW / $n) / 2 : $padL + $i * $espacio;
            $alinear = $i === 0 ? 'izquierda' : ($i === $ultimoLabel ? 'derecha' : 'centro');
            $xEtiq   = $i === 0 ? (int) $x - 4 : ((int) $i === $ultimoLabel ? (int) $x + 4 : (int) $x);
            $this->texto($img, self::FUENTE_REGULAR, 15, $xEtiq, $padT + $plotH + 18, $lab, $gris, $alinear);
        }

        if ($unidad !== '') {
            $this->texto($img, self::FUENTE_REGULAR, 15, $padL, 8, 'Unidad: ' . $unidad, $gris, 'izquierda');
        }

        return $this->exportarPng($img);
    }

    /** Rellena un rectángulo con un degradado vertical entre dos colores (barras con volumen). */
    private function rectanguloDegradado($img, int $x1, int $y1, int $x2, int $y2, int $colorArriba, int $colorAbajo): void {
        $alturaTotal = max($y2 - $y1, 1);
        [$r1, $g1, $b1] = [($colorArriba >> 16) & 0xFF, ($colorArriba >> 8) & 0xFF, $colorArriba & 0xFF];
        [$r2, $g2, $b2] = [($colorAbajo >> 16) & 0xFF, ($colorAbajo >> 8) & 0xFF, $colorAbajo & 0xFF];
        for ($y = $y1; $y <= $y2; $y++) {
            $t = ($y - $y1) / $alturaTotal;
            $color = imagecolorallocate(
                $img,
                (int) ($r1 + ($r2 - $r1) * $t),
                (int) ($g1 + ($g2 - $g1) * $t),
                (int) ($b1 + ($b2 - $b1) * $t)
            );
            imageline($img, $x1, $y, $x2, $y, $color);
        }
    }

    /** Escribe texto con TrueType (nítido) alineado a izquierda/centro/derecha sobre un punto (x,y). */
    private function texto($img, string $fuente, int $tamano, int $x, int $y, string $txt, int $color, string $alinear = 'izquierda'): void {
        $caja = imagettfbbox($tamano, 0, $fuente, $txt);
        $anchoTxt = $caja[2] - $caja[0];
        $altoTxt  = $caja[1] - $caja[7];
        $x0 = match ($alinear) {
            'centro'   => $x - (int) ($anchoTxt / 2),
            'derecha'  => $x - $anchoTxt,
            default    => $x,
        };
        imagettftext($img, $tamano, 0, $x0, $y + $altoTxt, $color, $fuente, $txt);
    }

    private function dibujarTorta($img, int $ancho, int $alto, array $labels, array $valores, int $texto, int $gris): void {
        $total = array_sum($valores) ?: 1;
        $cx = (int) ($ancho * 0.32); $cy = (int) ($alto / 2); $r = (int) ($alto * 0.36);

        // Paleta de colores distinguibles (misma familia de marca + variaciones de tono)
        $hexPaleta = ['1993b8', '198754', 'ffc107', 'dc3545', '6f42c1', 'fd7e14', '20c997', '6c757d'];
        $paleta = [];
        foreach ($hexPaleta as $hex) {
            $paleta[] = imagecolorallocate($img, hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2)));
        }

        $inicio = 0;
        foreach ($valores as $i => $v) {
            $barrido = $v / $total * 360;
            $color   = $paleta[$i % count($paleta)];
            imagefilledarc($img, $cx, $cy, $r * 2, $r * 2, (int) $inicio, (int) ($inicio + max($barrido, 0.5)), $color, IMG_ARC_PIE);
            $inicio += $barrido;
        }
        // Borde blanco entre porciones para separarlas visualmente
        $inicio = 0;
        imagesetthickness($img, 4);
        foreach ($valores as $v) {
            $barrido = $v / $total * 360;
            $rad = deg2rad($inicio);
            imageline($img, $cx, $cy, (int) ($cx + cos($rad) * $r), (int) ($cy + sin($rad) * $r), 0xFFFFFF);
            $inicio += $barrido;
        }

        // Leyenda: una sola línea por período (nombre + valor + porcentaje) para
        // evitar que el texto de renglones distintos se superponga.
        $alturaEntrada = 46;
        $ly = $cy - (int) (count($labels) * $alturaEntrada / 2);
        foreach ($labels as $i => $lab) {
            $color = $paleta[$i % count($paleta)];
            $pct   = round($valores[$i] / $total * 100, 1);
            imagefilledrectangle($img, $cx + $r + 50, $ly + 4, $cx + $r + 70, $ly + 24, $color);
            $this->texto($img, self::FUENTE_NEGRITA, 16, $cx + $r + 80, $ly, $lab . ':', $texto);
            $this->texto($img, self::FUENTE_REGULAR, 15, $cx + $r + 80, $ly + 24, number_format($valores[$i], 1) . " ($pct%)", $gris);
            $ly += $alturaEntrada;
        }
    }

    private function exportarPng($img): string {
        ob_start();
        imagepng($img);
        $png = ob_get_clean();
        imagedestroy($img);
        return 'data:image/png;base64,' . base64_encode($png);
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
