<?php
require_once __DIR__ . '/../models/Incidencia.php';
require_once __DIR__ . '/../models/Contenedor.php';
require_once __DIR__ . '/../models/Notificacion.php';

class IncidenciaControlador {

    private $modelo;
    private $modeloContenedor;
    private $modeloNotificacion;

    public function __construct($modelo = null, $modeloContenedor = null, $modeloNotificacion = null) {
        $this->modelo = $modelo ?? new Incidencia();
        $this->modeloContenedor = $modeloContenedor ?? new Contenedor();
        $this->modeloNotificacion = $modeloNotificacion ?? new Notificacion();
    }

    private const ESTADOS_REPORTABLES = ['Roto', 'Desbordado'];

    public function reportarIncidencia($datos, $idUsuario) {
        $descripcion    = trim($datos['descripcion'] ?? '');
        $idContenedor   = $datos['id_contenedor'] ?? '';
        $estadoReportado = trim($datos['estado_reportado'] ?? '');

        if ($descripcion === '' || $idContenedor === '') {
            return ["exito" => false, "mensaje" => "La descripción y el contenedor son obligatorios."];
        }

        if (!in_array($estadoReportado, self::ESTADOS_REPORTABLES, true)) {
            return ["exito" => false, "mensaje" => "Indicá qué problema tiene el contenedor (Roto o Desbordado)."];
        }

        $idNueva = $this->modelo->insertar($descripcion, (int)$idContenedor, (int)$idUsuario, $estadoReportado);
        $resultado = (bool)$idNueva;

        if ($resultado) {

            $this->modeloContenedor->actualizarEstado((int)$idContenedor, $estadoReportado);

            $this->modeloNotificacion->insertar(
                'Incidencia',
                "Nueva incidencia: contenedor $idContenedor $estadoReportado.",
                is_int($idNueva) ? $idNueva : null,
                (int)$idContenedor
            );
        }

        return $resultado
            ? ["exito" => true, "mensaje" => "Incidencia reportada con éxito."]
            : ["exito" => false, "mensaje" => "Error al reportar la incidencia."];
    }

    public function obtenerIncidencias() {
        return ["exito" => true, "data" => $this->modelo->listar()];
    }

    public function obtenerCuadrillas() {
        return ["exito" => true, "data" => $this->modelo->listarCuadrillas()];
    }

    public function actualizarIncidencia($id, $datos) {
        if (empty($id)) {
            return ["exito" => false, "mensaje" => "ID no válido."];
        }

        $estado       = trim($datos['estado'] ?? '');
        $idCuadrilla  = $datos['id_cuadrilla'] ?? '';
        $descripcion  = trim($datos['descripcion'] ?? '');
        $fchaResuelto = trim($datos['fecha_resuelto'] ?? '');

        if ($estado === '' || $descripcion === '') {
            return ["exito" => false, "mensaje" => "El estado y la descripción son obligatorios."];
        }

        $idCuadrilla = ($idCuadrilla === '') ? null : (int)$idCuadrilla;

        if ($estado === 'Resuelta' && $fchaResuelto === '') {
            $fchaResuelto = date('Y-m-d');
        }
        $fchaResuelto = ($fchaResuelto === '') ? null : $fchaResuelto;

        $resultado = $this->modelo->actualizar($id, $estado, $idCuadrilla, $descripcion, $fchaResuelto);

        if ($resultado && $estado === 'Resuelta') {

            $idContenedor = $this->modelo->obtenerContenedorDe($id);
            if ($idContenedor) {
                $this->modeloContenedor->actualizarEstado((int)$idContenedor, 'Funcional');
            }
        }

        return $resultado
            ? ["exito" => true, "mensaje" => "Incidencia actualizada correctamente."]
            : ["exito" => false, "mensaje" => "Error al actualizar la incidencia."];
    }

    public function borrarIncidencia($id) {
        if (empty($id)) {
            return ["exito" => false, "mensaje" => "ID no válido."];
        }
        $resultado = $this->modelo->eliminar($id);
        return $resultado
            ? ["exito" => true, "mensaje" => "Incidencia eliminada correctamente."]
            : ["exito" => false, "mensaje" => "Error al eliminar la incidencia."];
    }
}
