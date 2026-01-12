<?php
// On utilise security_functions pour la session sécurisée et les cookies
require_once '../includes/security_functions.php'; 
require_once '../includes/db.php'; // Connexion sécurisée via le port 3307

/** * PROTECTION DE SÉCURITÉ (RBAC) 
 * Vérification stricte du rôle ADMIN pour valider l'exigence de contrôle d'accès.
 */
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'ADMIN') {
    header("Location: ../login.php");
    exit();
}

try {
    // 1. Récupération des utilisateurs
    $stmt_users = $pdo->query("SELECT id, nom, email, role FROM users ORDER BY id DESC");
    $users = $stmt_users->fetchAll();

    // 2. Récupération des services pour modération
    $stmt_services = $pdo->query("
        SELECT s.*, u.nom as client_nom 
        FROM services s 
        JOIN users u ON s.client_id = u.id 
        ORDER BY s.date_publication DESC
    ");
    $services = $stmt_services->fetchAll();

    // 3. Statistique rapide pour la supervision
    $stmt_logs = $pdo->query("SELECT COUNT(*) FROM activity_logs");
    $total_logs = $stmt_logs->fetchColumn();

} catch (PDOException $e) {
    // Sécurité : on ne révèle pas l'erreur brute pour éviter la fuite d'infos
    die("Erreur de base de données sécurisée.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration - BricoSûr v2</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .admin-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; padding: 20px; }
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #0056b3; color: white; }
        .badge-role { padding: 5px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: bold; text-transform: uppercase; }
        .role-ADMIN { background: #e74c3c; color: white; }
        .role-CLIENT { background: #3498db; color: white; }
        .role-PROVIDER { background: #2ecc71; color: white; }
        .alert-success { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        
        /* Style pour la nouvelle carte de supervision */
        .supervision-card {
            background: white; padding: 25px; border-radius: 15px; border-top: 5px solid #2d3748;
            display: flex; align-items: center; gap: 20px; cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px; text-decoration: none; color: inherit;
        }
        .supervision-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
    </style>
</head>
<body style="background: #f0f2f5;">

    <nav class="navbar">
        <div class="container nav-container">
            <a href="../index.php" class="logo"><i class="fas fa-user-shield"></i> BricoSûr ADMIN</a>
            <div class="auth-buttons">
                <span>Connecté en tant que <strong><?php echo htmlspecialchars($_SESSION['user_nom']); ?></strong></span>
                <a href="../logout.php" class="btn btn-outline" style="margin-left:15px; border-color:#ff4d4d; color:#ff4d4d;">Déconnexion</a>
            </div>
        </div>
    </nav>

    <div class="container" style="margin-top: 100px;">
        <h1 style="margin-bottom: 20px;"><i class="fas fa-cogs"></i> Gestion de la Plateforme</h1>

        <?php if(isset($_GET['success'])): ?>
            <div class="alert-success">
                <i class="fas fa-check-circle"></i> L'action de modération a été effectuée avec succès.
            </div>
        <?php endif; ?>

        <a href="admin_logs.php" class="supervision-card">
            <div style="background: #f4f7f6; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-microchip fa-2x" style="color: #2d3748;"></i>
            </div>
            <div style="flex-grow: 1;">
                <h3 style="margin: 0; color: #2d3748;">Logs et Supervision Système</h3>
                <p style="margin: 5px 0 0 0; color: #718096; font-size: 0.9rem;">
                    Surveillez en temps réel les accès et les actions des utilisateurs pour garantir la <strong>non-répudiation</strong>.
                </p>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary-color);"><?php echo $total_logs; ?></div>
                <div style="font-size: 0.7rem; color: #a0aec0; text-transform: uppercase;">Événements tracés</div>
            </div>
        </a>

        <div class="admin-grid">
            <div>
                <h3><i class="fas fa-users"></i> Utilisateurs inscrits</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Rôle</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['nom']); ?></td> 
                            <td><span class="badge-role role-<?php echo $user['role']; ?>"><?php echo $user['role']; ?></span></td>
                            <td>
                                <?php if($user['role'] !== 'ADMIN'): ?>
                                    <a href="admin_action.php?type=user&id=<?php echo $user['id']; ?>" 
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')" 
                                       title="Supprimer l'utilisateur">
                                         <i class="fas fa-trash-alt" style="color:#e74c3c;"></i>
                                    </a>
                                <?php else: ?>
                                    <i class="fas fa-lock" title="Admin protégé" style="color:#ccc;"></i>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div>
                <h3><i class="fas fa-tools"></i> Services publiés</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Statut</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($services as $s): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($s['titre']); ?></td> 
                            <td><small class="badge" style="background:#eee;"><?php echo $s['statut']; ?></small></td>
                            <td>
                                <a href="admin_action.php?type=service&id=<?php echo $s['id']; ?>" 
                                   onclick="return confirm('Supprimer définitivement cette annonce ?')" 
                                   title="Supprimer l'annonce">
                                    <i class="fas fa-eraser" style="color:#e74c3c;"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>