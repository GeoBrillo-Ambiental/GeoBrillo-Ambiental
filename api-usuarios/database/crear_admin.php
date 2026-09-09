<?php
/**
 * Script de un solo uso: crea el usuario Administrador inicial.
 * Ejecutar UNA vez, por ejemplo:
 *   docker compose exec api-usuarios php database/crear_admin.php
 * y luego eliminar este archivo (o al menos restringir su acceso).
 */
require_once __DIR__ . '/../config/conexion.php';

$pdo = conectar();

$stmt = $pdo->prepare("SELECT Id_Rol FROM Rol WHERE Nombre_Rol = 'Administrador'");
$stmt->execute();
$rol = $stmt->fetch();

if (!$rol) {
    $pdo->prepare("INSERT INTO Rol (Nombre_Rol, Descripcion) VALUES ('Administrador', 'Acceso total al sistema')")
        ->execute();
    $idRol = $pdo->lastInsertId();
} else {
    $idRol = $rol['Id_Rol'];
}

// Evita crear un segundo admin si el script se corre más de una vez
$stmt = $pdo->prepare("SELECT Id_Usuario FROM Usuario WHERE Email = 'admin@eco.com'");
$stmt->execute();
if ($stmt->fetch()) {
    echo "El usuario Administrador ya existe. No se creó ninguno nuevo.\n";
    exit();
}

$passwordHash = password_hash('1234', PASSWORD_DEFAULT);

$sql = "INSERT INTO Usuario (Pri_Nom, Pri_Ape, Nom_Usuario, Password, Email, Id_Rol, Id_Cuadrilla)
        VALUES ('Admin', 'General', 'AdminGeneral', :password, 'admin@eco.com', :idRol, NULL)";
$stmt = $pdo->prepare($sql);
$stmt->execute([':password' => $passwordHash, ':idRol' => $idRol]);

echo "Usuario Administrador creado correctamente.\n";
echo "Email: admin@eco.com | Password: 1234\n";
