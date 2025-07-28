<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];
    
    // Validation
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Le nom de l\'entreprise est requis.';
    }
    
    if (empty($email) || !isValidEmail($email)) {
        $errors[] = 'Une adresse e-mail valide est requise.';
    }
    
    if (empty($password)) {
        $errors[] = 'Le mot de passe est requis.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
    }
    
    if ($password !== $confirm) {
        $errors[] = 'Les mots de passe ne correspondent pas.';
    }
    
    // Check if email already exists
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                $errors[] = 'Cette adresse e-mail est déjà utilisée.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Erreur de vérification. Veuillez réessayer.';
        }
    }
    
    // Insert new user if no errors
    if (empty($errors)) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'company')");
            
            if ($stmt->execute([$name, $email, $hashedPassword])) {
                setFlash('Inscription réussie ! Vous pouvez maintenant vous connecter.', 'success');
                header('Location: login.php');
                exit();
            } else {
                $errors[] = 'Erreur lors de l\'inscription. Veuillez réessayer.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Erreur lors de l\'inscription. Veuillez réessayer.';
        }
    }
    
    // Display errors
    if (!empty($errors)) {
        setFlash(implode('<br>', $errors), 'error');
    }
}

include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card">
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <span class="fs-4">🏢</span>
                    </div>
                    <h2 class="card-title">Inscription Entreprise</h2>
                    <p class="text-muted">Créez votre compte pour publier des offres de stage</p>
                </div>
                
                <form method="post" action="registration_company.php" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom de l'entreprise</label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            class="form-control" 
                            value="<?php echo isset($_POST['name']) ? sanitize($_POST['name']) : ''; ?>"
                            required
                        >
                        <div class="invalid-feedback">
                            Veuillez saisir le nom de votre entreprise.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse e-mail professionnelle</label>
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
                        <div class="form-text">Utilisez votre adresse e-mail professionnelle</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            class="form-control" 
                            minlength="6"
                            required
                        >
                        <div class="invalid-feedback">
                            Le mot de passe doit contenir au moins 6 caractères.
                        </div>
                        <div class="form-text">Minimum 6 caractères</div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="confirm" class="form-label">Confirmer le mot de passe</label>
                        <input 
                            type="password" 
                            name="confirm" 
                            id="confirm" 
                            class="form-control" 
                            required
                        >
                        <div class="invalid-feedback">
                            Veuillez confirmer votre mot de passe.
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">S'inscrire</button>
                    </div>
                </form>
                
                <hr class="my-4">
                
                <div class="text-center">
                    <p class="mb-0">Déjà un compte ? <a href="login.php">Se connecter</a></p>
                    <p class="mt-2 mb-0">
                        <small class="text-muted">
                            Vous êtes un étudiant ? 
                            <a href="registration_student.php">Inscription étudiant</a>
                        </small>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
