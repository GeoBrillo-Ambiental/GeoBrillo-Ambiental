<?php
require_once __DIR__ . '/../config/conexion.php';

class Incidencia {

    private $db;

    public function __construct() {
        $this->db = conectar();
    }

    public function insertar($descripcion, $idContenedor, $idUsuario, $tipoProblema = null) {
        $sql = "INSERT INTO Incidencia (EstadoInc, Descripcion, TipoProblema, FchaReportado, Id_Cuadrilla, Id_Usuario, Id_Contenedor)
                VALUES ('Abierta', :descripcion, :tipo, CURDATE(), NULL, :idUsuario, :idContenedor)";
        $stmt = $this->db->prepare($sql);
        $ok = $stmt->execute([
            ':descripcion'  => $descripcion,
            ':tipo'         => $tipoProblema,
            ':idUsuario'    => $idUsuario,
            ':idContenedor' => $idContenedor,
        ]);

        return $ok ? (int)$this->db->lastInsertId() : false;
    }

    public function listar() {
        $stmt = $this->db->query(
            "SELECT i.Id_Incidencia, i.EstadoInc, i.Descripcion, i.TipoProblema, i.FchaReportado, i.FchaResuelto,
                    i.Id_Cuadrilla, i.Id_Usuario, i.Id_Contenedor,
                    u.Nom_Usuario AS ReportadoPor
             FROM Incidencia i
             LEFT JOIN Usuario u ON u.Id_Usuario = i.Id_Usuario
             ORDER BY i.Id_Incidencia DESC"
        );
        return $stmt->fetchAll();
    }

    public function actualizar($id, $estado, $idCuadrilla, $descripcion, $fchaResuelto) {
        $sql = "UPDATE Incidencia
                SET EstadoInc = :estado,
                    Id_Cuadrilla = :idCuadrilla,
                    Descripcion = :descripcion,
                    FchaResuelto = :fchaResuelto
                WHERE Id_Incidencia = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':estado'       => $estado,
            ':idCuadrilla'  => $idCuadrilla,
            ':descripcion'  => $descripcion,
            ':fchaResuelto' => $fchaResuelto,
            ':id'           => $id,
        ]);
    }

    public function eliminar($id) {
        $stmt = $this->db->prepare("DELETE FROM Incidencia WHERE Id_Incidencia = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function obtenerContenedorDe($id) {
        $stmt = $this->db->prepare("SELECT Id_Contenedor FROM Incidencia WHERE Id_Incidencia = :id");
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch();
        return $fila ? $fila['Id_Contenedor'] : null;
    }

    public function listarCuadrillas() {
        $stmt = $this->db->query(
            "SELECT Id_Cuadrilla, Dia, Horario FROM Cuadrilla ORDER BY Id_Cuadrilla"
        );
        return $stmt->fetchAll();
    }
}
