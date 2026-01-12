<?php
session_start();
require_once '../includes/db.php';

// Sécurité : Seul un PROVIDER peut postuler
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'PROVIDER') {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['id'])) {
    $service_id = $_GET['id'];
    $provider_id = $_SESSION['user_id'];

    try {
        // 1. Vérifier si le bricoleur n'a pas déjà postulé
        $check = $pdo->prepare("SELECT id FROM postulations WHERE service_id = ? AND provider_id = ?");
        $check->execute([$service_id, $provider_id]);

        if ($check->rowCount() == 0) {
            // 2. Insérer la postulation
            $stmt = $pdo->prepare("INSERT INTO postulations (service_id, provider_id) VALUES (?, ?)");
            $stmt->execute([$service_id, $provider_id]);
            header("Location: tous_les_services.php?success=1");
        } else {
            header("Location: tous_les_services.php?error=already_applied");
        }
        exit();
    } catch (PDOException $e) {
        die("Erreur : " . $e->getMessage());
    }
}