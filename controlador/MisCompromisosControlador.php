<?php

/**
 * Vista del usuario de empresa (rol "usuario") sobre los compromisos de
 * comité donde él es el responsable. Permite actualizar estado/solución
 * y adjuntar documentos, todo en un solo POST (ver actualizar()).
 *
 * La identidad del compromiso "propio" se resuelve por nombre + empresa
 * de la sesión (no hay un usuario_id en comite_compromisos.responsable),
 * así que toda acción que modifique datos pasa primero por
 * ComitesModelo::obtenerCompromisoDeUsuario() para evitar que alguien
 * edite o descargue documentos de un compromiso ajeno.
 */
class MisCompromisosControlador {

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
    private const DIR_UPLOADS = __DIR__ . '/../uploads/compromisos/';

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    private function nombreUsuario(): string {
        return $_SESSION['usuario_nombre'] ?? '';
    }

    private function empresaId(): int {
        return (int) ($_SESSION['usuario_empresa_id'] ?? 0);
    }

    public function index(): void {
        require_once __DIR__ . '/../modelo/ComitesModelo.php';
        $modelo = new ComitesModelo($this->db);

        $compromisos = [];
        $historialPorCompromiso = [];

        if ($this->empresaId()) {
            $compromisos = $modelo->misCompromisos($this->nombreUsuario(), $this->empresaId());
            foreach ($compromisos as $c) {
                $historialPorCompromiso[$c['id']] = $modelo->historialCompromiso((int) $c['id']);
            }
        }

        require_once __DIR__ . '/../vista/modulos/mis_compromisos/index.php';
    }

    /**
     * Guarda estado + observaciones y, si viene un archivo, lo adjunta
     * a la MISMA actualización (un solo clic en "Guardar" = un registro
     * de historial con su(s) documento(s), en vez de dos acciones sueltas).
     * Bloqueada si el compromiso ya está "cumplido" (ver vista para el aviso).
     */
    public function actualizar(?int $id): void {
        if (!$id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?modulo=mis-compromisos');
            exit;
        }

        require_once __DIR__ . '/../modelo/ComitesModelo.php';
        $modelo = new ComitesModelo($this->db);

        $compromiso = $modelo->obtenerCompromisoDeUsuario($id, $this->nombreUsuario(), $this->empresaId());
        if (!$compromiso) {
            header('Location: index.php?modulo=mis-compromisos');
            exit;
        }

        if ($compromiso['estado'] === 'cumplido') {
            $_SESSION['flash_error'] = 'Este compromiso ya está marcado como cumplido y no se puede modificar.';
            header('Location: index.php?modulo=mis-compromisos');
            exit;
        }

        $actualizacion_id = $modelo->actualizarCompromiso($id, $_POST, $_SESSION['usuario_id'] ?? null);

        // Si viene un archivo adjunto, se guarda ligado a esta misma actualización
        if (!empty($_FILES['archivo']) && $_FILES['archivo']['error'] !== UPLOAD_ERR_NO_FILE) {
            $error = $this->guardarArchivo($modelo, $id, $actualizacion_id, $_FILES['archivo']);
            if ($error) {
                $_SESSION['flash_error'] = $error;
                header('Location: index.php?modulo=mis-compromisos');
                exit;
            }
        }

        $_SESSION['flash_success'] = 'Compromiso actualizado.';
        header('Location: index.php?modulo=mis-compromisos');
        exit;
    }

    /**
     * Valida y guarda el archivo subido junto a una actualización del
     * compromiso. Devuelve un mensaje de error legible si algo falla,
     * o null si quedó guardado correctamente.
     */
    private function guardarArchivo(ComitesModelo $modelo, int $compromiso_id, int $actualizacion_id, array $archivo): ?string {
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            return 'Error al recibir el archivo. Inténtalo de nuevo.';
        }

        $ext  = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $mime = mime_content_type($archivo['tmp_name']);

        if (!in_array($ext, self::EXTENSIONES_PERMITIDAS, true) || !in_array($mime, self::TIPOS_PERMITIDOS, true)) {
            return 'Tipo de archivo no permitido. Se aceptan: PDF, Word, Excel, JPG, PNG y ZIP.';
        }

        if ($archivo['size'] > self::TAMANO_MAX) {
            return 'El archivo supera el tamaño máximo permitido (10 MB).';
        }

        $dir = self::DIR_UPLOADS . $compromiso_id . '/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $nombreGuardado = uniqid($compromiso_id . '_', true) . '.' . $ext;
        if (!move_uploaded_file($archivo['tmp_name'], $dir . $nombreGuardado)) {
            return 'No se pudo guardar el archivo en el servidor.';
        }

        $modelo->agregarDocumentoCompromiso($compromiso_id, [
            'actualizacion_id' => $actualizacion_id,
            'nombre_original'  => $archivo['name'],
            'nombre_guardado'  => $nombreGuardado,
            'tipo_mime'        => $mime,
            'tamano'           => $archivo['size'],
            'subido_por'       => $_SESSION['usuario_id'] ?? null,
        ]);

        return null;
    }

    public function descargarDocumento(?int $id): void {
        if (!$id) { header('Location: index.php?modulo=mis-compromisos'); exit; }

        require_once __DIR__ . '/../modelo/ComitesModelo.php';
        $modelo = new ComitesModelo($this->db);

        $doc = $modelo->obtenerDocumentoCompromiso($id);
        if (!$doc) { header('Location: index.php?modulo=mis-compromisos'); exit; }

        // Solo operaciones/admin, o el propio responsable del compromiso, pueden descargar
        $rol = $_SESSION['usuario_rol'] ?? '';
        if ($rol === 'usuario') {
            $compromiso = $modelo->obtenerCompromisoDeUsuario((int) $doc['compromiso_id'], $this->nombreUsuario(), $this->empresaId());
            if (!$compromiso) { header('Location: index.php?modulo=mis-compromisos'); exit; }
        }

        $ruta = self::DIR_UPLOADS . $doc['compromiso_id'] . '/' . $doc['nombre_guardado'];
        if (!file_exists($ruta)) {
            $_SESSION['flash_error'] = 'El archivo ya no está disponible en el servidor.';
            header('Location: index.php?modulo=mis-compromisos');
            exit;
        }

        header('Content-Type: ' . ($doc['tipo_mime'] ?: 'application/octet-stream'));
        header('Content-Disposition: attachment; filename="' . addslashes($doc['nombre_original']) . '"');
        header('Content-Length: ' . filesize($ruta));
        readfile($ruta);
        exit;
    }
}
