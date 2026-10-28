<?php
require_once __DIR__ . '/../config/conexion.php';

class Camion {

    private $db;

    public function __construct() {
        $this->db = conectar();
    }

    public function insertar($estado, $modelo, $carga) {
        $sql = "INSERT INTO Camion (EstadoCam, Modelo, C_Carga) VALUES (:estado, :modelo, :carga)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':estado' => $estado,
            ':modelo' => $modelo,
            ':carga'  => $carga,
        ]);
    }

    public function listar() {
        $stmt = $this->db->query("SELECT Id_Camion, EstadoCam, Modelo, C_Carga FROM Camion ORDER BY Id_Camion DESC");
        return $stmt->fetchAll();
    }

    public function actualizar($id, $estado, $modelo, $carga) {
        $sql = "UPDATE Camion SET EstadoCam = :estado, Modelo = :modelo, C_Carga = :carga WHERE Id_Camion = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':estado' => $estado,
            ':modelo' => $modelo,
            ':carga'  => $carga,
            ':id'     => $id,
        ]);
    }

    public function eliminar($id) {
        $stmt = $this->db->prepare("DELETE FROM Camion WHERE Id_Camion = :id");
        return $stmt->execute([':id' => $id]);
    }
}
