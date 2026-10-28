<?php

$API_USUARIOS = "http://localhost:8081";

function post_form($url, $data) {
    $options = [
        "http" => [
            "header"  => "Content-type: application/x-www-form-urlencoded\r\n",
            "method"  => "POST",
            "content" => http_build_query($data),
            "ignore_errors" => true,
        ],
    ];
    $context = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);
    return json_decode($result, true);
}

echo "== Test de roles permitidos en el registro público ==\n";

$email = "intruso_" . time() . "@eco.com";
$respuesta = post_form("$API_USUARIOS/registro.php", [
    "pri_nom" => "Intruso", "pri_ape" => "Test", "nom_usuario" => "intruso_" . time(),
    "password" => "1234", "email" => $email, "id_rol" => 5,
]);

if (isset($respuesta['exito']) && $respuesta['exito'] === false) {
    echo "✅ El registro público rechazó id_rol=5 (Administrador) - correcto\n";
} else {
    echo "❌ FALLO DE SEGURIDAD: el registro público permitió crear un Administrador.\n";
    echo "   Respuesta: " . json_encode($respuesta) . "\n";
}

$emailOk = "ciudadano_ok_" . time() . "@eco.com";
$respuestaOk = post_form("$API_USUARIOS/registro.php", [
    "pri_nom" => "Vecino", "pri_ape" => "Test", "nom_usuario" => "vecino_" . time(),
    "password" => "1234", "email" => $emailOk, "id_rol" => 1,
]);

if (isset($respuestaOk['exito']) && $respuestaOk['exito'] === true) {
    echo "✅ El registro público con id_rol=1 (Ciudadano) sigue funcionando - correcto\n";
} else {
    echo "❌ El registro con un rol válido debería funcionar.\n";
    echo "   Respuesta: " . json_encode($respuestaOk) . "\n";
}

function post_json($url, $data) {
    $options = ["http" => ["header" => "Content-type: application/json\r\n", "method" => "POST", "content" => json_encode($data), "ignore_errors" => true]];
    return json_decode(@file_get_contents($url, false, stream_context_create($options)), true);
}

$r = post_json("$API_USUARIOS/login.php", ["email" => $emailOk, "password" => "1234"]);
if (isset($r['exito']) && $r['exito'] === false && stripos($r['mensaje'], 'aprobada') !== false) {
    echo "✅ Un usuario con registro pendiente NO puede iniciar sesión - correcto\n";
} else {
    echo "❌ Un usuario pendiente pudo iniciar sesión (o el mensaje es otro): " . json_encode($r) . "\n";
}

$admin = post_json("$API_USUARIOS/login.php", ["email" => "admin@eco.com", "password" => "1234"]);
$lista = json_decode(@file_get_contents("$API_USUARIOS/usuarioapi.php?accion=listar&token=" . urlencode($admin['token'] ?? '')), true);
$idNuevo = null;
foreach (($lista['data'] ?? []) as $u) { if ($u['Email'] === $emailOk) { $idNuevo = $u['Id_Usuario']; } }

if ($idNuevo) {
    post_form("$API_USUARIOS/usuarioapi.php", ["accion" => "aprobar", "id" => $idNuevo, "token" => $admin['token']]);
    $r = post_json("$API_USUARIOS/login.php", ["email" => $emailOk, "password" => "1234"]);
    echo !empty($r['token'])
        ? "✅ Tras la aprobación del Administrador, el usuario ya puede iniciar sesión - correcto\n"
        : "❌ Aprobado, pero el login sigue fallando: " . json_encode($r) . "\n";
} else {
    echo "❌ No se encontró al usuario recién registrado en el listado.\n";
}
