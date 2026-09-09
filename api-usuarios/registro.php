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

    // Roles habilitados para el AUTOregistro público (Ciudadano, Chofer,
    // Operador, Cuadrilla — los mismos 4 que ofrece registro.html).
    // Id_Rol 5 (Administrador) queda deliberadamente afuera: ese rol solo
    // se puede asignar desde la gestión de usuarios ya autenticada
    // (usuarioapi.php, protegido por Auth::requireGestion) o desde
    // database/crear_admin.php.
    $rolesAutoregistro = [1, 2, 3, 4];

    $controlador = new UsuarioControlador();
    $resultado = $controlador->agregarUsuario($_POST, $rolesAutoregistro);

    if (!$resultado['exito']) {
        http_response_code(400);
    }

    echo json_encode($resultado);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["exito" => false, "mensaje" => "Error interno del servidor: " . $e->getMessage()]);
}
exit();
