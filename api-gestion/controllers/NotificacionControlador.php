<?php
require_once __DIR__ . '/../models/Notificacion.php';

class NotificacionControlador {

    private $modelo;

    public function __construct($modelo = null) {
        $this->modelo = $modelo ?? new Notificacion();
    }

    public function obtenerNotificaciones() {
        return [
            "exito"    => true,
            "no_leidas" => $this->modelo->contarNoLeidas(),
            "data"     => $this->modelo->listar(),
        ];
    }

    public function marcarLeida($id) {
        if (empty($id)) {
            return ["exito" => false, "mensaje" => "ID no válido."];
        }
        return $this->modelo->marcarLeida($id)
            ? ["exito" => true, "mensaje" => "Notificación marcada como leída."]
            : ["exito" => false, "mensaje" => "No se pudo actualizar la notificación."];
    }

    public function marcarTodasLeidas() {
        return $this->modelo->marcarTodasLeidas()
            ? ["exito" => true, "mensaje" => "Todas las notificaciones quedaron como leídas."]
            : ["exito" => false, "mensaje" => "No se pudo actualizar."];
    }
}
