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
    require_once __DIR__ . '/controllers/IncidenciaControlador.php';
    require_once __DIR__ . '/middleware/Auth.php';

    $controlador = new IncidenciaControlador();
    $accion = $_POST['accion'] ?? $_GET['accion'] ?? '';
    $token  = $_POST['token']  ?? $_GET['token']  ?? '';

    switch ($accion) {

        case 'listar':

            Auth::requireLogin($token);
            echo json_encode($controlador->obtenerIncidencias());
            break;

        case 'reportar':

            $usuario = Auth::requireLogin($token);
            echo json_encode($controlador->reportarIncidencia($_POST, $usuario['Id_Usuario']));
            break;

        case 'listarCuadrillas':

            Auth::requireLogin($token);
            echo json_encode($controlador->obtenerCuadrillas());
            break;

        case 'actualizar':

            Auth::requireGestion($token);
            $id = $_POST['id'] ?? '';
            echo json_encode($controlador->actualizarIncidencia($id, $_POST));
            break;

        case 'eliminar':
            Auth::requireGestion($token);
            $id = $_POST['id'] ?? $_GET['id'] ?? '';
            echo json_encode($controlador->borrarIncidencia($id));
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
