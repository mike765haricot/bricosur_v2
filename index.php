<?php 
// Utilisation de la session sécurisée
session_start(); 
require_once 'includes/db.php'; 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BricoSûr v2 - Votre partenaire confiance</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Style spécifique pour intégrer l'image Hero */
        .hero {
            position: relative;
            background: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.65)), 
                        url('assets/img/hero-bg.png') no-repeat center center/cover;
            height: 80vh;
            display: flex;
            align-items: center;
            color: white;
            text-align: center;
        }

        .highlight { color: #fbc02d; font-weight: 700; }

        .feature-section { padding: 80px 0; background: #fff; text-align: center; }
        .feature-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; margin-top: 40px; }
        .feature-card { padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); transition: 0.3s; }
        .feature-card:hover { transform: translateY(-10px); }
        .feature-card i { font-size: 2.5rem; color: var(--primary-color); margin-bottom: 20px; }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="container nav-container">
            <a href="index.php" class="logo" style="text-decoration: none;">
                <i class="fas fa-hammer"></i> BricoSûr
            </a>

            <ul class="nav-links">
                <li><a href="index.php" class="active">Accueil</a></li>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="pages/tous_les_services.php">Explorer les Services</a></li>
                    
                    <?php if($_SESSION['user_role'] === 'CLIENT'): ?>
                        <li><a href="pages/client_dashboard.php">Mon Dashboard</a></li>
                    <?php else: ?>
                        <li><a href="pages/provider_dashboard.php">Mon Espace Pro</a></li>
                    <?php endif; ?>
                <?php endif; ?>
                
                <li><a href="pages/a_propos.php">À propos</a></li>
            </ul>
            
            <div class="auth-buttons">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <span style="margin-right: 15px;">Bonjour, <strong><?php echo htmlspecialchars($_SESSION['user_nom']); ?></strong></span>
                    <a href="logout.php" class="btn btn-outline" style="border-color: #e74c3c; color: #e74c3c; border-radius: 50px;">
                        <i class="fas fa-power-off"></i>
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline" style="border-radius: 50px;"><i class="fas fa-sign-in-alt"></i> Connexion</a>
                    <a href="register.php" class="btn btn-primary" style="border-radius: 50px;"><i class="fas fa-user-plus"></i> Inscription</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <header class="hero">
        <div class="container hero-content" style="z-index: 2;">
            <h1 style="font-size: 3.5rem; line-height: 1.2;">Trouvez le bricoleur idéal <br>en toute <span class="highlight">confiance</span>.</h1>
            <p style="font-size: 1.2rem; max-width: 700px; margin: 20px auto 40px auto; opacity: 0.9;">
                La plateforme sécurisée qui connecte vos projets aux meilleurs experts locaux.
            </p>
            <div class="hero-btns">
                <?php if(!isset($_SESSION['user_id'])): ?>
                    <a href="register.php" class="btn btn-lg btn-primary" style="padding: 15px 40px; border-radius: 50px;">Commencer maintenant <i class="fas fa-arrow-right"></i></a>
                <?php else: ?>
                    <a href="pages/tous_les_services.php" class="btn btn-lg btn-primary" style="padding: 15px 40px; border-radius: 50px;">Voir les chantiers <i class="fas fa-search"></i></a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <section class="feature-section container">
        <h2 style="font-size: 2.5rem; color: #333;">Une plateforme pensée pour votre sécurité</h2>
        <p style="color: #777;">Nous appliquons les standards de protection les plus élevés.</p>
        
        <div class="feature-grid">
            <div class="feature-card">
                <i class="fas fa-user-shield"></i>
                <h3>Identité Vérifiée</h3>
                <p>Chaque profil est soumis à un contrôle d'accès strict (RBAC) pour garantir votre tranquillité.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-lock"></i>
                <h3>Transactions Sûres</h3>
                <p>Vos échanges et données sont protégés par un chiffrement SSL de bout en bout.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-user-check"></i>
                <h3>Audit Constant</h3>
                <p>Nous enregistrons chaque action critique pour assurer une transparence totale.</p>
            </div>
        </div>
    </section>

    <footer style="background: #2c3e50; color: #fff; padding: 40px 0; text-align: center;">
        <div class="container">
            <p>&copy; <?php echo date('2026'); ?> BricoSûr v2. Tous droits réservés.</p>
            <div style="margin-top: 15px; opacity: 0.6; font-size: 0.8rem;">
                Navigation chiffrée SSL | Protection Anti-XSS & SQLi active
            </div>
        </div>
    </footer>

</body>
</html>