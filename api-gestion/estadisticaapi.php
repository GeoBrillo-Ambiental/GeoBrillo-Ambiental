<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

try {
    require_once __DIR__ . '/controllers/EstadisticaControlador.php';
    require_once __DIR__ . '/middleware/Auth.php';

    $controlador = new EstadisticaControlador();
    $accion = $_POST['accion'] ?? $_GET['accion'] ?? '';
    $token  = $_POST['token']  ?? $_GET['token']  ?? '';

    switch ($accion) {

        case 'panel':
            Auth::requireGestion($token);
            echo json_encode($controlador->panel());
            break;

        case 'informe':
            Auth::requireGestion($token);
            $res = $controlador->informeIncidencias($_GET['tipo'] ?? $_POST['tipo'] ?? '');
            if (!$res['exito']) { http_response_code(400); }
            echo json_encode($res);
            break;

        default:
            http_response_code(400);
            echo json_encode(["exito" => false, "mensaje" => "Acción no válida."]);
            break;
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["exito" => false, "mensaje" => "Error interno del servidor: " . $e->getMessage()]);
}
exit();
