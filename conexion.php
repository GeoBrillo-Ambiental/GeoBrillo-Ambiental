<?php
// Inicia la sesión para compartir datos en memoria entre archivos
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>