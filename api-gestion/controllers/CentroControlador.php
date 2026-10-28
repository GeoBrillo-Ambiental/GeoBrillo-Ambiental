<?php
require_once __DIR__ . '/../models/Centro.php';

class CentroControlador {

    private $modelo;

    public function __construct($modelo = null) {
        $this->modelo = $modelo ?? new Centro();
    }

    private function leerCoordenadas($datos) {
        $lat = trim((string)($datos['latitud'] ?? ''));
        $lng = trim((string)($datos['longitud'] ?? ''));
        if ($lat !== '' || $lng !== '') {
            if (!is_numeric($lat) || !is_numeric($lng) || abs($lat) > 90 || abs($lng) > 180) {
                return null;
            }
        }
        return [$lat === '' ? null : (float)$lat, $lng === '' ? null : (float)$lng];
    }

    public function agregarCentro($datos) {
        $nombre    = trim($datos['nombre'] ?? '');
        $direccion = trim($datos['direccion'] ?? '');
        $tipo      = trim($datos['tipo'] ?? '');
        $capTotal  = $datos['cap_total'] ?? '';
        $capActual = $datos['cap_actual'] ?? '';

        if ($nombre === '' || $direccion === '' || $tipo === '' || $capTotal === '' || $capActual === '') {
            return ["exito" => false, "mensaje" => "Todos los campos son obligatorios."];
        }

        $coord = $this->leerCoordenadas($datos);
        if ($coord === null) {
            return ["exito" => false, "mensaje" => "Latitud/longitud inválidas."];
        }

        $resultado = $this->modelo->insertar($nombre, $direccion, $tipo, (int)$capTotal, (int)$capActual, $coord[0], $coord[1]);

        return $resultado
            ? ["exito" => true, "mensaje" => "Centro guardado con éxito."]
            : ["exito" => false, "mensaje" => "Error al guardar el centro."];
    }

    public function obtenerCentros() {
        return ["exito" => true, "data" => $this->modelo->listar()];
    }

    public function actualizarCentro($id, $datos) {
        if (empty($id)) {
            return ["exito" => false, "mensaje" => "ID no válido."];
        }
        $nombre    = trim($datos['nombre'] ?? '');
        $direccion = trim($datos['direccion'] ?? '');
        $tipo      = trim($datos['tipo'] ?? '');
        $capTotal  = $datos['cap_total'] ?? '';
        $capActual = $datos['cap_actual'] ?? '';

        if ($nombre === '' || $direccion === '' || $tipo === '' || $capTotal === '' || $capActual === '') {
            return ["exito" => false, "mensaje" => "Todos los campos son obligatorios."];
        }

        $coord = $this->leerCoordenadas($datos);
        if ($coord === null) {
            return ["exito" => false, "mensaje" => "Latitud/longitud inválidas."];
        }

        $resultado = $this->modelo->actualizar($id, $nombre, $direccion, $tipo, (int)$capTotal, (int)$capActual, $coord[0], $coord[1]);

        return $resultado
            ? ["exito" => true, "mensaje" => "Centro actualizado correctamente."]
            : ["exito" => false, "mensaje" => "Error al actualizar el centro."];
    }

    public function borrarCentro($id) {
        if (empty($id)) {
            return ["exito" => false, "mensaje" => "ID no válido."];
        }
        $resultado = $this->modelo->eliminar($id);
        return $resultado
            ? ["exito" => true, "mensaje" => "Centro eliminado correctamente."]
            : ["exito" => false, "mensaje" => "Error al eliminar el centro."];
    }
}
