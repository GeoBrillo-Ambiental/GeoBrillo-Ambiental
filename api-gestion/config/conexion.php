<?php

function conectar() {
    $host   = getenv('DB_HOST') ?: 'host.docker.internal';
    $port   = getenv('DB_PORT') ?: '3306';
    $dbname = getenv('DB_NAME') ?: 'geobrillo';
    $user   = getenv('DB_USER') ?: 'sigeru_user';
    $pass   = getenv('DB_PASS') ?: 'sigeru_password_123';

    try {
        $pdo = new PDO(
            "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
            $user,
            $pass
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        date_default_timezone_set('America/Montevideo');
        $pdo->exec("SET time_zone = '-03:00'");
        return $pdo;
    } catch (PDOException $e) {
        http_response_code(500);
        header("Content-Type: application/json; charset=utf-8");
        die(json_encode(["exito" => false, "mensaje" => "Error de conexión a la base de datos: " . $e->getMessage()]));
    }
}
