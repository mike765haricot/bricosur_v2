<?php
session_start();
require_once 'includes/db.php'; // Utilise la connexion sécurisée via .env

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. VALIDATION ET NETTOYAGE DES ENTRÉES 
    // filter_input élimine les caractères dangereux dès la réception
    $nom   = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $pass  = $_POST['password'];
    $role  = $_POST['role'];

    // Vérification des rôles autorisés (Contrôle d'accès RBAC) [cite: 17, 25]
    $roles_autorises = ['CLIENT', 'PROVIDER'];
    if (!in_array($role, $roles_autorises)) {
        die("Erreur : Rôle non autorisé.");
    }

    if (!$email) {
        header("Location: register.php?error=invalid_email");
        exit();
    }

    // 2. SÉCURITÉ DU MOT DE PASSE [cite: 25, 46]
    if (strlen($pass) < 8) {
        header("Location: register.php?error=password_too_short");
        exit();
    }
    // Hachage sécurisé (stockage sécurisé exigé par le projet) 
    $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

    try {
        // 3. PROTECTION CONTRE LES INJECTIONS (Requêtes préparées) 
        $stmt = $pdo->prepare("INSERT INTO users (nom, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nom, $email, $hashed_password, $role]);

        header("Location: login.php?success=registered");
    } catch (PDOException $e) {
        // Protection contre la fuite d'informations [cite: 28]
        die("Erreur lors de l'inscription sécurisée.");
    }
}
