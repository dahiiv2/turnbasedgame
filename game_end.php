<?php
session_start();

$_SESSION['game_end'] = [
    'character' => $_POST['character'],
    'damage' => intval($_POST['damage']),
    'is_kill' => $_POST['is_kill'] === '1',
    'highest_hit' => intval($_POST['highest_hit'])
];
?>
