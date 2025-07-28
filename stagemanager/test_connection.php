<?php
// Test de connexion à la base de données
echo "<h2>Test de connexion StageManager</h2>";

// Test 1: Configuration
echo "<h3>1. Configuration</h3>";
echo "DB_HOST: localhost<br>";
echo "DB_NAME: stagemanager_db<br>";
echo "DB_USER: root<br>";
echo "DB_PASS: (vide)<br><br>";

// Test 2: Connexion PDO
echo "<h3>2. Test de connexion PDO</h3>";
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=stagemanager_db;charset=utf8",
        "root",
        "",
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        )
    );
    echo "✅ Connexion à la base de données réussie<br><br>";
} catch (PDOException $e) {
    echo "❌ Erreur de connexion : " . $e->getMessage() . "<br><br>";
    
    // Test de connexion sans spécifier la base de données
    echo "<h3>Test de connexion MySQL sans base de données</h3>";
    try {
        $pdo_test = new PDO("mysql:host=localhost", "root", "");
        echo "✅ Connexion MySQL réussie<br>";
        echo "❌ La base de données 'stagemanager_db' n'existe probablement pas<br><br>";
        
        // Créer la base de données
        echo "<h3>Création de la base de données</h3>";
        $pdo_test->exec("CREATE DATABASE IF NOT EXISTS stagemanager_db");
        echo "✅ Base de données 'stagemanager_db' créée<br><br>";
        
        // Reconnexion avec la nouvelle base
        $pdo = new PDO(
            "mysql:host=localhost;dbname=stagemanager_db;charset=utf8",
            "root",
            "",
            array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            )
        );
        echo "✅ Reconnexion à stagemanager_db réussie<br><br>";
        
    } catch (PDOException $e2) {
        echo "❌ Erreur MySQL : " . $e2->getMessage() . "<br><br>";
        exit;
    }
}

// Test 3: Vérification des tables
echo "<h3>3. Vérification des tables</h3>";
try {
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        echo "❌ Aucune table trouvée. Vous devez importer le fichier base.sql<br>";
        echo "<strong>Instructions:</strong><br>";
        echo "1. Ouvrez phpMyAdmin (http://localhost/phpmyadmin)<br>";
        echo "2. Sélectionnez la base 'stagemanager_db'<br>";
        echo "3. Cliquez sur 'Importer'<br>";
        echo "4. Sélectionnez le fichier 'base.sql'<br>";
        echo "5. Cliquez sur 'Exécuter'<br><br>";
    } else {
        echo "✅ Tables trouvées: " . implode(", ", $tables) . "<br><br>";
        
        // Test 4: Vérification des utilisateurs
        echo "<h3>4. Vérification des utilisateurs de test</h3>";
        $stmt = $pdo->query("SELECT email, role FROM users");
        $users = $stmt->fetchAll();
        
        if (empty($users)) {
            echo "❌ Aucun utilisateur trouvé<br>";
        } else {
            echo "✅ Utilisateurs trouvés:<br>";
            foreach ($users as $user) {
                echo "- " . $user['email'] . " (" . $user['role'] . ")<br>";
            }
        }
    }
} catch (PDOException $e) {
    echo "❌ Erreur lors de la vérification des tables : " . $e->getMessage() . "<br><br>";
}

// Test 5: Test de session
echo "<h3>5. Test des sessions</h3>";
session_start();
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "✅ Sessions PHP actives<br>";
    echo "Session ID: " . session_id() . "<br><br>";
} else {
    echo "❌ Problème avec les sessions PHP<br><br>";
}

// Test 6: Test de mot de passe
echo "<h3>6. Test de hachage des mots de passe</h3>";
$test_password = "admin123";
$hashed = password_hash($test_password, PASSWORD_DEFAULT);
echo "Mot de passe test: " . $test_password . "<br>";
echo "Hash généré: " . $hashed . "<br>";
if (password_verify($test_password, $hashed)) {
    echo "✅ Vérification du mot de passe réussie<br><br>";
} else {
    echo "❌ Problème avec le hachage des mots de passe<br><br>";
}

echo "<hr>";
echo "<p><strong>Si tous les tests sont verts, l'application devrait fonctionner.</strong></p>";
echo "<p><a href='login.php'>Retour à la page de connexion</a></p>";
?>
