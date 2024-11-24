<?php
/* logout script
 * script de cierre de sesión
 */

/* start session if not already started
 * iniciar sesión si no está iniciada
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* destroy session and all data
 * destruir sesión y todos los datos
 */
session_destroy();

/* redirect to login page
 * redirigir a la página de inicio de sesión
 */
header("Location: index.php");
exit();
