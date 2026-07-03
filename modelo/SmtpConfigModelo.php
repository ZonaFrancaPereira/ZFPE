<?php

require_once __DIR__ . '/../config/cifrado.php';

class SmtpConfigModelo {

    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function obtener(): ?array {
        $stmt = $this->db->query("SELECT * FROM smtp_config ORDER BY id ASC LIMIT 1");
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila ?: null;
    }

    public function guardar(array $datos, int $usuarioId): void {
        $existente = $this->obtener();

        $claveCifrada = $existente['clave_cifrada'] ?? '';
        if (!empty($datos['clave'])) {
            $claveCifrada = cifrar($datos['clave']);
        }

        $host            = trim($datos['host'] ?? '');
        $puerto          = (int) ($datos['puerto'] ?? 587);
        $usuario         = trim($datos['usuario'] ?? '');
        $cifrado         = ($datos['cifrado'] ?? 'tls') === 'ssl' ? 'ssl' : 'tls';
        $correoRemitente = trim($datos['correo_remitente'] ?? '');
        $nombreRemitente = trim($datos['nombre_remitente'] ?? '');
        $activo          = !empty($datos['activo']) ? 1 : 0;

        if ($existente) {
            $stmt = $this->db->prepare("
                UPDATE smtp_config
                SET host = ?, puerto = ?, usuario = ?, clave_cifrada = ?, cifrado = ?,
                    correo_remitente = ?, nombre_remitente = ?, activo = ?, actualizado_por = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $host, $puerto, $usuario, $claveCifrada, $cifrado,
                $correoRemitente, $nombreRemitente, $activo, $usuarioId,
                $existente['id'],
            ]);
            return;
        }

        $stmt = $this->db->prepare("
            INSERT INTO smtp_config
                (host, puerto, usuario, clave_cifrada, cifrado, correo_remitente, nombre_remitente, activo, actualizado_por)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $host, $puerto, $usuario, $claveCifrada, $cifrado,
            $correoRemitente, $nombreRemitente, $activo, $usuarioId,
        ]);
    }
}
