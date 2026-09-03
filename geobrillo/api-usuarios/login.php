<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

require_once __DIR__ . '/controllers/AuthControlador.php';

// Soporta tanto JSON (fetch con application/json) como formularios clásicos
$json = json_decode(file_get_contents('php://input'), true);
$data = $json ?: $_POST;

try {
    $auth = new AuthControlador();
    $resultado = $auth->login($data['email'] ?? '', $data['password'] ?? '');

    if (!$resultado['exito']) {
        http_response_code(401);
    }

    echo json_encode($resultado);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["exito" => false, "mensaje" => "Error interno del servidor: " . $e->getMessage()]);
}
exit();
