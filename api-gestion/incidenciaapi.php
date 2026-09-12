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
            // Cualquier usuario logueado puede ver el listado (incluido
            // Ciudadano): es la regla "registro y seguimiento de
            // incidencias" de la letra del proyecto.
            Auth::requireLogin($token);
            echo json_encode($controlador->obtenerIncidencias());
            break;

        case 'reportar':
            // Regla de negocio especial: acá el Ciudadano SÍ puede
            // (de hecho, es quien más va a usar esta acción). El
            // Id_Usuario que queda registrado sale del token validado,
            // nunca de lo que mande el cliente por POST.
            $usuario = Auth::requireLogin($token);
            echo json_encode($controlador->reportarIncidencia($_POST, $usuario['Id_Usuario']));
            break;

        case 'listarCuadrillas':
            // Para el combo de "asignar cuadrilla" en la pantalla de
            // gestión. Solo hace falta login, no cambia nada por sí
            // sola (es de solo lectura).
            Auth::requireLogin($token);
            echo json_encode($controlador->obtenerCuadrillas());
            break;

        case 'actualizar':
            // Asignar cuadrilla, cambiar estado o cerrar una
            // incidencia: solo lo puede hacer gestión, no el Ciudadano
            // que la reportó.
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
