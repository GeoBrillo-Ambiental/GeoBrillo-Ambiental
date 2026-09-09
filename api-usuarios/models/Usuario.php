<?php
require_once __DIR__ . '/../config/conexion.php';

class Usuario {

    private $db;

    public function __construct() {
        $this->db = conectar();
    }

    public function insertar($priNom, $priApe, $nomUsuario, $passwordPlano, $email, $idRol, $idCuadrilla) {
        $sql = "INSERT INTO Usuario (Pri_Nom, Pri_Ape, Nom_Usuario, Password, Email, Id_Rol, Id_Cuadrilla)
                VALUES (:priNom, :priApe, :nomUsuario, :password, :email, :idRol, :idCuadrilla)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':priNom'      => $priNom,
            ':priApe'      => $priApe,
            ':nomUsuario'  => $nomUsuario,
            ':password'    => password_hash($passwordPlano, PASSWORD_DEFAULT),
            ':email'       => $email,
            ':idRol'       => $idRol,
            ':idCuadrilla' => ($idCuadrilla === '' || $idCuadrilla === null) ? null : $idCuadrilla,
        ]);
    }

    public function listar() {
        $sql = "SELECT u.Id_Usuario, u.Pri_Nom, u.Pri_Ape, u.Nom_Usuario, u.Email,
                       u.Id_Rol, r.Nombre_Rol, u.Id_Cuadrilla
                FROM Usuario u
                LEFT JOIN Rol r ON u.Id_Rol = r.Id_Rol
                ORDER BY u.Id_Usuario DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function buscarPorId($id) {
        $stmt = $this->db->prepare(
            "SELECT u.Id_Usuario, u.Pri_Nom, u.Pri_Ape, u.Nom_Usuario, u.Email,
                    u.Id_Rol, r.Nombre_Rol, u.Id_Cuadrilla
             FROM Usuario u LEFT JOIN Rol r ON u.Id_Rol = r.Id_Rol
             WHERE u.Id_Usuario = :id LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    public function actualizar($id, $priNom, $priApe, $email, $idRol, $idCuadrilla) {
        $sql = "UPDATE Usuario
                SET Pri_Nom = :priNom, Pri_Ape = :priApe, Email = :email,
                    Id_Rol = :idRol, Id_Cuadrilla = :idCuadrilla
                WHERE Id_Usuario = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':priNom'      => $priNom,
            ':priApe'      => $priApe,
            ':email'       => $email,
            ':idRol'       => $idRol,
            ':idCuadrilla' => ($idCuadrilla === '' || $idCuadrilla === null) ? null : $idCuadrilla,
            ':id'          => $id,
        ]);
    }

    public function eliminar($id) {
        $stmt = $this->db->prepare("DELETE FROM Usuario WHERE Id_Usuario = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function buscarPorEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM Usuario WHERE Email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    public function buscarPorNomUsuario($nomUsuario) {
        $stmt = $this->db->prepare("SELECT * FROM Usuario WHERE Nom_Usuario = :nom LIMIT 1");
        $stmt->execute([':nom' => $nomUsuario]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    public function listarRoles() {
        $stmt = $this->db->query("SELECT Id_Rol, Nombre_Rol FROM Rol ORDER BY Id_Rol");
        return $stmt->fetchAll();
    }
}
