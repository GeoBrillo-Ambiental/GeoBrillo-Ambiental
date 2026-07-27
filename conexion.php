<?php
//session_start();
$conn = mysqli_connect("localhost", "root", "", "gestion_usuarios");

if (!$conn) {
    die("La conexión falló: " . mysqli_connect_error());
}

$usuario = $_POST['usuario'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario = ? AND password = ?");
$stmt->bind_param("ss", $usuario, $password);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $_SESSION['usuario'] = $usuario;
        echo "Inicio de sesión exitoso";
        header("Location: panel.php");
        exit();
    //} else {
        //echo "Usuario o contraseña incorrectos";
    }
} else {
    echo "Error en la consulta: " . $stmt->error;
}
$stmt->close();
?>
