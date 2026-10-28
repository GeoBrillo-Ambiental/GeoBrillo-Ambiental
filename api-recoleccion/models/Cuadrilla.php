<?php
require_once __DIR__ . '/../config/conexion.php';

class Cuadrilla {

    private $db;

    public function __construct() {
        $this->db = conectar();
    }

    public function insertar($dia, $horario, $id_rol, $id_rol2, $id_camion) {
        $sql = "INSERT INTO Cuadrilla (Dia, Horario, Id_Rol, Id_Rol2, Id_Camion) VALUES (:dia, :horario, :id_rol, :id_rol2, :id_camion)";
        return $this->db->prepare($sql)->execute([
            ':dia' => $dia,
            ':horario' => $horario,
            ':id_rol' => $id_rol,
            ':id_rol2' => $id_rol2,
            ':id_camion' => $id_camion,
        ]);
    }

    public function listar() {
        return $this->db->query("SELECT Id_Cuadrilla, Dia, Horario, Id_Rol, Id_Rol2, Id_Camion FROM Cuadrilla ORDER BY Id_Cuadrilla DESC")->fetchAll();
    }

    public function actualizar($id, $dia, $horario, $id_rol, $id_rol2, $id_camion) {
        $sql = "UPDATE Cuadrilla SET Dia = :dia, Horario = :horario, Id_Rol = :id_rol, Id_Rol2 = :id_rol2, Id_Camion = :id_camion WHERE Id_Cuadrilla = :id";
        return $this->db->prepare($sql)->execute([
            ':dia' => $dia,
            ':horario' => $horario,
            ':id_rol' => $id_rol,
            ':id_rol2' => $id_rol2,
            ':id_camion' => $id_camion,
            ':id' => $id,
        ]);
    }

    public function eliminar($id) {
        return $this->db->prepare("DELETE FROM Cuadrilla WHERE Id_Cuadrilla = :id")->execute([':id' => $id]);
    }
}
