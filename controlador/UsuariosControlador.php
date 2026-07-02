<?php

require_once __DIR__ . '/../modelo/UsuariosModelo.php';

class UsuariosControlador {

    private UsuariosModelo $modelo;

    public function __construct(PDO $db) {
        $this->modelo = new UsuariosModelo($db);
    }

    public function index(): void {
        $usuarios = $this->modelo->obtenerTodos();
        require_once __DIR__ . '/../vista/modulos/usuarios/index.php';
    }

    public function crear(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->modelo->crear($_POST);
            header('Location: index.php?modulo=usuarios');
            exit;
        }
        require_once __DIR__ . '/../vista/modulos/usuarios/crear.php';
    }

    public function editar(int $id): void {
        $usuario = $this->modelo->obtenerPorId($id);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->modelo->actualizar($id, $_POST);
            header('Location: index.php?modulo=usuarios');
            exit;
        }
        require_once __DIR__ . '/../vista/modulos/usuarios/editar.php';
    }

    public function eliminar(int $id): void {
        $this->modelo->eliminar($id);
        header('Location: index.php?modulo=usuarios');
        exit;
    }
}
