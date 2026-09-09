<?php
require_once __DIR__ . '/../models/Usuario.php';

class UsuarioControlador {

    private $modelo;

    /**
     * Se permite inyectar el modelo (dependencia) para poder testear
     * este controlador sin necesidad de una base de datos real.
     */
    public function __construct($modelo = null) {
        $this->modelo = $modelo ?? new Usuario();
    }

    /**
     * Da de alta un usuario.
     *
     * @param array         $datos           Datos del formulario (pri_nom, pri_ape, ...).
     * @param array|null    $rolesPermitidos Whitelist de Id_Rol permitidos para esta alta.
     *   - null (default): sin restricción. Lo usa usuarioapi.php, que ya está
     *     protegido por Auth::requireGestion (solo roles de gestión logueados
     *     pueden llegar hasta aquí).
     *   - array de IDs (ej. [1,2,3,4]): lo usa registro.php, el endpoint
     *     PÚBLICO (sin token). Evita que cualquiera pueda autoregistrarse
     *     como Administrador (Id_Rol 5) mandando el id_rol a mano por POST,
     *     sin depender de que el frontend oculte esa opción.
     */
    public function agregarUsuario($datos, $rolesPermitidos = null) {
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

        $resultado = $this->modelo->insertar($priNom, $priApe, $nomUsuario, $password, $email, $idRol, $idCuadrilla);

        return $resultado
            ? ["exito" => true, "mensaje" => "Usuario registrado correctamente."]
            : ["exito" => false, "mensaje" => "Error al registrar el usuario."];
    }

    public function obtenerUsuarios() {
        return ["exito" => true, "data" => $this->modelo->listar()];
    }

    public function actualizarUsuario($id, $datos) {
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
