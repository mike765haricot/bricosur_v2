<?php
session_start();
require_once 'includes/db.php'; // Utilise la connexion sécurisée via .env

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Validation d'entrée et Nettoyage
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($email) || empty($password)) {
        header("Location: login.php?error=1");
        exit();
    }

    try {
        // 2. Requête préparée pour bloquer les injections SQL
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // 3. Vérification du mot de passe haché
        if ($user && password_verify($password, $user['password'])) {
            
            /** * --- SÉCURISATION AVANCÉE DES SESSIONS ---
             * Régénération de l'ID pour prévenir la fixation de session.
             */
            session_regenerate_id(true); 

            /** * --- ÉTAPE 1 : INITIALISATION DU MFA (Exigence 25) ---
             * Au lieu de connecter l'utilisateur immédiatement, on stocke ses infos 
             * dans une session temporaire et on génère un code de sécurité.
             */
            $_SESSION['mfa_user'] = [
                'id'   => $user['id'],
                'nom'  => $user['nom'],
                'role' => $user['role']
            ];
            
            // Génération d'un code à 6 chiffres pour la simulation MFA
            $_SESSION['mfa_code'] = rand(100000, 999999);
            $_SESSION['mfa_pending'] = true;

            /** * NOTE DE DÉMONSTRATION :
             * Dans un système réel, ce code serait envoyé par SMS ou Email.
             * Pour votre projet, nous l'inscrivons dans les logs d'erreurs de PHP.
             */
            error_log("CODE MFA BRICOSUR POUR " . $user['email'] . " : " . $_SESSION['mfa_code']); 

            // Redirection vers la page de vérification
            header("Location: pages/mfa_verify.php");
            exit();

        } else {
            // Échec : Redirection générique pour éviter la fuite d'informations (STRIDE)
            header("Location: login.php?error=1");
            exit();
        }

    } catch (PDOException $e) {
        /**
         * SÉCURITÉ : Protection contre l'Information Disclosure.
         */
        die("Une erreur technique est survenue. Veuillez contacter l'administrateur.");
    }
} else {
    header("Location: login.php");
    exit();
}