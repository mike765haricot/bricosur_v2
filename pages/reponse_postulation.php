<?php
session_start();
require_once '../includes/db.php';

// Sécurité RBAC : Seul le client propriétaire peut décider 
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'CLIENT') { exit(); }

$postulation_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$action = $_GET['action']; // ACCEPTE ou REFUSE

try {
    $pdo->beginTransaction(); // Transaction pour la cohérence des données 

    // 1. Mettre à jour le statut de la postulation
    $stmt1 = $pdo->prepare("UPDATE postulations SET statut = ? WHERE id = ?");
    $stmt1->execute([$action, $postulation_id]);

    if ($action === 'ACCEPTE') {
        // 2. Récupérer l'ID du service lié
        $stmt2 = $pdo->prepare("SELECT service_id FROM postulations WHERE id = ?");
        $stmt2->execute([$postulation_id]);
        $res = $stmt2->fetch();
        
        // 3. Passer le service en statut 'EN COURS' 
        $stmt3 = $pdo->prepare("UPDATE services SET statut = 'EN COURS' WHERE id = ?");
        $stmt3->execute([$res['service_id']]);
    }

    $pdo->commit();
    header("Location: gestion_postulations.php?msg=updated");
} catch (PDOException $e) {
    $pdo->rollBack();
    die("Erreur lors de la mise à jour sécurisée.");
}