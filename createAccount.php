<?php
session_start();
require_once 'utils/password_validation.php';

// Database connection
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate input
    if (empty($username) || empty($password) || empty($confirm_password)) {
        header("Location: createAccount.php?error=1"); // Empty fields
        exit();
    }

    if (strlen($username) < 3 || strlen($username) > 16) {
        header("Location: createAccount.php?error=2"); // Invalid username length
        exit();
    }

    if ($password !== $confirm_password) {
        header("Location: createAccount.php?error=4"); // Passwords don't match
        exit();
    }

    // Validate password complexity
    try {
        validatePassword($password);
    } catch (InvalidPasswordException $e) {
        header("Location: createAccount.php?error=7&message=" . urlencode($e->getMessage()));
        exit();
    }

    // Check if username already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM cuentas WHERE usuario = ?");
    $stmt->execute([$username]);
    if ($stmt->fetchColumn() > 0) {
        header("Location: createAccount.php?error=5"); // Username taken
        exit();
    }

    // Create account
    try {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO cuentas (usuario, contrasenya) VALUES (?, ?)");
        $stmt->execute([$username, $hashed_password]);
        
        // Auto-login after account creation
        //$_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['username'] = $username;
        header("Location: game.php");
        exit();
    } catch(PDOException $e) {
        header("Location: createAccount.php?error=6"); // Database error
        exit();
    }
} else {
    // Handle error messages
    $error_message = '';
    if (isset($_GET['error'])) {
        switch ($_GET['error']) {
            case '1':
                $error_message = 'Please fill in all fields';
                break;
            case '2':
                $error_message = 'Username must be between 3 and 16 characters';
                break;
            case '4':
                $error_message = 'Passwords do not match';
                break;
            case '5':
                $error_message = 'Username already taken';
                break;
            case '6':
                $error_message = 'An error occurred. Please try again';
                break;
            case '7':
                $error_message = isset($_GET['message']) ? $_GET['message'] : 'Invalid password format';
                break;
        }
    }
    require "views/createAccount.view.php";
}
