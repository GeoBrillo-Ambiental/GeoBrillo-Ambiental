<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" || $_SERVER["REQUEST_METHOD"] === "GET") {
    $datos = $_SERVER["REQUEST_METHOD"] === "POST" ? $_POST : $_GET;

    $nuevoUsuario = [
        "Id_Usuario" => 3,
        "Nom_Usuario" => $datos['nom_usuario'] ?? 'Usuario',
        "Email" => $datos['email'] ?? 'correo@eco.com',
        "Rol" => $datos['rol'] ?? 'Chofer'
    ];

    header("Content-Type: application/json; charset=utf-8");
    echo json_encode(["status" => "ok"]);
    exit();
}
?>