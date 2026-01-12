<?php
require_once '../includes/security_functions.php';
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'PROVIDER') {
    header("Location: ../login.php"); exit();
}

$user_id = $_SESSION['user_id'];

// Récupération de l'historique des candidatures
$stmt = $pdo->prepare("
    SELECT p.*, s.titre as service_titre, s.categorie, s.prix_estime 
    FROM postulations p 
    JOIN services s ON p.service_id = s.id 
    WHERE p.provider_id = ? 
    ORDER BY p.date_postulation DESC
");
$stmt->execute([$user_id]);
$historique = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Historique Pro - BricoSûr</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f0f2f5; }
        .history-card {
            background: white; border-radius: 15px; padding: 20px;
            margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02); border-left: 5px solid #cbd5e0;
        }
        .status-ACCEPTE { border-left-color: #2ecc71; }
        .status-REFUSE { border-left-color: #e74c3c; }
        .badge { padding: 5px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 700; }
    </style>
</head>
<body>
    <div class="container" style="margin-top: 50px; max-width: 900px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 30px;">
            <h2><i class="fas fa-history"></i> Suivi de mes candidatures</h2>
            <a href="provider_dashboard.php" class="btn btn-outline">Retour Dashboard</a>
        </div>

        <?php foreach ($historique as $h): ?>
            <div class="history-card status-<?php echo $h['statut']; ?>">
                <div>
                    <h4 style="margin:0;"><?php echo htmlspecialchars($h['service_titre']); ?></h4>
                    <small style="color:#7f8c8d;"><?php echo htmlspecialchars($h['categorie']); ?> • Postulé le <?php echo date('d/m/Y', strtotime($h['date_postulation'])); ?></small>
                </div>
                <div style="text-align: right;">
                    <div style="font-weight: bold; color: var(--primary-color);"><?php echo number_format($h['prix_estime'], 2); ?> €</div>
                    <span class="badge" style="background: #f8fafc; color: #4a5568; border: 1px solid #ddd;">
                        <?php echo $h['statut']; ?>
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>