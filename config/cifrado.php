<?php

/** Cifra un texto con AES-256-CBC usando APP_KEY; antepone el IV (necesario para descifrar) en el mismo valor. */
function cifrar(string $texto): string {
    $clave = base64_decode(APP_KEY, true) ?: APP_KEY;
    $iv     = random_bytes(16);
    $cifrado = openssl_encrypt($texto, 'aes-256-cbc', $clave, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $cifrado);
}

/** Revierte cifrar(): separa el IV antepuesto y descifra el resto con APP_KEY. */
function descifrar(string $texto): string {
    $clave = base64_decode(APP_KEY, true) ?: APP_KEY;
    $datos = base64_decode($texto, true) ?: '';
    $iv       = substr($datos, 0, 16);
    $cifrado  = substr($datos, 16);
    return (string) openssl_decrypt($cifrado, 'aes-256-cbc', $clave, OPENSSL_RAW_DATA, $iv);
}
