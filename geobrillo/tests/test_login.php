<?php
/**
 * Test de integración de login.
 * Requiere que el sistema esté corriendo (docker compose up).
 * Ejecutar:  php tests/test_login.php
 */

$urlLogin = "http://localhost:8081/login.php";

function post_json($url, $data) {
    $options = [
        "http" => [
            "header"  => "Content-type: application/json\r\n",
            "method"  => "POST",
            "content" => json_encode($data),
            "ignore_errors" => true,
        ],
    ];
    $context = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);
    return json_decode($result, true);
}

echo "== Test de login ==\n";

// 1) Login con credenciales correctas (usuario admin sembrado por crear_admin.php)
$respuestaOk = post_json($urlLogin, ["email" => "admin@eco.com", "password" => "1234"]);

if (isset($respuestaOk["exito"]) && $respuestaOk["exito"] === true && !empty($respuestaOk["token"])) {
    echo "✅ Test de login correcto pasó (se recibió un token)\n";
} else {
    echo "❌ Test de login correcto falló\n";
    echo "   Respuesta: " . json_encode($respuestaOk) . "\n";
}

// 2) Login con credenciales incorrectas
$respuestaError = post_json($urlLogin, ["email" => "admin@eco.com", "password" => "clave_incorrecta"]);

if (isset($respuestaError["exito"]) && $respuestaError["exito"] === false) {
    echo "✅ Test de login incorrecto pasó (rechazó credenciales inválidas)\n";
} else {
    echo "❌ Test de login incorrecto falló (debería haber rechazado la contraseña)\n";
}
