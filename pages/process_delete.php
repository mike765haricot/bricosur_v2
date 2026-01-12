<?php
require_once '../includes/security_functions.php';
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    // SÉCURITÉ : On vérifie le jeton CSRF avant de supprimer
    verify_csrf_token($_POST['csrf_token']);

    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);

    session_destroy();
    header("Location: ../index.php?msg=account_deleted");
    exit();
}