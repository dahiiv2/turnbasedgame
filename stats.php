<?php

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Initialize game stats in session if not set
if (!isset($_SESSION['game_stats'])) {
    $_SESSION['game_stats'] = [
        'Blade Master' => ['kills' => 0, 'damage' => 0, 'highestHit' => 0],
        'Shadow Assassin' => ['kills' => 0, 'damage' => 0, 'highestHit' => 0],
        'Battle Mage' => ['kills' => 0, 'damage' => 0, 'highestHit' => 0],
        'Berserker' => ['kills' => 0, 'damage' => 0, 'highestHit' => 0]
    ];
}

// Update stats if game ended
if (isset($_SESSION['game_end'])) {
    $character = $_SESSION['game_end']['character'];
    $damage = $_SESSION['game_end']['damage'];
    $isKill = $_SESSION['game_end']['is_kill'];
    $highestHit = $_SESSION['game_end']['highest_hit'];

    // Update character stats
    $_SESSION['game_stats'][$character]['damage'] += $damage;
    if ($isKill) {
        $_SESSION['game_stats'][$character]['kills']++;
    }
    if ($highestHit > $_SESSION['game_stats'][$character]['highestHit']) {
        $_SESSION['game_stats'][$character]['highestHit'] = $highestHit;
    }

    // Clear game end data
    unset($_SESSION['game_end']);
}

// Calculate totals
$totalDamage = 0;
$totalKills = 0;
$gamesPlayed = 0;

foreach ($_SESSION['game_stats'] as $stats) {
    $totalDamage += $stats['damage'];
    $totalKills += $stats['kills'];
    $gamesPlayed += $stats['kills']; // Each kill represents a completed game
}

$averageDamage = $gamesPlayed > 0 ? round($totalDamage / $gamesPlayed) : 0;
$characterStats = $_SESSION['game_stats'];

require "views/stats.view.php";
?>
