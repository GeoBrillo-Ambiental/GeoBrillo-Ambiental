<?php
require_once __DIR__ . '/../models/Camion.php';

class CamionControlador {

    private $modelo;

    public function __construct($modelo = null) {
        $this->modelo = $modelo ?? new Camion();
    }

    public function agregarCamion($datos) {
        $estado = trim($datos['estado'] ?? '');
        $modelo = trim($datos['modelo'] ?? '');
        $carga  = $datos['capacidad'] ?? '';

        if ($estado === '' || $modelo === '' || $carga === '') {
            return ["exito" => false, "mensaje" => "Todos los campos son obligatorios."];
        }

        $resultado = $this->modelo->insertar($estado, $modelo, (int)$carga);

        return $resultado
            ? ["exito" => true, "mensaje" => "Camión guardado con éxito."]
            : ["exito" => false, "mensaje" => "Error al guardar el camión."];
    }

    public function obtenerCamiones() {
        return ["exito" => true, "data" => $this->modelo->listar()];
    }

    public function actualizarCamion($id, $datos) {
        if (empty($id)) {
            return ["exito" => false, "mensaje" => "ID no válido."];
        }
        $estado = trim($datos['estado'] ?? '');
        $modelo = trim($datos['modelo'] ?? '');
        $carga  = $datos['capacidad'] ?? '';

        if ($estado === '' || $modelo === '' || $carga === '') {
            return ["exito" => false, "mensaje" => "Todos los campos son obligatorios."];
        }

        $resultado = $this->modelo->actualizar($id, $estado, $modelo, (int)$carga);

        return $resultado
            ? ["exito" => true, "mensaje" => "Camión actualizado correctamente."]
            : ["exito" => false, "mensaje" => "Error al actualizar el camión."];
    }

    public function borrarCamion($id) {
        if (empty($id)) {
            return ["exito" => false, "mensaje" => "ID no válido."];
        }
        $resultado = $this->modelo->eliminar($id);
        return $resultado
            ? ["exito" => true, "mensaje" => "Camión eliminado correctamente."]
            : ["exito" => false, "mensaje" => "Error al eliminar el camión."];
    }
}
