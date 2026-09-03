<?php
require_once __DIR__ . '/../models/Contenedor.php';

class ContenedorControlador {

    private $modelo;

    public function __construct($modelo = null) {
        $this->modelo = $modelo ?? new Contenedor();
    }

    public function agregarContenedor($datos) {
        $estado    = trim($datos['estado'] ?? '');
        $tipo      = trim($datos['residuo'] ?? '');
        $capacidad = $datos['capacidad'] ?? '';
        $idRuta    = $datos['id_ruta'] ?? '';

        if ($estado === '' || $tipo === '' || $capacidad === '' || $idRuta === '') {
            return ["exito" => false, "mensaje" => "Todos los campos son obligatorios."];
        }

        $resultado = $this->modelo->insertar($estado, $capacidad, $tipo, (int)$idRuta);

        return $resultado
            ? ["exito" => true, "mensaje" => "Contenedor guardado con éxito."]
            : ["exito" => false, "mensaje" => "Error al guardar el contenedor."];
    }

    public function obtenerContenedores() {
        return ["exito" => true, "data" => $this->modelo->listar()];
    }

    public function actualizarContenedor($id, $datos) {
        if (empty($id)) {
            return ["exito" => false, "mensaje" => "ID no válido."];
        }
        $estado    = trim($datos['estado'] ?? '');
        $tipo      = trim($datos['residuo'] ?? '');
        $capacidad = $datos['capacidad'] ?? '';
        $idRuta    = $datos['id_ruta'] ?? '';

        if ($estado === '' || $tipo === '' || $capacidad === '' || $idRuta === '') {
            return ["exito" => false, "mensaje" => "Todos los campos son obligatorios."];
        }

        $resultado = $this->modelo->actualizar($id, $estado, $capacidad, $tipo, (int)$idRuta);

        return $resultado
            ? ["exito" => true, "mensaje" => "Contenedor actualizado correctamente."]
            : ["exito" => false, "mensaje" => "Error al actualizar el contenedor."];
    }

    public function borrarContenedor($id) {
        if (empty($id)) {
            return ["exito" => false, "mensaje" => "ID no válido."];
        }
        $resultado = $this->modelo->eliminar($id);
        return $resultado
            ? ["exito" => true, "mensaje" => "Contenedor eliminado correctamente."]
            : ["exito" => false, "mensaje" => "Error al eliminar el contenedor."];
    }
}
