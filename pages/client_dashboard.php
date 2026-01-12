<?php
session_start();
require_once '../includes/db.php'; // Toujours sur le port 3307

// Sécurité : on vérifie si l'utilisateur est connecté et est un CLIENT
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'CLIENT') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$userName = $_SESSION['user_nom'];

try {
    // 1. Récupération des annonces de l'utilisateur
    $stmt = $pdo->prepare("SELECT * FROM services WHERE client_id = ? ORDER BY date_publication DESC");
    $stmt->execute([$user_id]);
    $mes_annonces = $stmt->fetchAll();
    $total_annonces = count($mes_annonces);

    // 2. LOGIQUE DYNAMIQUE : Compter les postulations en attente pour ce client
    $stmt_post = $pdo->prepare("
        SELECT COUNT(p.id) as nb 
        FROM postulations p 
        JOIN services s ON p.service_id = s.id 
        WHERE s.client_id = ? AND p.statut = 'EN_ATTENTE'
    ");
    $stmt_post->execute([$user_id]);
    $res_post = $stmt_post->fetch();
    $total_interesses = $res_post['nb'];

} catch (PDOException $e) {
    die("Erreur technique de base de données.");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"> <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Tableau de Bord - BricoSûr</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .dashboard-layout { display: flex; min-height: 100vh; margin-top: 80px; }
        .sidebar { width: 280px; background: #fff; padding: 30px 20px; box-shadow: 2px 0 15px rgba(0,0,0,0.05); position: fixed; height: 100%; }
        .main-content { flex: 1; margin-left: 280px; padding: 40px; background: #f0f2f5; }
        
        .stat-card { background: #fff; padding: 25px; border-radius: 20px; border-left: 5px solid var(--primary-color); box-shadow: 0 5px 15px rgba(0,0,0,0.05); transition: 0.3s; text-decoration: none; color: inherit; display: block; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
        
        .welcome-box { background: var(--gradient); color: #fff; padding: 40px; border-radius: 20px; box-shadow: 0 10px 20px rgba(0, 86, 179, 0.2); margin-bottom: 40px; }
        
        .annonces-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 25px; }
        .card-annonce { background: #fff; padding: 25px; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #eee; transition: 0.3s; }
        .card-annonce:hover { border-color: var(--primary-color); }
        
        .sidebar-nav li a { display: flex; align-items: center; padding: 12px 15px; border-radius: 10px; color: #555; font-weight: 500; margin-bottom: 5px; transition: 0.3s; }
        .sidebar-nav li a i { margin-right: 12px; width: 20px; text-align: center; }
        .sidebar-nav li a:hover { background: #f8f9fa; color: var(--primary-color); }
        .sidebar-nav li a.active { background: var(--primary-color); color: #fff; }

        /* Style pour le bouton Terminer */
        .btn-finish { background: #27ae60; color: #fff; padding: 8px 12px; border-radius: 8px; text-decoration: none; font-size: 0.8rem; font-weight: 600; display: inline-block; transition: 0.3s; margin-top: 15px; }
        .btn-finish:hover { background: #219150; }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="container nav-container">
            <a href="../index.php" class="logo"><i class="fas fa-hammer"></i> BricoSûr</a>
            <div class="auth-buttons">
                <span style="font-weight: 600;"><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($userName); ?></span>
                <a href="../logout.php" class="btn btn-outline" style="margin-left:20px; border-color:#e74c3c; color:#e74c3c;"><i class="fas fa-power-off"></i></a>
            </div>
        </div>
    </nav>

    <div class="dashboard-layout">
        <aside class="sidebar">
            <ul class="sidebar-nav">
                <li><a href="client_dashboard.php" class="active"><i class="fas fa-th-large"></i> Vue d'ensemble</a></li>
                <li><a href="ajouter_service.php"><i class="fas fa-plus-circle"></i> Nouvelle annonce</a></li>
                <li><a href="gestion_postulations.php"><i class="fas fa-user-check"></i> Propositions reçues</a></li>
                <li><a href="tous_les_services.php"><i class="fas fa-search"></i> Explorer le site</a></li>
                <li><hr style="margin: 15px 0; border: 0; border-top: 1px solid #eee;"></li>
                <li><a href="profil.php"><i class="fas fa-cog"></i> Mon Profil</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="welcome-box">
                <h1 style="font-size: 2rem; margin-bottom: 10px;">Ravi de vous revoir, <?php echo htmlspecialchars($userName); ?> ! 👋</h1>
                <p style="opacity: 0.9; font-size: 1.1rem;">Vous avez <strong><?php echo $total_annonces; ?></strong> chantiers publiés sur BricoSûr.</p>
                <a href="ajouter_service.php" class="btn" style="background: #fff; color: var(--primary-color); margin-top: 20px;">
                    <i class="fas fa-plus"></i> Publier un nouveau besoin
                </a>
            </div>

            <div class="stat-cards" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 25px; margin-bottom: 40px;">
                <div class="stat-card">
                    <i class="fas fa-bullhorn" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                    <h3 style="font-size: 2rem; margin: 10px 0;"><?php echo $total_annonces; ?></h3>
                    <p style="color: #777;">Annonces actives</p>
                </div>
                
                <a href="gestion_postulations.php" class="stat-card" style="border-left-color: #f39c12;">
                    <i class="fas fa-handshake" style="font-size: 1.5rem; color: #f39c12;"></i>
                    <h3 style="font-size: 2rem; margin: 10px 0;"><?php echo $total_interesses; ?></h3>
                    <p style="color: #777;">Bricoleurs intéressés</p>
                    <small style="color: #f39c12; font-weight: 600;">Cliquez pour voir les profils <i class="fas fa-arrow-right"></i></small>
                </a>
            </div>

            <h3 style="margin-bottom: 25px; font-weight: 600;">Mes dernières publications</h3>
            
            <div class="annonces-grid">
                <?php if($total_annonces > 0): ?>
                    <?php foreach($mes_annonces as $annonce): ?>
                        <div class="card-annonce">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                                <span style="background: #fdf2e9; color: #e67e22; padding: 4px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">
                                    <?php echo htmlspecialchars($annonce['categorie']); ?>
                                </span>
                                <span style="font-size: 0.8rem; color: #bbb;"><i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($annonce['date_publication'])); ?></span>
                            </div>
                            <h4 style="margin-bottom: 12px; font-size: 1.1rem; color: var(--primary-color);"><?php echo htmlspecialchars($annonce['titre']); ?></h4>
                            <p style="color: #666; font-size: 0.9rem; margin-bottom: 20px; height: 50px; overflow: hidden;">
                                <?php echo nl2br(htmlspecialchars(substr($annonce['description'], 0, 80))); ?>...
                            </p>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 15px; border-top: 1px solid #f5f5f5;">
                                <span style="font-size: 1.2rem; font-weight: 700; color: #27ae60;"><?php echo number_format($annonce['prix_estime'], 2); ?> €</span>
                                <span style="background: #eef2f7; padding: 4px 8px; border-radius: 5px; font-size: 0.7rem; color: #666; text-transform: uppercase;">
                                    <?php echo $annonce['statut']; ?>
                                </span>
                            </div>

                            <?php if($annonce['statut'] === 'EN COURS'): ?>
                                <a href="terminer_service.php?id=<?php echo $annonce['id']; ?>" 
                                   class="btn-finish" 
                                   onclick="return confirm('Confirmez-vous que le travail est terminé ? Cela clôturera l\'annonce.')">
                                   <i class="fas fa-check-circle"></i> Marquer comme terminé
                                </a>
                            <?php endif; ?>

                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="grid-column: 1 / -1; background: #fff; padding: 60px; border-radius: 20px; text-align: center; border: 2px dashed #eee;">
                        <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" alt="vide" style="width: 80px; margin-bottom: 20px; opacity: 0.5;">
                        <p style="color: #888; font-size: 1.1rem;">Vous n'avez pas encore publié d'annonces.</p>
                        <a href="ajouter_service.php" style="color: var(--primary-color); font-weight: 700; display: inline-block; margin-top: 10px;">Lancer ma première demande</a>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

</body>
</html>