<?php
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: index.html");
    exit();
}

// Verificar que la petición sea GET
if ($_SERVER["REQUEST_METHOD"] != "GET") {
    http_response_code(405);
    echo json_encode(["mensaje" => "Método no permitido"]);
    exit();
}

include "conexion.php";

$sql = "SELECT
            Id_Usuario,
            Pri_Nom,
            Pri_Ape,
            Nom_Usuario,
            Email,
            Rol,
            Id_Cuadrilla
        FROM usuarios";

$resultado = mysqli_query($conn, $sql);

$usuarios = [];

while ($fila = mysqli_fetch_assoc($resultado)) {
    $usuarios[] = $fila;
}

header("Content-Type: application/json");
echo json_encode($usuarios);

mysqli_close($conn);
?>
