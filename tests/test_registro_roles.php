<?php
/**
 * Test de integración: el endpoint público registro.php no debe
 * permitir que alguien se autoasigne el rol Administrador (Id_Rol 5)
 * mandando id_rol=5 a mano, aunque el formulario (registro.html) no
 * muestre esa opción en el navegador.
 *
 * Requiere que el sistema esté corriendo (docker compose up).
 * Ejecutar:  php tests/test_registro_roles.php
 */

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

// --- Intento de autoregistro como Administrador (Id_Rol 5) ---
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

// --- Control: un rol permitido (Ciudadano, Id_Rol 1) sí debe funcionar ---
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
