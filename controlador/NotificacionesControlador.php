<?php

require_once __DIR__ . '/../modelo/NotificacionesModelo.php';

class NotificacionesControlador {

    private NotificacionesModelo $modelo;

    public function __construct(PDO $db) {
        $this->modelo = new NotificacionesModelo($db);
    }

    /** Llamado por fetch() cuando el usuario abre la campana: marca como leídas las claves visibles. */
    public function marcarLeidas(): void {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['usuario_id'])) {
            http_response_code(403);
            echo json_encode(['ok' => false]);
            exit;
        }

        $body   = json_decode(file_get_contents('php://input'), true);
        $claves = is_array($body['claves'] ?? null) ? array_map('strval', $body['claves']) : [];

        $this->modelo->marcarLeidas((int) $_SESSION['usuario_id'], $claves);

        echo json_encode(['ok' => true]);
        exit;
    }
}
