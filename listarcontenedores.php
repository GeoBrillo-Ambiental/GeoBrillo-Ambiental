<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");

// Array de contenedores
$contenedores = [
    [
        "Id_Contenedor" => 1,
        "EstadoCont" => "Funcional",
        "CapContenedor" => 2,
        "TipoResiduo" => "Reciclable",
        "Id_Ruta" => 101
    ],
    [
        "Id_Contenedor" => 2,
        "EstadoCont" => "Desbordado",
        "CapContenedor" => 3,
        "TipoResiduo" => "Mezclados",
        "Id_Ruta" => 102
    ]
];

// Devuelve el JSON para el fetch de JavaScript
echo json_encode($contenedores, JSON_UNESCAPED_UNICODE);
exit();
?>