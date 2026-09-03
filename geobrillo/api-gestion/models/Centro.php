<?php
require_once __DIR__ . '/../config/conexion.php';

class Centro {

    private $db;

    public function __construct() {
        $this->db = conectar();
    }

    public function insertar($nombre, $direccion, $tipo, $capTotal, $capActual) {
        $sql = "INSERT INTO CAcopioVertedero (NombreC, DireccionC, TipoCentro, CapTotal, CapActual)
                VALUES (:nombre, :direccion, :tipo, :capTotal, :capActual)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre'    => $nombre,
            ':direccion' => $direccion,
            ':tipo'      => $tipo,
            ':capTotal'  => $capTotal,
            ':capActual' => $capActual,
        ]);
    }

    public function listar() {
        $stmt = $this->db->query(
            "SELECT Id_Centro, NombreC, DireccionC, TipoCentro, CapTotal, CapActual
             FROM CAcopioVertedero ORDER BY Id_Centro DESC"
        );
        return $stmt->fetchAll();
    }

    public function actualizar($id, $nombre, $direccion, $tipo, $capTotal, $capActual) {
        $sql = "UPDATE CAcopioVertedero
                SET NombreC = :nombre, DireccionC = :direccion, TipoCentro = :tipo,
                    CapTotal = :capTotal, CapActual = :capActual
                WHERE Id_Centro = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre'    => $nombre,
            ':direccion' => $direccion,
            ':tipo'      => $tipo,
            ':capTotal'  => $capTotal,
            ':capActual' => $capActual,
            ':id'        => $id,
        ]);
    }

    public function eliminar($id) {
        $stmt = $this->db->prepare("DELETE FROM CAcopioVertedero WHERE Id_Centro = :id");
        return $stmt->execute([':id' => $id]);
    }
}
