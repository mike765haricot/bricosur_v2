<?php
require_once '../includes/security_functions.php';
require_once '../includes/db.php';

// Protection RBAC : Seul le client peut accepter
if ($_SESSION['user_role'] !== 'CLIENT') { die("Accès refusé."); }

$postulation_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

try {
    $pdo->beginTransaction();

    // 1. Récupérer les infos de la postulation
    $stmt = $pdo->prepare("SELECT service_id, provider_id FROM postulations WHERE id = ?");
    $stmt->execute([$postulation_id]);
    $postu = $stmt->fetch();

    if ($postu) {
        // 2. Mettre à jour le service : Statut EN COURS et assignation du prestataire
        $stmt_upd = $pdo->prepare("UPDATE services SET statut = 'EN COURS', provider_id = ? WHERE id = ? AND client_id = ?");
        $stmt_upd->execute([$postu['provider_id'], $postu['service_id'], $_SESSION['user_id']]);

        $pdo->commit();
        header("Location: client_dashboard.php?msg=service_demarre");
    } else {
        $pdo->rollBack();
        die("Postulation introuvable.");
    }
} catch (Exception $e) {
    $pdo->rollBack();
    die("Erreur de transition de statut.");
}
?>