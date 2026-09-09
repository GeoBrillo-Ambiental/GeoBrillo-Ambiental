<?php
require_once __DIR__ . '/../models/Sesion.php';

/**
 * Middleware de autorización.
 *
 * Centraliza la regla de negocio pedida por la cátedra:
 * "un Ciudadano no puede tener control en ningún CRUD (ni de camión,
 * usuarios o contenedores)". Cualquier otro rol (Chofer, Operador,
 * Cuadrilla, Administrador) sí puede.
 *
 * Devuelve el usuario autenticado (array) o corta la ejecución con
 * 401/403 y un JSON de error, siguiendo los códigos HTTP que pide la
 * guía "Testing de APIs y Backoffice".
 */
class Auth {

    /** Exige que exista un token válido. No exige ningún rol en particular. */
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

    /**
     * Exige un token válido Y que el rol NO sea Ciudadano.
     * Se usa en todas las acciones de alta/edición/baja de los CRUD.
     */
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
