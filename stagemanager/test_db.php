<?php
// Test de connexion à la base de données
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Test de connexion StageManager</h2>";

// Test 1: Vérifier les paramètres
echo "<h3>1. Paramètres de connexion:</h3>";
echo "Host: localhost<br>";
echo "Database: stagemanager_db<br>";
echo "User: root<br>";
echo "Password: (vide)<br><br>";

// Test 2: Connexion MySQL
echo "<h3>2. Test de connexion MySQL:</h3>";
try {
    $pdo = new PDO("mysql:host=localhost;charset=utf8", "root", "");
    echo "✅ Connexion MySQL réussie<br>";
    
    // Test 3: Vérifier si la base existe
    echo "<h3>3. Vérification de la base de données:</h3>";
    $stmt = $pdo->query("SHOW DATABASES LIKE 'stagemanager_db'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Base de données 'stagemanager_db' trouvée<br>";
        
        // Test 4: Se connecter à la base
        $pdo = new PDO("mysql:host=localhost;dbname=stagemanager_db;charset=utf8", "root", "");
        echo "✅ Connexion à la base réussie<br>";
        
        // Test 5: Vérifier les tables
        echo "<h3>4. Vérification des tables:</h3>";
        $tables = ['users', 'offers', 'applications'];
        foreach ($tables as $table) {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "✅ Table '$table' existe<br>";
            } else {
                echo "❌ Table '$table' manquante<br>";
            }
        }
        
        // Test 6: Compter les utilisateurs
        echo "<h3>5. Données de test:</h3>";
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $count = $stmt->fetch()['count'];
        echo "Nombre d'utilisateurs: $count<br>";
        
        if ($count > 0) {
            $stmt = $pdo->query("SELECT name, email, role FROM users LIMIT 3");
            echo "<strong>Utilisateurs de test:</strong><br>";
            while ($user = $stmt->fetch()) {
                echo "- {$user['name']} ({$user['email']}) - {$user['role']}<br>";
            }
        }
        
    } else {
        echo "❌ Base de données 'stagemanager_db' non trouvée<br>";
        echo "<strong>Solution:</strong> Importez le fichier base.sql dans phpMyAdmin<br>";
    }
    
} catch (PDOException $e) {
    echo "❌ Erreur de connexion: " . $e->getMessage() . "<br>";
    echo "<strong>Solutions possibles:</strong><br>";
    echo "1. Vérifiez que MySQL est démarré dans XAMPP<br>";
    echo "2. Vérifiez que le port 3306 est libre<br>";
    echo "3. Importez le fichier base.sql dans phpMyAdmin<br>";
}

echo "<br><hr>";
echo "<p><a href='index.php'>← Retour à l'accueil</a></p>";
echo "<p><strong>Note:</strong> Supprimez ce fichier après les tests pour la sécurité.</p>";
?>
