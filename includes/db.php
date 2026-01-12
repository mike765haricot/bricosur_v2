<?php
/**
 * CONFIGURATION DE LA BASE DE DONNÉES
 * Ce fichier ne gère plus la session pour éviter les conflits de Warning.
 * La session est désormais centralisée dans security_functions.php.
 */

function loadEnv($path) {
    if (!file_exists($path)) {
        return false;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue; // Ignore les commentaires
        list($name, $value) = explode('=', $line, 2);
        putenv(trim($name) . "=" . trim($value));
    }
}

// Chargement des variables d'environnement (Exigence 28)
loadEnv(__DIR__ . '/../.env');

// Récupération des secrets via getenv()
$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3307';
$db   = getenv('DB_NAME') ?: 'bricosur_v2';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

/**
 * CONFIGURATION SÉCURISÉE DE PDO
 * Protection maximale contre les injections SQL (Exigence 29).
 */
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false, 
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // SÉCURITÉ : On masque l'erreur technique brute (Information Disclosure).
    die("Erreur de connexion sécurisée à la base de données.");
}
?>