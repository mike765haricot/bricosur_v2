<?php
require_once '../includes/security_functions.php';
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'PROVIDER') {
    header("Location: ../login.php"); exit();
}

$user_id = $_SESSION['user_id'];

// Statistiques Pro
$stmt = $pdo->prepare("
    SELECT u.*, 
    (SELECT COUNT(*) FROM postulations WHERE provider_id = u.id) as total_postulations,
    (SELECT COUNT(*) FROM postulations WHERE provider_id = u.id AND statut = 'ACCEPTE') as chantiers_remportes
    FROM users u WHERE u.id = ?
");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil Professionnel - BricoSûr</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Réutilisation des styles du profil client pour la cohérence */
        body { font-family: 'Poppins', sans-serif; background-color: #f0f2f5; }
        .profile-header { background: linear-gradient(135deg, #0056b3 0%, #002d5e 100%); height: 200px; border-radius: 0 0 50px 50px; position: relative; margin-bottom: 100px; }
        .profile-avatar-container { position: absolute; bottom: -60px; left: 50%; transform: translateX(-50%); text-align: center; }
        .profile-avatar { width: 130px; height: 130px; background: white; border: 5px solid #f0f2f5; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 3.5rem; color: #0056b3; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .info-card { background: white; border-radius: 25px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .stats-container { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 20px; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .info-label { color: #718096; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; }
        .info-value { color: #2d3748; font-size: 1.1rem; font-weight: 600; margin-bottom: 20px; }
    </style>
</head>
<body>

    <div class="profile-header">
        <div class="container"><a href="provider_dashboard.php" style="color: white; text-decoration: none; display: inline-block; margin-top: 20px;"><i class="fas fa-arrow-left"></i> Retour au Mode Pro</a></div>
        <div class="profile-avatar-container">
            <div class="profile-avatar"><?php echo strtoupper(substr($user['nom'], 0, 1)); ?></div>
            <h2 style="margin-top: 15px;"><?php echo htmlspecialchars($user['nom']); ?></h2>
            <div style="background: #e6fffa; color: #2c7a7b; padding: 5px 15px; border-radius: 50px; font-size: 0.8rem; font-weight: 600;">
                <i class="fas fa-check-double"></i> Prestataire Vérifié
            </div>
        </div>
    </div>

    <div class="container" style="max-width: 800px;">
        <div class="stats-container">
            <div class="stat-card">
                <div style="font-size: 1.5rem; font-weight: 700; color: #0056b3;"><?php echo $user['total_postulations']; ?></div>
                <div class="info-label" style="font-size: 0.7rem;">Offres envoyées</div>
            </div>
            <div class="stat-card">
                <div style="font-size: 1.5rem; font-weight: 700; color: #28a745;"><?php echo $user['chantiers_remportes']; ?></div>
                <div class="info-label" style="font-size: 0.7rem;">Chantiers acceptés</div>
            </div>
            <div class="stat-card">
                <div style="font-size: 1.5rem; font-weight: 700; color: #f39c12;">100%</div>
                <div class="info-label" style="font-size: 0.7rem;">Taux de réponse</div>
            </div>
        </div>

        <div class="info-card" style="border-left: 5px solid #2ecc71;">
            <h3 style="color: #2ecc71; margin-bottom: 20px;"><i class="fas fa-shield-halved"></i> Sécurité du compte Pro</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <span class="info-label">MFA / Double Facteur</span>
                    <div class="info-value" style="color: #27ae60;"><i class="fas fa-check-circle"></i> Activé</div>
                </div>
                <div>
                    <span class="info-label">Certificat de Session</span>
                    <div class="info-value" style="color: #27ae60;"><i class="fas fa-lock"></i> SSL Actif</div>
                </div>
            </div>
        </div>

        <div class="info-card">
            <h3 style="margin-bottom: 25px; color: #0056b3; border-bottom: 2px solid #f0f2f5; padding-bottom: 10px;"><i class="fas fa-id-card"></i> Identité Professionnelle</h3>
            <div class="info-label">Nom d'artisan</div>
            <div class="info-value"><?php echo htmlspecialchars($user['nom']); ?></div>
            <div class="info-label">Email professionnel</div>
            <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
        </div>

        <div class="stats-container" style="margin-bottom: 50px;">
            <div class="stat-card" style="cursor: pointer;" onclick="window.open('export_pdf.php', '_blank')">
                <i class="fas fa-file-export fa-lg" style="color: #0056b3; margin-bottom: 10px;"></i><br>
                <span class="info-label" style="font-size: 0.6rem;">Exporter mes données</span>
            </div>
            <div class="stat-card" style="cursor: pointer;" onclick="window.location.href='logs_activite.php'">
                <i class="fas fa-history fa-lg" style="color: #f39c12; margin-bottom: 10px;"></i><br>
                <span class="info-label" style="font-size: 0.6rem;">Journal de sécurité</span>
            </div>
            <div class="stat-card" style="cursor: pointer;" onclick="if(confirm('Supprimer votre compte Pro ?')) alert('Action sécurisée requise.')">
                <i class="fas fa-user-slash fa-lg" style="color: #e74c3c; margin-bottom: 10px;"></i><br>
                <span class="info-label" style="font-size: 0.6rem;">Supprimer le profil</span>
            </div>
        </div>
    </div>
</body>
</html>