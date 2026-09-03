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

$t->resumen();
