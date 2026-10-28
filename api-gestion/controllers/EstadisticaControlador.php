<?php
require_once __DIR__ . '/../models/Estadistica.php';

class EstadisticaControlador {

    private $modelo;

    public function __construct($modelo = null) {
        $this->modelo = $modelo ?? new Estadistica();
    }

    public function panel() {
        $porEstado = $this->modelo->incidenciasPorEstado();
        $abiertas = 0; $cerradas = 0;
        foreach ($porEstado as $fila) {
            if ($fila['Estado'] === 'Resuelta') { $cerradas += (int)$fila['Total']; }
            else { $abiertas += (int)$fila['Total']; }
        }
        $contenedores = $this->modelo->contenedoresPorEstado();
        $repuesto = 0;
        foreach ($contenedores as $fila) {
            if ($fila['Estado'] === 'Repuesto') { $repuesto = (int)$fila['Total']; }
        }
        return [
            "exito" => true,
            "data" => [
                "contenedores_repuesto"   => $repuesto,
                "tiempo_medio_resolucion_dias" => $this->tiempoMedioResolucion(),
                "zonas_criticas"          => $this->modelo->zonasCriticas(),
                "recolecciones_por_mes"   => $this->modelo->recoleccionesPorMes(),
                "incidencias_abiertas"    => $abiertas,
                "incidencias_cerradas"    => $cerradas,
                "incidencias_por_estado"  => $porEstado,
                "contenedores_por_estado" => $contenedores,
                "top_desbordados_mes"     => $this->modelo->topDesbordadosDelMes(5),
                "kg_por_zona"             => $this->modelo->kgPorZona(),
            ],
        ];
    }

    private function tiempoMedioResolucion() {
        $filas = $this->modelo->fechasResueltas();
        if (count($filas) === 0) { return null; }
        $total = 0;
        foreach ($filas as $f) {
            $dias = (strtotime($f['FchaResuelto']) - strtotime($f['FchaReportado'])) / 86400;
            $total += max(0, $dias);
        }
        return round($total / count($filas), 1);
    }

    public function informeIncidencias($tipo) {
        if ($tipo !== 'abiertas' && $tipo !== 'cerradas') {
            return ["exito" => false, "mensaje" => "Tipo de informe no válido (abiertas o cerradas)."];
        }
        return ["exito" => true, "data" => $this->modelo->informeIncidencias($tipo === 'cerradas')];
    }
}
