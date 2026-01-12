<?php
/**
 * --- SÉCURISATION ET SESSION (Exigence 25) ---
 */
require_once '../includes/security_functions.php'; // Gère le session_start sécurisé
require_once '../includes/db.php'; // Connexion PDO Port 3307

// Vérification du rôle : Seul un CLIENT peut gérer les postulations
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'CLIENT') {
    header("Location: ../login.php"); 
    exit();
}

$user_id = $_SESSION['user_id'];

/**
 * RÉCUPÉRATION DES PROPOSITIONS (Requête préparée - Exigence 29)
 * On récupère les détails des bricoleurs ayant postulé aux annonces de ce client.
 */
$query = "SELECT p.*, s.titre as service_titre, u.nom as provider_nom 
          FROM postulations p 
          JOIN services s ON p.service_id = s.id 
          JOIN users u ON p.provider_id = u.id 
          WHERE s.client_id = ? 
          ORDER BY p.date_postulation DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$postulations = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Propositions - BricoSûr</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; margin: 0; }
        
        /* Bannière Hero */
        .hero-banner {
            background: linear-gradient(135deg, var(--primary-color) 0%, #003366 100%);
            color: white;
            padding: 60px 0 100px 0;
            text-align: center;
            border-radius: 0 0 50px 50px;
        }

        .container-custom { max-width: 1000px; margin: -60px auto 50px auto; padding: 0 20px; }

        /* Cartes de postulation */
        .proposal-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform 0.3s ease;
            border: 1px solid #eee;
        }

        .proposal-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.1); }

        .provider-profile { display: flex; align-items: center; gap: 20px; }

        .avatar {
            width: 60px; height: 60px;
            background: var(--primary-color);
            color: white; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; font-weight: 700;
        }

        .service-tag {
            background: rgba(0, 86, 179, 0.1);
            color: var(--primary-color);
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: 5px;
            display: inline-block;
        }

        /* Actions */
        .action-group { display: flex; gap: 12px; }

        .btn-chat { background: #007bff; color: white; }
        .btn-chat:hover { background: #0056b3; }

        .btn-approve { background: #28a745; color: white; }
        .btn-approve:hover { background: #218838; }

        .btn-decline { background: #dc3545; color: white; }
        .btn-decline:hover { background: #c82333; }

        .status-badge {
            padding: 8px 15px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            background: #e9ecef;
            color: #495057;
        }
    </style>
</head>
<body>

    <div class="hero-banner">
        <div class="container">
            <h1 style="margin: 0; font-size: 2.5rem;"><i class="fas fa-handshake"></i> Gestion des Propositions</h1>
            <p style="opacity: 0.8;">Bonjour <strong><?php echo htmlspecialchars($_SESSION['user_nom']); ?></strong>, choisissez le meilleur expert pour vos projets.</p>
        </div>
    </div>

    <div class="container-custom">
        <?php if (count($postulations) > 0): ?>
            <?php foreach($postulations as $p): ?>
                <div class="proposal-card">
                    <div class="provider-profile">
                        <div class="avatar">
                            <?php echo strtoupper(substr($p['provider_nom'], 0, 1)); ?>
                        </div>
                        <div>
                            <h3 style="margin: 0; color: #333;"><?php echo htmlspecialchars($p['provider_nom']); ?></h3>
                            <span class="service-tag"><?php echo htmlspecialchars($p['service_titre']); ?></span>
                            <div style="margin-top: 5px; font-size: 0.8rem; color: #999;">
                                <i class="far fa-calendar-alt"></i> Reçue le <?php echo date('d/m/Y', strtotime($p['date_postulation'])); ?>
                            </div>
                        </div>
                    </div>

                    <div class="action-group">
                        <?php if($p['statut'] === 'EN_ATTENTE'): ?>
                            <a href="chat.php?dest=<?php echo $p['provider_id']; ?>&service=<?php echo $p['service_id']; ?>" class="btn btn-chat" title="Discuter">
                                <i class="fas fa-comments"></i> <span>Discuter</span>
                            </a>
                            
                            <a href="accepter_postulation.php?id=<?php echo $p['id']; ?>" class="btn btn-approve">
                                <i class="fas fa-check"></i> <span>Accepter</span>
                            </a>
                            
                            <a href="reponse_postulation.php?id=<?php echo $p['id']; ?>&action=REFUSE" class="btn btn-decline" onclick="return confirm('Refuser cette proposition ?')">
                                <i class="fas fa-times"></i>
                            </a>
                        <?php else: ?>
                            <a href="chat.php?dest=<?php echo $p['provider_id']; ?>&service=<?php echo $p['service_id']; ?>" style="margin-right:15px; color:var(--primary-color); text-decoration:none; font-weight:600;">
                                <i class="fas fa-external-link-alt"></i> Ouvrir le chat
                            </a>
                            <span class="status-badge"><?php echo htmlspecialchars($p['statut']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card" style="text-align: center; padding: 60px;">
                <i class="fas fa-folder-open fa-4x" style="color: #ddd; margin-bottom: 20px;"></i>
                <h3>Aucune proposition pour le moment</h3>
                <p style="color: #777;">Dès qu'un bricoleur postule à l'une de vos annonces, elle apparaîtra ici.</p>
                <a href="ajouter_service.php" class="btn btn-primary" style="margin-top: 15px;">Publier une annonce</a>
            </div>
        <?php endif; ?>

        <div style="text-align: center; margin-top: 30px;">
            <a href="client_dashboard.php" style="text-decoration: none; color: #666; font-weight: 600;">
                <i class="fas fa-arrow-left"></i> Retour au tableau de bord
            </a>
        </div>
    </div>

</body>
</html>