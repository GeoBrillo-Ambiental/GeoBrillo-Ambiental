<?php
require_once __DIR__ . '/../models/Recoleccion.php';

class RecoleccionControlador {

    private $modelo;

    public function __construct($modelo = null) {
        $this->modelo = $modelo ?? new Recoleccion();
    }

    public function agregarRecoleccion($datos) {
        $fecha = trim((string)($datos['fecha'] ?? ''));
        $kg = trim((string)($datos['kg'] ?? ''));
        $observaciones = trim((string)($datos['observaciones'] ?? ''));
        $id_ruta = trim((string)($datos['id_ruta'] ?? ''));
        $id_contenedor = trim((string)($datos['id_contenedor'] ?? ''));

        if ($fecha === '' || $kg === '' || $id_ruta === '' || $id_contenedor === '') {
            return ["exito" => false, "mensaje" => "Los campos obligatorios no pueden estar vacíos."];
        }

        $ok = $this->modelo->insertar($fecha, (int)$kg, ($observaciones === '' ? null : $observaciones), (int)$id_ruta, (int)$id_contenedor);
        return $ok
            ? ["exito" => true, "mensaje" => "Recoleccion guardada con éxito."]
            : ["exito" => false, "mensaje" => "Error al guardar."];
    }

    public function obtenerRecoleccions() {
        return ["exito" => true, "data" => $this->modelo->listar()];
    }

    public function actualizarRecoleccion($id, $datos) {
        if (empty($id)) {
            return ["exito" => false, "mensaje" => "ID no válido."];
        }
        $fecha = trim((string)($datos['fecha'] ?? ''));
        $kg = trim((string)($datos['kg'] ?? ''));
        $observaciones = trim((string)($datos['observaciones'] ?? ''));
        $id_ruta = trim((string)($datos['id_ruta'] ?? ''));
        $id_contenedor = trim((string)($datos['id_contenedor'] ?? ''));

        if ($fecha === '' || $kg === '' || $id_ruta === '' || $id_contenedor === '') {
            return ["exito" => false, "mensaje" => "Los campos obligatorios no pueden estar vacíos."];
        }

        $ok = $this->modelo->actualizar($id, $fecha, (int)$kg, ($observaciones === '' ? null : $observaciones), (int)$id_ruta, (int)$id_contenedor);
        return $ok
            ? ["exito" => true, "mensaje" => "Recoleccion actualizada correctamente."]
            : ["exito" => false, "mensaje" => "Error al actualizar."];
    }

    public function borrarRecoleccion($id) {
        if (empty($id)) {
            return ["exito" => false, "mensaje" => "ID no válido."];
        }
        return $this->modelo->eliminar($id)
            ? ["exito" => true, "mensaje" => "Recoleccion eliminada correctamente."]
            : ["exito" => false, "mensaje" => "Error al eliminar."];
    }
}
