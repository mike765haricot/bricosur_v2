<?php 
// 1. On appelle les fonctions de sécurité et de session en premier
require_once 'includes/security_functions.php'; 
// 2. On inclut la base de données ensuite
require_once 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"> 
    <title>Inscription - BricoSûr v2</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body style="background: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh;">

<div class="card" style="width: 100%; max-width: 450px;">
    <h2 style="color: var(--primary-color); margin-bottom: 20px;">Créer un compte</h2>
    
    <form action="process_register.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

        <input type="text" name="nom" placeholder="Votre Nom complet" required 
               style="width:100%; padding:12px; margin-bottom:15px; border-radius:8px; border:1px solid #ddd;">
        
        <input type="email" name="email" placeholder="votre@email.com" required 
               style="width:100%; padding:12px; margin-bottom:15px; border-radius:8px; border:1px solid #ddd;">
        
        <input type="password" name="password" placeholder="Mot de passe" required 
               style="width:100%; padding:12px; margin-bottom:15px; border-radius:8px; border:1px solid #ddd;">
        
        <label style="display:block; text-align:left; margin-bottom:10px; font-weight:600;">Vous êtes ?</label>
        <select name="role" style="width:100%; padding:12px; margin-bottom:20px; border-radius:8px; border:1px solid #ddd;">
            <option value="CLIENT">Un Client (besoin d'aide)</option>
            <option value="PROVIDER">Un Bricoleur (proposer mes services)</option>
        </select>

        <button type="submit" class="btn btn-primary" style="width:100%;">S'inscrire gratuitement</button>
    </form>
    
    <p style="margin-top:15px;">Déjà inscrit ? <a href="login.php" style="color:var(--primary-color);">Se connecter</a></p>
</div>

</body>
</html>