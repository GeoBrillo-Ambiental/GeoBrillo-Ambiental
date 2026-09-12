<?php
require_once __DIR__ . '/../config/conexion.php';

class Incidencia {

    private $db;

    public function __construct() {
        $this->db = conectar();
    }

    /**
     * Alta de una incidencia. La puede reportar CUALQUIER usuario
     * logueado (incluido Ciudadano). Se crea siempre en estado
     * "Reportada" y SIN cuadrilla asignada (Id_Cuadrilla = NULL):
     * asignar una cuadrilla es tarea de gestión, en un paso posterior
     * (ver actualizar()).
     */
    public function insertar($descripcion, $idContenedor, $idUsuario) {
        $sql = "INSERT INTO Incidencia (EstadoInc, Descripcion, FchaReportado, Id_Cuadrilla, Id_Usuario, Id_Contenedor)
                VALUES ('Reportada', :descripcion, CURDATE(), NULL, :idUsuario, :idContenedor)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':descripcion'  => $descripcion,
            ':idUsuario'    => $idUsuario,
            ':idContenedor' => $idContenedor,
        ]);
    }

    public function listar() {
        $stmt = $this->db->query(
            "SELECT i.Id_Incidencia, i.EstadoInc, i.Descripcion, i.FchaReportado, i.FchaResuelto,
                    i.Id_Cuadrilla, i.Id_Usuario, i.Id_Contenedor,
                    u.Nom_Usuario AS ReportadoPor
             FROM Incidencia i
             LEFT JOIN Usuario u ON u.Id_Usuario = i.Id_Usuario
             ORDER BY i.Id_Incidencia DESC"
        );
        return $stmt->fetchAll();
    }

    /**
     * Actualización de gestión: asignar/cambiar cuadrilla, cambiar
     * estado, y marcar fecha de resolución cuando corresponda.
     * Si el nuevo estado es "Resuelta" y no se manda fecha, se usa
     * la fecha de hoy automáticamente.
     */
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

    /** Para saber a qué contenedor afecta una incidencia (ej. al resolverla). */
    public function obtenerContenedorDe($id) {
        $stmt = $this->db->prepare("SELECT Id_Contenedor FROM Incidencia WHERE Id_Incidencia = :id");
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch();
        return $fila ? $fila['Id_Contenedor'] : null;
    }

    /** Para el combo de "asignar cuadrilla" en la pantalla de gestión. */
    public function listarCuadrillas() {
        $stmt = $this->db->query(
            "SELECT Id_Cuadrilla, Dia, Horario FROM Cuadrilla ORDER BY Id_Cuadrilla"
        );
        return $stmt->fetchAll();
    }
}
