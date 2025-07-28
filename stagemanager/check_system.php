<?php
// Vérification système complète pour StageManager
error_reporting(E_ALL);
ini_set('display_errors', 1);

function checkPHP() {
    $version = phpversion();
    $required = '7.4.0';
    return [
        'status' => version_compare($version, $required, '>='),
        'message' => "PHP $version " . (version_compare($version, $required, '>=') ? '✅' : '❌ (requis: >= 7.4)')
    ];
}

function checkExtensions() {
    $required = ['pdo', 'pdo_mysql', 'mbstring', 'fileinfo'];
    $results = [];
    
    foreach ($required as $ext) {
        $loaded = extension_loaded($ext);
        $results[] = [
            'name' => $ext,
            'status' => $loaded,
            'message' => "$ext " . ($loaded ? '✅' : '❌')
        ];
    }
    
    return $results;
}

function checkDatabase() {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=stagemanager_db;charset=utf8", "root", "");
        
        // Vérifier les tables
        $tables = ['users', 'offers', 'applications'];
        $missing = [];
        
        foreach ($tables as $table) {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() == 0) {
                $missing[] = $table;
            }
        }
        
        if (empty($missing)) {
            return ['status' => true, 'message' => 'Base de données ✅'];
        } else {
            return ['status' => false, 'message' => 'Tables manquantes: ' . implode(', ', $missing) . ' ❌'];
        }
        
    } catch (PDOException $e) {
        return ['status' => false, 'message' => 'Connexion échouée: ' . $e->getMessage() . ' ❌'];
    }
}

function checkFiles() {
    $required = [
        'config/config.php',
        'includes/functions.php',
        'includes/header.php',
        'includes/footer.php',
        'css/custom.css',
        'js/custom.js',
        'uploads/'
    ];
    
    $results = [];
    foreach ($required as $file) {
        $exists = file_exists($file);
        $results[] = [
            'name' => $file,
            'status' => $exists,
            'message' => "$file " . ($exists ? '✅' : '❌')
        ];
    }
    
    return $results;
}

function checkPermissions() {
    $uploadDir = 'uploads/';
    $writable = is_writable($uploadDir);
    
    return [
        'status' => $writable,
        'message' => "Dossier uploads/ " . ($writable ? 'accessible en écriture ✅' : 'non accessible en écriture ❌')
    ];
}

function checkTestUsers() {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=stagemanager_db;charset=utf8", "root", "");
        
        $testUsers = [
            'admin@stagemanager.com' => 'admin',
            'contact@techcorp.com' => 'company',
            'jean.dupont@email.com' => 'student'
        ];
        
        $results = [];
        foreach ($testUsers as $email => $role) {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND role = ?");
            $stmt->execute([$email, $role]);
            $exists = $stmt->rowCount() > 0;
            
            $results[] = [
                'email' => $email,
                'role' => $role,
                'status' => $exists,
                'message' => "$email ($role) " . ($exists ? '✅' : '❌')
            ];
        }
        
        return $results;
        
    } catch (PDOException $e) {
        return [['message' => 'Erreur: ' . $e->getMessage() . ' ❌', 'status' => false]];
    }
}

// Exécuter les vérifications
$phpCheck = checkPHP();
$extChecks = checkExtensions();
$dbCheck = checkDatabase();
$fileChecks = checkFiles();
$permCheck = checkPermissions();
$userChecks = checkTestUsers();

$allGood = $phpCheck['status'] && 
           $dbCheck['status'] && 
           $permCheck['status'] && 
           !in_array(false, array_column($extChecks, 'status')) &&
           !in_array(false, array_column($fileChecks, 'status'));
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification Système - StageManager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-header text-center">
                        <h2>🔍 Vérification Système StageManager</h2>
                        <p class="text-muted mb-0">Diagnostic complet de l'installation</p>
                    </div>
                    <div class="card-body">
                        
                        <?php if ($allGood): ?>
                            <div class="alert alert-success text-center">
                                <h4>🎉 Système entièrement fonctionnel !</h4>
                                <p class="mb-0">Toutes les vérifications sont passées avec succès.</p>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning text-center">
                                <h4>⚠️ Problèmes détectés</h4>
                                <p class="mb-0">Certaines vérifications ont échoué. Consultez les détails ci-dessous.</p>
                            </div>
                        <?php endif; ?>

                        <!-- Vérification PHP -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5>🐘 Version PHP</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-0"><?php echo $phpCheck['message']; ?></p>
                            </div>
                        </div>

                        <!-- Extensions PHP -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5>🔧 Extensions PHP</h5>
                            </div>
                            <div class="card-body">
                                <?php foreach ($extChecks as $ext): ?>
                                    <p class="mb-1"><?php echo $ext['message']; ?></p>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Base de données -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5>🗄️ Base de données</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-0"><?php echo $dbCheck['message']; ?></p>
                            </div>
                        </div>

                        <!-- Fichiers système -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5>📁 Fichiers système</h5>
                            </div>
                            <div class="card-body">
                                <?php foreach ($fileChecks as $file): ?>
                                    <p class="mb-1"><?php echo $file['message']; ?></p>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Permissions -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5>🔐 Permissions</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-0"><?php echo $permCheck['message']; ?></p>
                            </div>
                        </div>

                        <!-- Utilisateurs de test -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5>👥 Utilisateurs de test</h5>
                            </div>
                            <div class="card-body">
                                <?php foreach ($userChecks as $user): ?>
                                    <p class="mb-1"><?php echo $user['message']; ?></p>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="text-center mt-4">
                            <?php if ($allGood): ?>
                                <a href="index.php" class="btn btn-success btn-lg me-2">
                                    🚀 Accéder à StageManager
                                </a>
                                <a href="login.php" class="btn btn-primary">
                                    🔑 Se connecter
                                </a>
                            <?php else: ?>
                                <a href="install.php" class="btn btn-warning me-2">
                                    🔧 Réinstaller
                                </a>
                                <a href="GUIDE_DEPANNAGE.md" class="btn btn-info">
                                    📖 Guide de dépannage
                                </a>
                            <?php endif; ?>
                        </div>

                        <!-- Informations supplémentaires -->
                        <div class="mt-4 pt-3 border-top">
                            <h6>ℹ️ Informations système :</h6>
                            <ul class="list-unstyled small text-muted">
                                <li><strong>Serveur :</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Non détecté'; ?></li>
                                <li><strong>PHP :</strong> <?php echo PHP_VERSION; ?></li>
                                <li><strong>Système :</strong> <?php echo PHP_OS; ?></li>
                                <li><strong>Date :</strong> <?php echo date('Y-m-d H:i:s'); ?></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <small class="text-muted">
                        <a href="README.md">📖 Documentation</a> | 
                        <a href="GUIDE_DEPANNAGE.md">🔧 Dépannage</a> | 
                        <a href="test_db.php">🧪 Test DB</a>
                    </small>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
