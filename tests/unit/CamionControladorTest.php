<?php

require_once __DIR__ . '/../../api-recoleccion/controllers/CamionControlador.php';
require_once __DIR__ . '/TestRunner.php';

class FakeCamionModelo {
    public $camiones = [];

    public function insertar($estado, $modelo, $carga) {
        $this->camiones[] = ["EstadoCam" => $estado, "Modelo" => $modelo, "C_Carga" => $carga];
        return true;
    }

    public function listar() { return $this->camiones; }
    public function actualizar($id, $estado, $modelo, $carga) { return true; }
    public function eliminar($id) { return true; }
}

$t = new TestRunner("CamionControlador");

$modelo = new FakeCamionModelo();
$controlador = new CamionControlador($modelo);
$resultado = $controlador->agregarCamion(["estado" => "", "modelo" => "", "capacidad" => ""]);
$t->assertFalse($resultado['exito'], "Rechaza el alta si faltan campos obligatorios");

$resultadoOk = $controlador->agregarCamion(["estado" => "Disponible", "modelo" => "Frontal", "capacidad" => "10"]);
$t->assertTrue($resultadoOk['exito'], "Permite el alta cuando todos los campos están completos");
$t->assertEquals(1, count($modelo->camiones), "El modelo guardó exactamente 1 camión");

$listado = $controlador->obtenerCamiones();
$t->assertEquals(1, count($listado['data']), "obtenerCamiones() devuelve los camiones insertados");

$resultadoSinId = $controlador->borrarCamion('');
$t->assertFalse($resultadoSinId['exito'], "Rechaza eliminar sin un ID válido");

$resultadoUpdateSinId = $controlador->actualizarCamion('', ["estado" => "Disponible", "modelo" => "Frontal", "capacidad" => "10"]);
$t->assertFalse($resultadoUpdateSinId['exito'], "Rechaza actualizar sin un ID válido");

$t->resumen();
