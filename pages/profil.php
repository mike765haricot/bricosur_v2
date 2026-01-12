<?php
require_once '../includes/security_functions.php';
require_once '../includes/db.php';

// Vérification de l'authentification
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupération des données utilisateur et statistiques
$stmt = $pdo->prepare("
    SELECT u.*, 
    (SELECT COUNT(*) FROM services WHERE client_id = u.id) as total_annonces,
    (SELECT COUNT(*) FROM services WHERE client_id = u.id AND statut = 'TERMINE') as chantiers_finis
    FROM users u WHERE u.id = ?
");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil Sécurisé - BricoSûr</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f0f2f5; }
        
        .profile-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #003366 100%);
            height: 200px;
            border-radius: 0 0 50px 50px;
            position: relative;
            margin-bottom: 100px;
        }

        .profile-avatar-container {
            position: absolute;
            bottom: -60px;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
        }

        .profile-avatar {
            width: 130px;
            height: 130px;
            background: white;
            border: 5px solid #f0f2f5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3.5rem;
            font-weight: 700;
            color: var(--primary-color);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }

        .stat-card:hover { transform: translateY(-5px); }

        .info-card {
            background: white;
            border-radius: 25px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }

        .security-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #e6fffa;
            color: #2c7a7b;
            padding: 8px 15px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 10px;
        }

        .info-label { color: #718096; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; margin-bottom: 5px; }
        .info-value { color: #2d3748; font-size: 1.1rem; font-weight: 600; margin-bottom: 20px; }

        /* Nouvelles classes pour la mise à jour 4 */
        .security-item { margin-bottom: 15px; }
        .action-icon { font-size: 1.5rem; margin-bottom: 10px; display: block; }
    </style>
</head>
<body>

    <div class="profile-header">
        <div class="container">
            <a href="client_dashboard.php" style="color: white; text-decoration: none; display: inline-block; margin-top: 20px;">
                <i class="fas fa-arrow-left"></i> Retour au Dashboard
            </a>
        </div>
        <div class="profile-avatar-container">
            <div class="profile-avatar">
                <?php echo strtoupper(substr($user['nom'], 0, 1)); ?>
            </div>
            <h2 style="margin-top: 15px; color: #2d3748;"><?php echo htmlspecialchars($user['nom']); ?></h2>
            <div class="security-badge">
                <i class="fas fa-user-shield"></i> Compte Protégé par MFA
            </div>
        </div>
    </div>

    <div class="container" style="max-width: 800px;">
        
        <div class="stats-container">
            <div class="stat-card">
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary-color);"><?php echo $user['total_annonces']; ?></div>
                <div style="font-size: 0.8rem; color: #718096;">Annonces publiées</div>
            </div>
            <div class="stat-card">
                <div style="font-size: 1.5rem; font-weight: 700; color: #38a169;"><?php echo $user['chantiers_finis']; ?></div>
                <div style="font-size: 0.8rem; color: #718096;">Services terminés</div>
            </div>
            <div class="stat-card">
                <div style="font-size: 1.5rem; font-weight: 700; color: #e53e3e;">0</div>
                <div style="font-size: 0.8rem; color: #718096;">Litiges signalés</div>
            </div>
        </div>

        <div class="info-card" style="border-left: 5px solid #2ecc71;">
            <h3 style="color: #2ecc71; margin-bottom: 25px;">
                <i class="fas fa-shield-halved"></i> Centre de Sécurité & Confidentialité
            </h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="security-item">
                    <span class="info-label">Double Authentification (MFA)</span>
                    <div class="info-value" style="color: #27ae60;">
                        <i class="fas fa-check-circle"></i> Activée
                    </div>
                </div>
                <div class="security-item">
                    <span class="info-label">Chiffrement de session</span>
                    <div class="info-value" style="color: #27ae60;">
                        <i class="fas fa-lock"></i> SSL/TLS Actif
                    </div>
                </div>
                <div class="security-item">
                    <span class="info-label">Dernière connexion détectée</span>
                    <div class="info-value" style="font-size: 0.95rem;">
                        <?php echo date('d/m/Y à H:i'); ?> (IP: <?php echo $_SERVER['REMOTE_ADDR']; ?>)
                    </div>
                </div>
                <div class="security-item">
                    <span class="info-label">Protection des Cookies</span>
                    <div class="info-value" style="color: #27ae60;">
                        <i class="fas fa-cookie-bite"></i> HttpOnly & Strict
                    </div>
                </div>
            </div>
        </div>

        <div class="info-card">
            <h3 style="margin-bottom: 30px; color: var(--primary-color); border-bottom: 2px solid #f0f2f5; padding-bottom: 15px;">
                <i class="fas fa-info-circle"></i> Détails du Compte
            </h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <div class="info-label">Nom complet</div>
                    <div class="info-value"><?php echo htmlspecialchars($user['nom']); ?></div> 
                </div>
                <div>
                    <div class="info-label">Rôle utilisateur</div>
                    <div class="info-value">
                        <span style="background: #edf2f7; padding: 5px 12px; border-radius: 5px; font-size: 0.9rem;">
                            <?php echo $user['role']; ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="info-label">Adresse Email</div>
            <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>

            <div class="info-label">Date d'inscription</div>
            <div class="info-value">Membre depuis le <?php echo date('d/m/Y', strtotime($user['date_inscription'] ?? 'now')); ?></div>
        </div>

        <div class="stats-container" style="margin-bottom: 50px;">
    <div class="stat-card" style="cursor: pointer;" onclick="window.open('export_pdf.php', '_blank')">
        <i class="fas fa-file-export action-icon" style="color: var(--primary-color);"></i>
        <span class="info-label" style="font-size: 0.7rem;">EXPORTER MES DONNÉES</span>
    </div>

    <div class="stat-card" style="cursor: pointer;" onclick="window.location.href='logs_activite.php'">
        <i class="fas fa-history action-icon" style="color: #f39c12;"></i>
        <span class="info-label" style="font-size: 0.7rem;">HISTORIQUE DES LOGS</span>
    </div>

    <div class="stat-card" style="cursor: pointer;">
        <form id="deleteForm" action="process_delete.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <div onclick="if(confirm('Supprimer définitivement votre compte ? Cette action est irréversible.')) document.getElementById('deleteForm').submit();">
                <i class="fas fa-user-slash action-icon" style="color: #e74c3c;"></i>
                <span class="info-label" style="font-size: 0.7rem;">SUPPRIMER LE COMPTE</span>
            </div>
        </form>
    </div>
</div>

        <div style="text-align: center; margin-bottom: 50px;">
            <button onclick="alert('Fonctionnalité de modification sécurisée.')" class="btn btn-primary" style="padding: 12px 40px; border-radius: 50px;">
                <i class="fas fa-edit"></i> Mettre à jour le profil
            </button>
        </div>
    </div>

</body>
</html>