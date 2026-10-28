<?php
require_once __DIR__ . '/../models/Sesion.php';

class Auth {

    public static function requireLogin($token) {
        $sesion = new Sesion();
        $usuario = $sesion->validar($token);

        if (!$usuario) {
            http_response_code(401);
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode(["exito" => false, "mensaje" => "No autorizado. Debe iniciar sesión."]);
            exit();
        }

        return $usuario;
    }

    public static function requireGestion($token) {
        $usuario = self::requireLogin($token);

        if ($usuario['Nombre_Rol'] === 'Ciudadano') {
            http_response_code(403);
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode(["exito" => false, "mensaje" => "Los usuarios con rol Ciudadano no tienen permiso para gestionar este recurso."]);
            exit();
        }

        return $usuario;
    }
}
