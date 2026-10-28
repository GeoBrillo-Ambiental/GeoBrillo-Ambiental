<?php

require_once __DIR__ . '/../../api-gestion/controllers/IncidenciaControlador.php';
require_once __DIR__ . '/TestRunner.php';

class FakeIncidencia {
    public $filas = [];
    public function insertar($desc, $idCont, $idUsu, $tipo = null) {
        $this->filas[] = compact('desc', 'idCont', 'idUsu', 'tipo');
        return count($this->filas);
    }
    public function listar() { return $this->filas; }
    public function listarCuadrillas() { return [["Id_Cuadrilla" => 1]]; }
    public function actualizar($id, $estado, $idCuad, $desc, $fecha) { $this->ultimo = compact('id', 'estado', 'idCuad', 'fecha'); return true; }
    public $ultimo = null;
    public function eliminar($id) { return true; }
    public function obtenerContenedorDe($id) { return 7; }
}
class FakeContenedor {
    public $estados = [];
    public function actualizarEstado($id, $estado) { $this->estados[$id] = $estado; return true; }
}
class FakeNotificacion {
    public $alertas = [];
    public function insertar($tipo, $msg, $inc = null, $cont = null) { $this->alertas[] = [$tipo, $msg, $inc, $cont]; return true; }
}

$t = new TestRunner("IncidenciaControlador");

$inc = new FakeIncidencia(); $con = new FakeContenedor(); $not = new FakeNotificacion();
$c = new IncidenciaControlador($inc, $con, $not);

$r = $c->reportarIncidencia(["descripcion" => "", "id_contenedor" => "3", "estado_reportado" => "Roto"], 1);
$t->assertFalse($r['exito'], "Rechaza reportar sin descripción");

$r = $c->reportarIncidencia(["descripcion" => "Tapa rota", "id_contenedor" => "3", "estado_reportado" => "Inventado"], 1);
$t->assertFalse($r['exito'], "Rechaza un estado reportado que no sea Roto/Desbordado");
$t->assertEquals(0, count($inc->filas), "No guarda nada si el estado es inválido");

$r = $c->reportarIncidencia(["descripcion" => "Desborda", "id_contenedor" => "3", "estado_reportado" => "Desbordado"], 5);
$t->assertTrue($r['exito'], "Reporte válido de un ciudadano");
$t->assertEquals("Desbordado", $con->estados[3], "El contenedor pasa a Desbordado al reportarse");
$t->assertEquals("Desbordado", $inc->filas[0]['tipo'], "Se guarda el tipo de problema para las estadísticas");
$t->assertEquals(5, $inc->filas[0]['idUsu'], "El usuario sale del token, no del POST");
$t->assertEquals(1, count($not->alertas), "Se genera una alerta por la incidencia reportada");

$r = $c->actualizarIncidencia(10, ["estado" => "En curso", "id_cuadrilla" => "2", "descripcion" => "x"]);
$t->assertTrue($r['exito'], "Gestión asigna cuadrilla");
$t->assertEquals(2, $inc->ultimo['idCuad'], "Se guarda la cuadrilla asignada");

$r = $c->actualizarIncidencia(10, ["estado" => "Resuelta", "id_cuadrilla" => "2", "descripcion" => "x"]);
$t->assertEquals("Funcional", $con->estados[7], "Al resolver, el contenedor vuelve a Funcional");
$t->assertEquals(date('Y-m-d'), $inc->ultimo['fecha'], "Al resolver se completa la fecha de hoy");

$t->assertFalse($c->actualizarIncidencia('', [])['exito'], "Rechaza actualizar sin ID");
$t->assertFalse($c->borrarIncidencia('')['exito'], "Rechaza eliminar sin ID");

$t->resumen();
