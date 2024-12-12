<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/general.css">
    <link rel="stylesheet" href="styles/navigation.css">
    <link rel="stylesheet" href="styles/footer.css">
    <link rel="stylesheet" href="styles/background.css">
    <link rel="stylesheet" href="styles/modify.css">
    <title>Strike - Modify Characters</title>
</head>
<body>
    <?php include 'partials/nav-menu.php'; ?>
    
    <div class="glass-container">
        <h1>Modify Characters</h1>
        
        <?php 
        $characters = $characters ?? [];
        ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <div class="characters-grid">
            <?php foreach ($characters as $character): ?>
                <div class="character-card" style="border-color: <?php echo htmlspecialchars($character['character_color']); ?>">
                    <h2><?php echo htmlspecialchars($character['name']); ?></h2>
                    <form action="modify.php" method="POST">
                        <input type="hidden" name="character_id" value="<?php echo $character['id']; ?>">
                        
                        <div class="stat-group">
                            <label>Max HP:</label>
                            <input type="number" name="max_hp" value="<?php echo $character['max_hp']; ?>" required>
                        </div>

                        <div class="stat-group">
                            <label>Crit Chance:</label>
                            <input type="number" step="0.01" name="crit_chance" value="<?php echo $character['crit_chance']; ?>" required>
                        </div>

                        <div class="stat-group">
                            <label>Accuracy:</label>
                            <input type="number" step="0.01" name="accuracy" value="<?php echo $character['accuracy']; ?>" required>
                        </div>

                        <div class="stat-group">
                            <label>Crit Damage:</label>
                            <input type="number" step="0.01" name="crit_damage" value="<?php echo $character['crit_damage']; ?>" required>
                        </div>

                        <div class="stat-group">
                            <label>Color:</label>
                            <input type="color" name="character_color" value="<?php echo $character['character_color']; ?>" required>
                        </div>

                        <button type="submit" class="modify-btn">Save Changes</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php include 'partials/footer.php'; ?>
</body>
</html>
