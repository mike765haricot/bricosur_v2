<?php
require_once '../includes/security_functions.php';
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) { exit(); }

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$stmt_ads = $pdo->prepare("SELECT * FROM services WHERE client_id = ?");
$stmt_ads->execute([$user_id]);
$annonces = $stmt_ads->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Archive des données - BricoSûr</title>
    <style>
        body { font-family: sans-serif; padding: 40px; color: #333; }
        .header { border-bottom: 2px solid #0056b3; padding-bottom: 10px; margin-bottom: 30px; }
        .section { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
    </style>
</head>
<body onload="window.print()"> <div class="header">
        <h1>Archive de données personnelles - BricoSûr</h1>
        <p>Généré le : <?php echo date('d/m/Y H:i'); ?></p>
    </div>

    <div class="section">
        <h3>Informations de profil</h3>
        <p><strong>Nom :</strong> <?php echo htmlspecialchars($user['nom']); ?></p>
        <p><strong>Email :</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Rôle :</strong> <?php echo $user['role']; ?></p>
    </div>

    <div class="section">
        <h3>Vos annonces publiées</h3>
        <table>
            <tr><th>Titre</th><th>Statut</th><th>Budget</th></tr>
            <?php foreach($annonces as $a): ?>
            <tr>
                <td><?php echo htmlspecialchars($a['titre']); ?></td>
                <td><?php echo $a['statut']; ?></td>
                <td><?php echo $a['prix_estime']; ?> €</td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>