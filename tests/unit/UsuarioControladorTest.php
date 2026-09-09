<?php
/**
 * Test unitario de UsuarioControlador.
 * No necesita base de datos: se inyecta un modelo "falso" (FakeUsuarioModelo)
 * que simula el comportamiento de Usuario.php en memoria.
 *
 * Ejecutar:  php tests/unit/UsuarioControladorTest.php
 */

require_once __DIR__ . '/../../api-usuarios/controllers/UsuarioControlador.php';
require_once __DIR__ . '/TestRunner.php';

class FakeUsuarioModelo {
    public $usuarios = [];
    private $siguienteId = 1;

    public function insertar($priNom, $priApe, $nomUsuario, $password, $email, $idRol, $idCuadrilla) {
        $this->usuarios[] = compact('priNom', 'priApe', 'nomUsuario', 'email', 'idRol', 'idCuadrilla');
        $this->siguienteId++;
        return true;
    }

    public function listar() { return $this->usuarios; }

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

// --- Caso 1: faltan campos obligatorios ---
$modelo = new FakeUsuarioModelo();
$controlador = new UsuarioControlador($modelo);
$resultado = $controlador->agregarUsuario(["pri_nom" => "", "pri_ape" => "", "nom_usuario" => "", "password" => "", "id_rol" => ""]);
$t->assertFalse($resultado['exito'], "Rechaza el alta si faltan campos obligatorios");

// --- Caso 2: alta correcta ---
$modelo = new FakeUsuarioModelo();
$controlador = new UsuarioControlador($modelo);
$resultado = $controlador->agregarUsuario([
    "pri_nom" => "Ana", "pri_ape" => "Pérez", "nom_usuario" => "anap",
    "password" => "1234", "email" => "ana@eco.com", "id_rol" => 2,
]);
$t->assertTrue($resultado['exito'], "Permite el alta cuando todos los campos están completos");

// --- Caso 3: no permite emails duplicados ---
$resultadoDup = $controlador->agregarUsuario([
    "pri_nom" => "Otra", "pri_ape" => "Persona", "nom_usuario" => "otrap",
    "password" => "1234", "email" => "ana@eco.com", "id_rol" => 2,
]);
$t->assertFalse($resultadoDup['exito'], "Rechaza un correo ya registrado");

// --- Caso 4: no permite nombres de usuario duplicados ---
$resultadoDupUser = $controlador->agregarUsuario([
    "pri_nom" => "Otra", "pri_ape" => "Persona", "nom_usuario" => "anap",
    "password" => "1234", "email" => "otra@eco.com", "id_rol" => 2,
]);
$t->assertFalse($resultadoDupUser['exito'], "Rechaza un nombre de usuario ya registrado");

// --- Caso 5: eliminar sin ID ---
$resultadoSinId = $controlador->borrarUsuario('');
$t->assertFalse($resultadoSinId['exito'], "Rechaza eliminar sin un ID válido");

// --- Caso 6: el autoregistro público (con whitelist) NO permite elegir
//     el rol Administrador (Id_Rol 5), aunque se lo pase a mano por POST ---
$modelo = new FakeUsuarioModelo();
$controlador = new UsuarioControlador($modelo);
$resultadoAdminPorRegistro = $controlador->agregarUsuario([
    "pri_nom" => "Intruso", "pri_ape" => "Test", "nom_usuario" => "intruso1",
    "password" => "1234", "email" => "intruso@eco.com", "id_rol" => 5,
], [1, 2, 3, 4]);
$t->assertFalse($resultadoAdminPorRegistro['exito'], "El registro público rechaza id_rol=5 (Administrador)");

// --- Caso 7: esa misma alta con id_rol=5 SÍ funciona sin whitelist
//     (uso desde usuarioapi.php, ya protegido por Auth::requireGestion) ---
$resultadoAdminPorGestion = $controlador->agregarUsuario([
    "pri_nom" => "Nuevo", "pri_ape" => "Admin", "nom_usuario" => "nuevoadmin",
    "password" => "1234", "email" => "nuevoadmin@eco.com", "id_rol" => 5,
]);
$t->assertTrue($resultadoAdminPorGestion['exito'], "La gestión autenticada sí puede dar de alta un Administrador");

$t->resumen();
