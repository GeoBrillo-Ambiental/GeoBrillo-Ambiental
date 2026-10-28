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
    require_once __DIR__ . '/controllers/UsuarioControlador.php';
    require_once __DIR__ . '/middleware/Auth.php';

    $controlador = new UsuarioControlador();
    $accion = $_POST['accion'] ?? $_GET['accion'] ?? '';
    $token  = $_POST['token']  ?? $_GET['token']  ?? '';

    switch ($accion) {

        case 'listar':

            Auth::requireGestion($token);
            echo json_encode($controlador->obtenerUsuarios());
            break;

        case 'insertar':
            $actor = Auth::requireGestion($token);
            $permitidos = ($actor['Nombre_Rol'] === 'Administrador') ? null : [1, 2, 3, 4];
            echo json_encode($controlador->agregarUsuario($_POST, $permitidos));
            break;

        case 'actualizar':
            $actor = Auth::requireGestion($token);
            $id = $_POST['id'] ?? '';
            $esAdmin = ($actor['Nombre_Rol'] === 'Administrador');
            if (!$esAdmin && $controlador->esAdministrador($id)) {
                http_response_code(403);
                echo json_encode(["exito" => false, "mensaje" => "Solo un Administrador puede modificar a otro Administrador."]);
                break;
            }
            echo json_encode($controlador->actualizarUsuario($id, $_POST, $esAdmin ? null : [1, 2, 3, 4]));
            break;

        case 'aprobar':
        case 'rechazar':
            $actor = Auth::requireGestion($token);
            if ($actor['Nombre_Rol'] !== 'Administrador') {
                http_response_code(403);
                echo json_encode(["exito" => false, "mensaje" => "Solo un Administrador puede resolver solicitudes de registro."]);
                break;
            }
            echo json_encode($controlador->resolverSolicitud($_POST['id'] ?? '', $accion === 'aprobar' ? 'Activo' : 'Rechazado'));
            break;

        case 'eliminar':
            $actor = Auth::requireGestion($token);
            $id = $_POST['id'] ?? $_GET['id'] ?? '';
            if ($actor['Nombre_Rol'] !== 'Administrador' && $controlador->esAdministrador($id)) {
                http_response_code(403);
                echo json_encode(["exito" => false, "mensaje" => "Solo un Administrador puede eliminar a otro Administrador."]);
                break;
            }
            echo json_encode($controlador->borrarUsuario($id));
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
