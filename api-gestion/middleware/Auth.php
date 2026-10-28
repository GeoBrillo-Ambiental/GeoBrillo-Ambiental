<?php
require_once __DIR__ . '/../config/conexion.php';

class Auth {

    private static function buscarUsuarioPorToken($token) {
        if (empty($token)) {
            return null;
        }

        $pdo = conectar();
        $sql = "SELECT s.Fecha_Expiracion, u.Id_Usuario, u.Nom_Usuario, u.Id_Rol, r.Nombre_Rol
                FROM Sesion s
                INNER JOIN Usuario u ON s.Id_Usuario = u.Id_Usuario
                INNER JOIN Rol r ON u.Id_Rol = r.Id_Rol
                WHERE s.Token = :token AND u.Estado = 'Activo'
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':token' => $token]);
        $fila = $stmt->fetch();

        if (!$fila) {
            return null;
        }

        if (strtotime($fila['Fecha_Expiracion']) < time()) {
            $pdo->prepare("DELETE FROM Sesion WHERE Token = :token")->execute([':token' => $token]);
            return null;
        }

        return $fila;
    }

    public static function requireLogin($token) {
        $usuario = self::buscarUsuarioPorToken($token);

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
