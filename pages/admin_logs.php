<?php
// 1. Sécurité et Inclusions
require_once '../includes/security_functions.php';
require_once '../includes/db.php';

// 2. Vérification RBAC : Strictement réservé à l'ADMIN
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'ADMIN') {
    header("Location: ../login.php");
    exit();
}

// 3. Récupération de TOUS les logs avec les noms d'utilisateurs
$query = "SELECT al.*, u.nom as utilisateur_nom, u.role as utilisateur_role 
          FROM activity_logs al 
          JOIN users u ON al.user_id = u.id 
          ORDER BY al.created_at DESC 
          LIMIT 50";
$stmt = $pdo->prepare($query);
$stmt->execute();
$all_logs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Supervision Globale - Admin BricoSûr</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f0f2f5; }
        .admin-header {
            background: linear-gradient(135deg, #2c3e50 0%, #000000 100%);
            color: white; padding: 40px 0; text-align: center; border-radius: 0 0 30px 30px;
        }
        .log-table-card {
            background: white; border-radius: 20px; padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-top: -30px;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; padding: 15px; border-bottom: 2px solid #eee; color: #718096; text-transform: uppercase; font-size: 0.8rem; }
        td { padding: 15px; border-bottom: 1px solid #f0f2f5; font-size: 0.9rem; }
        .badge-role { padding: 4px 10px; border-radius: 5px; font-size: 0.75rem; font-weight: 600; }
        .role-client { background: #ebf8ff; color: #3182ce; }
        .role-provider { background: #faf5ff; color: #805ad5; }
        .ip-text { font-family: monospace; color: #a0aec0; }
    </style>
</head>
<body>

    <div class="admin-header">
        <div class="container">
            <h1><i class="fas fa-user-shield"></i> Console de Supervision Sécurisée</h1>
            <p>Surveillance en temps réel de l'activité du système</p>
        </div>
    </div>

    <div class="container" style="max-width: 1100px;">
        <div class="log-table-card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2 style="color: #2d3748; margin: 0;">Flux d'activité global</h2>
                <a href="admin_dashboard.php" class="btn btn-outline" style="text-decoration: none;"><i class="fas fa-arrow-left"></i> Retour Dashboard</a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Date & Heure</th>
                        <th>Utilisateur</th>
                        <th>Rôle</th>
                        <th>Action effectuée</th>
                        <th>Adresse IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_logs as $log): ?>
                    <tr>
                        <td style="color: #718096;"><?php echo date('d/m/Y H:i', strtotime($log['created_at'])); ?></td>
                        <td><strong><?php echo htmlspecialchars($log['utilisateur_nom']); ?></strong></td>
                        <td>
                            <span class="badge-role <?php echo $log['utilisateur_role'] === 'CLIENT' ? 'role-client' : 'role-provider'; ?>">
                                <?php echo $log['utilisateur_role']; ?>
                            </span>
                        </td>
                        <td>
                            <i class="fas fa-circle" style="font-size: 0.5rem; color: #48bb78; margin-right: 8px;"></i>
                            <?php echo htmlspecialchars($log['action']); ?>
                        </td>
                        <td class="ip-text"><?php echo $log['ip_address']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>