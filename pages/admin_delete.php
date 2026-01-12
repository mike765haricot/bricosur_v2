<?php
session_start();
require_once '../includes/db.php';

// Sécurité RBAC stricte : Accès réservé à l'ADMIN 
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'ADMIN') {
    die("Accès non autorisé.");
}

$type = $_GET['type']; // 'user' ou 'service'
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

try {
    if ($type === 'service') {
        $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
    } elseif ($type === 'user') {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'ADMIN'");
    }
    
    $stmt->execute([$id]);
    header("Location: admin_dashboard.php?msg=deleted");
} catch (PDOException $e) {
    die("Erreur de suppression sécurisée.");
}