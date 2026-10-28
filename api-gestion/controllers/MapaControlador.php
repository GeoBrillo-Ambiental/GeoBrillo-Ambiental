<?php
require_once __DIR__ . '/../models/Mapa.php';

class MapaControlador {

    private $modelo;

    public function __construct($modelo = null) {
        $this->modelo = $modelo ?? new Mapa();
    }

    public function datos() {
        return [
            "exito" => true,
            "data" => [
                "contenedores" => $this->modelo->contenedores(),
                "incidencias"  => $this->modelo->incidenciasAbiertas(),
                "centros"      => $this->modelo->centros(),
                "sin_ubicacion" => $this->modelo->contenedoresSinUbicacion(),
            ],
        ];
    }
}
