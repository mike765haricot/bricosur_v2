# BricoSûr v2 - Plateforme de Services Sécurisée

## 🚀 Installation
1. Copier le dossier dans votre serveur local (WAMP/XAMPP).
2. Importer le fichier `bricosur_v2.sql` via phpMyAdmin (Port 3307).
3. Configurer les accès dans `includes/db.php`.

## 🛡️ Sécurité implémentée
- **Modèle STRIDE** : Analyse complète des menaces.
- **Anti-SQL Injection** : Requêtes préparées PDO.
- **Anti-XSS** : Filtrage systématique `htmlspecialchars`.
- **Audit** : Journalisation des IPs et actions (Logs).
- **RBAC** : Contrôle d'accès strict par rôles.