<?php
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Sesion.php';

class AuthControlador {

    private $modeloUsuario;
    private $modeloSesion;

    public function __construct() {
        $this->modeloUsuario = new Usuario();
        $this->modeloSesion  = new Sesion();
    }

    public function login($email, $password) {
        $email = trim($email ?? '');
        $password = $password ?? '';

        if ($email === '' || $password === '') {
            return ["exito" => false, "mensaje" => "Debe ingresar correo y contraseña."];
        }

        $usuario = $this->modeloUsuario->buscarPorEmail($email);

        if (!$usuario || !password_verify($password, $usuario['Password'])) {
            return ["exito" => false, "mensaje" => "Correo o contraseña incorrectos."];
        }

        $token = $this->modeloSesion->crear($usuario['Id_Usuario']);
        $rol = $this->modeloUsuario->listarRoles();
        $nombreRol = '';
        foreach ($rol as $r) {
            if ($r['Id_Rol'] == $usuario['Id_Rol']) {
                $nombreRol = $r['Nombre_Rol'];
                break;
            }
        }

        return [
            "exito" => true,
            "mensaje" => "Autenticación exitosa.",
            "token" => $token,
            "usuario" => [
                "id_usuario"  => $usuario['Id_Usuario'],
                "pri_nom"     => $usuario['Pri_Nom'],
                "pri_ape"     => $usuario['Pri_Ape'],
                "nom_usuario" => $usuario['Nom_Usuario'],
                "email"       => $usuario['Email'],
                "id_rol"      => $usuario['Id_Rol'],
                "nombre_rol"  => $nombreRol,
            ],
        ];
    }

    public function logout($token) {
        if (empty($token)) {
            return ["exito" => false, "mensaje" => "Token no informado."];
        }
        $this->modeloSesion->eliminar($token);
        return ["exito" => true, "mensaje" => "Sesión cerrada."];
    }

    /** Devuelve los datos del usuario dueño de un token, si es válido. */
    public function quienSoy($token) {
        $usuario = $this->modeloSesion->validar($token);

        if (!$usuario) {
            return ["exito" => false, "mensaje" => "Token inválido o expirado."];
        }

        return [
            "exito" => true,
            "usuario" => [
                "id_usuario"  => $usuario['Id_Usuario'],
                "pri_nom"     => $usuario['Pri_Nom'],
                "pri_ape"     => $usuario['Pri_Ape'],
                "nom_usuario" => $usuario['Nom_Usuario'],
                "email"       => $usuario['Email'],
                "id_rol"      => $usuario['Id_Rol'],
                "nombre_rol"  => $usuario['Nombre_Rol'],
            ],
        ];
    }
}
