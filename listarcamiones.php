<?php
// Configura la cabecera para devolver respuesta en formato JSON
header("Content-Type: application/json; charset=utf-8");

// Permite peticiones locales sin bloqueos de CORS
header("Access-Control-Allow-Origin: *");

// Array con la lista base de camiones
$camiones = [
    [
        "Id_Camion" => 1,
        "EstadoCam" => "Disponible",
        "Modelo" => "Frontal",
        "C_CargaContenedor" => 10
    ],
    [
        "Id_Camion" => 2,
        "EstadoCam" => "En Reparación",
        "Modelo" => "Lateral",
        "C_CargaContenedor" => 8
    ]
];

// Imprime los datos codificados en JSON
echo json_encode($camiones, JSON_UNESCAPED_UNICODE);
exit();
?>