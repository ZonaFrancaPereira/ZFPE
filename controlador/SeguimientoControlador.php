<?php

require_once __DIR__ . '/ControladorBase.php';

class SeguimientoControlador extends ControladorBase {

    public function index(?int $empresa_id): void {
        if (!$empresa_id) { header('Location: index.php?modulo=empresas'); exit; }

        $empresa = $this->db->prepare("SELECT id, razon_social, nit FROM empresas WHERE id = ?");
        $empresa->execute([$empresa_id]);
        $empresa = $empresa->fetch();

        if (!$empresa) { header('Location: index.php?modulo=empresas'); exit; }

        // Sincronizar etapas nuevas: insertar filas faltantes en empresa_etapa_progreso
        $this->sincronizarEtapas($empresa_id);

        // Etapas asignadas a la empresa con su progreso
        $stmt = $this->db->prepare("
            SELECT et.*,
                   f.id     AS fase_id,
                   f.nombre AS fase_nombre,
                   f.orden  AS fase_orden,
                   COALESCE(ep.porcentaje_avance, 0)    AS porcentaje_avance,
                   COALESCE(ep.estado, 'pendiente')     AS estado_progreso
            FROM etapas et
            LEFT JOIN fases f ON f.id = et.fase_id
            LEFT JOIN empresa_etapa_progreso ep ON ep.etapa_id = et.id AND ep.empresa_id = ?
            WHERE et.activo = 1
            ORDER BY COALESCE(f.orden, 999) ASC, et.orden ASC
        ");
        $stmt->execute([$empresa_id]);
        $etapas = $stmt->fetchAll();

        // Requisitos con estado por empresa e ítems
        $stmt = $this->db->prepare("
            SELECT r.*,
                   en.nombre AS entidad_nombre,
                   COALESCE(ere.estado, 'pendiente')       AS estado_req,
                   COALESCE(ere.observaciones, '')         AS observaciones,
                   ere.fecha_vencimiento,
                   ere.fecha_cumplimiento,
                   ere.aprobado,
                   ere.id AS estado_id
            FROM requisitos r
            LEFT JOIN empresa_etapa_progreso ep ON ep.etapa_id = r.etapa_id AND ep.empresa_id = ?
            LEFT JOIN entidades en ON en.id = r.entidad_id
            LEFT JOIN empresa_requisito_estado ere ON ere.requisito_id = r.id AND ere.empresa_id = ?
            WHERE r.activo = 1
            ORDER BY r.etapa_id ASC, r.nombre ASC
        ");
        $stmt->execute([$empresa_id, $empresa_id]);
        $requisitos = $stmt->fetchAll();

        // Ítems con su estado por empresa
        $stmt = $this->db->prepare("
            SELECT ri.*,
                   COALESCE(ei.cumplido, 0)        AS cumplido,
                   ei.observaciones                AS obs_item,
                   ei.fecha_cumplimiento
            FROM requisito_items ri
            JOIN requisitos r ON r.id = ri.requisito_id
            LEFT JOIN empresa_etapa_progreso ep ON ep.etapa_id = r.etapa_id AND ep.empresa_id = ?
            LEFT JOIN empresa_requisito_item_estado ei ON ei.requisito_item_id = ri.id AND ei.empresa_id = ?
            WHERE ri.activo = 1
            ORDER BY ri.requisito_id ASC, ri.orden ASC
        ");
        $stmt->execute([$empresa_id, $empresa_id]);
        $items = $stmt->fetchAll();

        // Agrupar ítems por requisito_id
        $itemsPorRequisito = [];
        foreach ($items as $item) {
            $itemsPorRequisito[$item['requisito_id']][] = $item;
        }

        // Agrupar requisitos por etapa_id
        $requisitosPorEtapa = [];
        foreach ($requisitos as $req) {
            $requisitosPorEtapa[$req['etapa_id']][] = $req;
        }

        // Documentos subidos por requisito para esta empresa
        $docsRaw = $this->db->prepare("
            SELECT d.id, d.requisito_id, d.nombre_original, d.descripcion, d.tamano, d.tipo_mime, d.created_at,
                   u.nombre AS subido_por_nombre
            FROM documentos d
            LEFT JOIN usuarios u ON u.id = d.subido_por
            WHERE d.empresa_id = ?
            ORDER BY d.created_at DESC
        ");
        $docsRaw->execute([$empresa_id]);
        $documentosPorRequisito = [];
        foreach ($docsRaw->fetchAll(PDO::FETCH_ASSOC) as $doc) {
            $documentosPorRequisito[$doc['requisito_id']][] = $doc;
        }

        // Historial de cambios por requisito para esta empresa
        $histRaw = $this->db->prepare("
            SELECT h.*, u.nombre AS registrado_por_nombre, d.nombre_original AS documento_nombre
            FROM empresa_requisito_historial h
            LEFT JOIN usuarios u ON u.id = h.registrado_por
            LEFT JOIN documentos d ON d.id = h.documento_id
            WHERE h.empresa_id = ?
            ORDER BY h.created_at DESC
        ");
        $histRaw->execute([$empresa_id]);
        $historialPorRequisito = [];
        foreach ($histRaw->fetchAll(PDO::FETCH_ASSOC) as $h) {
            $historialPorRequisito[$h['requisito_id']][] = $h;
        }

        require_once __DIR__ . '/../vista/modulos/seguimiento/index.php';
    }

    private const TIPOS_DOC     = ['application/pdf','application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'image/jpeg','image/png','application/zip','application/x-zip-compressed'];
    private const EXT_DOC       = ['pdf','doc','docx','xls','xlsx','jpg','jpeg','png','zip'];
    private const TAMANO_MAX    = 10 * 1024 * 1024;
    private const DIR_UPLOADS   = __DIR__ . '/../uploads/documentos/';

    public function guardar(?int $empresa_id): void {
        if (!$empresa_id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?modulo=empresas');
            exit;
        }

        $requisito_id    = (int) ($_POST['requisito_id'] ?? 0);
        $estado          = $_POST['estado_req'] ?? 'pendiente';
        $observaciones   = trim($_POST['observaciones'] ?? '');
        $fecha_vencimiento = $_POST['fecha_vencimiento'] ?? null;
        $items_cumplidos = $_POST['items'] ?? [];
        $documento_descripcion = trim($_POST['documento_descripcion'] ?? '');

        // Estado/observaciones previos, para poder detectar cambios y registrar historial
        $stmtPrevio = $this->db->prepare("
            SELECT estado, observaciones FROM empresa_requisito_estado
            WHERE empresa_id = ? AND requisito_id = ?
        ");
        $stmtPrevio->execute([$empresa_id, $requisito_id]);
        $estadoPrevio = $stmtPrevio->fetch() ?: ['estado' => 'pendiente', 'observaciones' => ''];

        $documento_id = null;

        // Subida de documento si viene archivo
        if (!empty($_FILES['documento_soporte']) && $_FILES['documento_soporte']['error'] === UPLOAD_ERR_OK) {
            $archivo = $_FILES['documento_soporte'];
            $ext     = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
            $mime    = mime_content_type($archivo['tmp_name']);

            if (!in_array($ext, self::EXT_DOC, true) || !in_array($mime, self::TIPOS_DOC, true)) {
                $_SESSION['flash_error'] = 'Tipo de archivo no permitido para el documento soporte.';
                header("Location: index.php?modulo=seguimiento&id=$empresa_id#req-$requisito_id");
                exit;
            }
            if ($archivo['size'] > self::TAMANO_MAX) {
                $_SESSION['flash_error'] = 'El archivo supera el tamaño máximo de 10 MB.';
                header("Location: index.php?modulo=seguimiento&id=$empresa_id#req-$requisito_id");
                exit;
            }

            $dir = self::DIR_UPLOADS . $empresa_id . '/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);

            $nombreGuardado = uniqid($empresa_id . '_', true) . '.' . $ext;
            if (move_uploaded_file($archivo['tmp_name'], $dir . $nombreGuardado)) {
                $this->db->prepare("
                    INSERT INTO documentos (empresa_id, requisito_id, nombre_original, descripcion, nombre_guardado, tipo_mime, tamano, subido_por)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ")->execute([
                    $empresa_id, $requisito_id,
                    $archivo['name'], $documento_descripcion ?: null, $nombreGuardado,
                    $mime, $archivo['size'],
                    $_SESSION['usuario_id'] ?? null,
                ]);
                $documento_id = (int) $this->db->lastInsertId();
            }
        }

        if (!$requisito_id) {
            header("Location: index.php?modulo=seguimiento&id=$empresa_id");
            exit;
        }

        // La fecha de vencimiento se fija una sola vez: si ya existe, no se sobrescribe.
        $stmtVenc = $this->db->prepare("
            SELECT fecha_vencimiento FROM empresa_requisito_estado
            WHERE empresa_id = ? AND requisito_id = ?
        ");
        $stmtVenc->execute([$empresa_id, $requisito_id]);
        $fechaVencExistente = $stmtVenc->fetchColumn();
        if ($fechaVencExistente) {
            $fecha_vencimiento = $fechaVencExistente;
        }

        // Actualizar cada ítem del requisito
        $todosLosItems = $this->db->prepare("
            SELECT id FROM requisito_items WHERE requisito_id = ? AND activo = 1
        ");
        $todosLosItems->execute([$requisito_id]);
        $todosLosItems = $todosLosItems->fetchAll(PDO::FETCH_COLUMN);

        $stmtItem = $this->db->prepare("
            INSERT INTO empresa_requisito_item_estado
              (empresa_id, requisito_item_id, cumplido, fecha_cumplimiento, registrado_por)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
              cumplido           = VALUES(cumplido),
              fecha_cumplimiento = VALUES(fecha_cumplimiento),
              registrado_por     = VALUES(registrado_por)
        ");

        foreach ($todosLosItems as $item_id) {
            $cumplido = in_array((string) $item_id, array_keys($items_cumplidos)) ? 1 : 0;
            $fechaCumplido = $cumplido ? date('Y-m-d H:i:s') : null;
            $stmtItem->execute([$empresa_id, $item_id, $cumplido, $fechaCumplido, $_SESSION['usuario_id']]);
        }

        // Recalcular estado del requisito automáticamente
        $estadoFinal = $this->recalcularRequisito($empresa_id, $requisito_id, $estado, $observaciones, $fecha_vencimiento ?: null);

        // Registrar en el historial solo si hubo un cambio real (estado, observaciones o archivo nuevo)
        $cambioEstado = $estadoFinal !== $estadoPrevio['estado'];
        $cambioObs    = trim((string) $estadoPrevio['observaciones']) !== $observaciones;
        if ($cambioEstado || $cambioObs || $documento_id) {
            $this->registrarHistorial($empresa_id, $requisito_id, $estadoPrevio['estado'], $estadoFinal, $observaciones, $documento_id);
        }

        // Recalcular progreso de la etapa
        $etapa = $this->db->prepare("SELECT etapa_id FROM requisitos WHERE id = ?");
        $etapa->execute([$requisito_id]);
        $etapa_id = (int) $etapa->fetchColumn();
        $this->recalcularEtapa($empresa_id, $etapa_id);

        $_SESSION['flash_success'] = 'Seguimiento actualizado correctamente.';
        header("Location: index.php?modulo=seguimiento&id=$empresa_id#req-$requisito_id");
        exit;
    }

    private function recalcularRequisito(int $empresa_id, int $requisito_id, string $estado_manual, string $observaciones, ?string $fecha_vencimiento): string {
        // Si el estado es no_aplica, respetar la decisión manual
        if ($estado_manual === 'no_aplica') {
            $this->upsertRequisito($empresa_id, $requisito_id, 'no_aplica', $observaciones, $fecha_vencimiento);
            return 'no_aplica';
        }

        // Verificar si todos los ítems obligatorios están cumplidos
        $stmt = $this->db->prepare("
            SELECT
              COUNT(*) AS total_oblig,
              SUM(COALESCE(ei.cumplido,0)) AS cumplidos_oblig
            FROM requisito_items ri
            LEFT JOIN empresa_requisito_item_estado ei
              ON ei.requisito_item_id = ri.id AND ei.empresa_id = ?
            WHERE ri.requisito_id = ? AND ri.activo = 1 AND ri.obligatorio = 1
        ");
        $stmt->execute([$empresa_id, $requisito_id]);
        $resultado = $stmt->fetch();

        $totalOblig = (int) $resultado['total_oblig'];
        $cumplidos  = (int) $resultado['cumplidos_oblig'];

        if ($totalOblig === 0) {
            // Sin ítems obligatorios que validar: respetar la selección manual del usuario
            $estado = $estado_manual;
        } elseif ($cumplidos >= $totalOblig) {
            $estado = 'cumplido';
        } else {
            $stmtAlguno = $this->db->prepare("
                SELECT COUNT(*) FROM empresa_requisito_item_estado ei
                JOIN requisito_items ri ON ri.id = ei.requisito_item_id
                WHERE ri.requisito_id = ? AND ei.empresa_id = ? AND ei.cumplido = 1
            ");
            $stmtAlguno->execute([$requisito_id, $empresa_id]);
            $algunoCumplido = (int) $stmtAlguno->fetchColumn();

            $estado = $algunoCumplido > 0
                ? 'en_progreso'
                : ($estado_manual === 'cumplido' ? 'en_progreso' : $estado_manual);
        }

        $this->upsertRequisito($empresa_id, $requisito_id, $estado, $observaciones, $fecha_vencimiento);
        return $estado;
    }

    private function registrarHistorial(int $empresa_id, int $requisito_id, ?string $estadoAnterior, string $estadoNuevo, string $observaciones, ?int $documento_id): void {
        $fechaCumplimiento = $estadoNuevo === 'cumplido' ? date('Y-m-d H:i:s') : null;
        $this->db->prepare("
            INSERT INTO empresa_requisito_historial
              (empresa_id, requisito_id, estado_anterior, estado_nuevo, observaciones, fecha_cumplimiento, documento_id, registrado_por)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ")->execute([
            $empresa_id, $requisito_id, $estadoAnterior, $estadoNuevo,
            $observaciones ?: null, $fechaCumplimiento, $documento_id,
            $_SESSION['usuario_id'] ?? null,
        ]);
    }

    private function upsertRequisito(int $empresa_id, int $requisito_id, string $estado, string $observaciones, ?string $fecha_vencimiento): void {
        $stmt = $this->db->prepare("
            INSERT INTO empresa_requisito_estado
              (empresa_id, requisito_id, estado, observaciones, fecha_vencimiento,
               fecha_cumplimiento)
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
              estado             = VALUES(estado),
              observaciones      = VALUES(observaciones),
              fecha_vencimiento  = VALUES(fecha_vencimiento),
              fecha_cumplimiento = IF(VALUES(estado) = 'cumplido', NOW(), fecha_cumplimiento)
        ");
        $stmt->execute([
            $empresa_id, $requisito_id, $estado,
            $observaciones ?: null,
            $fecha_vencimiento,
            $estado === 'cumplido' ? date('Y-m-d H:i:s') : null,
        ]);
    }

    private function recalcularEtapa(int $empresa_id, int $etapa_id): void {
        // % de avance = (requisitos cumplidos o no_aplica / total requisitos activos en etapa) * 100
        $stmt = $this->db->prepare("
            SELECT
              COUNT(*) AS total,
              SUM(CASE WHEN COALESCE(ere.estado,'pendiente') IN ('cumplido','no_aplica') THEN 1 ELSE 0 END) AS avanzados
            FROM requisitos r
            LEFT JOIN empresa_requisito_estado ere ON ere.requisito_id = r.id AND ere.empresa_id = ?
            WHERE r.etapa_id = ? AND r.activo = 1
        ");
        $stmt->execute([$empresa_id, $etapa_id]);
        $res = $stmt->fetch();

        $total    = (int) $res['total'];
        $avanzados = (int) $res['avanzados'];
        $porcentaje = $total > 0 ? round(($avanzados / $total) * 100, 2) : 0;

        $estado = $porcentaje >= 100 ? 'completa'
                : ($porcentaje > 0   ? 'en_progreso'
                                     : 'pendiente');

        $stmt = $this->db->prepare("
            INSERT INTO empresa_etapa_progreso (empresa_id, etapa_id, porcentaje_avance, estado, fecha_completado, fecha_inicio)
            VALUES (?, ?, ?, ?, IF(? = 'completa', NOW(), NULL), IF(? > 0, NOW(), NULL))
            ON DUPLICATE KEY UPDATE
                porcentaje_avance = VALUES(porcentaje_avance),
                estado            = VALUES(estado),
                fecha_completado  = IF(VALUES(estado) = 'completa', COALESCE(fecha_completado, NOW()), fecha_completado),
                fecha_inicio      = IF(fecha_inicio IS NULL AND VALUES(porcentaje_avance) > 0, NOW(), fecha_inicio)
        ");
        $stmt->execute([$empresa_id, $etapa_id, $porcentaje, $estado, $estado, $porcentaje]);
    }

    private function sincronizarEtapas(int $empresa_id): void {
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
    }
}
