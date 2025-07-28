<?php
// Script pour corriger les permissions du dossier uploads
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔧 Correction des permissions - StageManager</h2>";

$uploadDir = 'uploads/';
$success = true;

// 1. Créer le dossier s'il n'existe pas
if (!is_dir($uploadDir)) {
    echo "<p>📁 Création du dossier uploads...</p>";
    if (mkdir($uploadDir, 0755, true)) {
        echo "<p style='color: green;'>✅ Dossier uploads créé avec succès</p>";
    } else {
        echo "<p style='color: red;'>❌ Impossible de créer le dossier uploads</p>";
        $success = false;
    }
} else {
    echo "<p style='color: green;'>✅ Dossier uploads existe déjà</p>";
}

// 2. Vérifier les permissions
if (is_dir($uploadDir)) {
    $perms = fileperms($uploadDir);
    $permsOctal = substr(sprintf('%o', $perms), -4);
    echo "<p>📋 Permissions actuelles: $permsOctal</p>";
    
    // 3. Corriger les permissions si nécessaire
    if (!is_writable($uploadDir)) {
        echo "<p>🔧 Correction des permissions...</p>";
        if (chmod($uploadDir, 0755)) {
            echo "<p style='color: green;'>✅ Permissions corrigées (755)</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Impossible de modifier les permissions automatiquement</p>";
            echo "<p><strong>Solution manuelle:</strong></p>";
            echo "<ul>";
            echo "<li><strong>Windows:</strong> Clic droit sur le dossier uploads > Propriétés > Sécurité > Modifier > Donner contrôle total à 'Utilisateurs'</li>";
            echo "<li><strong>Linux/Mac:</strong> <code>chmod 755 uploads/</code></li>";
            echo "</ul>";
        }
    } else {
        echo "<p style='color: green;'>✅ Dossier accessible en écriture</p>";
    }
}

// 4. Test d'écriture
echo "<p>🧪 Test d'écriture...</p>";
$testFile = $uploadDir . 'test_write.txt';
if (file_put_contents($testFile, 'Test d\'écriture')) {
    echo "<p style='color: green;'>✅ Test d'écriture réussi</p>";
    unlink($testFile); // Supprimer le fichier de test
} else {
    echo "<p style='color: red;'>❌ Test d'écriture échoué</p>";
    $success = false;
}

// 5. Vérifier le fichier .htaccess
$htaccessFile = $uploadDir . '.htaccess';
if (!file_exists($htaccessFile)) {
    echo "<p>📝 Création du fichier .htaccess...</p>";
    $htaccessContent = '# Permissions pour le dossier uploads
Options -Indexes
<Files "*.php">
    Deny from all
</Files>

# Autoriser seulement certains types de fichiers
<FilesMatch "\.(pdf|doc|docx)$">
    Allow from all
</FilesMatch>';
    
    if (file_put_contents($htaccessFile, $htaccessContent)) {
        echo "<p style='color: green;'>✅ Fichier .htaccess créé</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Impossible de créer le fichier .htaccess</p>";
    }
} else {
    echo "<p style='color: green;'>✅ Fichier .htaccess existe</p>";
}

// 6. Résumé
echo "<hr>";
if ($success) {
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; color: #155724;'>";
    echo "<h3>🎉 Correction terminée avec succès !</h3>";
    echo "<p>Le dossier uploads est maintenant configuré correctement.</p>";
    echo "<p><strong>Vous pouvez maintenant:</strong></p>";
    echo "<ul>";
    echo "<li>Tester l'upload de CV dans l'application</li>";
    echo "<li>Supprimer ce fichier fix_permissions.php pour la sécurité</li>";
    echo "</ul>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; color: #721c24;'>";
    echo "<h3>⚠️ Correction partiellement réussie</h3>";
    echo "<p>Certains problèmes nécessitent une intervention manuelle.</p>";
    echo "<p>Consultez les messages ci-dessus pour les solutions.</p>";
    echo "</div>";
}

echo "<br><p><a href='index.php'>← Retour à l'accueil</a> | <a href='check_system.php'>🔍 Vérifier le système</a></p>";
?>
