<?php
require_once __DIR__ . '/../config/conexion.php';

class Recoleccion {

    private $db;

    public function __construct() {
        $this->db = conectar();
    }

    public function insertar($fecha, $kg, $observaciones, $id_ruta, $id_contenedor) {
        $sql = "INSERT INTO Recoleccion (Fecha, KgRecolectados, Observaciones, Id_Ruta, Id_Contenedor) VALUES (:fecha, :kg, :observaciones, :id_ruta, :id_contenedor)";
        return $this->db->prepare($sql)->execute([
            ':fecha' => $fecha,
            ':kg' => $kg,
            ':observaciones' => $observaciones,
            ':id_ruta' => $id_ruta,
            ':id_contenedor' => $id_contenedor,
        ]);
    }

    public function listar() {
        return $this->db->query("SELECT Id_Recoleccion, Fecha, KgRecolectados, Observaciones, Id_Ruta, Id_Contenedor FROM Recoleccion ORDER BY Fecha DESC, Id_Recoleccion DESC")->fetchAll();
    }

    public function actualizar($id, $fecha, $kg, $observaciones, $id_ruta, $id_contenedor) {
        $sql = "UPDATE Recoleccion SET Fecha = :fecha, KgRecolectados = :kg, Observaciones = :observaciones, Id_Ruta = :id_ruta, Id_Contenedor = :id_contenedor WHERE Id_Recoleccion = :id";
        return $this->db->prepare($sql)->execute([
            ':fecha' => $fecha,
            ':kg' => $kg,
            ':observaciones' => $observaciones,
            ':id_ruta' => $id_ruta,
            ':id_contenedor' => $id_contenedor,
            ':id' => $id,
        ]);
    }

    public function eliminar($id) {
        return $this->db->prepare("DELETE FROM Recoleccion WHERE Id_Recoleccion = :id")->execute([':id' => $id]);
    }
}
