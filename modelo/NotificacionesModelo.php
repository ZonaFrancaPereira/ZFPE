<?php

require_once __DIR__ . '/ComitesModelo.php';

/**
 * Arma la lista de la campana de notificaciones a partir de datos que el
 * sistema ya guarda (sin tabla propia de eventos): vencimientos, cambios de
 * estado, documentos nuevos y compromisos de comité pendientes.
 *
 * Cada notificación recibe una "clave" estable (ligada al id real de su
 * origen: requisito+empresa, fila de historial, documento o compromiso) que
 * se guarda en `notificaciones_leidas` cuando el usuario abre la campana,
 * para poder distinguir leídas de no leídas en la siguiente carga.
 */
class NotificacionesModelo {

    private PDO $db;
    private ComitesModelo $comites;

    private const ESTADO_LABEL = [
        'pendiente'   => 'Pendiente',
        'en_progreso' => 'En progreso',
        'cumplido'    => 'Cumplido',
        'no_aplica'   => 'No aplica',
    ];

    // Menor número = más urgente = aparece primero en la campana.
    private const PRIORIDAD = [
        'alerta_critica'      => 1,
        'vencido'             => 1,
        'compromiso_vencido'  => 1,
        'alerta_potencial'    => 2,
        'por_vencer'          => 2,
        'compromiso'          => 2,
        'alerta_reunion'      => 2,
        'cambio'              => 3,
        'documento'           => 3,
    ];

    public function __construct(PDO $db) {
        $this->db      = $db;
        $this->comites = new ComitesModelo($db);
    }

    public function paraUsuario(int $usuarioId, string $rol, ?int $empresaId, string $nombreUsuario): array {
        $items = in_array($rol, ['admin', 'operaciones'], true)
            ? $this->paraOperaciones($usuarioId, $nombreUsuario)
            : ($empresaId ? $this->paraCliente($usuarioId, $empresaId, $nombreUsuario) : []);

        if (empty($items)) return [];

        $leidas = $this->leidasDe($usuarioId, array_column($items, 'clave'));
        foreach ($items as &$item) {
            $item['leido'] = in_array($item['clave'], $leidas, true);
        }
        unset($item);

        return $items;
    }

    /** Marca como leídas todas las claves indicadas para este usuario (idempotente). */
    public function marcarLeidas(int $usuarioId, array $claves): void {
        $claves = array_values(array_unique(array_filter($claves)));
        if (empty($claves)) return;

        $stmt = $this->db->prepare("
            INSERT IGNORE INTO notificaciones_leidas (usuario_id, clave) VALUES (?, ?)
        ");
        foreach ($claves as $clave) {
            $stmt->execute([$usuarioId, $clave]);
        }
    }

    private function leidasDe(int $usuarioId, array $claves): array {
        $claves = array_values(array_unique(array_filter($claves)));
        if (empty($claves)) return [];

        $in   = implode(',', array_fill(0, count($claves), '?'));
        $stmt = $this->db->prepare("
            SELECT clave FROM notificaciones_leidas WHERE usuario_id = ? AND clave IN ($in)
        ");
        $stmt->execute(array_merge([$usuarioId], $claves));
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    private function paraCliente(int $usuarioId, int $empresaId, string $nombreUsuario): array {
        $items = [];

        // Alertas ejecutivas de mi empresa: las veo si me asignaron explícitamente,
        // o si nadie fue asignado y yo soy el/la gerente de la empresa.
        require_once __DIR__ . '/AlertasModelo.php';
        $stmtGerente = $this->db->prepare("SELECT es_gerente FROM usuarios WHERE id = ?");
        $stmtGerente->execute([$usuarioId]);
        $esGerente = (bool) $stmtGerente->fetchColumn();

        foreach ((new AlertasModelo($this->db))->listarPorEmpresa($empresaId) as $a) {
            $esNotificable = in_array($a['prioridad'], ['alta', 'media'], true) || $a['tipo'] === 'reunion';
            if (!$esNotificable) continue;

            $destinatarios = AlertasModelo::idsDesdeConcat($a['destinatario_ids'] ?? null);
            $meVeToca = !empty($destinatarios) ? in_array($usuarioId, $destinatarios, true) : $esGerente;
            if (!$meVeToca) continue;

            $items[] = $this->itemAlertaEjecutiva($a, 'index.php?modulo=reportes&id=' . $empresaId);
        }

        // Compromisos de comité donde soy responsable y aún no están cumplidos
        foreach ($this->comites->misCompromisos($nombreUsuario, $empresaId) as $c) {
            if ($c['estado'] === 'cumplido') continue;
            $items[] = $this->itemCompromiso($c, 'index.php?modulo=mis-compromisos#compromiso-' . $c['id']);
        }

        // Requisitos vencidos / por vencer (30 días)
        $stmt = $this->db->prepare("
            SELECT r.id AS requisito_id, r.nombre AS requisito, ere.fecha_vencimiento
            FROM empresa_requisito_estado ere
            JOIN requisitos r ON r.id = ere.requisito_id
            WHERE ere.empresa_id = ?
              AND ere.fecha_vencimiento IS NOT NULL
              AND ere.fecha_vencimiento <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
              AND ere.estado NOT IN ('cumplido','no_aplica')
            ORDER BY ere.fecha_vencimiento ASC
            LIMIT 8
        ");
        $stmt->execute([$empresaId]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $items[] = $this->itemVencimiento($r, $empresaId, 'index.php?modulo=cronograma&id=' . $empresaId . '#req-' . $r['requisito_id']);
        }

        // Cambios de estado hechos por otra persona (operaciones) en mis requisitos
        $stmt = $this->db->prepare("
            SELECT h.id, h.requisito_id, r.nombre AS requisito, h.estado_nuevo, h.created_at,
                   u.nombre AS usuario_nombre
            FROM empresa_requisito_historial h
            JOIN requisitos r ON r.id = h.requisito_id
            LEFT JOIN usuarios u ON u.id = h.registrado_por
            WHERE h.empresa_id = ?
              AND (h.registrado_por IS NULL OR h.registrado_por != ?)
              AND h.created_at >= DATE_SUB(NOW(), INTERVAL 14 DAY)
            ORDER BY h.created_at DESC
            LIMIT 5
        ");
        $stmt->execute([$empresaId, $usuarioId]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $h) {
            $items[] = $this->itemCambioEstado($h, 'index.php?modulo=cronograma&id=' . $empresaId . '#req-' . $h['requisito_id']);
        }

        return $this->ordenar($items);
    }

    private function paraOperaciones(int $usuarioId, string $nombreUsuario): array {
        $items = [];

        // Alertas ejecutivas activas (riesgo potencial/crítico o reuniones; "sin novedad" no notifica).
        // Si la alerta tiene destinatarios puntuales, solo la ve quien esté en esa lista.
        require_once __DIR__ . '/AlertasModelo.php';
        foreach ((new AlertasModelo($this->db))->listarGlobal() as $a) {
            $esNotificable = in_array($a['prioridad'], ['alta', 'media'], true) || $a['tipo'] === 'reunion';
            if (!$esNotificable) continue;

            $destinatarios = AlertasModelo::idsDesdeConcat($a['destinatario_ids'] ?? null);
            if (!empty($destinatarios) && !in_array($usuarioId, $destinatarios, true)) continue;

            $items[] = $this->itemAlertaEjecutiva($a, 'index.php?modulo=reportes&id=' . $a['empresa_id']);
        }

        // Compromisos de comité donde soy responsable (cualquier empresa) y no están cumplidos
        foreach ($this->comites->misCompromisosGlobal($nombreUsuario) as $c) {
            if ($c['estado'] === 'cumplido') continue;
            $items[] = $this->itemCompromiso($c, 'index.php?modulo=comites&accion=ver&id=' . $c['comite_id'] . '#compromiso-' . $c['id'], $c['empresa_nombre'] ?? null);
        }

        // Requisitos vencidos / por vencer en cualquier empresa
        $stmt = $this->db->prepare("
            SELECT r.id AS requisito_id, r.nombre AS requisito, ere.fecha_vencimiento,
                   e.id AS empresa_id, e.razon_social AS empresa_nombre
            FROM empresa_requisito_estado ere
            JOIN requisitos r ON r.id = ere.requisito_id
            JOIN empresas e  ON e.id = ere.empresa_id
            WHERE ere.fecha_vencimiento IS NOT NULL
              AND ere.fecha_vencimiento <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
              AND ere.estado NOT IN ('cumplido','no_aplica')
            ORDER BY ere.fecha_vencimiento ASC
            LIMIT 8
        ");
        $stmt->execute();
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $items[] = $this->itemVencimiento($r, (int) $r['empresa_id'], 'index.php?modulo=seguimiento&id=' . $r['empresa_id'] . '#req-' . $r['requisito_id'], $r['empresa_nombre']);
        }

        // Cambios de estado hechos por otra persona, en cualquier empresa
        $stmt = $this->db->prepare("
            SELECT h.id, h.requisito_id, r.nombre AS requisito, h.estado_nuevo, h.created_at,
                   u.nombre AS usuario_nombre, e.id AS empresa_id, e.razon_social AS empresa_nombre
            FROM empresa_requisito_historial h
            JOIN requisitos r ON r.id = h.requisito_id
            JOIN empresas e   ON e.id = h.empresa_id
            LEFT JOIN usuarios u ON u.id = h.registrado_por
            WHERE (h.registrado_por IS NULL OR h.registrado_por != ?)
              AND h.created_at >= DATE_SUB(NOW(), INTERVAL 14 DAY)
            ORDER BY h.created_at DESC
            LIMIT 5
        ");
        $stmt->execute([$usuarioId]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $h) {
            $items[] = $this->itemCambioEstado($h, 'index.php?modulo=seguimiento&id=' . $h['empresa_id'] . '#req-' . $h['requisito_id'], $h['empresa_nombre']);
        }

        // Documentos nuevos subidos por otra persona, en cualquier empresa
        $stmt = $this->db->prepare("
            SELECT d.id, d.nombre_original, d.created_at, e.id AS empresa_id, e.razon_social AS empresa_nombre
            FROM documentos d
            JOIN empresas e ON e.id = d.empresa_id
            WHERE (d.subido_por IS NULL OR d.subido_por != ?)
              AND d.created_at >= DATE_SUB(NOW(), INTERVAL 14 DAY)
            ORDER BY d.created_at DESC
            LIMIT 5
        ");
        $stmt->execute([$usuarioId]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $d) {
            $items[] = [
                'tipo'  => 'documento',
                'clave' => 'doc-' . $d['id'],
                'texto' => 'Nuevo documento: «' . $d['nombre_original'] . '»',
                'meta'  => ($d['empresa_nombre'] ? $d['empresa_nombre'] . ' · ' : '') . $this->tiempoRelativo($d['created_at']),
                'url'   => 'index.php?modulo=documentos&accion=ver&id=' . $d['empresa_id'],
                'fecha' => $d['created_at'],
            ];
        }

        return $this->ordenar($items);
    }

    private function ordenar(array $items, int $limite = 15): array {
        usort($items, function (array $a, array $b): int {
            $pa = self::PRIORIDAD[$a['tipo']] ?? 9;
            $pb = self::PRIORIDAD[$b['tipo']] ?? 9;
            if ($pa !== $pb) return $pa <=> $pb;
            // Vencimientos/compromisos: el más urgente (fecha más próxima o vencida) primero.
            // Cambios/documentos: el más reciente primero.
            return $pa <= 2
                ? strtotime($a['fecha']) <=> strtotime($b['fecha'])
                : strtotime($b['fecha']) <=> strtotime($a['fecha']);
        });
        return array_slice($items, 0, $limite);
    }

    // --- Helpers de armado de cada tipo de notificación ---

    private function itemAlertaEjecutiva(array $a, string $url): array {
        $prefijo = !empty($a['empresa_nombre']) ? $a['empresa_nombre'] . ' · ' : '';

        if ($a['tipo'] === 'reunion') {
            return [
                'tipo'  => 'alerta_reunion',
                'clave' => 'alerta-' . $a['id'],
                'texto' => 'Reunión: ' . $a['mensaje'],
                'meta'  => $prefijo . $this->tiempoRelativo($a['creado_en']),
                'url'   => $url,
                'fecha' => $a['creado_en'],
            ];
        }

        $esCritica = $a['prioridad'] === 'alta';
        return [
            'tipo'  => $esCritica ? 'alerta_critica' : 'alerta_potencial',
            'clave' => 'alerta-' . $a['id'],
            'texto' => ($esCritica ? 'Decisión requerida: ' : 'Riesgo potencial: ') . $a['mensaje'],
            'meta'  => $prefijo . $this->tiempoRelativo($a['creado_en']),
            'url'   => $url,
            'fecha' => $a['creado_en'],
        ];
    }

    private function itemVencimiento(array $r, int $empresaId, string $url, ?string $empresaNombre = null): array {
        $dias    = (int) floor((strtotime($r['fecha_vencimiento']) - strtotime(date('Y-m-d'))) / 86400);
        $vencido = $dias < 0;
        $prefijo = $empresaNombre ? $empresaNombre . ' · ' : '';
        return [
            'tipo'  => $vencido ? 'vencido' : 'por_vencer',
            'clave' => 'venc-' . $empresaId . '-' . $r['requisito_id'],
            'texto' => $vencido
                ? '«' . $r['requisito'] . '» está vencido (' . abs($dias) . ' día' . (abs($dias) === 1 ? '' : 's') . ')'
                : '«' . $r['requisito'] . '» vence en ' . $dias . ' día' . ($dias === 1 ? '' : 's'),
            'meta'  => $prefijo . date('d/m/Y', strtotime($r['fecha_vencimiento'])),
            'url'   => $url,
            'fecha' => $r['fecha_vencimiento'] . ' 00:00:00',
        ];
    }

    private function itemCambioEstado(array $h, string $url, ?string $empresaNombre = null): array {
        $autor   = $h['usuario_nombre'] ?: 'Operaciones';
        $estado  = self::ESTADO_LABEL[$h['estado_nuevo']] ?? $h['estado_nuevo'];
        $prefijo = $empresaNombre ? $empresaNombre . ' · ' : '';
        return [
            'tipo'  => 'cambio',
            'clave' => 'hist-' . $h['id'],
            'texto' => $autor . ' actualizó «' . $h['requisito'] . '» a ' . $estado,
            'meta'  => $prefijo . $this->tiempoRelativo($h['created_at']),
            'url'   => $url,
            'fecha' => $h['created_at'],
        ];
    }

    private function itemCompromiso(array $c, string $url, ?string $empresaNombre = null): array {
        $vencido = !empty($c['fecha_limite']) && $c['fecha_limite'] < date('Y-m-d');
        $prefijo = $empresaNombre ? $empresaNombre . ' · ' : ($c['comite_titulo'] ?? '');
        return [
            'tipo'  => $vencido ? 'compromiso_vencido' : 'compromiso',
            'clave' => 'comp-' . $c['id'],
            'texto' => 'Decisión pendiente: «' . $c['descripcion'] . '»',
            'meta'  => $prefijo . ($c['fecha_limite'] ? ' · vence ' . date('d/m/Y', strtotime($c['fecha_limite'])) : ' · sin fecha límite'),
            'url'   => $url,
            'fecha' => $c['fecha_limite'] ? $c['fecha_limite'] . ' 00:00:00' : date('Y-m-d H:i:s'),
        ];
    }

    public function tiempoRelativo(string $datetime): string {
        $diff = time() - strtotime($datetime);
        if ($diff < 60)     return 'hace un momento';
        if ($diff < 3600)   return 'hace ' . floor($diff / 60) . ' min';
        if ($diff < 86400)  return 'hace ' . floor($diff / 3600) . ' h';
        $dias = floor($diff / 86400);
        return $dias === 1.0 ? 'hace 1 día' : 'hace ' . $dias . ' días';
    }
}
