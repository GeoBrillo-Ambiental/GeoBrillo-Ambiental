<?php

require_once __DIR__ . '/../../api-gestion/controllers/ContenedorControlador.php';
require_once __DIR__ . '/../../api-gestion/controllers/NotificacionControlador.php';
require_once __DIR__ . '/../../api-gestion/controllers/EstadisticaControlador.php';
require_once __DIR__ . '/../../api-gestion/controllers/MapaControlador.php';
require_once __DIR__ . '/../../api-gestion/controllers/ContactoControlador.php';
require_once __DIR__ . '/TestRunner.php';

class FakeCont {
    public $ultimo;
    public function insertar($e, $c, $t, $r, $u = null, $lat = null, $lng = null) { $this->ultimo = [$u, $lat, $lng]; return true; }
    public function listar() { return []; }
    public function actualizar(...$a) { return true; }
    public function eliminar($id) { return true; }
}
class FakeNot {
    public $leidas = 0;
    public function listar() { return [["Id_Notificacion" => 1]]; }
    public function contarNoLeidas() { return 2; }
    public function marcarLeida($id) { return true; }
    public function marcarTodasLeidas() { $this->leidas++; return true; }
}
class FakeEst {
    public function topDesbordadosDelMes($l) { return [["Id_Contenedor" => 2, "Reportes" => 3]]; }
    public function incidenciasPorEstado() { return [["Estado" => "Abierta", "Total" => 2], ["Estado" => "En curso", "Total" => 1], ["Estado" => "Resuelta", "Total" => 4]]; }
    public function contenedoresPorEstado() { return []; }
    public function kgPorZona() { return []; }
    public function zonasCriticas() { return [["Zona" => "Centro", "Incidencias" => 3]]; }
    public function fechasResueltas() { return [["FchaReportado" => "2026-10-01", "FchaResuelto" => "2026-10-03"], ["FchaReportado" => "2026-10-01", "FchaResuelto" => "2026-10-02"]]; }
    public function recoleccionesPorMes() { return [["Mes" => "2026-10", "Recolecciones" => 2, "Kg" => 300]]; }
    public function informeIncidencias($cerradas) { return [["cerradas" => $cerradas]]; }
}
class FakeMapa {
    public function contenedores() { return [1, 2]; }
    public function incidenciasAbiertas() { return [1]; }
    public function centros() { return []; }
    public function contenedoresSinUbicacion() { return 3; }
}

class FakeContacto {
    public $filas = [];
    public function insertar($n, $e, $t, $m) { $this->filas[] = [$n, $e, $t, $m]; return true; }
    public function listar() { return $this->filas; }
    public function marcarLeido($id) { return true; }
    public function eliminar($id) { return true; }
}
class FakeNotif2 { public $n = 0; public function insertar(...$a) { $this->n++; return true; } }

$t = new TestRunner("Gestión: Contenedor/Notificación/Estadística/Mapa");

$m = new FakeCont(); $c = new ContenedorControlador($m);
$base = ["estado" => "Funcional", "residuo" => "Reciclable", "capacidad" => "2", "id_ruta" => "1"];
$t->assertTrue($c->agregarContenedor($base)['exito'], "Contenedor: alta sin coordenadas (opcionales)");
$t->assertEquals(null, $m->ultimo[1], "Contenedor: sin latitud queda NULL");
$t->assertTrue($c->agregarContenedor($base + ["latitud" => "-34.9", "longitud" => "-56.1"])['exito'], "Contenedor: alta con coordenadas válidas");
$t->assertFalse($c->agregarContenedor($base + ["latitud" => "999", "longitud" => "-56.1"])['exito'], "Contenedor: rechaza latitud fuera de rango");
$t->assertFalse($c->agregarContenedor($base + ["latitud" => "abc", "longitud" => "x"])['exito'], "Contenedor: rechaza coordenadas no numéricas");
$t->assertTrue($c->agregarContenedor($base + ["id_ruta" => ""])['exito'], "Contenedor: la ruta es opcional (queda sin ruta)");
$t->assertFalse($c->agregarContenedor(["estado" => ""])['exito'], "Contenedor: rechaza campos obligatorios vacíos");

$n = new FakeNot(); $c = new NotificacionControlador($n);
$r = $c->obtenerNotificaciones();
$t->assertEquals(2, $r['no_leidas'], "Notificación: informa cuántas no están leídas");
$t->assertFalse($c->marcarLeida('')['exito'], "Notificación: marcar leída sin ID falla");
$t->assertTrue($c->marcarTodasLeidas()['exito'], "Notificación: marcar todas como leídas");

$c = new EstadisticaControlador(new FakeEst());
$p = $c->panel()['data'];
$t->assertEquals(3, $p['incidencias_abiertas'], "Panel: abiertas = todo lo que no está Resuelta");
$t->assertEquals(4, $p['incidencias_cerradas'], "Panel: cerradas = Resuelta");
$t->assertEquals(1, count($p['top_desbordados_mes']), "Panel: incluye el top de desbordados del mes");
$t->assertEquals(1.5, $p['tiempo_medio_resolucion_dias'], "Panel: tiempo medio de resolución en días");
$t->assertEquals("Centro", $p['zonas_criticas'][0]['Zona'], "Panel: incluye las zonas críticas");
$t->assertEquals(1, count($p['recolecciones_por_mes']), "Panel: incluye la frecuencia de recolección por mes");
$t->assertFalse($c->informeIncidencias('otra')['exito'], "Informe: rechaza un tipo desconocido");
$t->assertEquals(true, $c->informeIncidencias('cerradas')['data'][0]['cerradas'], "Informe: 'cerradas' pide las resueltas");
$t->assertEquals(false, $c->informeIncidencias('abiertas')['data'][0]['cerradas'], "Informe: 'abiertas' pide las no resueltas");

$d = (new MapaControlador(new FakeMapa()))->datos()['data'];
$t->assertEquals(2, count($d['contenedores']), "Mapa: devuelve contenedores");
$t->assertEquals(1, count($d['incidencias']), "Mapa: devuelve incidencias abiertas");
$t->assertEquals(3, $d['sin_ubicacion'], "Mapa: informa cuántos contenedores no tienen ubicación");

$fc = new FakeContacto(); $fn = new FakeNotif2(); $c = new ContactoControlador($fc, $fn);
$t->assertFalse($c->enviar(["name" => "", "email" => "a@b.com", "message" => "hola"])['exito'], "Contacto: exige nombre");
$t->assertFalse($c->enviar(["name" => "Ana", "email" => "no-es-mail", "message" => "hola"])['exito'], "Contacto: rechaza correo inválido");
$t->assertFalse($c->enviar(["name" => "Ana", "email" => "a@b.com", "message" => str_repeat("x", 501)])['exito'], "Contacto: rechaza mensaje de más de 500 caracteres");
$t->assertTrue($c->enviar(["name" => "Ana", "email" => "a@b.com", "phone" => "", "message" => "Hola"])['exito'], "Contacto: envío válido");
$t->assertEquals(null, $fc->filas[0][2], "Contacto: teléfono vacío se guarda como NULL");
$t->assertEquals(1, $fn->n, "Contacto: genera una alerta para gestión");
$t->assertFalse($c->marcarLeido('')['exito'], "Contacto: marcar leído sin ID falla");

$t->resumen();
