<?php
// account controller
// controlador de cuenta

//Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

session_start();
require_once 'utils/password_validation.php';

/* check if user is logged in
comprobar si el usuario ha iniciado sesión */
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

/* database setup
configuracion de base de datos */
$host = 'localhost';
$dbname = 'dwes';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

/* fetch user data including profile picture
obtener datos del usuario incluyendo foto de perfil */
$stmt = $pdo->prepare("SELECT * FROM cuentas WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

/* handle profile picture upload */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_picture']) && isset($_FILES['profile_picture'])) {
    $file = $_FILES['profile_picture'];
    
    if ($file['size'] > 2 * 1024 * 1024) { 
        header("Location: account.php?error=7");
        exit();
    }

    try {
        $imageData = file_get_contents($file['tmp_name']);
        $stmt = $pdo->prepare("UPDATE cuentas SET imagen = ? WHERE id = ?");
        $stmt->execute([$imageData, $_SESSION['user_id']]);
        header("Location: account.php?success=2");
    } catch(PDOException $e) {
        header("Location: account.php?error=4");
    }
    exit();
}

/* handle password change
 * manejar cambio de contraseña
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_new_password'] ?? '';

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        header("Location: account.php?error=1"); // Empty fields
        exit();
    }

    if ($new_password !== $confirm_password) {
        header("Location: account.php?error=2"); // Passwords don't match
        exit();
    }

    try {
        // Validate new password format
        validatePassword($new_password);

        // Verify current password
        $stmt = $pdo->prepare("SELECT contrasenya FROM cuentas WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();

        if (!password_verify($current_password, $user['contrasenya'])) {
            header("Location: account.php?error=3"); // Current password incorrect
            exit();
        }

        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE cuentas SET contrasenya = ? WHERE id = ?");
        $stmt->execute([$hashed_password, $_SESSION['user_id']]);

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = gethostbyname('smtp.gmail.com');
            $mail->SMTPAuth = true;
            $mail->Username = 'phpdahii@gmail.com';
            $mail->Password = 'gyjf dbvm mpgc mwdb';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->SMTPDebug = 0;
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ]
            ];

            $mail->setFrom('phpdahii@gmail.com');
            $mail->addAddress('phpdahii@gmail.com');
            $mail->addReplyTo('phpdahii@gmail.com');

            $mail->isHTML(true);
            $mail->Subject = 'Password Change';
            $mail->Body = "
                <h2>Password Change Notification</h2>
                <p>Your password was successfully changed on: 2024-12-12 19:28:43</p>
            ";

            $mail->send();
        } catch (Exception $e) {
            error_log("Failed to send email: {$mail->ErrorInfo}");
        }

        header("Location: account.php?success=1"); 
        exit();
    } catch(InvalidPasswordException $e) {
        header("Location: account.php?error=5&message=" . urlencode($e->getMessage()));
        exit();
    } catch(PDOException $e) {
        header("Location: account.php?error=4"); 
        exit();
    }
}

/* handle account deletion
 * manejar eliminación de cuenta
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM cuentas WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        session_destroy();
        header("Location: index.php?message=account_deleted");
        exit();
    } catch(PDOException $e) {
        header("Location: account.php?error=4"); // Database error
        exit();
    }
}

/* error handling
manejo de errores por GET */

$error_message = '';
$success_message = '';

if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case '1':
            $error_message = 'Please fill in all fields';
            break;
        case '2':
            $error_message = 'New passwords do not match';
            break;
        case '3':
            $error_message = 'Current password is incorrect';
            break;
        case '4':
            $error_message = 'An error occurred. Please try again';
            break;
        case '5':
            $error_message = isset($_GET['message']) ? $_GET['message'] : 'Invalid password format';
            break;
        case '6':
            $error_message = 'Invalid file type. Please upload a JPEG, PNG, or GIF image';
            break;
        case '7':
            $error_message = 'File is too large. Maximum size is 2MB';
            break;
        case '8':
            $error_message = 'Error uploading file. Please try again';
            break;
    }
}

if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case '1':
            $success_message = 'Password updated successfully';
            break;
        case '2':
            $success_message = 'Profile picture updated successfully';
            break;
    }
}

/* load account view
 * cargar vista de cuenta
 */
require "views/account.view.php";