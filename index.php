<?php
session_start();

/* if user is already logged in, redirect to game
if (isset($_SESSION['user_id'])) {
    header("Location: game.php");
    exit();
}
*/

// handle error messages
// manejo de mensajes de error
$error_message = '';
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case '1':
            $error_message = 'Invalid username or password';
            break;
        case '2':
            $error_message = 'Please fill in all fields';
            break;
    }
}

require "views/index.view.php";