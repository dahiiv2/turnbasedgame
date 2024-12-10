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

/* database setup
 * configuracion de base de datos
 */
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

/* handle character selection
 * manejar selección de personaje
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['select_character'])) {
    $character_id = $_POST['character_id'];
    
    // Validate character exists
    $stmt = $pdo->prepare("SELECT * FROM characters WHERE id = ?");
    $stmt->execute([$character_id]);
    $selected_character = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($selected_character) {
        $_SESSION['selected_character_id'] = $character_id;
        $_SESSION['selected_character_name'] = $selected_character['name'];
        $_SESSION['selected_character_color'] = $selected_character['character_color'];
    }
}

/* fetch all characters with their moves
 * obtener todos los personajes con sus movimientos
 */
try {
    // Fetch unique characters with their full details
    $stmt = $pdo->query("
        SELECT DISTINCT 
            id, 
            name, 
            max_hp, 
            crit_chance, 
            accuracy, 
            character_color, 
            character_icon
        FROM characters
    ");
    $characters = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch moves for each character
    foreach ($characters as &$character) {
        $moveStmt = $pdo->prepare("
            SELECT 
                move_name, 
                move_type,
                base_damage, 
                damage_variance, 
                special_effect,
                move_description
            FROM character_moves 
            WHERE character_id = ?
        ");
        $moveStmt->execute([$character['id']]);
        $character['moves'] = $moveStmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch(PDOException $e) {
    $error_message = "Error fetching characters: " . $e->getMessage();
    $characters = [];
}

/* load characters view
 * cargar vista de personajes
 */
require 'views/characters.view.php';
