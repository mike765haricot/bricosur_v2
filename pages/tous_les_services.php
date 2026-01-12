<?php
/**
 * SÉCURITÉ ET CONNEXION
 */
require_once '../includes/security_functions.php'; 
require_once '../includes/db.php'; // Connexion au port 3307

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

try {
    // Requête préparée pour récupérer les services ouverts (Exigence 29)
    $query = "SELECT s.*, u.nom as client_nom 
              FROM services s 
              JOIN users u ON s.client_id = u.id 
              WHERE s.statut = 'OUVERT' 
              ORDER BY s.date_publication DESC";
    $stmt = $pdo->query($query);
    $services = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erreur de base de données.");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travaux Disponibles - BricoSûr</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8fafc; margin: 0; }
        
        /* Hero Section */
        .hero-banner {
            background: linear-gradient(135deg, var(--primary-color) 0%, #003366 100%);
            color: white;
            padding: 80px 0 120px 0;
            text-align: center;
            border-radius: 0 0 50px 50px;
        }

        .container-custom { max-width: 1200px; margin: -60px auto 50px auto; padding: 0 20px; }

        /* Grille d'annonces */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
        }

        /* Cartes modernes */
        .service-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            border: 1px solid #edf2f7;
            display: flex;
            flex-direction: column;
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        .card-body { padding: 25px; flex-grow: 1; }

        .category-badge {
            background: rgba(0, 86, 179, 0.1);
            color: var(--primary-color);
            padding: 6px 15px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .price-tag {
            font-size: 1.4rem;
            font-weight: 700;
            color: #2ecc71;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #2d3748;
            margin: 15px 0 10px 0;
        }

        .card-desc {
            color: #718096;
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .card-footer {
            background: #f8fafc;
            padding: 15px 25px;
            border-top: 1px solid #edf2f7;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .client-info { display: flex; align-items: center; gap: 8px; font-size: 0.85rem; color: #4a5568; }
        
        /* Alertes */
        .alert {
            padding: 15px 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .alert-success { background: #c6f6d5; color: #22543d; }
        .alert-warning { background: #feebc8; color: #744210; }
    </style>
</head>
<body>

    <header style="background: white; padding: 15px 0; position: fixed; width: 100%; top: 0; z-index: 1000; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <a href="../index.php" class="logo" style="text-decoration: none; color: var(--primary-color); font-weight: 800; font-size: 1.5rem;">
                <i class="fas fa-hammer"></i> BricoSûr
            </a>
            <div class="nav-links">
                <?php if($_SESSION['user_role'] === 'CLIENT'): ?>
                    <a href="client_dashboard.php" class="btn btn-primary" style="border-radius: 50px; padding: 10px 25px;">Mon Tableau de Bord</a>
                <?php else: ?>
                    <a href="provider_dashboard.php" class="btn btn-primary" style="border-radius: 50px; padding: 10px 25px;">Mon Espace Pro</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <section class="hero-banner">
        <div class="container">
            <h1 style="font-size: 3rem; margin: 0;">🛠️ Travaux Disponibles</h1>
            <p style="font-size: 1.1rem; opacity: 0.9;">Trouvez votre prochain chantier et proposez votre expertise en toute sécurité.</p>
        </div>
    </section>

    <div class="container-custom">
        
        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> Félicitations ! Votre postulation a été envoyée avec succès.
            </div>
        <?php endif; ?>
        <?php if(isset($_GET['error']) && $_GET['error'] == 'already_applied'): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> Attention : Vous avez déjà postulé à cette offre.
            </div>
        <?php endif; ?>

        <div class="services-grid">
            <?php if (count($services) > 0): ?>
                <?php foreach ($services as $s): ?>
                    <div class="service-card">
                        <div class="card-body">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span class="category-badge"><?php echo htmlspecialchars($s['categorie']); ?></span>
                                <span class="price-tag"><?php echo number_format($s['prix_estime'], 0, ',', ' '); ?> €</span>
                            </div>
                            
                            <h3 class="card-title"><?php echo htmlspecialchars($s['titre']); ?></h3>
                            
                            <p class="card-desc">
                                <?php echo nl2br(htmlspecialchars(substr($s['description'], 0, 150))); ?>...
                            </p>
                        </div>

                        <div class="card-footer">
                            <div class="client-info">
                                <i class="fas fa-user-circle"></i>
                                <span>Par <strong><?php echo htmlspecialchars($s['client_nom']); ?></strong></span>
                            </div>
                            
                            <?php if($_SESSION['user_role'] === 'PROVIDER'): ?>
                                <a href="postuler.php?id=<?php echo $s['id']; ?>" class="btn btn-primary" 
                                   style="padding: 10px 20px; border-radius: 12px; font-size: 0.85rem;" 
                                   onclick="return confirm('Envoyer votre candidature pour ce chantier ?')">
                                    <i class="fas fa-paper-plane"></i> Postuler
                                </a>
                            <?php else: ?>
                                <span style="font-size: 0.7rem; color: #a0aec0; font-style: italic;">Accès Pro</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align: center; background: white; padding: 80px; border-radius: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
                    <i class="fas fa-search" style="font-size: 4rem; color: #edf2f7; margin-bottom: 25px;"></i>
                    <h2 style="color: #2d3748;">Aucun chantier disponible</h2>
                    <p style="color: #718096;">Revenez plus tard pour découvrir de nouvelles opportunités.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>