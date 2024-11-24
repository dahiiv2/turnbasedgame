<?php
session_start();
require_once 'utils/password_validation.php';

// DB Connection
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
    
    $error = '';
    
    // Basic checks
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = 'Fill in all fields';
    } elseif (strlen($username) < 3 || strlen($username) > 16) {
        $error = 'Username must be between 3 and 16 characters';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        // Check if username exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM cuentas WHERE usuario = ?");
        $stmt->execute([$username]);
        if ($stmt->fetchColumn() > 0) {
            $error = 'That username is taken';
        } else {
            try {
                validatePassword($password);
                
                // Create account
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO cuentas (usuario, contrasenya) VALUES (?, ?)");
                $stmt->execute([$username, $hashed_password]);
                
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['username'] = $username;
                header("Location: characters.php");
                exit();
                
            } catch (InvalidPasswordException $e) {
                $error = $e->getMessage();
            } catch(PDOException $e) {
                $error = 'Something went wrong, try again';
            }
        }
    }
    
    if ($error) {
        header("Location: createAccount.php?error=" . urlencode($error));
        exit();
    }
}

// Show error if any
$error_message = isset($_GET['error']) ? $_GET['error'] : '';

include 'views/createAccount.view.php';
?>
