<?php
session_start();
require_once '../includes/db.php'; // Connexion sécurisée via .env
require_once '../includes/security_functions.php'; // Pour la vérification CSRF

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. MESURE DE SÉCURITÉ : Vérification du jeton CSRF
    // Si le jeton est absent ou invalide, le script s'arrête immédiatement.
    verify_csrf_token($_POST['csrf_token']);

    // 2. MESURE DE SÉCURITÉ : Contrôle d'accès (RBAC)
    // Seul un utilisateur avec le rôle 'CLIENT' peut publier une annonce.
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'CLIENT') {
        die("Accès non autorisé : Vous devez être un client pour publier.");
    }

    // 3. NETTOYAGE ET VALIDATION DES ENTRÉES
    // On utilise filter_input pour neutraliser les balises HTML (Anti-XSS).
    $titre = filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_SPECIAL_CHARS);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);
    $categorie = filter_input(INPUT_POST, 'categorie', FILTER_SANITIZE_SPECIAL_CHARS);
    
    // Validation du prix (doit être un nombre positif)
    $prix_estime = filter_input(INPUT_POST, 'prix_estime', FILTER_VALIDATE_FLOAT);
    
    $client_id = $_SESSION['user_id'];

    // Vérification que les champs ne sont pas vides
    if (!$titre || !$description || !$prix_estime || $prix_estime <= 0) {
        header("Location: ajouter_service.php?error=invalid_data");
        exit();
    }

    try {
        // 4. PROTECTION CONTRE LES INJECTIONS SQL
        // On utilise des requêtes préparées avec le port 3307.
        $sql = "INSERT INTO services (titre, description, categorie, prix_estime, client_id, statut) 
                VALUES (?, ?, ?, ?, ?, 'OUVERT')";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$titre, $description, $categorie, $prix_estime, $client_id]);

        // Succès : Redirection vers le tableau de bord du client
        header("Location: client_dashboard.php?success=service_added");
        exit();

    } catch (PDOException $e) {
        // SÉCURITÉ : On masque l'erreur réelle pour éviter la fuite d'informations
        die("Une erreur technique est survenue lors de la publication.");
    }
} else {
    // Si quelqu'un tente d'accéder au fichier directement sans formulaire
    header("Location: ajouter_service.php");
    exit();
}