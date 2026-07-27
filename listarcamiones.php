<?php

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    include "conexion.php";

    $sql = "SELECT * FROM camion";

    $resultado = mysqli_query($conn, $sql);

    $camiones = array();

    while($fila = mysqli_fetch_assoc($resultado)){
        $camiones[] = $fila;
    }

    header("Content-Type: application/json");

    echo json_encode($camiones);

} else {

    http_response_code(405);

    echo "Método no permitido";

}

?>