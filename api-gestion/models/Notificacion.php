<?php
require_once __DIR__ . '/../config/conexion.php';

class Notificacion {

    private $db;

    public function __construct() {
        $this->db = conectar();
    }

    public function insertar($tipo, $mensaje, $idIncidencia = null, $idContenedor = null) {
        $sql = "INSERT INTO Notificacion (Tipo, Mensaje, Fecha, Leida, Id_Incidencia, Id_Contenedor)
                VALUES (:tipo, :mensaje, NOW(), 0, :inc, :cont)";
        return $this->db->prepare($sql)->execute([
            ':tipo' => $tipo, ':mensaje' => $mensaje, ':inc' => $idIncidencia, ':cont' => $idContenedor,
        ]);
    }

    public function listar() {
        return $this->db->query(
            "SELECT Id_Notificacion, Tipo, Mensaje, Fecha, Leida, Id_Incidencia, Id_Contenedor
             FROM Notificacion ORDER BY Id_Notificacion DESC LIMIT 50"
        )->fetchAll();
    }

    public function contarNoLeidas() {
        return (int)$this->db->query("SELECT COUNT(*) FROM Notificacion WHERE Leida = 0")->fetchColumn();
    }

    public function marcarLeida($id) {
        return $this->db->prepare("UPDATE Notificacion SET Leida = 1 WHERE Id_Notificacion = :id")->execute([':id' => $id]);
    }

    public function marcarTodasLeidas() {
        return $this->db->exec("UPDATE Notificacion SET Leida = 1 WHERE Leida = 0") !== false;
    }
}
