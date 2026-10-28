<?php

require_once __DIR__ . '/../../api-usuarios/controllers/UsuarioControlador.php';
require_once __DIR__ . '/TestRunner.php';

class FakeUsuarioModelo {
    public $usuarios = [];
    private $siguienteId = 1;

    public function insertar($priNom, $priApe, $nomUsuario, $password, $email, $idRol, $idCuadrilla, $estado = 'Activo') {
        $this->usuarios[] = compact('priNom', 'priApe', 'nomUsuario', 'email', 'idRol', 'idCuadrilla', 'estado');
        $this->siguienteId++;
        return true;
    }

    public function listar() { return $this->usuarios; }
    public function cambiarEstado($id, $estado) { return true; }
    public function buscarPorId($id) { return $id == 99 ? ['Id_Usuario' => 99, 'Id_Rol' => 5] : null; }

    public function buscarPorEmail($email) {
        foreach ($this->usuarios as $u) {
            if ($u['email'] === $email) return $u;
        }
        return null;
    }

    public function buscarPorNomUsuario($nomUsuario) {
        foreach ($this->usuarios as $u) {
            if ($u['nomUsuario'] === $nomUsuario) return $u;
        }
        return null;
    }

    public function eliminar($id) { return true; }
    public function actualizar($id, $priNom, $priApe, $email, $idRol, $idCuadrilla) { return true; }
}

$t = new TestRunner("UsuarioControlador");

$modelo = new FakeUsuarioModelo();
$controlador = new UsuarioControlador($modelo);
$resultado = $controlador->agregarUsuario(["pri_nom" => "", "pri_ape" => "", "nom_usuario" => "", "password" => "", "id_rol" => ""]);
$t->assertFalse($resultado['exito'], "Rechaza el alta si faltan campos obligatorios");

$modelo = new FakeUsuarioModelo();
$controlador = new UsuarioControlador($modelo);
$resultado = $controlador->agregarUsuario([
    "pri_nom" => "Ana", "pri_ape" => "Pérez", "nom_usuario" => "anap",
    "password" => "1234", "email" => "ana@eco.com", "id_rol" => 2,
]);
$t->assertTrue($resultado['exito'], "Permite el alta cuando todos los campos están completos");

$resultadoDup = $controlador->agregarUsuario([
    "pri_nom" => "Otra", "pri_ape" => "Persona", "nom_usuario" => "otrap",
    "password" => "1234", "email" => "ana@eco.com", "id_rol" => 2,
]);
$t->assertFalse($resultadoDup['exito'], "Rechaza un correo ya registrado");

$resultadoDupUser = $controlador->agregarUsuario([
    "pri_nom" => "Otra", "pri_ape" => "Persona", "nom_usuario" => "anap",
    "password" => "1234", "email" => "otra@eco.com", "id_rol" => 2,
]);
$t->assertFalse($resultadoDupUser['exito'], "Rechaza un nombre de usuario ya registrado");

$resultadoSinId = $controlador->borrarUsuario('');
$t->assertFalse($resultadoSinId['exito'], "Rechaza eliminar sin un ID válido");

$modelo = new FakeUsuarioModelo();
$controlador = new UsuarioControlador($modelo);
$resultadoAdminPorRegistro = $controlador->agregarUsuario([
    "pri_nom" => "Intruso", "pri_ape" => "Test", "nom_usuario" => "intruso1",
    "password" => "1234", "email" => "intruso@eco.com", "id_rol" => 5,
], [1, 2, 3, 4]);
$t->assertFalse($resultadoAdminPorRegistro['exito'], "El registro público rechaza id_rol=5 (Administrador)");

$resultadoAdminPorGestion = $controlador->agregarUsuario([
    "pri_nom" => "Nuevo", "pri_ape" => "Admin", "nom_usuario" => "nuevoadmin",
    "password" => "1234", "email" => "nuevoadmin@eco.com", "id_rol" => 5,
]);
$t->assertTrue($resultadoAdminPorGestion['exito'], "La gestión autenticada sí puede dar de alta un Administrador");

$r = $controlador->actualizarUsuario(5, ["pri_nom" => "A", "pri_ape" => "B", "email" => "a@b.com", "id_rol" => 5], [1, 2, 3, 4]);
$t->assertFalse($r['exito'], "Un no-Administrador no puede asignar el rol Administrador al editar");
$r = $controlador->actualizarUsuario(5, ["pri_nom" => "A", "pri_ape" => "B", "email" => "a@b.com", "id_rol" => 5]);
$t->assertTrue($r['exito'], "Un Administrador sí puede asignar el rol Administrador");
$t->assertTrue($controlador->esAdministrador(99), "Detecta cuentas Administrador");
$t->assertFalse($controlador->esAdministrador(5), "Una cuenta no-Administrador no se detecta como admin");

$modelo = new FakeUsuarioModelo(); $controlador = new UsuarioControlador($modelo);
$r = $controlador->agregarUsuario(["pri_nom" => "Vec", "pri_ape" => "Ino", "nom_usuario" => "vecino9", "password" => "1234", "email" => "v9@eco.com", "id_rol" => 1], [1, 2, 3, 4], 'Pendiente');
$t->assertTrue($r['exito'] && stripos($r['mensaje'], 'aprobarla') !== false, "El registro público informa que queda pendiente de aprobación");
$t->assertEquals('Pendiente', $modelo->usuarios[0]['estado'], "El usuario se guarda con estado Pendiente");

$t->assertTrue($controlador->resolverSolicitud(1, 'Activo')['exito'], "Se puede aprobar una solicitud");
$t->assertTrue($controlador->resolverSolicitud(1, 'Rechazado')['exito'], "Se puede rechazar una solicitud");
$t->assertFalse($controlador->resolverSolicitud(1, 'Admin')['exito'], "Rechaza una decisión que no sea Activo/Rechazado");
$t->assertFalse($controlador->resolverSolicitud('', 'Activo')['exito'], "Rechaza resolver sin ID");

$t->resumen();
