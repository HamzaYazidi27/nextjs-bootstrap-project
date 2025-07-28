<?php
// Installation automatique de StageManager
error_reporting(E_ALL);
ini_set('display_errors', 1);

$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$message = '';
$error = '';

// Fonction pour tester la connexion MySQL
function testMySQLConnection() {
    try {
        $pdo = new PDO("mysql:host=localhost;charset=utf8", "root", "");
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Fonction pour créer la base de données
function createDatabase() {
    try {
        $pdo = new PDO("mysql:host=localhost;charset=utf8", "root", "");
        
        // Lire le fichier SQL
        $sql = file_get_contents('base.sql');
        if ($sql === false) {
            return "Erreur: Fichier base.sql non trouvé";
        }
        
        // Exécuter les requêtes
        $statements = explode(';', $sql);
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                $pdo->exec($statement);
            }
        }
        
        return "Base de données créée avec succès";
    } catch (PDOException $e) {
        return "Erreur: " . $e->getMessage();
    }
}

// Traitement des étapes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($step) {
        case 1:
            if (testMySQLConnection()) {
                $step = 2;
                $message = "Connexion MySQL réussie !";
            } else {
                $error = "Impossible de se connecter à MySQL. Vérifiez que XAMPP MySQL est démarré.";
            }
            break;
            
        case 2:
            $result = createDatabase();
            if (strpos($result, 'Erreur') === false) {
                $step = 3;
                $message = $result;
            } else {
                $error = $result;
            }
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation StageManager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            font-weight: bold;
        }
        .step.active {
            background-color: #0d6efd;
            color: white;
        }
        .step.completed {
            background-color: #198754;
            color: white;
        }
        .step.pending {
            background-color: #e9ecef;
            color: #6c757d;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center">
                        <h2>🚀 Installation StageManager</h2>
                        <p class="text-muted">Assistant d'installation automatique</p>
                    </div>
                    <div class="card-body">
                        <!-- Indicateur d'étapes -->
                        <div class="step-indicator">
                            <div class="step <?php echo $step >= 1 ? ($step > 1 ? 'completed' : 'active') : 'pending'; ?>">1</div>
                            <div class="step <?php echo $step >= 2 ? ($step > 2 ? 'completed' : 'active') : 'pending'; ?>">2</div>
                            <div class="step <?php echo $step >= 3 ? 'active' : 'pending'; ?>">3</div>
                        </div>

                        <?php if (!empty($message)): ?>
                            <div class="alert alert-success"><?php echo $message; ?></div>
                        <?php endif; ?>

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <?php if ($step == 1): ?>
                            <h4>Étape 1: Vérification de MySQL</h4>
                            <p>Nous allons vérifier que MySQL est accessible.</p>
                            <div class="alert alert-info">
                                <strong>Prérequis:</strong>
                                <ul class="mb-0">
                                    <li>XAMPP doit être installé</li>
                                    <li>Apache doit être démarré (vert dans XAMPP)</li>
                                    <li>MySQL doit être démarré (vert dans XAMPP)</li>
                                </ul>
                            </div>
                            <form method="post">
                                <button type="submit" class="btn btn-primary">Tester la connexion MySQL</button>
                            </form>

                        <?php elseif ($step == 2): ?>
                            <h4>Étape 2: Création de la base de données</h4>
                            <p>Nous allons créer la base de données et les tables nécessaires.</p>
                            <div class="alert alert-warning">
                                <strong>Attention:</strong> Si la base de données existe déjà, elle sera recréée.
                            </div>
                            <form method="post">
                                <input type="hidden" name="step" value="2">
                                <button type="submit" class="btn btn-success">Créer la base de données</button>
                            </form>

                        <?php elseif ($step == 3): ?>
                            <h4>✅ Installation terminée !</h4>
                            <div class="alert alert-success">
                                <h5>StageManager est maintenant installé et prêt à utiliser !</h5>
                            </div>

                            <h5>Comptes de test disponibles :</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h6>👨‍💼 Administrateur</h6>
                                            <small>admin@stagemanager.com<br>admin123</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h6>🏢 Entreprise</h6>
                                            <small>contact@techcorp.com<br>admin123</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <h6>👨‍🎓 Étudiant</h6>
                                            <small>jean.dupont@email.com<br>admin123</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 text-center">
                                <a href="index.php" class="btn btn-primary btn-lg">
                                    🚀 Accéder à StageManager
                                </a>
                            </div>

                            <div class="mt-4">
                                <h6>Prochaines étapes :</h6>
                                <ul>
                                    <li>Testez la connexion avec les comptes ci-dessus</li>
                                    <li>Changez les mots de passe par défaut</li>
                                    <li>Supprimez ce fichier install.php pour la sécurité</li>
                                    <li>Consultez le README.md pour plus d'informations</li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <small class="text-muted">
                        StageManager - Système de gestion des stages<br>
                        <a href="README.md">Documentation</a> | 
                        <a href="GUIDE_DEPANNAGE.md">Guide de dépannage</a>
                    </small>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
