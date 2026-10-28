<?php
require_once __DIR__ . '/../config/conexion.php';

class Contacto {

    private $db;

    public function __construct() {
        $this->db = conectar();
    }

    public function insertar($nombre, $email, $telefono, $mensaje) {
        $sql = "INSERT INTO Contacto (Nombre, Email, Telefono, Mensaje, Fecha, Leido)
                VALUES (:nombre, :email, :telefono, :mensaje, NOW(), 0)";
        return $this->db->prepare($sql)->execute([
            ':nombre' => $nombre, ':email' => $email, ':telefono' => $telefono, ':mensaje' => $mensaje,
        ]);
    }

    public function listar() {
        return $this->db->query(
            "SELECT Id_Contacto, Nombre, Email, Telefono, Mensaje, Fecha, Leido
             FROM Contacto ORDER BY Id_Contacto DESC LIMIT 200"
        )->fetchAll();
    }

    public function marcarLeido($id) {
        return $this->db->prepare("UPDATE Contacto SET Leido = 1 WHERE Id_Contacto = :id")->execute([':id' => $id]);
    }

    public function eliminar($id) {
        return $this->db->prepare("DELETE FROM Contacto WHERE Id_Contacto = :id")->execute([':id' => $id]);
    }
}
