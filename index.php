<?php

session_start();

require_once __DIR__ . '/config/database.php';

$modulo = $_GET['modulo'] ?? null;
$accion = $_GET['accion'] ?? 'index';
$id     = isset($_GET['id']) ? (int) $_GET['id'] : null;

// --- Autenticación ---
require_once __DIR__ . '/controlador/AuthControlador.php';
$auth = new AuthControlador();

if ($accion === 'login') {
    $auth->login();
    exit;
}

if ($accion === 'logout') {
    $auth->logout();
    exit;
}

// Proteger todas las rutas
if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php?accion=login');
    exit;
}

// --- Helpers de rol ---
$rol           = $_SESSION['usuario_rol'] ?? '';
$esAdmin       = $rol === 'admin';
$esOperaciones = $rol === 'operaciones';

// Módulos por rol
$modulosAdmin       = [];
$modulosOperaciones = ['usuarios', 'empresas', 'configuracion', 'comites', 'seguimiento', 'indicadores'];
$modulosUsuario     = ['cronograma', 'entidades', 'documentos', 'reportes', 'mis-compromisos', 'indicadores', 'informes'];
$modulosValidos     = array_merge($modulosAdmin, $modulosOperaciones, $modulosUsuario, ['manual', 'perfil', 'notificaciones']);

$db = conectar();

// Tablero
if ($modulo === null || !in_array($modulo, $modulosValidos, true)) {
    require_once __DIR__ . '/controlador/TableroControlador.php';
    (new TableroControlador($db))->index();
    exit;
}

// Control de acceso por rol
if (in_array($modulo, $modulosAdmin, true) && !$esAdmin) {
    $_SESSION['flash_error'] = 'No tienes permiso para acceder a esa sección.';
    header('Location: index.php');
    exit;
}

if (in_array($modulo, $modulosOperaciones, true) && !$esAdmin && !$esOperaciones
    && !in_array($modulo, ['comites'], true)) {
    $_SESSION['flash_error'] = 'No tienes permiso para acceder a esa sección.';
    header('Location: index.php');
    exit;
}

if (in_array($modulo, $modulosUsuario, true) && $esOperaciones && !in_array($modulo, ['cronograma', 'reportes', 'documentos', 'mis-compromisos', 'indicadores', 'informes'])) {
    $_SESSION['flash_error'] = 'No tienes permiso para acceder a esa sección.';
    header('Location: index.php');
    exit;
}

match ($modulo) {
    'usuarios' => (function () use ($accion, $id, $db) {
        require_once __DIR__ . '/controlador/UsuariosControlador.php';
        $ctrl = new UsuariosControlador($db);
        match ($accion) {
            'crear'    => $ctrl->crear(),
            'editar'   => $ctrl->editar($id),
            'eliminar' => $ctrl->eliminar($id),
            default    => $ctrl->index(),
        };
    })(),

    'empresas' => (function () use ($accion, $id, $db) {
        require_once __DIR__ . '/controlador/EmpresasControlador.php';
        $ctrl = new EmpresasControlador($db);
        match ($accion) {
            'crear'            => $ctrl->crear(),
            'editar'           => $ctrl->editar($id),
            'eliminar'         => $ctrl->eliminar($id),
            'ver'              => $ctrl->ver($id),
            'asignar-usuario'  => $ctrl->asignarUsuario($id),
            'quitar-usuario'   => $ctrl->quitarUsuario($id),
            'agregar-etapa'    => $ctrl->agregarEtapa($id),
            'usuarios'         => $ctrl->listarUsuarios(),
            'crear-usuario'    => $ctrl->crearUsuario($id),
            'editar-usuario'   => $ctrl->editarUsuario($id),
            'eliminar-usuario' => $ctrl->eliminarUsuario($id),
            default            => $ctrl->index(),
        };
    })(),

    'configuracion' => (function () use ($accion, $id, $db) {
        require_once __DIR__ . '/controlador/ConfiguracionControlador.php';
        $ctrl = new ConfiguracionControlador($db);
        match ($accion) {
            'fases'                => $ctrl->fases(),
            'crear-fase'           => $ctrl->crearFase(),
            'editar-fase'          => $ctrl->editarFase($id),
            'eliminar-fase'        => $ctrl->eliminarFase($id),
            'entidades'            => $ctrl->entidades(),
            'crear-entidad'        => $ctrl->crearEntidad(),
            'editar-entidad'       => $ctrl->editarEntidad($id),
            'eliminar-entidad'     => $ctrl->eliminarEntidad($id),
            'etapas'               => $ctrl->etapas(),
            'crear-etapa'          => $ctrl->crearEtapa(),
            'editar-etapa'         => $ctrl->editarEtapa($id),
            'eliminar-etapa'       => $ctrl->eliminarEtapa($id),
            'requisitos'           => $ctrl->requisitos(),
            'crear-requisito'      => $ctrl->crearRequisito(),
            'editar-requisito'     => $ctrl->editarRequisito($id),
            'eliminar-requisito'   => $ctrl->eliminarRequisito($id),
            'items'                => $ctrl->items(),
            'crear-item'           => $ctrl->crearItem(),
            'editar-item'          => $ctrl->editarItem($id),
            'eliminar-item'        => $ctrl->eliminarItem($id),
            'indicadores'          => $ctrl->indicadores(),
            'crear-indicador'      => $ctrl->crearIndicador(),
            'editar-indicador'     => $ctrl->editarIndicador($id),
            'eliminar-indicador'   => $ctrl->eliminarIndicador($id),
            default                => $ctrl->index(),
        };
    })(),

    'seguimiento' => (function () use ($accion, $id, $db) {
        require_once __DIR__ . '/controlador/SeguimientoControlador.php';
        $ctrl = new SeguimientoControlador($db);
        match ($accion) {
            'guardar' => $ctrl->guardar($id),
            default   => $ctrl->index($id),
        };
    })(),

    'cronograma' => (function () use ($id, $db) {
        require_once __DIR__ . '/controlador/CronogramaControlador.php';
        (new CronogramaControlador($db))->index($id);
    })(),

    'entidades' => (function () use ($db) {
        require_once __DIR__ . '/controlador/EntidadesUsuarioControlador.php';
        (new EntidadesUsuarioControlador($db))->index();
    })(),

    'reportes' => (function () use ($accion, $id, $db) {
        require_once __DIR__ . '/controlador/ReportesControlador.php';
        $ctrl = new ReportesControlador($db);
        match ($accion) {
            'crear-alerta'   => $ctrl->crearAlerta($id),
            'resolver-alerta' => $ctrl->resolverAlerta($id),
            default          => $ctrl->index($id),
        };
    })(),

    'informes' => (function () use ($accion, $id, $db) {
        require_once __DIR__ . '/controlador/InformesControlador.php';
        $ctrl = new InformesControlador($db);
        match ($accion) {
            'pdf'   => $ctrl->pdf($id),
            default => $ctrl->excel($id),
        };
    })(),

    'documentos' => (function () use ($accion, $id, $db) {
        require_once __DIR__ . '/controlador/DocumentosControlador.php';
        $ctrl = new DocumentosControlador($db);
        match ($accion) {
            'subir'     => $ctrl->subir($id),
            'ver'       => $ctrl->ver($id),
            'descargar' => $ctrl->descargar($id),
            'eliminar'  => $ctrl->eliminar($id),
            default     => $ctrl->index(),
        };
    })(),

    'indicadores' => (function () use ($accion, $id, $db) {
        require_once __DIR__ . '/controlador/IndicadoresControlador.php';
        $ctrl = new IndicadoresControlador($db);
        match ($accion) {
            'asignar'        => $ctrl->asignar($id),
            'actualizar'     => $ctrl->actualizar($id),
            'eliminar-valor' => $ctrl->eliminarValor($id),
            'desasignar'     => $ctrl->desasignar($id),
            default          => $ctrl->index($id),
        };
    })(),

    'manual' => (function () {
        require_once __DIR__ . '/vista/modulos/manual/index.php';
    })(),

    'perfil' => (function () use ($accion, $db) {
        require_once __DIR__ . '/controlador/PerfilControlador.php';
        $ctrl = new PerfilControlador($db);
        match ($accion) {
            'actualizar' => $ctrl->actualizar(),
            default      => $ctrl->index(),
        };
    })(),

    'notificaciones' => (function () use ($accion, $db) {
        require_once __DIR__ . '/controlador/NotificacionesControlador.php';
        $ctrl = new NotificacionesControlador($db);
        match ($accion) {
            'marcar-leidas' => $ctrl->marcarLeidas(),
            default         => null,
        };
    })(),

    'mis-compromisos' => (function () use ($accion, $id, $db) {
        require_once __DIR__ . '/controlador/MisCompromisosControlador.php';
        $ctrl = new MisCompromisosControlador($db);
        match ($accion) {
            'actualizar'          => $ctrl->actualizar($id),
            'descargar-documento' => $ctrl->descargarDocumento($id),
            default                => $ctrl->index(),
        };
    })(),

    'comites' => (function () use ($accion, $id, $db) {
        require_once __DIR__ . '/controlador/ComitesControlador.php';
        $ctrl = new ComitesControlador($db);
        match ($accion) {
            'crear'                => $ctrl->crear(),
            'ver'                  => $ctrl->ver($id),
            'editar'               => $ctrl->editar($id),
            'eliminar'             => $ctrl->eliminar($id),
            'guardar-compromiso'   => $ctrl->guardarCompromiso(),
            'actualizar-compromiso'=> $ctrl->actualizarCompromiso($id),
            'eliminar-compromiso'  => $ctrl->eliminarCompromiso($id),
            default                => $ctrl->index(),
        };
    })(),
};
