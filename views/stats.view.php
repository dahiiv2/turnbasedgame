<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player Statistics</title>
    <link rel="stylesheet" href="styles/general.css">
    <link rel="stylesheet" href="styles/navigation.css">
    <link rel="stylesheet" href="styles/stats.css">
    <link rel="stylesheet" href="styles/footer.css">
</head>
<body>
    <?php include 'partials/nav-menu.php'; ?>
    <main>
        <div class="glass-container">
            <div class="stats-page">
                <h1>Player Statistics</h1>
                <div class="stats-container">
                    <div class="section">
                        <h2>General Stats</h2>
                        <div class="general-stats">
                            <p>Games Played: <?php echo $gamesPlayed; ?></p>
                            <p>Total Damage Dealt: <?php echo $totalDamage; ?></p>
                            <p>Total Kills: <?php echo $totalKills; ?></p>
                            <p>Average Damage per Game: <?php echo $averageDamage; ?></p>
                        </div>
                    </div>
                    <div class="section">
                        <h2>Character Stats</h2>
                        <div class="character-stats">
                            <?php foreach ($characterStats as $character => $stats): ?>
                            <div class="character">
                                <h3><?php echo $character; ?></h3>
                                <p>Kills: <?php echo $stats['kills']; ?></p>
                                <p>Damage Dealt: <?php echo $stats['damage']; ?></p>
                                <p>Highest Damage Hit: <?php echo $stats['highestHit']; ?></p>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include 'partials/footer.php'; ?>
</body>
</html>
