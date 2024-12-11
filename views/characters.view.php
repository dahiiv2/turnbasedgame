<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/general.css">
    <link rel="stylesheet" href="styles/navigation.css">
    <link rel="stylesheet" href="styles/background.css">
    <link rel="stylesheet" href="styles/characters.css">
    <title>Strike - Character Selection</title>
</head>
<body>
    <?php include 'partials/nav-menu.php'; ?>
    
    <div class="characters-screen">
        <?php 
        // Ensure characters array is not null
        $characters = $characters ?? [];

        // Ensure selected character is handled safely
        $selected_character = isset($_SESSION['selected_character_name']) ? 
            [
                'name' => $_SESSION['selected_character_name'], 
                'color' => $_SESSION['selected_character_color'] ?? '',
                'max_hp' => 0,
                'crit_chance' => 0,
                'accuracy' => 0,
                'moves' => []
            ] : 
            null; 

        // If a character is selected, find its full details
        if ($selected_character) {
            foreach ($characters as $character) {
                if ($character['name'] === $selected_character['name']) {
                    $selected_character = array_merge($selected_character, $character);
                    break;
                }
            }
        }
        ?>

        <?php if ($selected_character): ?>
            <div class="selected-character-banner">
                <div class="selected-character-details">
                    <div class="selected-character-icon" style="background-color: <?php echo $selected_character['color']; ?>"></div>
                    <div class="selected-character-info">
                        <h2><?php echo htmlspecialchars($selected_character['name']); ?></h2>
                        <div class="selected-character-stats">
                            <span>HP: <?php echo $selected_character['max_hp']; ?></span>
                            <span>Crit Chance: <?php echo number_format($selected_character['crit_chance'] * 100, 0); ?>%</span>
                            <span>Accuracy: <?php echo number_format($selected_character['accuracy'] * 100, 0); ?>%</span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="character-selection-container">
            <h1>Choose Your Champion</h1>
            
            <div class="characters-grid">
                <?php foreach ($characters as $character): ?>
                    <div class="character-card" data-character-id="<?php echo $character['id']; ?>">
                        <div class="character-header">
                            <h2><?php echo htmlspecialchars($character['name']); ?></h2>
                        </div>
                        
                        <div class="character-stats">
                            <div class="stat">
                                <span class="stat-label">HP:</span>
                                <span class="stat-value"><?php echo $character['max_hp']; ?></span>
                            </div>
                            <div class="stat">
                                <span class="stat-label">Crit Chance:</span>
                                <span class="stat-value"><?php echo number_format($character['crit_chance'] * 100, 0); ?>%</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label">Accuracy:</span>
                                <span class="stat-value"><?php echo number_format($character['accuracy'] * 100, 0); ?>%</span>
                            </div>
                        </div>
                        
                        <div class="character-moves">
                            <h3>Moves</h3>
                            <ul>
                                <?php foreach ($character['moves'] as $move): ?>
                                    <li>
                                        <div class="move-header">
                                            <span class="move-name"><?php echo htmlspecialchars($move['move_name']); ?></span>
                                            <?php if (!empty($move['move_type'])): ?>
                                            <span class="move-type"><?php echo htmlspecialchars($move['move_type']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="move-stats">
                                            <div class="stat-group">
                                                <span class="stat-label">Base Damage:</span>
                                                <span class="stat-value"><?php echo $move['base_damage']; ?></span>
                                            </div>
                                            <div class="stat-group">
                                                <span class="stat-label">Damage Variance:</span>
                                                <span class="stat-value">±<?php echo $move['damage_variance']; ?></span>
                                            </div>
                                            <?php if (!empty($move['special_effect']) && $move['special_effect'] !== 'None'): ?>
                                            <div class="stat-group">
                                                <span class="stat-label">Special Effect:</span>
                                                <span class="stat-value"><?php echo htmlspecialchars($move['special_effect']); ?></span>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="move-description">
                                            <?php echo htmlspecialchars($move['move_description'] ?? 'No description available'); ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        
                        <form method="POST" action="characters.php">
                            <input type="hidden" name="character_id" value="<?php echo $character['id']; ?>">
                            <input type="hidden" name="select_character" value="1">
                            <button type="submit" class="select-character-btn">
                                Select Character
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script type="module" src="js/characters.js"></script>
</body>
</html>