<?php
require_once __DIR__ . '/../config/conexion.php';

class Contenedor {

    private $db;

    public function __construct() {
        $this->db = conectar();
    }

    public function insertar($estado, $capacidad, $tipoReciduo, $idRuta, $ubicacion = null, $lat = null, $lng = null) {
        $sql = "INSERT INTO Contenedor (EstadoCont, CapContenedor, TipoReciduo, Id_Ruta, Ubicacion, Latitud, Longitud)
                VALUES (:estado, :capacidad, :tipo, :idRuta, :ubicacion, :lat, :lng)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':estado'    => $estado,
            ':capacidad' => $capacidad,
            ':tipo'      => $tipoReciduo,
            ':idRuta'    => $idRuta,
            ':ubicacion' => $ubicacion,
            ':lat'       => $lat,
            ':lng'       => $lng,
        ]);
    }

    public function listar() {
        $sql = "SELECT Id_Contenedor, EstadoCont, CapContenedor, TipoReciduo, Id_Ruta, OrdenRuta, Ubicacion, Latitud, Longitud
                FROM Contenedor
                ORDER BY Id_Contenedor DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function actualizar($id, $estado, $capacidad, $tipoReciduo, $idRuta, $ubicacion = null, $lat = null, $lng = null) {
        $sql = "UPDATE Contenedor
                SET EstadoCont = :estado, CapContenedor = :capacidad, TipoReciduo = :tipo,
                    OrdenRuta = CASE WHEN Id_Ruta = :idRutaActual THEN OrdenRuta ELSE NULL END,
                    Id_Ruta = :idRuta,
                    Ubicacion = :ubicacion, Latitud = :lat, Longitud = :lng
                WHERE Id_Contenedor = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':estado'    => $estado,
            ':capacidad' => $capacidad,
            ':tipo'      => $tipoReciduo,
            ':idRutaActual' => $idRuta,
            ':idRuta'    => $idRuta,
            ':ubicacion' => $ubicacion,
            ':lat'       => $lat,
            ':lng'       => $lng,
            ':id'        => $id,
        ]);
    }

    public function eliminar($id) {
        $stmt = $this->db->prepare("DELETE FROM Contenedor WHERE Id_Contenedor = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function actualizarEstado($id, $estado) {
        $stmt = $this->db->prepare("UPDATE Contenedor SET EstadoCont = :estado WHERE Id_Contenedor = :id");
        return $stmt->execute([':estado' => $estado, ':id' => $id]);
    }
}
