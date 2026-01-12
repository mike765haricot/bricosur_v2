<?php
require_once '../includes/security_functions.php';
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit(); }

$user_id = $_SESSION['user_id'];

// Récupération des 20 derniers logs pour cet utilisateur
$stmt = $pdo->prepare("SELECT * FROM activity_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT 20");
$stmt->execute([$user_id]);
$logs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique de Sécurité - BricoSûr</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f8fafc; }
        .log-card {
            background: white; border-radius: 15px; padding: 20px;
            margin-bottom: 15px; display: flex; align-items: center;
            gap: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);
            border-left: 5px solid var(--primary-color);
        }
        .log-icon {
            width: 45px; height: 45px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            background: #edf2f7; color: #4a5568;
        }
        .ip-badge {
            background: #e2e8f0; color: #4a5568; padding: 4px 10px;
            border-radius: 5px; font-size: 0.75rem; font-family: monospace;
        }
    </style>
</head>
<body>

<div class="container" style="margin-top: 50px; max-width: 800px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="color: var(--primary-color);"><i class="fas fa-history"></i> Journal d'activité</h2>
        <a href="profil.php" class="btn btn-outline" style="text-decoration: none;">Retour au profil</a>
    </div>

    <?php if (count($logs) > 0): ?>
        <?php foreach ($logs as $log): ?>
            <div class="log-card">
                <div class="log-icon">
                    <i class="fas <?php echo strpos($log['action'], 'Connexion') !== false ? 'fa-sign-in-alt' : 'fa-info-circle'; ?>"></i>
                </div>
                <div style="flex-grow: 1;">
                    <div style="font-weight: 600; color: #2d3748;"><?php echo htmlspecialchars($log['action']); ?></div>
                    <div style="font-size: 0.85rem; color: #718096;">
                        <?php echo date('d/m/Y à H:i', strtotime($log['created_at'])); ?>
                    </div>
                </div>
                <div class="ip-badge"><?php echo $log['ip_address']; ?></div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div style="text-align: center; padding: 50px; background: white; border-radius: 20px;">
            <p style="color: #a0aec0;">Aucun log d'activité disponible pour le moment.</p>
        </div>
    <?php endif; ?>

    <p style="font-size: 0.8rem; color: #a0aec0; text-align: center; margin-top: 20px;">
        <i class="fas fa-info-circle"></i> Ces journaux sont conservés pour votre sécurité conformément au RGPD.
    </p>
</div>

</body>
</html>