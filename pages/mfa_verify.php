<?php
require_once '../includes/security_functions.php'; // Gère la session et le CSRF

if (!isset($_SESSION['mfa_pending'])) {
    header("Location: ../login.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token']); // Protection Tampering

    $code_saisi = filter_input(INPUT_POST, 'code', FILTER_SANITIZE_NUMBER_INT);

    if ($code_saisi == $_SESSION['mfa_code']) {
        // Succès : On transfère les infos vers la session finale
        $_SESSION['user_id'] = $_SESSION['mfa_user']['id'];
        $_SESSION['user_nom'] = $_SESSION['mfa_user']['nom'];
        $_SESSION['user_role'] = $_SESSION['mfa_user']['role'];

        // Nettoyage MFA
        unset($_SESSION['mfa_user'], $_SESSION['mfa_code'], $_SESSION['mfa_pending']);

        // Redirection vers le bon dashboard
        $role = $_SESSION['user_role'];
        if ($role === 'ADMIN') header("Location: admin_dashboard.php");
        elseif ($role === 'CLIENT') header("Location: client_dashboard.php");
        else header("Location: provider_dashboard.php");
        exit();
    } else {
        $error = "Code incorrect. Tentative enregistrée.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vérification de sécurité - BricoSûr</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body style="background: #f0f2f5; display: flex; align-items: center; justify-content: center; height: 100vh;">
    <div style="background: #fff; padding: 40px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 100%; max-width: 400px;">
        <h2 style="text-align: center; color: #0056b3; margin-bottom: 20px;">Vérification MFA</h2>
        <p style="text-align: center; font-size: 0.9rem; color: #666; margin-bottom: 25px;">
            Pour votre sécurité, entrez le code de vérification à 6 chiffres. 
            <br><small>(Consultez les logs du serveur pour le code de test)</small>
        </p>

        <?php if($error): ?>
            <div style="color: #e74c3c; background: #fdf2f2; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 0.85rem; text-align: center;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="text" name="code" maxlength="6" placeholder="000000" required 
                   style="width: 100%; padding: 15px; font-size: 1.5rem; text-align: center; letter-spacing: 10px; border: 2px solid #ddd; border-radius: 10px; margin-bottom: 20px;">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Vérifier l'identité</button>
        </form>
    </div>
</body>
</html>