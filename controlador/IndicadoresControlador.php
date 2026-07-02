<?php

require_once __DIR__ . '/../modelo/IndicadoresModelo.php';
require_once __DIR__ . '/../modelo/EmpresasModelo.php';
require_once __DIR__ . '/ControladorBase.php';

class IndicadoresControlador extends ControladorBase {

    private IndicadoresModelo $modelo;
    private EmpresasModelo    $modeloEmpresas;

    public function __construct(PDO $db) {
        parent::__construct($db);
        $this->modelo         = new IndicadoresModelo($db);
        $this->modeloEmpresas = new EmpresasModelo($db);
    }

    public function index(?int $empresa_id): void {
        if ($this->esOp()) {
            if (!$empresa_id) {
                $todasEmpresas = $this->modeloEmpresas->obtenerTodas();
                require_once __DIR__ . '/../vista/modulos/indicadores/selector.php';
                return;
            }
            $empresa     = $this->modeloEmpresas->obtenerPorId($empresa_id);
            $asignados   = $this->modelo->obtenerPorEmpresa($empresa_id);
            $disponibles = $this->modelo->obtenerNoAsignados($empresa_id);
        } else {
            $empresa_id  = $this->empresaId();
            $empresa     = $empresa_id ? $this->modeloEmpresas->obtenerPorId($empresa_id) : null;
            $asignados   = $empresa_id ? $this->modelo->obtenerPorEmpresa($empresa_id) : [];
            $disponibles = [];
        }

        // Adjuntar historial de valores a cada indicador asignado
        foreach ($asignados as &$ind) {
            $ind['valores'] = $empresa_id
                ? $this->modelo->obtenerValoresPorIndicador($empresa_id, (int) $ind['id'])
                : [];
        }
        unset($ind);

        require_once __DIR__ . '/../vista/modulos/indicadores/index.php';
    }

    public function asignar(?int $empresa_id): void {
        if (!$empresa_id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?modulo=indicadores');
            exit;
        }
        $indicador_id = (int) ($_POST['indicador_id'] ?? 0);
        if ($indicador_id) {
            $this->modelo->asignar($empresa_id, $indicador_id);
            $_SESSION['flash_success'] = 'Indicador asignado correctamente.';
        }
        header("Location: index.php?modulo=indicadores&id=$empresa_id");
        exit;
    }

    public function actualizar(?int $empresa_id): void {
        if (!$empresa_id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?modulo=indicadores');
            exit;
        }
        $indicador_id = (int) ($_POST['indicador_id'] ?? 0);
        if ($indicador_id) {
            $this->modelo->actualizarValor(
                $empresa_id,
                $indicador_id,
                $_POST,
                $_SESSION['usuario_id'] ?? null
            );
            $_SESSION['flash_success'] = 'Indicador actualizado correctamente.';
        }
        header("Location: index.php?modulo=indicadores&id=$empresa_id");
        exit;
    }

    public function eliminarValor(?int $empresa_id): void {
        if (!$empresa_id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?modulo=indicadores');
            exit;
        }
        $indicador_id = (int) ($_POST['indicador_id'] ?? 0);
        $periodo      = trim($_POST['periodo'] ?? '');
        if ($indicador_id && $periodo !== '') {
            $this->modelo->eliminarValor($empresa_id, $indicador_id, $periodo);
            $_SESSION['flash_success'] = 'Período eliminado.';
        }
        header("Location: index.php?modulo=indicadores&id=$empresa_id");
        exit;
    }

    public function desasignar(?int $empresa_id): void {
        if (!$empresa_id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?modulo=indicadores');
            exit;
        }
        $indicador_id = (int) ($_POST['indicador_id'] ?? 0);
        if ($indicador_id) {
            $this->modelo->desasignar($empresa_id, $indicador_id);
            $_SESSION['flash_success'] = 'Indicador removido.';
        }
        header("Location: index.php?modulo=indicadores&id=$empresa_id");
        exit;
    }
}
