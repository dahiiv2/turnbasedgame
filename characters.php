<?php
/* characters controller
 * controlador de personajes
 */

session_start();

if (isset($_GET['get_selected_character'])) {
    header('Content-Type: application/json');
    
    // Check if a character is selected in the session
    if (isset($_SESSION['selected_character_id'])) {
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=dwes", 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Fetch character details
            $stmt = $pdo->prepare("
                SELECT 
                    id, 
                    name, 
                    max_hp, 
                    crit_chance, 
                    accuracy, 
                    character_color, 
                    character_icon
                FROM characters 
                WHERE id = ?
            ");
            $stmt->execute([$_SESSION['selected_character_id']]);
            $character = $stmt->fetch(PDO::FETCH_ASSOC);

            // Fetch character moves
            if ($character) {
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

                // Return character data as JSON
                echo json_encode($character);
                exit;
            }
        } catch(PDOException $e) {
            // Error handling
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
            exit;
        }
    }

    // No character selected
    http_response_code(404);
    echo json_encode(null);
    exit;
}

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
    // Fetch characters with their full details
    $stmt = $pdo->query("
        SELECT 
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

    $charactersWithMoves = [];
    foreach ($characters as $character) {
        $moveStmt = $pdo->prepare("SELECT move_name, move_type, base_damage, damage_variance, special_effect, move_description FROM character_moves WHERE character_id = ?");
        $moveStmt->execute([$character['id']]);
        $character['moves'] = $moveStmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($character['moves'])) {
            $character['moves'] = [];
        }
        
        $charactersWithMoves[] = $character;
    }
    $characters = $charactersWithMoves;
} catch(PDOException $e) {
    $error_message = "Error fetching characters: " . $e->getMessage();
    $characters = [];
}

/* load characters view
 * cargar vista de personajes
 */
require 'views/characters.view.php';
