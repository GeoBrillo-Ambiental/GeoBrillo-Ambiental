<?php

$API_USUARIOS = "http://localhost:8081";
$API_GESTION  = "http://localhost:8082";

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
    $status = isset($http_response_header[0]) ? $http_response_header[0] : '';
    return [json_decode($result, true), $status];
}

function get($url) {
    $options = ["http" => ["ignore_errors" => true]];
    $context = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);
    $status = isset($http_response_header[0]) ? $http_response_header[0] : '';
    return [json_decode($result, true), $status];
}

echo "== Test de permisos por rol ==\n";

$options = [
    "http" => [
        "header"  => "Content-type: application/json\r\n",
        "method"  => "POST",
        "content" => json_encode(["email" => "ciudadano@eco.com", "password" => "1234"]),
        "ignore_errors" => true,
    ],
];
$result = @file_get_contents("$API_USUARIOS/login.php", false, stream_context_create($options));
$login = json_decode($result, true);

$tokenCiudadano = $login['token'] ?? null;

if (!$tokenCiudadano) {
    echo "❌ No se pudo obtener token de Ciudadano, se aborta el test.\n";
    exit(1);
}

echo "  (token de Ciudadano obtenido)\n";

list($respUsuarios, $statusUsuarios) = get("$API_USUARIOS/usuarioapi.php?accion=listar&token=$tokenCiudadano");
if (strpos($statusUsuarios, '403') !== false) {
    echo "✅ Ciudadano NO puede listar usuarios (403 Forbidden) - correcto\n";
} else {
    echo "❌ Ciudadano pudo acceder a usuarioapi.php (debería estar prohibido). Status: $statusUsuarios\n";
}

list($respCamion, $statusCamion) = post_form("$API_GESTION/camionapi.php", [
    "accion" => "insertar", "token" => $tokenCiudadano,
    "estado" => "Disponible", "modelo" => "Frontal", "capacidad" => 5,
]);
if (strpos($statusCamion, '403') !== false) {
    echo "✅ Ciudadano NO puede insertar camiones (403 Forbidden) - correcto\n";
} else {
    echo "❌ Ciudadano pudo insertar un camión (debería estar prohibido). Status: $statusCamion\n";
}

list(, $statusSinToken) = get("$API_GESTION/contenedorapi.php?accion=listar");
if (strpos($statusSinToken, '401') !== false) {
    echo "✅ Sin token, la API responde 401 Unauthorized - correcto\n";
} else {
    echo "❌ Sin token debería responder 401. Status: $statusSinToken\n";
}

$options = [
    "http" => [
        "header"  => "Content-type: application/json\r\n",
        "method"  => "POST",
        "content" => json_encode(["email" => "admin@eco.com", "password" => "1234"]),
        "ignore_errors" => true,
    ],
];
$context = stream_context_create($options);
$result = @file_get_contents("$API_USUARIOS/login.php", false, $context);
$loginAdmin = json_decode($result, true);
$tokenAdmin = $loginAdmin['token'] ?? null;

if ($tokenAdmin) {
    list($respAdmin, $statusAdmin) = get("$API_GESTION/camionapi.php?accion=listar&token=$tokenAdmin");
    if (strpos($statusAdmin, '200') !== false && !empty($respAdmin['exito'])) {
        echo "✅ Administrador SÍ puede listar camiones (200 OK) - correcto\n";
    } else {
        echo "❌ Administrador debería poder listar camiones. Status: $statusAdmin\n";
    }
} else {
    echo "⚠️  No se pudo loguear como Administrador (¿cargaste schema.sql?), se omite el paso 5.\n";
}

list($respListarCiud, $statusListarCiud) = get("$API_GESTION/contenedorapi.php?accion=listar&token=$tokenCiudadano");
if (strpos($statusListarCiud, '200') !== false && !empty($respListarCiud['exito'])) {
    echo "✅ Ciudadano SÍ puede listar contenedores en modo solo lectura (200 OK) - correcto\n";
} else {
    echo "❌ Ciudadano debería poder listar contenedores (solo lectura). Status: $statusListarCiud\n";
}

list($respReportar, $statusReportar) = post_form("$API_GESTION/incidenciaapi.php", [
    "accion" => "reportar", "token" => $tokenCiudadano,
    "descripcion" => "Contenedor de prueba desbordado (test automático)",
    "id_contenedor" => 1, "estado_reportado" => "Desbordado",
]);
if (strpos($statusReportar, '200') !== false && !empty($respReportar['exito'])) {
    echo "✅ Ciudadano SÍ puede reportar una incidencia (200 OK) - correcto\n";
} else {
    echo "❌ Ciudadano debería poder reportar una incidencia. Status: $statusReportar / " . json_encode($respReportar) . "\n";
}

list($respContenedores) = get("$API_GESTION/contenedorapi.php?accion=listar&token=$tokenCiudadano");
$contenedor1 = null;
foreach (($respContenedores['data'] ?? []) as $c) {
    if ((int)$c['Id_Contenedor'] === 1) { $contenedor1 = $c; break; }
}
if ($contenedor1 && $contenedor1['EstadoCont'] === 'Desbordado') {
    echo "✅ El contenedor #1 pasó a estado \"Desbordado\" tras el reporte - correcto\n";
} else {
    echo "❌ El contenedor #1 debería figurar como \"Desbordado\". Estado actual: " . ($contenedor1['EstadoCont'] ?? 'no encontrado') . "\n";
}

list(, $statusActualizarInc) = post_form("$API_GESTION/incidenciaapi.php", [
    "accion" => "actualizar", "token" => $tokenCiudadano,
    "id" => 1, "estado" => "Resuelta", "descripcion" => "intento no autorizado",
]);
if (strpos($statusActualizarInc, '403') !== false) {
    echo "✅ Ciudadano NO puede gestionar (asignar cuadrilla/cerrar) una incidencia (403 Forbidden) - correcto\n";
} else {
    echo "❌ Ciudadano pudo gestionar una incidencia (debería estar prohibido). Status: $statusActualizarInc\n";
}
