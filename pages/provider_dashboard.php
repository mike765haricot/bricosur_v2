<?php
require_once '../includes/security_functions.php';
require_once '../includes/db.php';

// Vérification RBAC : Strictement réservé au prestataire
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'PROVIDER') {
    header("Location: ../login.php"); exit();
}

$user_id = $_SESSION['user_id'];
$user_nom = $_SESSION['user_nom']; // Pour l'affichage "Stakyra"

// 1. Récupération des statistiques dynamiques
$stmt = $pdo->prepare("
    SELECT 
        (SELECT COUNT(*) FROM postulations WHERE provider_id = ?) as total,
        (SELECT COUNT(*) FROM postulations WHERE provider_id = ? AND statut = 'EN_ATTENTE') as en_attente,
        (SELECT COUNT(*) FROM postulations WHERE provider_id = ? AND statut = 'ACCEPTE') as gagnes
    FROM users LIMIT 1
");
$stmt->execute([$user_id, $user_id, $user_id]);
$stats = $stmt->fetch();

// 2. Récupération des 3 dernières candidatures pour l'affichage rapide
$stmt_recent = $pdo->prepare("
    SELECT p.*, s.titre 
    FROM postulations p 
    JOIN services s ON p.service_id = s.id 
    WHERE p.provider_id = ? 
    ORDER BY p.date_postulation DESC LIMIT 3
");
$stmt_recent->execute([$user_id]);
$recent_apps = $stmt_recent->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vue d'ensemble - BricoSûr Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8fafc; margin: 0; }
        
        /* --- STYLE DE L'EN-TÊTE RÉINTÉGRÉ --- */
        .top-header {
            background: white;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-sizing: border-box;
        }
        .header-user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #1e293b;
            font-weight: 500;
        }
        .logout-circle-btn {
            width: 45px;
            height: 45px;
            border: 2px solid #ff4d4d;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ff4d4d;
            text-decoration: none;
            transition: all 0.3s;
        }
        .logout-circle-btn:hover {
            background: #ff4d4d;
            color: white;
            transform: rotate(90deg);
        }

        /* --- MISE EN PAGE DU DASHBOARD --- */
        .dashboard-layout { display: flex; margin-top: 80px; }
        
        .sidebar { 
            width: 280px; 
            background: white; 
            height: calc(100vh - 80px); 
            position: fixed; 
            padding: 30px 20px;
            border-right: 1px solid #e2e8f0;
        }
        .sidebar-link { 
            display: flex; 
            align-items: center; 
            gap: 15px; 
            padding: 15px; 
            text-decoration: none; 
            color: #64748b; 
            border-radius: 12px; 
            margin-bottom: 5px; 
            font-weight: 500; 
            transition: 0.3s; 
        }
        .sidebar-link.active { background: var(--primary-color); color: white; }
        .sidebar-link:hover:not(.active) { background: #f1f5f9; color: var(--primary-color); }

        .main-content { margin-left: 280px; padding: 40px; width: 100%; }
        
        .welcome-banner { 
            background: linear-gradient(135deg, var(--primary-color) 0%, #003366 100%); 
            color: white; 
            padding: 40px; 
            border-radius: 30px; 
            margin-bottom: 35px; 
        }
        
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px; margin-bottom: 35px; }
        .stat-card { background: white; padding: 25px; border-radius: 20px; border: 1px solid #edf2f7; text-align: center; }
        .stat-val { font-size: 1.8rem; font-weight: 700; color: #1e293b; }
        
        .recent-activity { background: white; border-radius: 25px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .activity-item { display: flex; justify-content: space-between; align-items: center; padding: 15px 0; border-bottom: 1px solid #f1f5f9; }
        .status-badge { padding: 4px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
        .status-badge.accepted { background: #dcfce7; color: #166534; }
        .status-badge.pending { background: #fef3c7; color: #92400e; }
    </style>
</head>
<body>

    <header class="top-header">
        <div class="logo">
            <img src="../assets/img/logo.png" alt="BricoSûr" style="height: 40px;">
        </div>
        
        <div style="display: flex; align-items: center; gap: 30px;">
            <div class="header-user-info">
                <i class="fas fa-user-tie"></i> 
                <span>Mode Pro : <strong><?php echo htmlspecialchars($user_nom); ?></strong></span>
            </div>
            
            <a href="../logout.php" class="logout-circle-btn" title="Déconnexion">
                <i class="fas fa-power-off"></i>
            </a>
        </div>
    </header>

    <div class="dashboard-layout">
        <aside class="sidebar">
            <nav>
                <a href="provider_dashboard.php" class="sidebar-link active"><i class="fas fa-th-large"></i> Vue d'ensemble</a>
                <a href="tous_les_services.php" class="sidebar-link"><i class="fas fa-search"></i> Trouver du travail</a>
                <a href="provider_history.php" class="sidebar-link"><i class="fas fa-history"></i> Historique</a>
                <hr style="margin: 20px 0; border: 0; border-top: 1px solid #f1f5f9;">
                <a href="provider_profile.php" class="sidebar-link"><i class="fas fa-user-circle"></i> Mon Profil</a>
            </nav>
        </aside>

        <main class="main-content">
            <div class="welcome-banner">
                <h1 style="margin: 0; font-size: 2.2rem;">Ravi de vous revoir, <?php echo htmlspecialchars($user_nom); ?> ! 👋</h1>
                <p style="opacity: 0.9; margin-top: 10px;">Gérez vos interventions et sécurisez vos revenus sur BricoSûr.</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-val"><?php echo $stats['total']; ?></div>
                    <div style="color: #64748b; font-size: 0.9rem;">Candidatures</div>
                </div>
                <div class="stat-card">
                    <div class="stat-val" style="color: #f59e0b;"><?php echo $stats['en_attente']; ?></div>
                    <div style="color: #64748b; font-size: 0.9rem;">En attente</div>
                </div>
                <div class="stat-card">
                    <div class="stat-val" style="color: #10b981;"><?php echo $stats['gagnes']; ?></div>
                    <div style="color: #64748b; font-size: 0.9rem;">Chantiers gagnés</div>
                </div>
            </div>

            <div class="recent-activity">
                <h3 style="margin-bottom: 25px; color: #1e293b;"><i class="fas fa-bolt" style="color: #f59e0b;"></i> Suivi de mes candidatures</h3>
                <?php if(count($recent_apps) > 0): ?>
                    <?php foreach($recent_apps as $app): ?>
                        <div class="activity-item">
                            <div>
                                <div style="font-weight: 600; color: #1e293b;"><?php echo htmlspecialchars($app['titre']); ?></div>
                                <div style="font-size: 0.8rem; color: #94a3b8;">Envoyé le <?php echo date('d/m/Y', strtotime($app['date_postulation'])); ?></div>
                            </div>
                            <span class="status-badge <?php echo ($app['statut'] == 'ACCEPTE') ? 'accepted' : 'pending'; ?>">
                                <?php echo $app['statut']; ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center; color: #94a3b8; padding: 20px;">Aucune candidature récente.</p>
                <?php endif; ?>
            </div>
            
            <div style="margin-top: 30px; background: #f0fdf4; border: 1px solid #bbf7d0; padding: 15px; border-radius: 15px; display: flex; align-items: center; gap: 15px;">
                <i class="fas fa-shield-check" style="color: #16a34a;"></i>
                <span style="color: #166534; font-size: 0.85rem;">Session protégée par audit de sécurité en temps réel.</span>
            </div>
        </main>
    </div>

</body>
</html>