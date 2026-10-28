<?php
require_once __DIR__ . '/../config/conexion.php';

class Estadistica {

    private $db;

    public function __construct() {
        $this->db = conectar();
    }

    public function topDesbordadosDelMes($limite = 5) {
        $limite = max(1, (int)$limite);
        $sql = "SELECT c.Id_Contenedor, c.Ubicacion, COUNT(*) AS Reportes
                FROM Incidencia i
                INNER JOIN Contenedor c ON c.Id_Contenedor = i.Id_Contenedor
                WHERE i.TipoProblema = 'Desbordado'
                  AND YEAR(i.FchaReportado) = YEAR(CURDATE())
                  AND MONTH(i.FchaReportado) = MONTH(CURDATE())
                GROUP BY c.Id_Contenedor, c.Ubicacion
                ORDER BY Reportes DESC, c.Id_Contenedor
                LIMIT $limite";
        return $this->db->query($sql)->fetchAll();
    }

    public function incidenciasPorEstado() {
        return $this->db->query("SELECT EstadoInc AS Estado, COUNT(*) AS Total FROM Incidencia GROUP BY EstadoInc ORDER BY Total DESC")->fetchAll();
    }

    public function contenedoresPorEstado() {
        return $this->db->query("SELECT EstadoCont AS Estado, COUNT(*) AS Total FROM Contenedor GROUP BY EstadoCont ORDER BY Total DESC")->fetchAll();
    }

    public function kgPorZona() {
        return $this->db->query(
            "SELECT z.NomZona AS Zona, SUM(r.KgRecolectados) AS Kg
             FROM Recoleccion r
             INNER JOIN Ruta ru ON ru.Id_Ruta = r.Id_Ruta
             INNER JOIN Zona z ON z.Id_Zona = ru.Id_Zona
             GROUP BY z.Id_Zona, z.NomZona
             ORDER BY Kg DESC"
        )->fetchAll();
    }

    public function zonasCriticas() {
        return $this->db->query(
            "SELECT z.NomZona AS Zona, COUNT(*) AS Incidencias
             FROM Incidencia i
             INNER JOIN Contenedor c ON c.Id_Contenedor = i.Id_Contenedor
             INNER JOIN Ruta r ON r.Id_Ruta = c.Id_Ruta
             INNER JOIN Zona z ON z.Id_Zona = r.Id_Zona
             GROUP BY z.Id_Zona, z.NomZona
             ORDER BY Incidencias DESC, z.NomZona"
        )->fetchAll();
    }

    public function fechasResueltas() {
        return $this->db->query(
            "SELECT FchaReportado, FchaResuelto FROM Incidencia
             WHERE EstadoInc = 'Resuelta' AND FchaResuelto IS NOT NULL"
        )->fetchAll();
    }

    public function recoleccionesPorMes() {
        return $this->db->query(
            "SELECT SUBSTR(Fecha, 1, 7) AS Mes, COUNT(*) AS Recolecciones, SUM(KgRecolectados) AS Kg
             FROM Recoleccion
             GROUP BY Mes
             ORDER BY Mes"
        )->fetchAll();
    }

    public function informeIncidencias($cerradas) {
        $op = $cerradas ? "=" : "<>";
        return $this->db->query(
            "SELECT i.Id_Incidencia, i.EstadoInc, i.Descripcion, i.TipoProblema, i.FchaReportado, i.FchaResuelto, i.Id_Contenedor
             FROM Incidencia i WHERE i.EstadoInc $op 'Resuelta' ORDER BY i.Id_Incidencia DESC"
        )->fetchAll();
    }
}
