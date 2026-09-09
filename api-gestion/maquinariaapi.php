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
    require_once __DIR__ . '/controllers/MaquinariaControlador.php';
    require_once __DIR__ . '/middleware/Auth.php';

    $controlador = new MaquinariaControlador();
    $accion = $_POST['accion'] ?? $_GET['accion'] ?? '';
    $token  = $_POST['token']  ?? $_GET['token']  ?? '';

    switch ($accion) {

        case 'listar':
            Auth::requireGestion($token);
            echo json_encode($controlador->obtenerMaquinaria());
            break;

        case 'insertar':
            Auth::requireGestion($token);
            echo json_encode($controlador->agregarMaquinaria($_POST));
            break;

        case 'actualizar':
            Auth::requireGestion($token);
            $id = $_POST['id'] ?? '';
            echo json_encode($controlador->actualizarMaquinaria($id, $_POST));
            break;

        case 'eliminar':
            Auth::requireGestion($token);
            $id = $_POST['id'] ?? $_GET['id'] ?? '';
            echo json_encode($controlador->borrarMaquinaria($id));
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
