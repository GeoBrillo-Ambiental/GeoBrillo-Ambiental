<?php
require_once __DIR__ . '/../config/conexion.php';

class Ruta {

    private $db;

    public function __construct() {
        $this->db = conectar();
    }

    public function insertar($dia_hora, $id_zona, $id_centro, $id_cuadrilla) {
        $sql = "INSERT INTO Ruta (DiaHora, Id_Zona, Id_Centro, Id_Cuadrilla) VALUES (:dia_hora, :id_zona, :id_centro, :id_cuadrilla)";
        return $this->db->prepare($sql)->execute([
            ':dia_hora' => $dia_hora,
            ':id_zona' => $id_zona,
            ':id_centro' => $id_centro,
            ':id_cuadrilla' => $id_cuadrilla,
        ]);
    }

    public function listar() {
        return $this->db->query("SELECT r.Id_Ruta, r.DiaHora, r.Id_Zona, z.NomZona, r.Id_Centro, r.Id_Cuadrilla FROM Ruta r LEFT JOIN Zona z ON z.Id_Zona = r.Id_Zona ORDER BY r.Id_Ruta DESC")->fetchAll();
    }

    public function actualizar($id, $dia_hora, $id_zona, $id_centro, $id_cuadrilla) {
        $sql = "UPDATE Ruta SET DiaHora = :dia_hora, Id_Zona = :id_zona, Id_Centro = :id_centro, Id_Cuadrilla = :id_cuadrilla WHERE Id_Ruta = :id";
        return $this->db->prepare($sql)->execute([
            ':dia_hora' => $dia_hora,
            ':id_zona' => $id_zona,
            ':id_centro' => $id_centro,
            ':id_cuadrilla' => $id_cuadrilla,
            ':id' => $id,
        ]);
    }

    public function eliminar($id) {
        return $this->db->prepare("DELETE FROM Ruta WHERE Id_Ruta = :id")->execute([':id' => $id]);
    }

    public function listarZonas() {
        return $this->db->query("SELECT Id_Zona, NomZona FROM Zona ORDER BY Id_Zona")->fetchAll();
    }

    public function existe($idRuta) {
        $st = $this->db->prepare("SELECT COUNT(*) FROM Ruta WHERE Id_Ruta = :id");
        $st->execute([':id' => $idRuta]);
        return (int)$st->fetchColumn() > 0;
    }

    public function contarContenedores(array $ids) {
        if (count($ids) === 0) { return 0; }
        $marcas = implode(',', array_fill(0, count($ids), '?'));
        $st = $this->db->prepare("SELECT COUNT(*) FROM Contenedor WHERE Id_Contenedor IN ($marcas)");
        $st->execute(array_values($ids));
        return (int)$st->fetchColumn();
    }

    public function asignarContenedores($idRuta, array $idsEnOrden) {
        $this->db->beginTransaction();
        try {
            $this->db->prepare("UPDATE Contenedor SET Id_Ruta = NULL, OrdenRuta = NULL WHERE Id_Ruta = :ruta")
                     ->execute([':ruta' => $idRuta]);
            $up = $this->db->prepare("UPDATE Contenedor SET Id_Ruta = :ruta, OrdenRuta = :orden WHERE Id_Contenedor = :id");
            foreach (array_values($idsEnOrden) as $i => $idCont) {
                $up->execute([':ruta' => $idRuta, ':orden' => $i + 1, ':id' => $idCont]);
            }
            $this->db->commit();
            return true;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
