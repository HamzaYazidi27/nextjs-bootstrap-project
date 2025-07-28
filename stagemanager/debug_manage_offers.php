<?php
// Script de debug pour manage_offers.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔍 Debug - Gérer les offres</h2>";

// Test 1: Connexion à la base de données
echo "<h3>1. Test de connexion à la base de données</h3>";
try {
    require_once 'config/config.php';
    echo "✅ Connexion à la base réussie<br>";
} catch (Exception $e) {
    echo "❌ Erreur de connexion: " . $e->getMessage() . "<br>";
    exit();
}

// Test 2: Fonctions
echo "<h3>2. Test des fonctions</h3>";
try {
    require_once 'includes/functions.php';
    echo "✅ Fonctions chargées<br>";
} catch (Exception $e) {
    echo "❌ Erreur fonctions: " . $e->getMessage() . "<br>";
    exit();
}

// Test 3: Session et authentification
echo "<h3>3. Test de session</h3>";
session_start();
if (isset($_SESSION['user_id'])) {
    echo "✅ Session active - Utilisateur ID: " . $_SESSION['user_id'] . "<br>";
    echo "✅ Rôle: " . ($_SESSION['role'] ?? 'non défini') . "<br>";
    
    if ($_SESSION['role'] !== 'admin') {
        echo "⚠️ Attention: Vous n'êtes pas connecté en tant qu'admin<br>";
        echo "<a href='login.php'>Se connecter en admin</a><br>";
    }
} else {
    echo "❌ Aucune session active<br>";
    echo "<a href='login.php'>Se connecter</a><br>";
}

// Test 4: Requête des offres
echo "<h3>4. Test de récupération des offres</h3>";
try {
    $stmt = $pdo->prepare("
        SELECT o.*, u.name as company_name, u.email as company_email,
               (SELECT COUNT(*) FROM applications WHERE offer_id = o.id) as application_count
        FROM offers o 
        JOIN users u ON o.company_id = u.id 
        ORDER BY o.posted_date DESC
    ");
    $stmt->execute();
    $offers = $stmt->fetchAll();
    
    echo "✅ Requête réussie<br>";
    echo "📊 Nombre d'offres trouvées: " . count($offers) . "<br>";
    
    if (count($offers) > 0) {
        echo "<h4>Détails des offres:</h4>";
        foreach ($offers as $offer) {
            echo "- " . htmlspecialchars($offer['title']) . " par " . htmlspecialchars($offer['company_name']) . "<br>";
        }
    } else {
        echo "⚠️ Aucune offre dans la base de données<br>";
        echo "<strong>Solution:</strong> Connectez-vous en tant qu'entreprise et publiez une offre<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur requête: " . $e->getMessage() . "<br>";
}

// Test 5: Test des includes
echo "<h3>5. Test des fichiers include</h3>";
$files_to_check = ['includes/header.php', 'includes/footer.php'];
foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "✅ $file existe<br>";
    } else {
        echo "❌ $file manquant<br>";
    }
}

// Test 6: Simulation de la page manage_offers
echo "<h3>6. Test de chargement de manage_offers.php</h3>";
echo "<iframe src='manage_offers.php' width='100%' height='400' style='border: 1px solid #ccc;'></iframe>";

echo "<hr>";
echo "<h3>🔧 Actions recommandées:</h3>";
echo "<ul>";
echo "<li><a href='login.php'>Se connecter en admin</a> (admin@stagemanager.com / admin123)</li>";
echo "<li><a href='post_offer.php'>Publier une offre de test</a> (en tant qu'entreprise)</li>";
echo "<li><a href='manage_offers.php'>Tester la page manage_offers</a></li>";
echo "<li><a href='check_system.php'>Vérification système complète</a></li>";
echo "</ul>";

echo "<p><a href='index.php'>← Retour à l'accueil</a></p>";
?>
