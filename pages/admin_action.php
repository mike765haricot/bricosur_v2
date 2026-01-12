<?php
session_start();
require_once '../includes/db.php'; // Sécurisé via .env

// SÉCURITÉ : Contrôle d'accès RBAC strict 
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'ADMIN') {
    die("Accès non autorisé.");
}

$type = $_GET['type']; // 'user' ou 'service'
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

try {
    if ($type === 'user') {
        // Protection : l'admin ne peut pas se supprimer lui-même
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'ADMIN'");
        $stmt->execute([$id]);
    } elseif ($type === 'service') {
        $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
        $stmt->execute([$id]);
    }
    header("Location: admin_dashboard.php?success=action_done");
} catch (PDOException $e) {
    die("Erreur de modération sécurisée.");
}