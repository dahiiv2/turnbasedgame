<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=dwes", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $stmt = $pdo->prepare("
            UPDATE characters 
            SET max_hp = ?, 
                crit_chance = ?, 
                accuracy = ?, 
                crit_damage = ?, 
                character_color = ? 
            WHERE id = ?
        ");
        $stmt->execute([
            $_POST['max_hp'],
            $_POST['crit_chance'],
            $_POST['accuracy'],
            $_POST['crit_damage'],
            $_POST['character_color'],
            $_POST['character_id']
        ]);
    }

    $stmt = $pdo->query("
        SELECT 
            id, 
            name, 
            max_hp, 
            crit_chance, 
            accuracy, 
            crit_damage,
            character_color
        FROM characters
    ");
    $characters = $stmt->fetchAll(PDO::FETCH_ASSOC);

    require_once 'views/modify.view.php';
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}
