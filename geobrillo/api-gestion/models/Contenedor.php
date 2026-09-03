<?php
require_once __DIR__ . '/../config/conexion.php';

class Contenedor {

    private $db;

    public function __construct() {
        $this->db = conectar();
    }

    public function insertar($estado, $capacidad, $tipoReciduo, $idRuta) {
        $sql = "INSERT INTO Contenedor (EstadoCont, CapContenedor, TipoReciduo, Id_Ruta)
                VALUES (:estado, :capacidad, :tipo, :idRuta)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':estado'    => $estado,
            ':capacidad' => $capacidad,
            ':tipo'      => $tipoReciduo,
            ':idRuta'    => $idRuta,
        ]);
    }

    public function listar() {
        $sql = "SELECT Id_Contenedor, EstadoCont, CapContenedor, TipoReciduo, Id_Ruta
                FROM Contenedor
                ORDER BY Id_Contenedor DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function actualizar($id, $estado, $capacidad, $tipoReciduo, $idRuta) {
        $sql = "UPDATE Contenedor
                SET EstadoCont = :estado, CapContenedor = :capacidad, TipoReciduo = :tipo, Id_Ruta = :idRuta
                WHERE Id_Contenedor = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':estado'    => $estado,
            ':capacidad' => $capacidad,
            ':tipo'      => $tipoReciduo,
            ':idRuta'    => $idRuta,
            ':id'        => $id,
        ]);
    }

    public function eliminar($id) {
        $stmt = $this->db->prepare("DELETE FROM Contenedor WHERE Id_Contenedor = :id");
        return $stmt->execute([':id' => $id]);
    }
}
