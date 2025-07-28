<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    $role = $_SESSION['role'];
    if ($role === 'student') {
        header('Location: student_dashboard.php');
    } elseif ($role === 'company') {
        header('Location: company_dashboard.php');
    } elseif ($role === 'admin') {
        header('Location: admin_dashboard.php');
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        setFlash('Veuillez remplir tous les champs.', 'error');
    } elseif (!isValidEmail($email)) {
        setFlash('Adresse e-mail invalide.', 'error');
    } else {
        try {
            // Debug: Vérifier la connexion à la base de données
            if (!isset($pdo)) {
                setFlash('Erreur: Connexion à la base de données non établie.', 'error');
            } else {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
                
                // Debug: Vérifier si l'utilisateur existe
                if (!$user) {
                    setFlash('Utilisateur non trouvé. Email: ' . $email, 'error');
                } else {
                    // Debug: Vérifier le mot de passe
                    if (password_verify($password, $user['password'])) {
                        // Démarrer la session si pas déjà fait
                        if (session_status() === PHP_SESSION_NONE) {
                            session_start();
                        }
                        
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_name'] = $user['name'];
                        $_SESSION['role'] = $user['role'];
                        
                        setFlash('Connexion réussie ! Bienvenue ' . $user['name'] . '.', 'success');
                        
                        // Debug: Vérifier les variables de session
                        error_log("Session créée - ID: " . $_SESSION['user_id'] . ", Role: " . $_SESSION['role']);
                        
                        // Redirect based on role
                        if ($user['role'] === 'student') {
                            header('Location: student_dashboard.php');
                        } elseif ($user['role'] === 'company') {
                            header('Location: company_dashboard.php');
                        } elseif ($user['role'] === 'admin') {
                            header('Location: admin_dashboard.php');
                        }
                        exit();
                    } else {
                        setFlash('Mot de passe incorrect pour: ' . $email, 'error');
                    }
                }
            }
        } catch (PDOException $e) {
            setFlash('Erreur de base de données: ' . $e->getMessage(), 'error');
        } catch (Exception $e) {
            setFlash('Erreur générale: ' . $e->getMessage(), 'error');
        }
    }
}

include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title text-center mb-4">Connexion</h2>
                
                <form method="post" action="login.php" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse e-mail</label>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            class="form-control" 
                            value="<?php echo isset($_POST['email']) ? sanitize($_POST['email']) : ''; ?>"
                            required
                        >
                        <div class="invalid-feedback">
                            Veuillez saisir une adresse e-mail valide.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            class="form-control" 
                            required
                        >
                        <div class="invalid-feedback">
                            Veuillez saisir votre mot de passe.
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Se connecter</button>
                    </div>
                </form>
                
                <hr class="my-4">
                
                <div class="text-center">
                    <p class="mb-2">Pas encore de compte ?</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="registration_student.php" class="btn btn-outline-primary btn-sm">
                            Inscription Étudiant
                        </a>
                        <a href="registration_company.php" class="btn btn-outline-success btn-sm">
                            Inscription Entreprise
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Demo credentials info -->
        <div class="card mt-3">
            <div class="card-body">
                <h6 class="card-title">Comptes de démonstration</h6>
                <small class="text-muted">
                    <strong>Admin:</strong> admin@stagemanager.com / admin123<br>
                    <strong>Entreprise:</strong> contact@techcorp.com / admin123<br>
                    <strong>Étudiant:</strong> jean.dupont@email.com / admin123
                </small>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
