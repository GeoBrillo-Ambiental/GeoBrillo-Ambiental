<?php
/**
 * Conexión a la base de datos usando PDO.
 * Los 3 contenedores (frontend, api-usuarios, api-gestion) apuntan a
 * la MISMA base de datos MySQL (servicio "db" del docker-compose), por
 * eso el host es el nombre del servicio Docker y no "localhost".
 */

function conectar() {
    $host   = getenv('DB_HOST') ?: 'db';
    $dbname = getenv('DB_NAME') ?: 'geobrillo';
    $user   = getenv('DB_USER') ?: 'sigeru_user';
    $pass   = getenv('DB_PASS') ?: 'sigeru_password_123';

    try {
        $pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            $user,
            $pass
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        http_response_code(500);
        header("Content-Type: application/json; charset=utf-8");
        die(json_encode(["exito" => false, "mensaje" => "Error de conexión a la base de datos: " . $e->getMessage()]));
    }
}
