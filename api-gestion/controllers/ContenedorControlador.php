<?php
require_once __DIR__ . '/../models/Contenedor.php';

class ContenedorControlador {

    private $modelo;

    public function __construct($modelo = null) {
        $this->modelo = $modelo ?? new Contenedor();
    }

    private function leerUbicacion($datos) {
        $ubicacion = trim($datos['ubicacion'] ?? '');
        $lat = trim((string)($datos['latitud'] ?? ''));
        $lng = trim((string)($datos['longitud'] ?? ''));
        if ($lat !== '' || $lng !== '') {
            if (!is_numeric($lat) || !is_numeric($lng) || abs($lat) > 90 || abs($lng) > 180) {
                return null;
            }
        }
        return [$ubicacion === '' ? null : $ubicacion, $lat === '' ? null : (float)$lat, $lng === '' ? null : (float)$lng];
    }

    public function agregarContenedor($datos) {
        $estado    = trim($datos['estado'] ?? '');
        $tipo      = trim($datos['residuo'] ?? '');
        $capacidad = $datos['capacidad'] ?? '';
        $idRuta    = $datos['id_ruta'] ?? '';

        if ($estado === '' || $tipo === '' || $capacidad === '') {
            return ["exito" => false, "mensaje" => "Estado, tipo de residuo y capacidad son obligatorios."];
        }

        $ubic = $this->leerUbicacion($datos);
        if ($ubic === null) {
            return ["exito" => false, "mensaje" => "Latitud/longitud inválidas."];
        }

        $resultado = $this->modelo->insertar($estado, $capacidad, $tipo, ($idRuta === '' ? null : (int)$idRuta), $ubic[0], $ubic[1], $ubic[2]);

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

        if ($estado === '' || $tipo === '' || $capacidad === '') {
            return ["exito" => false, "mensaje" => "Estado, tipo de residuo y capacidad son obligatorios."];
        }

        $ubic = $this->leerUbicacion($datos);
        if ($ubic === null) {
            return ["exito" => false, "mensaje" => "Latitud/longitud inválidas."];
        }

        $resultado = $this->modelo->actualizar($id, $estado, $capacidad, $tipo, ($idRuta === '' ? null : (int)$idRuta), $ubic[0], $ubic[1], $ubic[2]);

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
