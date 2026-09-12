<?php
require_once __DIR__ . '/../models/Incidencia.php';
require_once __DIR__ . '/../models/Contenedor.php';

class IncidenciaControlador {

    private $modelo;
    private $modeloContenedor;

    public function __construct($modelo = null, $modeloContenedor = null) {
        $this->modelo = $modelo ?? new Incidencia();
        $this->modeloContenedor = $modeloContenedor ?? new Contenedor();
    }

    /**
     * Reportar una incidencia nueva. $idUsuario viene SIEMPRE del token
     * validado (no del POST), para que nadie pueda reportar "en nombre"
     * de otro usuario.
     *
     * Además de crear la incidencia, actualiza el ESTADO del contenedor
     * afectado según lo que reportó el Ciudadano (ej. "Desbordado" o
     * "Roto"), para que el contenedor deje de figurar como "Funcional"
     * mientras la incidencia sigue abierta.
     */
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

        $resultado = $this->modelo->insertar($descripcion, (int)$idContenedor, (int)$idUsuario);

        if ($resultado) {
            // El contenedor pasa a reflejar el problema reportado.
            $this->modeloContenedor->actualizarEstado((int)$idContenedor, $estadoReportado);
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

    /**
     * Gestión: asigna/cambia cuadrilla y estado. Si el estado pasa a
     * "Resuelta", completa FchaResuelto con la fecha de hoy salvo que
     * se mande una específica.
     */
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
            // Al cerrar la incidencia, el contenedor vuelve a estar operativo.
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
