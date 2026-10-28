<?php
require_once __DIR__ . '/../models/Cuadrilla.php';

class CuadrillaControlador {

    private $modelo;

    public function __construct($modelo = null) {
        $this->modelo = $modelo ?? new Cuadrilla();
    }

    public function agregarCuadrilla($datos) {
        $dia = trim((string)($datos['dia'] ?? ''));
        $horario = trim((string)($datos['horario'] ?? ''));
        $id_rol = trim((string)($datos['id_rol'] ?? ''));
        $id_rol2 = trim((string)($datos['id_rol2'] ?? ''));
        $id_camion = trim((string)($datos['id_camion'] ?? ''));

        if ($dia === '' || $horario === '' || $id_rol === '' || $id_rol2 === '' || $id_camion === '') {
            return ["exito" => false, "mensaje" => "Los campos obligatorios no pueden estar vacíos."];
        }

        $ok = $this->modelo->insertar($dia, $horario, (int)$id_rol, (int)$id_rol2, (int)$id_camion);
        return $ok
            ? ["exito" => true, "mensaje" => "Cuadrilla guardada con éxito."]
            : ["exito" => false, "mensaje" => "Error al guardar."];
    }

    public function obtenerCuadrillas() {
        return ["exito" => true, "data" => $this->modelo->listar()];
    }

    public function actualizarCuadrilla($id, $datos) {
        if (empty($id)) {
            return ["exito" => false, "mensaje" => "ID no válido."];
        }
        $dia = trim((string)($datos['dia'] ?? ''));
        $horario = trim((string)($datos['horario'] ?? ''));
        $id_rol = trim((string)($datos['id_rol'] ?? ''));
        $id_rol2 = trim((string)($datos['id_rol2'] ?? ''));
        $id_camion = trim((string)($datos['id_camion'] ?? ''));

        if ($dia === '' || $horario === '' || $id_rol === '' || $id_rol2 === '' || $id_camion === '') {
            return ["exito" => false, "mensaje" => "Los campos obligatorios no pueden estar vacíos."];
        }

        $ok = $this->modelo->actualizar($id, $dia, $horario, (int)$id_rol, (int)$id_rol2, (int)$id_camion);
        return $ok
            ? ["exito" => true, "mensaje" => "Cuadrilla actualizada correctamente."]
            : ["exito" => false, "mensaje" => "Error al actualizar."];
    }

    public function borrarCuadrilla($id) {
        if (empty($id)) {
            return ["exito" => false, "mensaje" => "ID no válido."];
        }
        return $this->modelo->eliminar($id)
            ? ["exito" => true, "mensaje" => "Cuadrilla eliminada correctamente."]
            : ["exito" => false, "mensaje" => "Error al eliminar."];
    }
}
