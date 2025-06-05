<?php
    session_start(); // Iniciar la sesión para poder acceder a $_SESSION
    session_unset(); // Eliminar todas las variables de sesión
    session_destroy(); // Destruir la sesión actual
    header("Location: ../index.php"); // Redirigir al usuario a la página de inicio
    exit; // Salir del script
?>