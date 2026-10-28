<?php
require_once __DIR__ . '/../config/conexion.php';

class Mapa {

    private $db;

    public function __construct() {
        $this->db = conectar();
    }

    public function contenedores() {
        return $this->db->query(
            "SELECT c.Id_Contenedor, c.EstadoCont, c.TipoReciduo, c.Ubicacion, c.Latitud, c.Longitud,
                    c.Id_Ruta, c.OrdenRuta, r.DiaHora, z.NomZona
             FROM Contenedor c
             LEFT JOIN Ruta r ON r.Id_Ruta = c.Id_Ruta
             LEFT JOIN Zona z ON z.Id_Zona = r.Id_Zona
             WHERE c.Latitud IS NOT NULL AND c.Longitud IS NOT NULL
             ORDER BY c.Id_Ruta, c.OrdenRuta, c.Id_Contenedor"
        )->fetchAll();
    }

    public function contenedoresSinUbicacion() {
        return (int)$this->db->query(
            "SELECT COUNT(*) FROM Contenedor WHERE Latitud IS NULL OR Longitud IS NULL"
        )->fetchColumn();
    }

    public function incidenciasAbiertas() {
        return $this->db->query(
            "SELECT i.Id_Incidencia, i.EstadoInc, i.Descripcion, i.TipoProblema, c.Latitud, c.Longitud
             FROM Incidencia i
             INNER JOIN Contenedor c ON c.Id_Contenedor = i.Id_Contenedor
             WHERE i.EstadoInc <> 'Resuelta' AND c.Latitud IS NOT NULL AND c.Longitud IS NOT NULL"
        )->fetchAll();
    }

    public function centros() {
        return $this->db->query(
            "SELECT Id_Centro, NombreC, TipoCentro, DireccionC, Latitud, Longitud
             FROM CAcopioVertedero WHERE Latitud IS NOT NULL AND Longitud IS NOT NULL"
        )->fetchAll();
    }
}
