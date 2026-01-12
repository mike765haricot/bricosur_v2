<?php 
// 1. On inclut la sécurité qui gère la session
require_once 'includes/security_functions.php';
// 2. On inclut la base de données après
require_once 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"> <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - BricoSûr v2</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .login-container { height: 100vh; display: flex; align-items: center; justify-content: center; background: #f0f2f5; }
        .login-card { width: 100%; max-width: 400px; }
        .form-group { text-align: left; margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; }
        input[type="email"], input[type="password"] {
            width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit; font-size: 1rem;
        }
        .error-msg { color: #d93025; background: #f8d7da; padding: 10px; border-radius: 5px; margin-bottom: 20px; font-size: 0.9rem; }
    </style>
</head>
<body>

<div class="login-container">
    <div class="card login-card">
        <h2 style="margin-bottom: 30px; color: var(--primary-color);">Connexion</h2>

        <?php if(isset($_GET['error'])): ?>
            <div class="error-msg">Email ou mot de passe incorrect.</div>
        <?php endif; ?>

        <form action="process_login.php" method="POST">
            
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <div class="form-group">
                <label>Adresse Email</label>
                <input type="email" name="email" placeholder="votre@email.com" required>
            </div>
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">Se connecter</button>
        </form>
        
        <p style="margin-top: 20px; font-size: 0.9rem;">
            Pas encore de compte ? <a href="register.php" style="color: var(--primary-color); font-weight: 600;">S'inscrire</a>
        </p>
    </div>
</div>

</body>
</html>