<?php
require 'db.php';

$allowed = ['McDonalds', 'BurgerKing', 'KFC', 'OTacos'];

if (!isset($_POST['restaurant'])) {
    header("Location: index.php?error=invalid_choice");
    exit();
}

$restaurant = $_POST['restaurant'];

if (!in_array($restaurant, $allowed, true)) {
    header("Location: index.php?error=invalid_choice");
    exit();
}

if (isset($_COOKIE['voted'])) {
    header("Location: index.php?error=already_voted");
    exit();
}

try {
    $stmt = $pdo->prepare("INSERT INTO votes (restaurant) VALUES (?)");
    $stmt->execute([$restaurant]);

    setcookie('voted', '1', time() + 3600, '/');

    header("Location: index.php?success=1");
    exit();
} catch (PDOException $e) {
    header("Location: index.php?error=db");
    exit();
}
