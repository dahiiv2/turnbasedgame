<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Not logged in');
}

// Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

// Store game end data in session
$_SESSION['game_end'] = [
    'character' => $_POST['character'],
    'damage' => intval($_POST['damage']),
    'is_kill' => $_POST['is_kill'] === '1',
    'highest_hit' => intval($_POST['highest_hit'])
];

// Return success
echo json_encode(['success' => true]);
?>
