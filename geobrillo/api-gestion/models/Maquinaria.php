<?php
require_once __DIR__ . '/../config/conexion.php';

class Maquinaria {

    private $db;

    public function __construct() {
        $this->db = conectar();
    }

    public function insertar($nombre, $tipo, $estado, $idCentro) {
        $sql = "INSERT INTO Maquinaria (NombreMaq, TipoMaq, EstadoMaq, Id_Centro)
                VALUES (:nombre, :tipo, :estado, :idCentro)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre'   => $nombre,
            ':tipo'     => $tipo,
            ':estado'   => $estado,
            ':idCentro' => $idCentro,
        ]);
    }

    public function listar() {
        $sql = "SELECT m.Id_Maquinaria, m.NombreMaq, m.TipoMaq, m.EstadoMaq, m.Id_Centro, c.NombreC
                FROM Maquinaria m
                LEFT JOIN CAcopioVertedero c ON m.Id_Centro = c.Id_Centro
                ORDER BY m.Id_Maquinaria DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function actualizar($id, $nombre, $tipo, $estado, $idCentro) {
        $sql = "UPDATE Maquinaria
                SET NombreMaq = :nombre, TipoMaq = :tipo, EstadoMaq = :estado, Id_Centro = :idCentro
                WHERE Id_Maquinaria = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre'   => $nombre,
            ':tipo'     => $tipo,
            ':estado'   => $estado,
            ':idCentro' => $idCentro,
            ':id'       => $id,
        ]);
    }

    public function eliminar($id) {
        $stmt = $this->db->prepare("DELETE FROM Maquinaria WHERE Id_Maquinaria = :id");
        return $stmt->execute([':id' => $id]);
    }
}
