<?php

$U = "http://localhost:8081"; $G = "http://localhost:8082"; $R = "http://localhost:8083";
$fallos = 0;

function http($url, $data = null, $json = false) {
    $o = ["http" => ["ignore_errors" => true, "method" => $data === null ? "GET" : "POST"]];
    if ($data !== null) {
        $o["http"]["header"] = "Content-type: " . ($json ? "application/json" : "application/x-www-form-urlencoded") . "\r\n";
        $o["http"]["content"] = $json ? json_encode($data) : http_build_query($data);
    }
    $r = @file_get_contents($url, false, stream_context_create($o));
    $codigo = (int)substr($http_response_header[0] ?? 'HTTP/1.1 0', 9, 3);
    return [json_decode($r, true), $codigo];
}
function chequear($ok, $msg) { global $fallos; echo ($ok ? "  ✅ " : "  ❌ ") . $msg . "\n"; if (!$ok) $fallos++; }
function login($email) { global $U; [$r] = http("$U/login.php", ["email" => $email, "password" => "1234"], true); return $r['token'] ?? ''; }

echo "== Test 3ra entrega ==\n";
$ciud = login("ciudadano@eco.com");
$chof = login("chofer@eco.com");
chequear($ciud !== '' && $chof !== '', "Login de los usuarios semilla");

[$r, $c] = http("$G/mapaapi.php?accion=datos");
chequear($c === 200 && count($r['data']['contenedores']) > 0, "Mapa: datos públicos con contenedores");
chequear(!isset($r['data']['incidencias'][0]['Id_Usuario']), "Mapa: no expone el usuario que reportó");

[, $c] = http("$G/estadisticaapi.php?accion=panel");               chequear($c === 401, "Panel sin sesión → 401");
[, $c] = http("$G/estadisticaapi.php?accion=panel&token=$ciud");   chequear($c === 403, "Panel como Ciudadano → 403");
[$r, $c] = http("$G/estadisticaapi.php?accion=panel&token=$chof"); chequear($c === 200 && isset($r['data']['top_desbordados_mes']), "Panel como Chofer → 200 con top de desbordados");
[, $c] = http("$G/estadisticaapi.php?accion=informe&tipo=abiertas&token=$ciud"); chequear($c === 403, "Informe como Ciudadano → 403");
[$r, $c] = http("$G/estadisticaapi.php?accion=informe&tipo=cerradas&token=$chof"); chequear($c === 200, "Informe de cerradas como Chofer → 200");
[, $c] = http("$G/estadisticaapi.php?accion=informe&tipo=xx&token=$chof"); chequear($c === 400, "Informe con tipo inválido → 400");
[, $c] = http("$G/notificacionapi.php?accion=listar&token=$ciud"); chequear($c === 403, "Alertas como Ciudadano → 403");
[$r, $c] = http("$G/notificacionapi.php?accion=listar&token=$chof"); chequear($c === 200 && isset($r['no_leidas']), "Alertas como Chofer → 200");

[$antes] = http("$G/notificacionapi.php?accion=listar&token=$chof");
[$r] = http("$G/incidenciaapi.php", ["accion" => "reportar", "token" => $ciud, "descripcion" => "Test 3ra entrega", "id_contenedor" => 3, "estado_reportado" => "Desbordado"]);
chequear(!empty($r['exito']), "Ciudadano reporta una incidencia");
[$desp] = http("$G/notificacionapi.php?accion=listar&token=$chof");
chequear($desp['no_leidas'] === $antes['no_leidas'] + 1, "El reporte generó una alerta nueva");

foreach (["camionapi.php", "cuadrillaapi.php", "rutaapi.php", "recoleccionapi.php"] as $ep) {
    [, $c] = http("$R/$ep?accion=listar");                 chequear($c === 401, "$ep sin sesión → 401");
    [, $c] = http("$R/$ep?accion=listar&token=$ciud");     chequear($c === 200, "$ep listar como Ciudadano (solo lectura) → 200");
    [, $c] = http("$R/$ep", ["accion" => "eliminar", "id" => 1, "token" => $ciud]); chequear($c === 403, "$ep eliminar como Ciudadano → 403");
}

[$r, $c] = http("$G/contactoapi.php", ["accion" => "enviar", "name" => "Test", "email" => "test@eco.com", "message" => "Hola desde el test"]);
chequear($c === 200 && !empty($r['exito']), "Contacto: se puede enviar sin iniciar sesión");
[$r] = http("$G/contactoapi.php", ["accion" => "enviar", "name" => "Test", "email" => "malo", "message" => "x"]);
chequear(empty($r['exito']), "Contacto: rechaza correo inválido");
[, $c] = http("$G/contactoapi.php?accion=listar&token=$ciud"); chequear($c === 403, "Mensajes de contacto como Ciudadano → 403");
[, $c] = http("$G/contactoapi.php?accion=listar&token=$chof"); chequear($c === 200, "Mensajes de contacto como Chofer → 200");

[$r] = http("$U/usuarioapi.php", ["accion" => "insertar", "token" => $chof, "pri_nom" => "X", "pri_ape" => "Y",
    "nom_usuario" => "x" . time(), "password" => "1234", "email" => "x" . time() . "@eco.com", "id_rol" => 5]);
chequear(empty($r['exito']), "Un Chofer NO puede crear un Administrador");
$adm = login("admin@eco.com");
chequear($adm !== '', "Login del Administrador semilla");
[$r] = http("$U/usuarioapi.php", ["accion" => "insertar", "token" => $adm, "pri_nom" => "Nuevo", "pri_ape" => "Admin",
    "nom_usuario" => "adm" . time(), "password" => "1234", "email" => "adm" . time() . "@eco.com", "id_rol" => 5]);
chequear(!empty($r['exito']), "Un Administrador SÍ puede crear otro Administrador");

[, $c] = http("$R/rutaapi.php", ["accion" => "asignarContenedores", "id" => 1, "contenedores" => "1,3", "token" => $ciud]);
chequear($c === 403, "Armar ruta como Ciudadano → 403");
[$r] = http("$R/rutaapi.php", ["accion" => "asignarContenedores", "id" => 1, "contenedores" => "1,3,6", "token" => $chof]);
chequear(!empty($r['exito']), "Armar ruta como Chofer → OK");
[$r] = http("$R/rutaapi.php", ["accion" => "asignarContenedores", "id" => 1, "contenedores" => "999", "token" => $chof]);
chequear(empty($r['exito']), "Armar ruta con un contenedor inexistente → rechazado");

echo $fallos === 0 ? "\n✅ Todo OK\n" : "\n❌ Fallos: $fallos\n";
exit($fallos === 0 ? 0 : 1);
