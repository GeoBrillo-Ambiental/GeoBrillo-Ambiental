<?php
header("Content-Type: application/json");

// Array de usuarios
$usuarios = [
    [
        "Id_Usuario" => 1,
        "Nom_Usuario" => "AdminGeneral",
        "Email" => "admin@eco.com",
        "Rol" => "Administrador"
    ],
    [
        "Id_Usuario" => 2,
        "Nom_Usuario" => "JuanPerez",
        "Email" => "juan@eco.com",
        "Rol" => "Chofer"
    ]
];

// Devuelve el JSON para el fetch de JavaScript
echo json_encode($usuarios);
?>