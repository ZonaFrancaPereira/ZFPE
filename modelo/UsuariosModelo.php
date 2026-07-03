<?php

class UsuariosModelo {

    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function obtenerTodos(): array {
        $stmt = $this->db->query("SELECT * FROM usuarios ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear(array $datos): bool {
        $stmt = $this->db->prepare("
            INSERT INTO usuarios (nombre, correo, contrasena, rol, creado_en)
            VALUES (?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([
            $datos['nombre'],
            $datos['correo'],
            password_hash($datos['contrasena'], PASSWORD_DEFAULT),
            $datos['rol'] ?? 'usuario',
        ]);
    }

    public function actualizar(int $id, array $datos): bool {
        $stmt = $this->db->prepare("
            UPDATE usuarios SET nombre = ?, correo = ?, rol = ? WHERE id = ?
        ");
        return $stmt->execute([$datos['nombre'], $datos['correo'], $datos['rol'], $id]);
    }

    public function eliminar(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function obtenerPorEmpresa(int $empresa_id): array {
        $stmt = $this->db->prepare("
            SELECT * FROM usuarios
            WHERE empresa_id = ? AND rol = 'usuario'
            ORDER BY nombre ASC
        ");
        $stmt->execute([$empresa_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Equipo interno (Operaciones/Admin) — para elegir a quién asignar una decisión conjunta. */
    public function obtenerEquipoInterno(): array {
        return $this->db->query("
            SELECT * FROM usuarios WHERE rol IN ('operaciones','admin') ORDER BY nombre ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTodosDeEmpresas(): array {
        return $this->db->query("
            SELECT u.*, e.razon_social AS empresa_nombre
            FROM usuarios u
            LEFT JOIN empresas e ON e.id = u.empresa_id
            WHERE u.rol = 'usuario'
            ORDER BY e.razon_social ASC, u.nombre ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crearParaEmpresa(array $datos, int $empresa_id, bool $debeCambiarContrasena = false): bool {
        $stmt = $this->db->prepare("
            INSERT INTO usuarios (nombre, correo, contrasena, rol, empresa_id, es_gerente, debe_cambiar_contrasena, creado_en)
            VALUES (?, ?, ?, 'usuario', ?, ?, ?, NOW())
        ");
        return $stmt->execute([
            $datos['nombre'] ?? $datos['razon_social'] ?? 'Empresa',
            $datos['correo'],
            password_hash($datos['contrasena'], PASSWORD_DEFAULT),
            $empresa_id,
            !empty($datos['es_gerente']) ? 1 : 0,
            $debeCambiarContrasena ? 1 : 0,
        ]);
    }

    public function actualizarUsuarioEmpresa(int $id, array $datos): bool {
        $empresa_id = array_key_exists('empresa_id', $datos) ? $datos['empresa_id'] : false;
        $esGerente  = !empty($datos['es_gerente']) ? 1 : 0;
        if (!empty($datos['contrasena'])) {
            $stmt = $this->db->prepare("
                UPDATE usuarios SET nombre=?, correo=?, contrasena=?, empresa_id=?, es_gerente=? WHERE id=? AND rol='usuario'
            ");
            return $stmt->execute([
                $datos['nombre'],
                $datos['correo'],
                password_hash($datos['contrasena'], PASSWORD_DEFAULT),
                $empresa_id !== false ? $empresa_id : null,
                $esGerente,
                $id,
            ]);
        }
        $stmt = $this->db->prepare("
            UPDATE usuarios SET nombre=?, correo=?, empresa_id=?, es_gerente=? WHERE id=? AND rol='usuario'
        ");
        return $stmt->execute([
            $datos['nombre'],
            $datos['correo'],
            $empresa_id !== false ? $empresa_id : null,
            $esGerente,
            $id,
        ]);
    }

    public function actualizarPerfil(int $id, string $nombre, string $correo): bool {
        $stmt = $this->db->prepare("UPDATE usuarios SET nombre = ?, correo = ? WHERE id = ?");
        return $stmt->execute([$nombre, $correo, $id]);
    }

    public function actualizarContrasena(int $id, string $nuevaContrasena): bool {
        $stmt = $this->db->prepare("UPDATE usuarios SET contrasena = ?, debe_cambiar_contrasena = 0 WHERE id = ?");
        return $stmt->execute([password_hash($nuevaContrasena, PASSWORD_DEFAULT), $id]);
    }

    public function correoExiste(string $correo, ?int $excluirId = null): bool {
        if ($excluirId) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM usuarios WHERE correo=? AND id != ?");
            $stmt->execute([$correo, $excluirId]);
        } else {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM usuarios WHERE correo=?");
            $stmt->execute([$correo]);
        }
        return (int) $stmt->fetchColumn() > 0;
    }
}
