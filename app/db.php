<?php

$host = "db";
$user = "root";
$password = "P@ssw0rdEN24";
$database = "votes";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$database;charset=utf8",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données");
}
