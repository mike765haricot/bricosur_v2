<?php
// On inclut la sécurité pour gérer la session du menu (Bonjour, DAYAWA)
require_once '../includes/security_functions.php'; 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À propos de nous - BricoSûr</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa; /* Fond gris très clair et moderne */
        }
        /* Style de la bannière Hero */
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, #004494 100%); /* Dégradé avec votre bleu */
            color: white;
            padding: 80px 0;
            text-align: center;
            margin-bottom: -50px; /* Pour faire chevaucher les cartes */
        }
        .hero-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
        }
        .hero-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }
        /* Style des cartes de contenu */
        .content-card {
            background: #fff;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            margin-bottom: 30px;
            position: relative; /* Pour le chevauchement */
            z-index: 10;
        }
        .section-title {
            color: var(--primary-color);
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .feature-list {
            list-style: none;
            padding: 0;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); /* Grille responsive */
            gap: 20px;
        }
        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }
        .feature-icon {
            background: rgba(0, 86, 179, 0.1); /* Fond bleu clair transparent */
            color: var(--primary-color);
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        /* Bouton de retour stylisé */
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 50px;
            box-shadow: 0 5px 15px rgba(0, 86, 179, 0.2);
            transition: transform 0.2s;
        }
        .btn-back:hover {
            transform: translateY(-3px);
        }
    </style>
</head>
<body>

    <header style="background: #fff; padding: 15px 0; box-shadow: 0 2px 15px rgba(0,0,0,0.05);">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <a href="../index.php" class="logo">
                <img src="../assets/img/logo.png" alt="BricoSûr" style="height: 45px;">
            </a>
            <nav style="display: flex; align-items: center; gap: 25px;">
                <a href="../index.php" style="text-decoration: none; color: #555; font-weight: 600;">Accueil</a>
                <a href="../index.php#services" style="text-decoration: none; color: #555; font-weight: 600;">Explorer les Services</a>
                <?php if(isset($_SESSION['user_role'])): ?>
                    <?php 
                        $dashboard_link = ($_SESSION['user_role'] === 'CLIENT') ? 'client_dashboard.php' : 
                                         (($_SESSION['user_role'] === 'PROVIDER') ? 'provider_dashboard.php' : 'admin_dashboard.php');
                    ?>
                    <a href="<?php echo $dashboard_link; ?>" style="text-decoration: none; color: #555; font-weight: 600;">Mon Dashboard</a>
                <?php endif; ?>
                <a href="a_propos.php" style="text-decoration: none; color: var(--primary-color); font-weight: 700;">À propos</a>
            </nav>
            <div style="display: flex; align-items: center; gap: 20px;">
                <?php if(isset($_SESSION['user_nom'])): ?>
                    <span style="font-weight: 600; color: #333;">Bonjour, <strong><?php echo htmlspecialchars($_SESSION['user_nom']); ?></strong></span>
                    <a href="../logout.php" class="btn btn-outline-primary" style="border: 2px solid var(--primary-color); padding: 8px 20px; border-radius: 50px; text-decoration: none; font-weight: 600;">Déconnexion</a>
                <?php else: ?>
                    <a href="../login.php" class="btn btn-primary" style="padding: 10px 25px; border-radius: 50px; text-decoration: none; font-weight: 600;">Connexion</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <section class="hero-section">
        <div class="container">
            <h1 class="hero-title">Plus qu'une plateforme, votre partenaire de confiance.</h1>
            <p class="hero-subtitle">Découvrez comment BricoSûr redéfinit la mise en relation pour vos travaux, en plaçant la sécurité et la simplicité au cœur de l'expérience.</p>
        </div>
    </section>

    <div class="container" style="max-width: 900px; padding-bottom: 50px;">
        
        <div class="content-card">
            <h2 class="section-title">
                <i class="fas fa-rocket"></i> Notre Mission
            </h2>
            <p style="font-size: 1.05rem; line-height: 1.7; color: #555;">
                BricoSûr est née d'une ambition claire : simplifier la vie de ceux qui ont des projets de travaux tout en offrant de nouvelles opportunités aux artisans talentueux. Nous croyons que la technologie doit servir à créer des liens de <strong>confiance</strong>, pas à les complexifier. C'est pourquoi nous avons bâti une plateforme robuste, intuitive et, surtout, incroyablement sécurisée.
            </p>
        </div>

        <div class="content-card">
            <h2 class="section-title">
                <i class="fas fa-shield-alt"></i> Votre sécurité, notre priorité absolue
            </h2>
            <p style="margin-bottom: 25px; color: #555;">
                Dans un monde numérique, la protection de vos données n'est pas une option. Chez BricoSûr, nous appliquons les standards les plus stricts du <strong>développement sécurisé (SDLC)</strong> pour vous offrir une tranquillité d'esprit totale.
            </p>
            <ul class="feature-list">
                <li class="feature-item">
                    <div class="feature-icon"><i class="fas fa-lock"></i></div>
                    <div>
                        <strong>Authentification Renforcée (MFA)</strong>
                        <p style="font-size: 0.9rem; color: #666; margin: 5px 0 0 0;">Un double verrouillage pour empêcher tout accès non autorisé à votre compte.</p>
                    </div>
                </li>
                <li class="feature-item">
                    <div class="feature-icon"><i class="fas fa-key"></i></div>
                    <div>
                        <strong>Navigation 100% Chiffrée</strong>
                        <p style="font-size: 0.9rem; color: #666; margin: 5px 0 0 0;">Vos données voyagent dans un tunnel HTTPS sécurisé, à l'abri des regards indiscrets.</p>
                    </div>
                </li>
                <li class="feature-item">
                    <div class="feature-icon"><i class="fas fa-user-shield"></i></div>
                    <div>
                        <strong>Contrôle d'Accès Strict (RBAC)</strong>
                        <p style="font-size: 0.9rem; color: #666; margin: 5px 0 0 0;">Chaque utilisateur ne voit et ne modifie que les informations qui le concernent.</p>
                    </div>
                </li>
                <li class="feature-item">
                    <div class="feature-icon"><i class="fas fa-bug"></i></div>
                    <div>
                        <strong>Protection Anti-Injections</strong>
                        <p style="font-size: 0.9rem; color: #666; margin: 5px 0 0 0;">Notre code est blindé contre les attaques SQL et XSS pour protéger vos saisies.</p>
                    </div>
                </li>
            </ul>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <a href="../index.php" class="btn btn-primary btn-back">
                <i class="fas fa-home"></i> Retour à l'accueil
            </a>
        </div>
    </div>

</body>
</html>