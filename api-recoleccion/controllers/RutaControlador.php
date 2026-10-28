<?php
require_once __DIR__ . '/../models/Ruta.php';

class RutaControlador {

    private $modelo;

    public function __construct($modelo = null) {
        $this->modelo = $modelo ?? new Ruta();
    }

    public function agregarRuta($datos) {
        $dia_hora = trim((string)($datos['dia_hora'] ?? ''));
        $id_zona = trim((string)($datos['id_zona'] ?? ''));
        $id_centro = trim((string)($datos['id_centro'] ?? ''));
        $id_cuadrilla = trim((string)($datos['id_cuadrilla'] ?? ''));

        if ($dia_hora === '' || $id_zona === '' || $id_centro === '' || $id_cuadrilla === '') {
            return ["exito" => false, "mensaje" => "Los campos obligatorios no pueden estar vacíos."];
        }

        $ok = $this->modelo->insertar($dia_hora, (int)$id_zona, (int)$id_centro, (int)$id_cuadrilla);
        return $ok
            ? ["exito" => true, "mensaje" => "Ruta guardada con éxito."]
            : ["exito" => false, "mensaje" => "Error al guardar."];
    }

    public function obtenerRutas() {
        return ["exito" => true, "data" => $this->modelo->listar()];
    }

    public function obtenerZonas() {
        return ["exito" => true, "data" => $this->modelo->listarZonas()];
    }

    public function actualizarRuta($id, $datos) {
        if (empty($id)) {
            return ["exito" => false, "mensaje" => "ID no válido."];
        }
        $dia_hora = trim((string)($datos['dia_hora'] ?? ''));
        $id_zona = trim((string)($datos['id_zona'] ?? ''));
        $id_centro = trim((string)($datos['id_centro'] ?? ''));
        $id_cuadrilla = trim((string)($datos['id_cuadrilla'] ?? ''));

        if ($dia_hora === '' || $id_zona === '' || $id_centro === '' || $id_cuadrilla === '') {
            return ["exito" => false, "mensaje" => "Los campos obligatorios no pueden estar vacíos."];
        }

        $ok = $this->modelo->actualizar($id, $dia_hora, (int)$id_zona, (int)$id_centro, (int)$id_cuadrilla);
        return $ok
            ? ["exito" => true, "mensaje" => "Ruta actualizada correctamente."]
            : ["exito" => false, "mensaje" => "Error al actualizar."];
    }

    public function borrarRuta($id) {
        if (empty($id)) {
            return ["exito" => false, "mensaje" => "ID no válido."];
        }
        return $this->modelo->eliminar($id)
            ? ["exito" => true, "mensaje" => "Ruta eliminada correctamente."]
            : ["exito" => false, "mensaje" => "Error al eliminar."];
    }

    public function asignarContenedores($idRuta, $ids) {
        if (empty($idRuta) || !$this->modelo->existe((int)$idRuta)) {
            return ["exito" => false, "mensaje" => "La ruta no existe."];
        }

        $lista = [];
        foreach (explode(',', (string)$ids) as $trozo) {
            $trozo = trim($trozo);
            if ($trozo === '') { continue; }
            if (!ctype_digit($trozo)) {
                return ["exito" => false, "mensaje" => "Lista de contenedores inválida."];
            }
            $lista[] = (int)$trozo;
        }
        if (count($lista) !== count(array_unique($lista))) {
            return ["exito" => false, "mensaje" => "Hay contenedores repetidos en la selección."];
        }
        if ($this->modelo->contarContenedores($lista) !== count($lista)) {
            return ["exito" => false, "mensaje" => "Algún contenedor elegido no existe."];
        }

        $this->modelo->asignarContenedores((int)$idRuta, $lista);
        return ["exito" => true, "mensaje" => "Ruta actualizada: " . count($lista) . " contenedor(es) asignados."];
    }
}
