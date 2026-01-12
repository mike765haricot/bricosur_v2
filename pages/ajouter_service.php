<?php
// On inclut les fichiers nécessaires pour la sécurité et la base de données
// Assurez-vous que les chemins sont corrects par rapport à l'emplacement de ce fichier
require_once '../includes/security_functions.php';
require_once '../includes/db.php';

// Vérification de l'authentification et du rôle
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'CLIENT') {
    // Redirection si l'utilisateur n'est pas connecté ou n'est pas un client
    header("Location: ../login.php");
    exit();
}

// Traitement du formulaire lors de la soumission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification du jeton CSRF
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("Erreur de sécurité : Jeton CSRF invalide.");
    }

    // Récupération et nettoyage des données
    $titre = trim($_POST['titre']);
    $categorie = trim($_POST['categorie']);
    $description = trim($_POST['description']);
    $budget = floatval($_POST['budget']);

    // Validation des données (à améliorer selon vos besoins)
    if (empty($titre) || empty($categorie) || empty($description) || $budget <= 0) {
        $error_message = "Veuillez remplir tous les champs correctement.";
    } else {
        try {
            // Requête préparée pour insérer la nouvelle annonce
            $stmt = $pdo->prepare("INSERT INTO annonces (client_id, titre, categorie, description, budget, date_creation, statut) VALUES (?, ?, ?, ?, ?, NOW(), 'ouverte')");
            $stmt->execute([$_SESSION['user_id'], $titre, $categorie, $description, $budget]);

            // Redirection avec message de succès
            header("Location: client_dashboard.php?success=Annonce publiée avec succès!");
            exit();
        } catch (PDOException $e) {
            $error_message = "Erreur lors de la publication de l'annonce : " . $e->getMessage();
        }
    }
}
// Utilisation de requêtes préparées pour bloquer l'injection SQL
try {
    $stmt = $pdo->prepare("INSERT INTO services (client_id, titre, categorie, description, prix_estime, statut) VALUES (?, ?, ?, ?, ?, 'OUVERT')");
    $stmt->execute([
        $_SESSION['user_id'],
        $_POST['titre'],
        $_POST['categorie'],
        $_POST['description'], // Le script <script> sera stocké mais neutralisé à l'affichage
        $_POST['budget']
    ]);
    header("Location: client_dashboard.php?success=1");
} catch (PDOException $e) {
    // En production, on ne montre pas $e->getMessage() pour éviter la fuite d'infos
    die("Erreur technique lors de la publication.");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publier une annonce - BricoSûr</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Variables de couleurs */
        :root {
            --primary-color: #0056b3;
            --secondary-color: #004494;
            --background-color: #f4f7f6;
            --text-color: #333;
            --light-text-color: #777;
            --border-color: #e0e0e0;
            --success-color: #28a745;
            --error-color: #dc3545;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .container:hover {
            transform: translateY(-5px);
        }

        h1 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 30px;
            font-size: 28px;
        }

        h1 i {
            margin-right: 10px;
        }

        .form-group {
            margin-bottom: 25px;
            text-align: left;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-color);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px 12px 45px; /* Espace pour l'icône à gauche */
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            box-sizing: border-box; /* Important pour le padding */
        }

        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 86, 179, 0.1);
        }

        .form-icon {
            position: absolute;
            top: 42px; /* Ajuster selon la hauteur du label et du champ */
            left: 15px;
            color: var(--light-text-color);
            font-size: 18px;
        }
        
        /* Ajustement pour le textarea */
        textarea.form-control {
            resize: vertical;
            height: 120px;
            padding-top: 12px;
        }
        .form-group-textarea .form-icon {
            top: 42px;
        }

        /* Style spécifique pour le select */
        select.form-control {
            appearance: none; /* Supprime le style par défaut du navigateur */
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg fill="%23333" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/><path d="M0 0h24v24H0z" fill="none"/></svg>'); /* Flèche personnalisée */
            background-repeat: no-repeat;
            background-position: right 15px center;
            padding-right: 45px; /* Espace pour la flèche */
        }

        .btn-submit {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: #fff;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: background 0.3s ease, transform 0.2s ease;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .btn-submit:hover {
            background: linear-gradient(45deg, var(--secondary-color), var(--primary-color));
            transform: scale(1.02);
        }
        
        .btn-submit i {
            margin-right: 10px;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: var(--light-text-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: var(--primary-color);
        }

        .back-link i {
            margin-right: 8px;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: left;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: var(--error-color);
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1><i class="fas fa-plus-circle"></i> Publier une annonce</h1>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <div class="form-group">
                <label for="titre" class="form-label">Titre du travail</label>
                <i class="fas fa-heading form-icon"></i>
                <input type="text" id="titre" name="titre" class="form-control" placeholder="Ex: Réparation robinet, Peinture salon..." required value="<?php echo isset($_POST['titre']) ? htmlspecialchars($_POST['titre']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="categorie" class="form-label">Catégorie</label>
                <i class="fas fa-tags form-icon"></i>
                <select id="categorie" name="categorie" class="form-control" required>
                    <option value="">Sélectionnez une catégorie</option>
                    <option value="Plomberie" <?php echo (isset($_POST['categorie']) && $_POST['categorie'] === 'Plomberie') ? 'selected' : ''; ?>>Plomberie</option>
                    <option value="Électricité" <?php echo (isset($_POST['categorie']) && $_POST['categorie'] === 'Électricité') ? 'selected' : ''; ?>>Électricité</option>
                    <option value="Peinture" <?php echo (isset($_POST['categorie']) && $_POST['categorie'] === 'Peinture') ? 'selected' : ''; ?>>Peinture</option>
                    <option value="Jardinage" <?php echo (isset($_POST['categorie']) && $_POST['categorie'] === 'Jardinage') ? 'selected' : ''; ?>>Jardinage</option>
                    <option value="Menuiserie" <?php echo (isset($_POST['categorie']) && $_POST['categorie'] === 'Menuiserie') ? 'selected' : ''; ?>>Menuiserie</option>
                    <option value="Autre" <?php echo (isset($_POST['categorie']) && $_POST['categorie'] === 'Autre') ? 'selected' : ''; ?>>Autre</option>
                </select>
            </div>

            <div class="form-group form-group-textarea">
                <label for="description" class="form-label">Description détaillée</label>
                <i class="fas fa-align-left form-icon"></i>
                <textarea id="description" name="description" class="form-control" placeholder="Détaillez votre besoin, les contraintes, etc..." required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
            </div>

            <div class="form-group">
                <label for="budget" class="form-label">Budget estimé (€)</label>
                <i class="fas fa-euro-sign form-icon"></i>
                <input type="number" id="budget" name="budget" class="form-control" placeholder="Ex: 150.00" step="0.01" min="0" required value="<?php echo isset($_POST['budget']) ? htmlspecialchars($_POST['budget']) : ''; ?>">
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-paper-plane"></i> Publier l'annonce sécurisée
            </button>

            <a href="client_dashboard.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Retour au tableau de bord
            </a>
        </form>
    </div>

</body>
</html>