<?php

class ConfiguracionControlador {

    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function index(): void {
        $totalFases       = (int) $this->db->query("SELECT COUNT(*) FROM fases")->fetchColumn();
        $totalEntidades   = (int) $this->db->query("SELECT COUNT(*) FROM entidades")->fetchColumn();
        $totalEtapas      = (int) $this->db->query("SELECT COUNT(*) FROM etapas")->fetchColumn();
        $totalRequisitos  = (int) $this->db->query("SELECT COUNT(*) FROM requisitos")->fetchColumn();
        $totalItems       = (int) $this->db->query("SELECT COUNT(*) FROM requisito_items")->fetchColumn();
        $totalIndicadores = (int) $this->db->query("SELECT COUNT(*) FROM indicadores")->fetchColumn();
        require_once __DIR__ . '/../vista/modulos/configuracion/index.php';
    }

    // ── FASES ─────────────────────────────────────────────────

    public function fases(): void {
        $fases = $this->db->query("
            SELECT f.*, COUNT(e.id) AS total_etapas
            FROM fases f
            LEFT JOIN etapas e ON e.fase_id = f.id
            GROUP BY f.id
            ORDER BY f.orden ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
        require_once __DIR__ . '/../vista/modulos/configuracion/fases.php';
    }

    public function crearFase(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre      = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $orden       = (int) ($_POST['orden'] ?? 0);
            $activo      = isset($_POST['activo']) ? 1 : 0;

            if ($nombre === '') {
                $_SESSION['flash_error'] = 'El nombre es obligatorio.';
                header('Location: index.php?modulo=configuracion&accion=crear-fase');
                exit;
            }

            $this->db->prepare(
                "INSERT INTO fases (nombre, descripcion, orden, activo) VALUES (?, ?, ?, ?)"
            )->execute([$nombre, $descripcion ?: null, $orden, $activo]);

            $_SESSION['flash_success'] = 'Fase creada correctamente.';
            header('Location: index.php?modulo=configuracion&accion=fases');
            exit;
        }

        $siguienteOrden = (int) $this->db->query("SELECT COALESCE(MAX(orden),0)+1 FROM fases")->fetchColumn();
        require_once __DIR__ . '/../vista/modulos/configuracion/crear_fase.php';
    }

    public function editarFase(?int $id): void {
        if (!$id) { header('Location: index.php?modulo=configuracion&accion=fases'); exit; }

        $stmt = $this->db->prepare("SELECT * FROM fases WHERE id = ?");
        $stmt->execute([$id]);
        $fase = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$fase) {
            $_SESSION['flash_error'] = 'Fase no encontrada.';
            header('Location: index.php?modulo=configuracion&accion=fases');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre      = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $orden       = (int) ($_POST['orden'] ?? 0);
            $activo      = isset($_POST['activo']) ? 1 : 0;

            if ($nombre === '') {
                $_SESSION['flash_error'] = 'El nombre es obligatorio.';
                header("Location: index.php?modulo=configuracion&accion=editar-fase&id=$id");
                exit;
            }

            $this->db->prepare(
                "UPDATE fases SET nombre=?, descripcion=?, orden=?, activo=? WHERE id=?"
            )->execute([$nombre, $descripcion ?: null, $orden, $activo, $id]);

            $_SESSION['flash_success'] = 'Fase actualizada correctamente.';
            header('Location: index.php?modulo=configuracion&accion=fases');
            exit;
        }

        require_once __DIR__ . '/../vista/modulos/configuracion/editar_fase.php';
    }

    public function eliminarFase(?int $id): void {
        if (!$id) { header('Location: index.php?modulo=configuracion&accion=fases'); exit; }
        $this->db->prepare("DELETE FROM fases WHERE id = ?")->execute([$id]);
        $_SESSION['flash_success'] = 'Fase eliminada. Las etapas asociadas quedaron sin fase.';
        header('Location: index.php?modulo=configuracion&accion=fases');
        exit;
    }

    // ── ENTIDADES ──────────────────────────────────────────────

    public function entidades(): void {
        $entidades = $this->db->query("SELECT * FROM entidades ORDER BY nombre")->fetchAll();
        require_once __DIR__ . '/../vista/modulos/configuracion/entidades.php';
    }

    public function crearEntidad(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre      = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $activo      = isset($_POST['activo']) ? 1 : 0;

            if ($nombre === '') {
                $_SESSION['flash_error'] = 'El nombre es obligatorio.';
                header('Location: index.php?modulo=configuracion&accion=crear-entidad');
                exit;
            }

            $stmt = $this->db->prepare("INSERT INTO entidades (nombre, descripcion, activo) VALUES (?, ?, ?)");
            $stmt->execute([$nombre, $descripcion ?: null, $activo]);

            $_SESSION['flash_success'] = 'Entidad creada correctamente.';
            header('Location: index.php?modulo=configuracion&accion=entidades');
            exit;
        }

        require_once __DIR__ . '/../vista/modulos/configuracion/crear_entidad.php';
    }

    public function editarEntidad(?int $id): void {
        if (!$id) { header('Location: index.php?modulo=configuracion&accion=entidades'); exit; }

        $stmt = $this->db->prepare("SELECT * FROM entidades WHERE id = ?");
        $stmt->execute([$id]);
        $entidad = $stmt->fetch();

        if (!$entidad) {
            $_SESSION['flash_error'] = 'Entidad no encontrada.';
            header('Location: index.php?modulo=configuracion&accion=entidades');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre      = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $activo      = isset($_POST['activo']) ? 1 : 0;

            if ($nombre === '') {
                $_SESSION['flash_error'] = 'El nombre es obligatorio.';
                header("Location: index.php?modulo=configuracion&accion=editar-entidad&id=$id");
                exit;
            }

            $stmt = $this->db->prepare("UPDATE entidades SET nombre = ?, descripcion = ?, activo = ? WHERE id = ?");
            $stmt->execute([$nombre, $descripcion ?: null, $activo, $id]);

            $_SESSION['flash_success'] = 'Entidad actualizada correctamente.';
            header('Location: index.php?modulo=configuracion&accion=entidades');
            exit;
        }

        require_once __DIR__ . '/../vista/modulos/configuracion/editar_entidad.php';
    }

    public function eliminarEntidad(?int $id): void {
        if (!$id) { header('Location: index.php?modulo=configuracion&accion=entidades'); exit; }

        $stmt = $this->db->prepare("DELETE FROM entidades WHERE id = ?");
        $stmt->execute([$id]);

        $_SESSION['flash_success'] = 'Entidad eliminada correctamente.';
        header('Location: index.php?modulo=configuracion&accion=entidades');
        exit;
    }

    // ── ETAPAS ────────────────────────────────────────────────

    public function etapas(): void {
        $etapas = $this->db->query("
            SELECT et.*, f.nombre AS fase_nombre
            FROM etapas et
            LEFT JOIN fases f ON f.id = et.fase_id
            ORDER BY f.orden ASC, et.orden ASC, et.id ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
        require_once __DIR__ . '/../vista/modulos/configuracion/etapas.php';
    }

    public function crearEtapa(): void {
        $fases = $this->db->query("SELECT id, nombre FROM fases WHERE activo = 1 ORDER BY orden")->fetchAll(PDO::FETCH_ASSOC);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre          = trim($_POST['nombre'] ?? '');
            $descripcion     = trim($_POST['descripcion'] ?? '');
            $fase_id         = (int) ($_POST['fase_id'] ?? 0) ?: null;
            $orden           = (int) ($_POST['orden'] ?? 0);
            $peso_porcentual = (float) str_replace(',', '.', $_POST['peso_porcentual'] ?? 0);
            $activo          = isset($_POST['activo']) ? 1 : 0;

            if ($nombre === '') {
                $_SESSION['flash_error'] = 'El nombre es obligatorio.';
                header('Location: index.php?modulo=configuracion&accion=crear-etapa');
                exit;
            }

            $this->db->prepare(
                "INSERT INTO etapas (fase_id, nombre, descripcion, orden, peso_porcentual, activo) VALUES (?, ?, ?, ?, ?, ?)"
            )->execute([$fase_id, $nombre, $descripcion ?: null, $orden, $peso_porcentual, $activo]);

            $_SESSION['flash_success'] = 'Etapa creada correctamente.';
            header('Location: index.php?modulo=configuracion&accion=etapas');
            exit;
        }

        $siguienteOrden = (int) $this->db->query("SELECT COALESCE(MAX(orden),0)+1 FROM etapas")->fetchColumn();
        require_once __DIR__ . '/../vista/modulos/configuracion/crear_etapa.php';
    }

    public function editarEtapa(?int $id): void {
        if (!$id) { header('Location: index.php?modulo=configuracion&accion=etapas'); exit; }

        $stmt = $this->db->prepare("SELECT * FROM etapas WHERE id = ?");
        $stmt->execute([$id]);
        $etapa = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$etapa) {
            $_SESSION['flash_error'] = 'Etapa no encontrada.';
            header('Location: index.php?modulo=configuracion&accion=etapas');
            exit;
        }

        $fases = $this->db->query("SELECT id, nombre FROM fases WHERE activo = 1 ORDER BY orden")->fetchAll(PDO::FETCH_ASSOC);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre          = trim($_POST['nombre'] ?? '');
            $descripcion     = trim($_POST['descripcion'] ?? '');
            $fase_id         = (int) ($_POST['fase_id'] ?? 0) ?: null;
            $orden           = (int) ($_POST['orden'] ?? 0);
            $peso_porcentual = (float) str_replace(',', '.', $_POST['peso_porcentual'] ?? 0);
            $activo          = isset($_POST['activo']) ? 1 : 0;

            if ($nombre === '') {
                $_SESSION['flash_error'] = 'El nombre es obligatorio.';
                header("Location: index.php?modulo=configuracion&accion=editar-etapa&id=$id");
                exit;
            }

            $this->db->prepare(
                "UPDATE etapas SET fase_id=?, nombre=?, descripcion=?, orden=?, peso_porcentual=?, activo=? WHERE id=?"
            )->execute([$fase_id, $nombre, $descripcion ?: null, $orden, $peso_porcentual, $activo, $id]);

            $_SESSION['flash_success'] = 'Etapa actualizada correctamente.';
            header('Location: index.php?modulo=configuracion&accion=etapas');
            exit;
        }

        require_once __DIR__ . '/../vista/modulos/configuracion/editar_etapa.php';
    }

    public function eliminarEtapa(?int $id): void {
        if (!$id) { header('Location: index.php?modulo=configuracion&accion=etapas'); exit; }

        $stmt = $this->db->prepare("DELETE FROM etapas WHERE id = ?");
        $stmt->execute([$id]);

        $_SESSION['flash_success'] = 'Etapa eliminada correctamente.';
        header('Location: index.php?modulo=configuracion&accion=etapas');
        exit;
    }

    // ── REQUISITOS ────────────────────────────────────────────

    public function requisitos(): void {
        $requisitos = $this->db->query("
            SELECT r.*, e.nombre AS etapa_nombre, en.nombre AS entidad_nombre
            FROM requisitos r
            JOIN etapas e ON r.etapa_id = e.id
            LEFT JOIN entidades en ON r.entidad_id = en.id
            ORDER BY e.orden ASC, r.nombre ASC
        ")->fetchAll();
        require_once __DIR__ . '/../vista/modulos/configuracion/requisitos.php';
    }

    public function crearRequisito(): void {
        $etapas    = $this->db->query("SELECT et.id, et.nombre, f.nombre AS fase_nombre FROM etapas et LEFT JOIN fases f ON f.id = et.fase_id WHERE et.activo = 1 ORDER BY f.orden ASC, et.orden ASC")->fetchAll(PDO::FETCH_ASSOC);
        $entidades = $this->db->query("SELECT id, nombre FROM entidades WHERE activo = 1 ORDER BY nombre")->fetchAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre                    = trim($_POST['nombre'] ?? '');
            $etapa_id                  = (int) ($_POST['etapa_id'] ?? 0);
            $entidad_id                = (int) ($_POST['entidad_id'] ?? 0) ?: null;
            $descripcion               = trim($_POST['descripcion'] ?? '');
            $obligatorio               = isset($_POST['obligatorio']) ? 1 : 0;
            $requiere_documento        = isset($_POST['requiere_documento']) ? 1 : 0;
            $requiere_fecha_vencimiento = isset($_POST['requiere_fecha_vencimiento']) ? 1 : 0;
            $requiere_aprobacion       = isset($_POST['requiere_aprobacion']) ? 1 : 0;
            $peso_porcentual           = (float) str_replace(',', '.', $_POST['peso_porcentual'] ?? 0);
            $responsable               = trim($_POST['responsable'] ?? '');
            $alerta_asociada           = trim($_POST['alerta_asociada'] ?? '');
            $accion_recomendada        = trim($_POST['accion_recomendada'] ?? '');
            $activo                    = isset($_POST['activo']) ? 1 : 0;

            if ($nombre === '' || !$etapa_id) {
                $_SESSION['flash_error'] = 'El nombre y la etapa son obligatorios.';
                header('Location: index.php?modulo=configuracion&accion=crear-requisito');
                exit;
            }

            $stmt = $this->db->prepare("
                INSERT INTO requisitos
                  (etapa_id, entidad_id, nombre, descripcion, obligatorio,
                   requiere_documento, requiere_fecha_vencimiento, requiere_aprobacion,
                   peso_porcentual, responsable, alerta_asociada, accion_recomendada, activo)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)
            ");
            $stmt->execute([
                $etapa_id, $entidad_id, $nombre, $descripcion ?: null, $obligatorio,
                $requiere_documento, $requiere_fecha_vencimiento, $requiere_aprobacion,
                $peso_porcentual, $responsable ?: null, $alerta_asociada ?: null,
                $accion_recomendada ?: null, $activo,
            ]);

            $_SESSION['flash_success'] = 'Requisito creado correctamente.';
            header('Location: index.php?modulo=configuracion&accion=requisitos');
            exit;
        }

        require_once __DIR__ . '/../vista/modulos/configuracion/crear_requisito.php';
    }

    public function editarRequisito(?int $id): void {
        if (!$id) { header('Location: index.php?modulo=configuracion&accion=requisitos'); exit; }

        $stmt = $this->db->prepare("SELECT * FROM requisitos WHERE id = ?");
        $stmt->execute([$id]);
        $requisito = $stmt->fetch();

        if (!$requisito) {
            $_SESSION['flash_error'] = 'Requisito no encontrado.';
            header('Location: index.php?modulo=configuracion&accion=requisitos');
            exit;
        }

        $etapas    = $this->db->query("SELECT et.id, et.nombre, f.nombre AS fase_nombre FROM etapas et LEFT JOIN fases f ON f.id = et.fase_id WHERE et.activo = 1 ORDER BY f.orden ASC, et.orden ASC")->fetchAll(PDO::FETCH_ASSOC);
        $entidades = $this->db->query("SELECT id, nombre FROM entidades WHERE activo = 1 ORDER BY nombre")->fetchAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre                    = trim($_POST['nombre'] ?? '');
            $etapa_id                  = (int) ($_POST['etapa_id'] ?? 0);
            $entidad_id                = (int) ($_POST['entidad_id'] ?? 0) ?: null;
            $descripcion               = trim($_POST['descripcion'] ?? '');
            $obligatorio               = isset($_POST['obligatorio']) ? 1 : 0;
            $requiere_documento        = isset($_POST['requiere_documento']) ? 1 : 0;
            $requiere_fecha_vencimiento = isset($_POST['requiere_fecha_vencimiento']) ? 1 : 0;
            $requiere_aprobacion       = isset($_POST['requiere_aprobacion']) ? 1 : 0;
            $peso_porcentual           = (float) str_replace(',', '.', $_POST['peso_porcentual'] ?? 0);
            $responsable               = trim($_POST['responsable'] ?? '');
            $alerta_asociada           = trim($_POST['alerta_asociada'] ?? '');
            $accion_recomendada        = trim($_POST['accion_recomendada'] ?? '');
            $activo                    = isset($_POST['activo']) ? 1 : 0;

            if ($nombre === '' || !$etapa_id) {
                $_SESSION['flash_error'] = 'El nombre y la etapa son obligatorios.';
                header("Location: index.php?modulo=configuracion&accion=editar-requisito&id=$id");
                exit;
            }

            $stmt = $this->db->prepare("
                UPDATE requisitos SET
                  etapa_id = ?, entidad_id = ?, nombre = ?, descripcion = ?, obligatorio = ?,
                  requiere_documento = ?, requiere_fecha_vencimiento = ?, requiere_aprobacion = ?,
                  peso_porcentual = ?, responsable = ?, alerta_asociada = ?,
                  accion_recomendada = ?, activo = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $etapa_id, $entidad_id, $nombre, $descripcion ?: null, $obligatorio,
                $requiere_documento, $requiere_fecha_vencimiento, $requiere_aprobacion,
                $peso_porcentual, $responsable ?: null, $alerta_asociada ?: null,
                $accion_recomendada ?: null, $activo, $id,
            ]);

            $_SESSION['flash_success'] = 'Requisito actualizado correctamente.';
            header('Location: index.php?modulo=configuracion&accion=requisitos');
            exit;
        }

        require_once __DIR__ . '/../vista/modulos/configuracion/editar_requisito.php';
    }

    public function eliminarRequisito(?int $id): void {
        if (!$id) { header('Location: index.php?modulo=configuracion&accion=requisitos'); exit; }

        $stmt = $this->db->prepare("DELETE FROM requisitos WHERE id = ?");
        $stmt->execute([$id]);

        $_SESSION['flash_success'] = 'Requisito eliminado correctamente.';
        header('Location: index.php?modulo=configuracion&accion=requisitos');
        exit;
    }

    // ── INDICADORES ───────────────────────────────────────────

    public function indicadores(): void {
        require_once __DIR__ . '/../modelo/IndicadoresModelo.php';
        $indicadores = (new IndicadoresModelo($this->db))->obtenerTodos();
        require_once __DIR__ . '/../vista/modulos/configuracion/indicadores.php';
    }

    public function crearIndicador(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            if ($nombre === '') {
                $_SESSION['flash_error'] = 'El nombre es obligatorio.';
                header('Location: index.php?modulo=configuracion&accion=crear-indicador');
                exit;
            }
            require_once __DIR__ . '/../modelo/IndicadoresModelo.php';
            (new IndicadoresModelo($this->db))->crear($_POST);
            $_SESSION['flash_success'] = 'Indicador creado correctamente.';
            header('Location: index.php?modulo=configuracion&accion=indicadores');
            exit;
        }
        require_once __DIR__ . '/../vista/modulos/configuracion/crear_indicador.php';
    }

    public function editarIndicador(?int $id): void {
        if (!$id) { header('Location: index.php?modulo=configuracion&accion=indicadores'); exit; }
        require_once __DIR__ . '/../modelo/IndicadoresModelo.php';
        $modelo    = new IndicadoresModelo($this->db);
        $indicador = $modelo->obtenerPorId($id);
        if (!$indicador) {
            $_SESSION['flash_error'] = 'Indicador no encontrado.';
            header('Location: index.php?modulo=configuracion&accion=indicadores');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            if ($nombre === '') {
                $_SESSION['flash_error'] = 'El nombre es obligatorio.';
                header("Location: index.php?modulo=configuracion&accion=editar-indicador&id=$id");
                exit;
            }
            $modelo->actualizar($id, $_POST);
            $_SESSION['flash_success'] = 'Indicador actualizado correctamente.';
            header('Location: index.php?modulo=configuracion&accion=indicadores');
            exit;
        }
        require_once __DIR__ . '/../vista/modulos/configuracion/editar_indicador.php';
    }

    public function eliminarIndicador(?int $id): void {
        if (!$id) { header('Location: index.php?modulo=configuracion&accion=indicadores'); exit; }
        require_once __DIR__ . '/../modelo/IndicadoresModelo.php';
        (new IndicadoresModelo($this->db))->eliminar($id);
        $_SESSION['flash_success'] = 'Indicador eliminado correctamente.';
        header('Location: index.php?modulo=configuracion&accion=indicadores');
        exit;
    }

    // ── ÍTEMS ─────────────────────────────────────────────────

    public function items(): void {
        // Todas las etapas activas
        $etapas = $this->db->query("
            SELECT id, nombre, orden FROM etapas WHERE activo = 1 ORDER BY orden ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Todos los requisitos activos agrupados por etapa_id
        $requisitosRaw = $this->db->query("
            SELECT r.id, r.nombre, r.etapa_id
            FROM requisitos r
            WHERE r.activo = 1
            ORDER BY r.nombre ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $requisitosPorEtapa = [];
        foreach ($requisitosRaw as $req) {
            $requisitosPorEtapa[$req['etapa_id']][] = $req;
        }

        // Ítems existentes agrupados por requisito_id
        $itemsRaw = $this->db->query("
            SELECT ri.*
            FROM requisito_items ri
            ORDER BY ri.requisito_id ASC, ri.orden ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $itemsPorRequisito = [];
        foreach ($itemsRaw as $item) {
            $itemsPorRequisito[$item['requisito_id']][] = $item;
        }

        require_once __DIR__ . '/../vista/modulos/configuracion/items.php';
    }

    public function crearItem(): void {
        $requisitos = $this->db->query("
            SELECT r.id, r.nombre, e.nombre AS etapa_nombre
            FROM requisitos r
            JOIN etapas e ON r.etapa_id = e.id
            WHERE r.activo = 1
            ORDER BY e.orden ASC, r.nombre ASC
        ")->fetchAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre       = trim($_POST['nombre'] ?? '');
            $requisito_id = (int) ($_POST['requisito_id'] ?? 0);
            $descripcion  = trim($_POST['descripcion'] ?? '');
            $obligatorio  = isset($_POST['obligatorio']) ? 1 : 0;
            $orden        = (int) ($_POST['orden'] ?? 0);
            $activo       = isset($_POST['activo']) ? 1 : 0;

            if ($nombre === '' || !$requisito_id) {
                $_SESSION['flash_error'] = 'El nombre y el requisito son obligatorios.';
                header('Location: index.php?modulo=configuracion&accion=crear-item');
                exit;
            }

            $stmt = $this->db->prepare(
                "INSERT INTO requisito_items (requisito_id, nombre, descripcion, obligatorio, orden, activo) VALUES (?,?,?,?,?,?)"
            );
            $stmt->execute([$requisito_id, $nombre, $descripcion ?: null, $obligatorio, $orden, $activo]);

            $_SESSION['flash_success'] = 'Ítem creado correctamente.';
            header('Location: index.php?modulo=configuracion&accion=items');
            exit;
        }

        $requisito_id_pre = (int) ($_GET['requisito_id'] ?? 0);
        $siguienteOrden   = 1;
        if ($requisito_id_pre) {
            $siguienteOrden = (int) $this->db->prepare(
                "SELECT COALESCE(MAX(orden),0)+1 FROM requisito_items WHERE requisito_id = ?"
            )->execute([$requisito_id_pre]) ? $this->db->query(
                "SELECT COALESCE(MAX(orden),0)+1 FROM requisito_items WHERE requisito_id = $requisito_id_pre"
            )->fetchColumn() : 1;
        }

        require_once __DIR__ . '/../vista/modulos/configuracion/crear_item.php';
    }

    public function editarItem(?int $id): void {
        if (!$id) { header('Location: index.php?modulo=configuracion&accion=items'); exit; }

        $stmt = $this->db->prepare("SELECT * FROM requisito_items WHERE id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch();

        if (!$item) {
            $_SESSION['flash_error'] = 'Ítem no encontrado.';
            header('Location: index.php?modulo=configuracion&accion=items');
            exit;
        }

        $requisitos = $this->db->query("
            SELECT r.id, r.nombre, e.nombre AS etapa_nombre
            FROM requisitos r
            JOIN etapas e ON r.etapa_id = e.id
            WHERE r.activo = 1
            ORDER BY e.orden ASC, r.nombre ASC
        ")->fetchAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre       = trim($_POST['nombre'] ?? '');
            $requisito_id = (int) ($_POST['requisito_id'] ?? 0);
            $descripcion  = trim($_POST['descripcion'] ?? '');
            $obligatorio  = isset($_POST['obligatorio']) ? 1 : 0;
            $orden        = (int) ($_POST['orden'] ?? 0);
            $activo       = isset($_POST['activo']) ? 1 : 0;

            if ($nombre === '' || !$requisito_id) {
                $_SESSION['flash_error'] = 'El nombre y el requisito son obligatorios.';
                header("Location: index.php?modulo=configuracion&accion=editar-item&id=$id");
                exit;
            }

            $stmt = $this->db->prepare(
                "UPDATE requisito_items SET requisito_id=?, nombre=?, descripcion=?, obligatorio=?, orden=?, activo=? WHERE id=?"
            );
            $stmt->execute([$requisito_id, $nombre, $descripcion ?: null, $obligatorio, $orden, $activo, $id]);

            $_SESSION['flash_success'] = 'Ítem actualizado correctamente.';
            header('Location: index.php?modulo=configuracion&accion=items');
            exit;
        }

        require_once __DIR__ . '/../vista/modulos/configuracion/editar_item.php';
    }

    public function eliminarItem(?int $id): void {
        if (!$id) { header('Location: index.php?modulo=configuracion&accion=items'); exit; }

        $stmt = $this->db->prepare("DELETE FROM requisito_items WHERE id = ?");
        $stmt->execute([$id]);

        $_SESSION['flash_success'] = 'Ítem eliminado correctamente.';
        header('Location: index.php?modulo=configuracion&accion=items');
        exit;
    }
}
