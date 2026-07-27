<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" || $_SERVER["REQUEST_METHOD"] === "GET") {
    $datos = $_SERVER["REQUEST_METHOD"] === "POST" ? $_POST : $_GET;

    $nuevoContenedor = [
        "Id_Contenedor" => 3,
        "EstadoCont" => $datos['estado'] ?? 'Funcional',
        "CapContenedor" => (int)($datos['capacidad'] ?? 2),
        "TipoResiduo" => $datos['residuo'] ?? 'Reciclable',
        "Id_Ruta" => (int)($datos['id_ruta'] ?? 101)
    ];

    // Redirige directamente a la lista HTML de contenedores
    header("Location: listarcontenedores.html");
    exit();
}
?>