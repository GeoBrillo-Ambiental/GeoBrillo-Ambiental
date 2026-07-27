<?php
// 1. Inicia la sesión para almacenar los datos del usuario en el servidor
session_start();

// Configura la cabecera por si la petición se envía mediante AJAX/Fetch o HTML tradicional
header("Content-Type: text/html; charset=utf-8");

// 2. Verifica que la solicitud provenga de un formulario enviado con método POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Captura los datos enviados desde el formulario (o los deja vacíos si no existen)
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // 3. Validación de credenciales
    // (Sustituye esta condición por tu consulta SQL a la base de datos cuando la conectes)
    $usuarioValido = "admin@eco.com";
    $passwordValida = "1234";

    if ($email === $usuarioValido && $password === $passwordValida) {
        
        // Regenera el ID de sesión por seguridad (evita ataques de fijación de sesión)
        session_regenerate_id(true);

        // 4. Guarda las variables en la sesión del servidor
        $_SESSION['usuario_id'] = 1;
        $_SESSION['email'] = $email;
        $_SESSION['rol'] = "Administrador";
        $_SESSION['autenticado'] = true;

        // 5. Redirige al panel principal (por ejemplo, a camiones.html)
        header("Location: camiones.html");
        exit();

    } else {
        
        // Si las credenciales no coinciden, devuelve al login pasando un parámetro de error en la URL
        header("Location: login.html?error=1");
        exit();

    }

} else {

    // Si intentan entrar a login.php directamente desde la barra de navegador (GET), lo manda a login.html
    header("Location: login.html");
    exit();

}
?>
