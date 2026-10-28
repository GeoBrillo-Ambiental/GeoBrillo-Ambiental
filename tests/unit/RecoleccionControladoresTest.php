<?php

require_once __DIR__ . '/../../api-recoleccion/controllers/CuadrillaControlador.php';
require_once __DIR__ . '/../../api-recoleccion/controllers/RutaControlador.php';
require_once __DIR__ . '/../../api-recoleccion/controllers/RecoleccionControlador.php';
require_once __DIR__ . '/TestRunner.php';

class FakeGenerico {
    public $filas = [];
    public function insertar(...$a) { $this->filas[] = $a; return true; }
    public function listar() { return $this->filas; }
    public function actualizar(...$a) { return true; }
    public function eliminar($id) { return true; }
    public $asignado = null;
    public function existe($id) { return $id == 1; }
    public function contarContenedores(array $ids) { return count(array_filter($ids, fn($i) => $i <= 10)); }
    public function asignarContenedores($idRuta, array $ids) { $this->asignado = [$idRuta, $ids]; return true; }
    public function listarZonas() { return [["Id_Zona" => 1, "NomZona" => "Centro"]]; }
}

$t = new TestRunner("Cuadrilla / Ruta / Recoleccion");

$m = new FakeGenerico(); $c = new CuadrillaControlador($m);
$t->assertFalse($c->agregarCuadrilla(["dia" => "", "horario" => "", "id_rol" => "", "id_rol2" => "", "id_camion" => ""])['exito'], "Cuadrilla: rechaza campos vacíos");
$t->assertTrue($c->agregarCuadrilla(["dia" => "Lunes", "horario" => "08:00-14:00", "id_rol" => "2", "id_rol2" => "4", "id_camion" => "1"])['exito'], "Cuadrilla: alta válida");
$t->assertEquals(1, count($c->obtenerCuadrillas()['data']), "Cuadrilla: listar devuelve lo cargado");
$t->assertFalse($c->borrarCuadrilla('')['exito'], "Cuadrilla: eliminar sin ID falla");
$t->assertFalse($c->actualizarCuadrilla('', [])['exito'], "Cuadrilla: actualizar sin ID falla");

$m = new FakeGenerico(); $c = new RutaControlador($m);
$t->assertFalse($c->agregarRuta(["dia_hora" => "Lunes 08:00"])['exito'], "Ruta: rechaza si falta zona/centro/cuadrilla");
$t->assertTrue($c->agregarRuta(["dia_hora" => "Lunes 08:00", "id_zona" => "1", "id_centro" => "1", "id_cuadrilla" => "1"])['exito'], "Ruta: alta válida");
$t->assertEquals(1, count($c->obtenerZonas()['data']), "Ruta: lista las zonas para el combo");
$t->assertFalse($c->borrarRuta('')['exito'], "Ruta: eliminar sin ID falla");

$r = $c->asignarContenedores(1, "3, 1,5");
$t->assertTrue($r['exito'], "Ruta: asigna contenedores elegidos a mano");
$t->assertEquals([3, 1, 5], $m->asignado[1], "Ruta: respeta el orden de recorrido elegido");
$t->assertTrue($c->asignarContenedores(1, "")['exito'], "Ruta: se puede vaciar (sin contenedores)");
$t->assertFalse($c->asignarContenedores(99, "1")['exito'], "Ruta: rechaza una ruta inexistente");
$t->assertFalse($c->asignarContenedores(1, "1,1")['exito'], "Ruta: rechaza contenedores repetidos");
$t->assertFalse($c->asignarContenedores(1, "1,abc")['exito'], "Ruta: rechaza una lista mal formada");
$t->assertFalse($c->asignarContenedores(1, "1,999")['exito'], "Ruta: rechaza contenedores inexistentes");

$m = new FakeGenerico(); $c = new RecoleccionControlador($m);
$t->assertFalse($c->agregarRecoleccion(["fecha" => "2026-10-01", "kg" => "", "id_ruta" => "1", "id_contenedor" => "1"])['exito'], "Recolección: exige kg");
$t->assertTrue($c->agregarRecoleccion(["fecha" => "2026-10-01", "kg" => "120", "id_ruta" => "1", "id_contenedor" => "1"])['exito'], "Recolección: alta sin observaciones (opcional)");
$t->assertEquals(null, $m->filas[0][2], "Recolección: observaciones vacías se guardan como NULL");
$t->assertFalse($c->actualizarRecoleccion('', [])['exito'], "Recolección: actualizar sin ID falla");

$t->resumen();
