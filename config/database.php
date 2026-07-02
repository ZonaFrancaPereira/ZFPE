<?php

/** Carga pares CLAVE=VALOR de un .env sin pisar variables de entorno reales ya definidas. */
function cargarEnv(string $ruta): void {
    if (!is_file($ruta)) return;

    foreach (file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linea) {
        $linea = trim($linea);
        if ($linea === '' || str_starts_with($linea, '#')) continue;

        [$clave, $valor] = array_pad(explode('=', $linea, 2), 2, '');
        $clave = trim($clave);
        $valor = trim($valor, " \t\"'");

        if ($clave !== '' && getenv($clave) === false) {
            putenv("$clave=$valor");
        }
    }
}

cargarEnv(__DIR__ . '/../.env');

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'zfipe');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', getenv('DB_CHARSET') ?: 'utf8mb4');

function conectar(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }

    return $pdo;
}
