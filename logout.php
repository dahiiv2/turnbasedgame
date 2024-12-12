<?php
// start session if not already started
// iniciar sesión si no está iniciada
 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// destroy session and all data
// destruir sesión y todos los datos

session_destroy();
header("Location: index.php");
exit();
