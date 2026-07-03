<?php
/**
 * Dispatcher del módulo "Manual de uso". No usa un controlador propio
 * (igual que antes de esta separación): cada rol ve un manual distinto.
 *
 * - admin: sin accion -> selector de 4 manuales; con accion=administrador/operaciones/empresa/usuario -> esa página.
 * - operaciones: siempre el manual de Operaciones.
 * - usuario (empresa): siempre un manual combinado Empresa + Usuario.
 */
$rol           = $_SESSION['usuario_rol'] ?? '';
$esAdmin       = $rol === 'admin';
$esOperaciones = $rol === 'operaciones';

if ($esAdmin) {
    match ($accion ?? 'index') {
        'administrador' => require __DIR__ . '/administrador.php',
        'operaciones'   => require __DIR__ . '/operaciones.php',
        'empresa'       => require __DIR__ . '/empresa.php',
        'usuario'       => require __DIR__ . '/usuario.php',
        default         => require __DIR__ . '/selector.php',
    };
} elseif ($esOperaciones) {
    require __DIR__ . '/operaciones.php';
} else {
    require __DIR__ . '/mi_manual.php';
}
