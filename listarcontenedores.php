<?php

include "conexion.php";

// Solo permitir método GET
if ($_SERVER["REQUEST_METHOD"] != "GET") {

    http_response_code(405);

    echo json_encode([
        "mensaje" => "Método no permitido"
    ]);

    exit();
}

$sql = "SELECT
            Id_Contenedor,
            EstadoCont,
            CapContenedor,
            TipoResiduo,
            Id_Ruta
        FROM contenedor";

$resultado = mysqli_query($conn, $sql);

$contenedores = array();

while ($fila = mysqli_fetch_assoc($resultado)) {

    $contenedores[] = $fila;

}

header("Content-Type: application/json");

echo json_encode($contenedores);

mysqli_close($conn);

?>