<?php

class DocumentosControlador {

    private PDO $db;

    private const TIPOS_PERMITIDOS = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'image/jpeg',
        'image/png',
        'application/zip',
        'application/x-zip-compressed',
    ];
    private const EXTENSIONES_PERMITIDAS = ['pdf','doc','docx','xls','xlsx','jpg','jpeg','png','zip'];
    private const TAMANO_MAX  = 10 * 1024 * 1024;
    private const DIR_UPLOADS = __DIR__ . '/../uploads/documentos/';

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    private function rol(): string  { return $_SESSION['usuario_rol'] ?? ''; }
    private function esOp(): bool   { return in_array($this->rol(), ['operaciones','admin']); }

    public function index(?int $empresa_id = null): void {
        $empresa    = null;
        $requisitos = [];
        $documentos = [];
        $todasEmpresas = [];

        if ($this->esOp()) {
            if ($empresa_id) {
                $empresa    = $this->obtenerEmpresa($empresa_id);
                $requisitos = $this->obtenerRequisitos($empresa_id);
                $documentos = $this->obtenerDocumentos($empresa_id);
            } else {
                // Sin empresa seleccionada: mostrar listado
                $todasEmpresas = $this->db->query("
                    SELECT e.id, e.razon_social, e.nit,
                           COUNT(d.id) AS total_docs
                    FROM empresas e
                    LEFT JOIN documentos d ON d.empresa_id = e.id
                    GROUP BY e.id ORDER BY e.razon_social ASC
                ")->fetchAll(PDO::FETCH_ASSOC);
            }
        } else {
            // Usuario: usa su empresa de sesión
            $eid = (int)($_SESSION['usuario_empresa_id'] ?? 0);
            if ($eid) {
                $empresa    = $this->obtenerEmpresa($eid);
                $documentos = $this->obtenerDocumentos($eid);
            }
        }

        require_once __DIR__ . '/../vista/modulos/documentos/index.php';
    }

    public function subir(?int $empresa_id = null): void {
        // Solo operaciones puede subir
        if (!$this->esOp()) {
            header('Location: index.php?modulo=documentos');
            exit;
        }
        if (!$empresa_id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?modulo=documentos');
            exit;
        }

        $requisito_id = (int)($_POST['requisito_id'] ?? 0);
        if (!$requisito_id) {
            $_SESSION['flash_error'] = 'Debes seleccionar un requisito.';
            header('Location: index.php?modulo=documentos&accion=ver&id=' . $empresa_id);
            exit;
        }

        if (empty($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash_error'] = 'Error al recibir el archivo. Inténtalo de nuevo.';
            header('Location: index.php?modulo=documentos&accion=ver&id=' . $empresa_id);
            exit;
        }

        $archivo = $_FILES['archivo'];
        $ext     = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $mime    = mime_content_type($archivo['tmp_name']);

        if (!in_array($ext, self::EXTENSIONES_PERMITIDAS, true) || !in_array($mime, self::TIPOS_PERMITIDOS, true)) {
            $_SESSION['flash_error'] = 'Tipo de archivo no permitido. Se aceptan: PDF, Word, Excel, JPG, PNG y ZIP.';
            header('Location: index.php?modulo=documentos&accion=ver&id=' . $empresa_id);
            exit;
        }

        if ($archivo['size'] > self::TAMANO_MAX) {
            $_SESSION['flash_error'] = 'El archivo supera el tamaño máximo permitido (10 MB).';
            header('Location: index.php?modulo=documentos&accion=ver&id=' . $empresa_id);
            exit;
        }

        $dir = self::DIR_UPLOADS . $empresa_id . '/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $descripcion = trim($_POST['descripcion'] ?? '');

        $nombreGuardado = uniqid($empresa_id . '_', true) . '.' . $ext;
        if (!move_uploaded_file($archivo['tmp_name'], $dir . $nombreGuardado)) {
            $_SESSION['flash_error'] = 'No se pudo guardar el archivo en el servidor.';
            header('Location: index.php?modulo=documentos&accion=ver&id=' . $empresa_id);
            exit;
        }

        $this->db->prepare("
            INSERT INTO documentos (empresa_id, requisito_id, nombre_original, descripcion, nombre_guardado, tipo_mime, tamano, subido_por)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ")->execute([
            $empresa_id, $requisito_id,
            $archivo['name'], $descripcion ?: null, $nombreGuardado,
            $mime, $archivo['size'],
            $_SESSION['usuario_id'] ?? null,
        ]);

        $_SESSION['flash_success'] = 'Documento subido correctamente.';
        header('Location: index.php?modulo=documentos&accion=ver&id=' . $empresa_id);
        exit;
    }

    public function ver(?int $empresa_id = null): void {
        // Alias de index con empresa_id
        $this->index($empresa_id);
    }

    public function descargar(?int $id): void {
        if (!$id) { header('Location: index.php?modulo=documentos'); exit; }

        $stmt = $this->db->prepare("SELECT * FROM documentos WHERE id = ?");
        $stmt->execute([$id]);
        $doc = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$doc) { header('Location: index.php?modulo=documentos'); exit; }

        // Verificar que el usuario tenga acceso a esta empresa
        if ($this->rol() === 'usuario') {
            $eid = (int)($_SESSION['usuario_empresa_id'] ?? 0);
            if ((int)$doc['empresa_id'] !== $eid) {
                header('Location: index.php?modulo=documentos');
                exit;
            }
        }

        $ruta = self::DIR_UPLOADS . $doc['empresa_id'] . '/' . $doc['nombre_guardado'];
        if (!file_exists($ruta)) {
            $_SESSION['flash_error'] = 'El archivo ya no está disponible en el servidor.';
            header('Location: index.php?modulo=documentos');
            exit;
        }

        header('Content-Type: ' . ($doc['tipo_mime'] ?: 'application/octet-stream'));
        header('Content-Disposition: attachment; filename="' . addslashes($doc['nombre_original']) . '"');
        header('Content-Length: ' . filesize($ruta));
        readfile($ruta);
        exit;
    }

    public function eliminar(?int $id): void {
        // Solo operaciones puede eliminar
        if (!$this->esOp() || !$id) {
            header('Location: index.php?modulo=documentos');
            exit;
        }

        $stmt = $this->db->prepare("SELECT * FROM documentos WHERE id = ?");
        $stmt->execute([$id]);
        $doc = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($doc) {
            $ruta = self::DIR_UPLOADS . $doc['empresa_id'] . '/' . $doc['nombre_guardado'];
            if (file_exists($ruta)) unlink($ruta);
            $this->db->prepare("DELETE FROM documentos WHERE id = ?")->execute([$id]);
            $_SESSION['flash_success'] = 'Documento eliminado correctamente.';
            header('Location: index.php?modulo=documentos&accion=ver&id=' . $doc['empresa_id']);
            exit;
        }

        header('Location: index.php?modulo=documentos');
        exit;
    }

    // --- Helpers privados ---

    private function obtenerEmpresa(int $id): array|false {
        $stmt = $this->db->prepare("SELECT id, razon_social, nit FROM empresas WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function obtenerRequisitos(int $empresa_id): array {
        $stmt = $this->db->prepare("
            SELECT r.id, r.nombre, et.nombre AS etapa_nombre
            FROM requisitos r
            JOIN etapas et ON et.id = r.etapa_id
            JOIN empresa_etapa_progreso ep ON ep.empresa_id = ? AND ep.etapa_id = r.etapa_id
            WHERE r.activo = 1
            ORDER BY et.orden ASC, r.nombre ASC
        ");
        $stmt->execute([$empresa_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function obtenerDocumentos(int $empresa_id): array {
        $stmt = $this->db->prepare("
            SELECT d.*, r.nombre AS requisito_nombre, et.nombre AS etapa_nombre,
                   u.nombre AS subido_por_nombre
            FROM documentos d
            JOIN requisitos r ON r.id = d.requisito_id
            JOIN etapas et    ON et.id = r.etapa_id
            LEFT JOIN usuarios u ON u.id = d.subido_por
            WHERE d.empresa_id = ?
            ORDER BY et.orden ASC, r.nombre ASC, d.created_at DESC
        ");
        $stmt->execute([$empresa_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $agrupados = [];
        foreach ($rows as $doc) {
            $agrupados[$doc['etapa_nombre']][$doc['requisito_nombre']][] = $doc;
        }
        return $agrupados;
    }
}
