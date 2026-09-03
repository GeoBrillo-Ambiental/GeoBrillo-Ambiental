<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

try {
    require_once __DIR__ . '/controllers/UsuarioControlador.php';

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        http_response_code(405);
        echo json_encode(["exito" => false, "mensaje" => "Método no permitido."]);
        exit();
    }

    $controlador = new UsuarioControlador();
    $resultado = $controlador->agregarUsuario($_POST);

    if (!$resultado['exito']) {
        http_response_code(400);
    }

    echo json_encode($resultado);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["exito" => false, "mensaje" => "Error interno del servidor: " . $e->getMessage()]);
}
exit();
