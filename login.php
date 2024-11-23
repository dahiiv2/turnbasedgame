<?php
/* initialization */
/* inicializacion */
session_start();

/* database setup */
/* configuracion de base de datos */
$host = 'localhost';
$dbname = 'dwes';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

/* login logic */
/* logica de inicio de sesion */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // check credentials
    // verificar credenciales
    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM cuentas WHERE usuario = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['contrasenya'])) {
            // start session and redirect
            // iniciar sesion y redirigir
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['usuario'];
            header("Location: game.php");
            exit();
        } else {
            // invalid credentials
            // credenciales invalidas
            header("Location: index.php?error=1");
            exit();
        }
    } else {
        // empty fields
        // campos vacios
        header("Location: index.php?error=2");
        exit();
    }
} else {
    // redirect non-post requests
    // redirigir peticiones que no son post
    header("Location: index.php");
    exit();
}