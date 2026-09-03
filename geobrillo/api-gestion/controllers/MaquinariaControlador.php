<?php
require_once __DIR__ . '/../models/Maquinaria.php';

class MaquinariaControlador {

    private $modelo;

    public function __construct($modelo = null) {
        $this->modelo = $modelo ?? new Maquinaria();
    }

    public function agregarMaquinaria($datos) {
        $nombre   = trim($datos['nombre'] ?? '');
        $tipo     = trim($datos['tipo'] ?? '');
        $estado   = trim($datos['estado'] ?? '');
        $idCentro = $datos['id_centro'] ?? '';

        if ($nombre === '' || $tipo === '' || $estado === '' || $idCentro === '') {
            return ["exito" => false, "mensaje" => "Todos los campos son obligatorios."];
        }

        $resultado = $this->modelo->insertar($nombre, $tipo, $estado, (int)$idCentro);

        return $resultado
            ? ["exito" => true, "mensaje" => "Maquinaria guardada con éxito."]
            : ["exito" => false, "mensaje" => "Error al guardar la maquinaria."];
    }

    public function obtenerMaquinaria() {
        return ["exito" => true, "data" => $this->modelo->listar()];
    }

    public function actualizarMaquinaria($id, $datos) {
        if (empty($id)) {
            return ["exito" => false, "mensaje" => "ID no válido."];
        }
        $nombre   = trim($datos['nombre'] ?? '');
        $tipo     = trim($datos['tipo'] ?? '');
        $estado   = trim($datos['estado'] ?? '');
        $idCentro = $datos['id_centro'] ?? '';

        if ($nombre === '' || $tipo === '' || $estado === '' || $idCentro === '') {
            return ["exito" => false, "mensaje" => "Todos los campos son obligatorios."];
        }

        $resultado = $this->modelo->actualizar($id, $nombre, $tipo, $estado, (int)$idCentro);

        return $resultado
            ? ["exito" => true, "mensaje" => "Maquinaria actualizada correctamente."]
            : ["exito" => false, "mensaje" => "Error al actualizar la maquinaria."];
    }

    public function borrarMaquinaria($id) {
        if (empty($id)) {
            return ["exito" => false, "mensaje" => "ID no válido."];
        }
        $resultado = $this->modelo->eliminar($id);
        return $resultado
            ? ["exito" => true, "mensaje" => "Maquinaria eliminada correctamente."]
            : ["exito" => false, "mensaje" => "Error al eliminar la maquinaria."];
    }
}
