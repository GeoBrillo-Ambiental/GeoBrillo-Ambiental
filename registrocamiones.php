<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" || $_SERVER["REQUEST_METHOD"] === "GET") {
    $datos = $_SERVER["REQUEST_METHOD"] === "POST" ? $_POST : $_GET;

    $nuevoCamion = [
        "Id_Camion" => 3,
        "EstadoCam" => $datos['estado'] ?? 'Disponible',
        "Modelo" => $datos['modelo'] ?? 'Frontal',
        "C_CargaContenedor" => (int)($datos['capacidad'] ?? 5)
    ];

    // Redirige directamente a la lista HTML de camiones
    header("Location: listarcamiones.html");
    exit();
}
?>