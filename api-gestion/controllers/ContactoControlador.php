<?php
require_once __DIR__ . '/../models/Contacto.php';
require_once __DIR__ . '/../models/Notificacion.php';

class ContactoControlador {

    private $modelo;
    private $modeloNotificacion;

    public function __construct($modelo = null, $modeloNotificacion = null) {
        $this->modelo = $modelo ?? new Contacto();
        $this->modeloNotificacion = $modeloNotificacion ?? new Notificacion();
    }

    public function enviar($datos) {
        $nombre   = trim($datos['name'] ?? $datos['nombre'] ?? '');
        $email    = trim($datos['email'] ?? '');
        $telefono = trim($datos['phone'] ?? $datos['telefono'] ?? '');
        $mensaje  = trim($datos['message'] ?? $datos['mensaje'] ?? '');

        if ($nombre === '' || $email === '' || $mensaje === '') {
            return ["exito" => false, "mensaje" => "Nombre, correo y mensaje son obligatorios."];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["exito" => false, "mensaje" => "El correo no es válido."];
        }
        if (mb_strlen($nombre) > 60 || mb_strlen($email) > 100 || mb_strlen($telefono) > 20 || mb_strlen($mensaje) > 500) {
            return ["exito" => false, "mensaje" => "Algún campo supera el largo permitido (mensaje máx. 500 caracteres)."];
        }

        $ok = $this->modelo->insertar($nombre, $email, $telefono === '' ? null : $telefono, $mensaje);
        if ($ok) {
            $this->modeloNotificacion->insertar('Contacto', "Nuevo mensaje de contacto de $nombre.");
        }
        return $ok
            ? ["exito" => true, "mensaje" => "¡Gracias por tu mensaje! Te contactaremos a la brevedad."]
            : ["exito" => false, "mensaje" => "No se pudo enviar el mensaje."];
    }

    public function obtenerMensajes() {
        return ["exito" => true, "data" => $this->modelo->listar()];
    }

    public function marcarLeido($id) {
        if (empty($id)) { return ["exito" => false, "mensaje" => "ID no válido."]; }
        return $this->modelo->marcarLeido($id)
            ? ["exito" => true, "mensaje" => "Mensaje marcado como leído."]
            : ["exito" => false, "mensaje" => "No se pudo actualizar."];
    }

    public function borrarMensaje($id) {
        if (empty($id)) { return ["exito" => false, "mensaje" => "ID no válido."]; }
        return $this->modelo->eliminar($id)
            ? ["exito" => true, "mensaje" => "Mensaje eliminado."]
            : ["exito" => false, "mensaje" => "No se pudo eliminar."];
    }
}
