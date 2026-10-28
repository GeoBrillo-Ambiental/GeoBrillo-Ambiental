<?php
require_once __DIR__ . '/../config/conexion.php';

class Sesion {

    private $db;

    public function __construct() {
        $this->db = conectar();
    }

    public function crear($idUsuario, $horasValidez = 8) {
        $token = bin2hex(random_bytes(32));
        $creacion = date('Y-m-d H:i:s');
        $expiracion = date('Y-m-d H:i:s', strtotime("+{$horasValidez} hours"));

        $sql = "INSERT INTO Sesion (Token, Id_Usuario, Fecha_Creacion, Fecha_Expiracion)
                VALUES (:token, :idUsuario, :creacion, :expiracion)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':token'      => $token,
            ':idUsuario'  => $idUsuario,
            ':creacion'   => $creacion,
            ':expiracion' => $expiracion,
        ]);

        return $token;
    }

    public function validar($token) {
        if (empty($token)) {
            return null;
        }

        $sql = "SELECT s.Token, s.Fecha_Expiracion,
                       u.Id_Usuario, u.Pri_Nom, u.Pri_Ape, u.Nom_Usuario, u.Email,
                       u.Id_Rol, r.Nombre_Rol
                FROM Sesion s
                INNER JOIN Usuario u ON s.Id_Usuario = u.Id_Usuario
                INNER JOIN Rol r ON u.Id_Rol = r.Id_Rol
                WHERE s.Token = :token AND u.Estado = 'Activo'
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':token' => $token]);
        $fila = $stmt->fetch();

        if (!$fila) {
            return null;
        }

        if (strtotime($fila['Fecha_Expiracion']) < time()) {
            $this->eliminar($token);
            return null;
        }

        return $fila;
    }

    public function eliminar($token) {
        $stmt = $this->db->prepare("DELETE FROM Sesion WHERE Token = :token");
        return $stmt->execute([':token' => $token]);
    }
}
