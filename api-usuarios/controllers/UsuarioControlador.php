<?php
require_once __DIR__ . '/../models/Usuario.php';

class UsuarioControlador {

    private $modelo;

    public function __construct($modelo = null) {
        $this->modelo = $modelo ?? new Usuario();
    }

    public function agregarUsuario($datos, $rolesPermitidos = null, $estado = 'Activo') {
        $priNom      = trim($datos['pri_nom'] ?? '');
        $priApe      = trim($datos['pri_ape'] ?? '');
        $nomUsuario  = trim($datos['nom_usuario'] ?? '');
        $password    = $datos['password'] ?? '';
        $email       = trim($datos['email'] ?? '');
        $idRol       = $datos['id_rol'] ?? '';
        $idCuadrilla = $datos['id_cuadrilla'] ?? '';

        if ($priNom === '' || $priApe === '' || $nomUsuario === '' || $password === '' || $idRol === '') {
            return ["exito" => false, "mensaje" => "Todos los campos obligatorios deben completarse."];
        }

        if ($rolesPermitidos !== null && !in_array((int)$idRol, $rolesPermitidos, true)) {
            return ["exito" => false, "mensaje" => "El rol seleccionado no está permitido para el autoregistro."];
        }

        if ($email !== '' && $this->modelo->buscarPorEmail($email)) {
            return ["exito" => false, "mensaje" => "Ya existe un usuario registrado con ese correo."];
        }

        if ($this->modelo->buscarPorNomUsuario($nomUsuario)) {
            return ["exito" => false, "mensaje" => "Ese nombre de usuario ya está en uso."];
        }

        $resultado = $this->modelo->insertar($priNom, $priApe, $nomUsuario, $password, $email, $idRol, $idCuadrilla, $estado);

        if ($resultado && $estado === 'Pendiente') {
            return ["exito" => true, "mensaje" => "Solicitud de registro enviada. Un administrador debe aprobarla antes de que puedas iniciar sesión."];
        }

        return $resultado
            ? ["exito" => true, "mensaje" => "Usuario registrado correctamente."]
            : ["exito" => false, "mensaje" => "Error al registrar el usuario."];
    }

    public function resolverSolicitud($id, $decision) {
        if (empty($id)) {
            return ["exito" => false, "mensaje" => "ID no válido."];
        }
        if ($decision !== 'Activo' && $decision !== 'Rechazado') {
            return ["exito" => false, "mensaje" => "Decisión no válida."];
        }
        return $this->modelo->cambiarEstado($id, $decision)
            ? ["exito" => true, "mensaje" => $decision === 'Activo' ? "Solicitud aprobada." : "Solicitud rechazada."]
            : ["exito" => false, "mensaje" => "No se pudo actualizar la solicitud."];
    }

    public function obtenerUsuarios() {
        return ["exito" => true, "data" => $this->modelo->listar()];
    }

    public function esAdministrador($id) {
        $u = $this->modelo->buscarPorId($id);
        return $u !== null && (int)$u['Id_Rol'] === 5;
    }

    public function actualizarUsuario($id, $datos, $rolesPermitidos = null) {
        if (empty($id)) {
            return ["exito" => false, "mensaje" => "ID no válido."];
        }

        $priNom      = trim($datos['pri_nom'] ?? '');
        $priApe      = trim($datos['pri_ape'] ?? '');
        $email       = trim($datos['email'] ?? '');
        $idRol       = $datos['id_rol'] ?? '';
        $idCuadrilla = $datos['id_cuadrilla'] ?? '';

        if ($priNom === '' || $priApe === '' || $idRol === '') {
            return ["exito" => false, "mensaje" => "Nombre, apellido y rol son obligatorios."];
        }

        if ($rolesPermitidos !== null && !in_array((int)$idRol, $rolesPermitidos, true)) {
            return ["exito" => false, "mensaje" => "No tenés permiso para asignar ese rol."];
        }

        $resultado = $this->modelo->actualizar($id, $priNom, $priApe, $email, $idRol, $idCuadrilla);

        return $resultado
            ? ["exito" => true, "mensaje" => "Usuario actualizado correctamente."]
            : ["exito" => false, "mensaje" => "Error al actualizar el usuario."];
    }

    public function borrarUsuario($id) {
        if (empty($id)) {
            return ["exito" => false, "mensaje" => "ID no válido."];
        }
        $resultado = $this->modelo->eliminar($id);
        return $resultado
            ? ["exito" => true, "mensaje" => "Usuario eliminado correctamente."]
            : ["exito" => false, "mensaje" => "Error al eliminar el usuario."];
    }
}
