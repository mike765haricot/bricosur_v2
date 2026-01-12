<?php
require_once '../includes/security_functions.php';
require_once '../includes/db.php';

if ($_SESSION['user_role'] !== 'CLIENT') { die("Accès refusé."); }

$service_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// SÉCURITÉ : On vérifie que le service appartient bien au client (Anti-IDOR)
$stmt = $pdo->prepare("UPDATE services SET statut = 'TERMINE' WHERE id = ? AND client_id = ?");
$stmt->execute([$service_id, $_SESSION['user_id']]);

header("Location: client_dashboard.php?msg=service_termine");
exit();
?>