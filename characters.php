<?php
/* characters controller
 * controlador de personajes
 */

session_start();

/* check if user is logged in
 * comprobar si el usuario ha iniciado sesión
 */
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

/* load characters view
 * cargar vista de personajes
 */
require 'views/characters.view.php';
