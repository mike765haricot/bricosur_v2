<?php
session_start();
require_once '../includes/db.php'; // Inclus déjà le session_start sécurisé et PDO
require_once '../includes/security_functions.php'; // Pour le jeton CSRF

if (!isset($_SESSION['user_id'])) { 
    header("Location: ../login.php"); 
    exit(); 
}

$user_id = $_SESSION['user_id'];
$destinataire_id = filter_input(INPUT_GET, 'dest', FILTER_SANITIZE_NUMBER_INT);
$service_id = filter_input(INPUT_GET, 'service', FILTER_SANITIZE_NUMBER_INT);

/** * 🛡️ PROTECTION IDOR (Exigence 25 & 29)
 * On vérifie que l'utilisateur est légitime pour cette discussion :
 * Il doit être soit le Client du service, soit le Prestataire (destinataire ou expéditeur).
 */
$stmt_check = $pdo->prepare("
    SELECT id FROM services WHERE id = ? AND client_id = ?
    UNION
    SELECT service_id FROM postulations WHERE service_id = ? AND provider_id = ?
");
$stmt_check->execute([$service_id, $user_id, $service_id, $user_id]);

if (!$stmt_check->fetch()) {
    // Si l'utilisateur n'est pas lié à ce chantier, on bloque l'accès
    die("Erreur de sécurité : Vous n'avez pas l'autorisation d'accéder à cette discussion.");
}

// 1. Envoi d'un message sécurisé
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {
    // Vérification CSRF obligatoire pour chaque envoi
    verify_csrf_token($_POST['csrf_token']);

    $contenu = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS); // Anti-XSS

    $stmt = $pdo->prepare("INSERT INTO messages (expediteur_id, destinataire_id, service_id, contenu) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $destinataire_id, $service_id, $contenu]);
    
    header("Location: chat.php?dest=$destinataire_id&service=$service_id");
    exit();
}

// 2. Récupération des messages (Requête préparée contre l'Injection SQL)
$stmt = $pdo->prepare("
    SELECT m.*, u.nom as expediteur_nom 
    FROM messages m 
    JOIN users u ON m.expediteur_id = u.id 
    WHERE m.service_id = ? 
    AND ((m.expediteur_id = ? AND m.destinataire_id = ?) OR (m.expediteur_id = ? AND m.destinataire_id = ?))
    ORDER BY m.date_envoi ASC
");
$stmt->execute([$service_id, $user_id, $destinataire_id, $destinataire_id, $user_id]);
$messages = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"> <title>Discussion Sécurisée - BricoSûr</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .chat-box { max-width: 700px; margin: 50px auto; background: #fff; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .chat-history { height: 400px; overflow-y: auto; padding: 20px; background: #f9f9f9; display: flex; flex-direction: column; }
        .msg { margin-bottom: 15px; padding: 10px 15px; border-radius: 10px; max-width: 80%; position: relative; }
        .msg-me { align-self: flex-end; background: #0056b3; color: #fff; border-bottom-right-radius: 2px; }
        .msg-them { align-self: flex-start; background: #e9ecef; color: #333; border-bottom-left-radius: 2px; }
        .chat-input { padding: 20px; border-top: 1px solid #eee; display: flex; gap: 10px; }
        input[type="text"] { flex: 1; padding: 12px; border: 1px solid #ddd; border-radius: 8px; outline: none; }
    </style>
</head>
<body style="background: #f0f2f5;">
    <div class="container">
        <a href="client_dashboard.php" style="display:inline-block; margin-top:20px; color:#0056b3; text-decoration:none;">
            <i class="fas fa-arrow-left"></i> Retour au Dashboard
        </a>
        
        <div class="chat-box">
            <div style="padding: 15px; background: #0056b3; color: #fff; text-align: center; font-weight: 600;">
                <i class="fas fa-lock"></i> Discussion Sécurisée (Chantier #<?php echo htmlspecialchars($service_id); ?>)
            </div>
            
            <div class="chat-history">
                <?php foreach($messages as $m): ?>
                    <div class="msg <?php echo ($m['expediteur_id'] == $user_id) ? 'msg-me' : 'msg-them'; ?>">
                        <small style="display: block; font-size: 0.65rem; font-weight: bold; margin-bottom: 3px;">
                            <?php echo htmlspecialchars($m['expediteur_nom']); ?>
                        </small>
                        <?php echo htmlspecialchars($m['contenu']); ?> </div>
                <?php endforeach; ?>
            </div>

            <form class="chat-input" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                
                <input type="text" name="message" placeholder="Votre message sécurisé..." required autocomplete="off">
                <button type="submit" class="btn btn-primary">Envoyer</button>
            </form>
        </div>
    </div>
</body>
</html>