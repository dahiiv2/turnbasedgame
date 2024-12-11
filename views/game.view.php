<?php
session_start();

// Fetch selected character's moves
if (isset($_SESSION['selected_character_id'])) {
    $pdo = new PDO("mysql:host=localhost;dbname=dwes", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $moveStmt = $pdo->prepare("
        SELECT 
            move_name, 
            move_type,
            base_damage
        FROM character_moves 
        WHERE character_id = ?
    ");
    $moveStmt->execute([$_SESSION['selected_character_id']]);
    $moves = $moveStmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/general.css">
    <link rel="stylesheet" href="styles/navigation.css">
    <link rel="stylesheet" href="styles/background.css">
    <link rel="stylesheet" href="styles/game.css">
    <title>Juego</title>
</head>
<body>
    <?php include 'partials/nav-menu.php'; ?>
    <div class="game-screen">
        <div class="game-container glass-container">
            <div class="canvas-container">
                <canvas id="gameCanvas" width="800" height="600"></canvas>
                <div class="attack-buttons moves-container">
                    <?php 
                    if (!empty($moves)) {
                        foreach ($moves as $move) {
                            // Convert move name to a valid JavaScript function parameter
                            $safeMoveParam = strtolower(str_replace(' ', '', $move['move_name']));
                            echo "<button class='btn' onclick='playerAttack(\"{$safeMoveParam}\")'>{$move['move_name']}</button>";
                        }
                    } else {
                        // Fallback to default moves if no moves found
                        echo "<button class='btn' onclick='playerAttack(\"slash\")'>Slash</button>";
                        echo "<button class='btn' onclick='playerAttack(\"focus\")'>Focus</button>";
                        echo "<button class='btn' onclick='playerAttack(\"bladestorm\")'>Bladestorm</button>";
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="chat-log glass-container">
            <div id="log-container"></div>
        </div>
        <div class="player-section">
            <div class="player-info">
                <div class="player-icon"></div>
                <div class="player-details">
                    <h2 class="player-name"></h2>
                    <div class="player-stats">
                        <span class="player-hp"></span>
                        <span class="player-crit-chance"></span>
                        <span class="player-accuracy"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="module" src="js/game.js"></script>
    <script type="module" src="js/characters.js"></script>
    <script type="module" src="js/attacks.js"></script>
</body>
</html>