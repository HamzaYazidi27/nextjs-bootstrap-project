<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

// Require company role
requireRole('company');

$currentUser = getCurrentUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $requirements = sanitize($_POST['requirements']);
    
    // Validation
    $errors = [];
    
    if (empty($title)) {
        $errors[] = 'Le titre de l\'offre est requis.';
    }
    
    if (empty($description)) {
        $errors[] = 'La description est requise.';
    }
    
    // Insert offer if no errors
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO offers (company_id, title, description, requirements) VALUES (?, ?, ?, ?)");
            
            if ($stmt->execute([$_SESSION['user_id'], $title, $description, $requirements])) {
                setFlash('Offre publiée avec succès !', 'success');
                header('Location: company_dashboard.php');
                exit();
            } else {
                $errors[] = 'Erreur lors de la publication de l\'offre.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Erreur lors de la publication de l\'offre.';
        }
    }
    
    // Display errors
    if (!empty($errors)) {
        setFlash(implode('<br>', $errors), 'error');
    }
}

include 'includes/header.php';
?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0">Publier une nouvelle offre de stage</h3>
                <p class="text-muted mb-0">Créez une offre attractive pour attirer les meilleurs talents</p>
            </div>
            <div class="card-body">
                <form method="post" action="post_offer.php" class="needs-validation" novalidate>
                    <div class="mb-4">
                        <h5>Informations de l'entreprise</h5>
                        <div class="bg-light p-3 rounded">
                            <strong><?php echo sanitize($currentUser['name']); ?></strong>
                            <br>
                            <small class="text-muted"><?php echo sanitize($currentUser['email']); ?></small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">
                            <strong>Titre de l'offre *</strong>
                        </label>
                        <input 
                            type="text" 
                            name="title" 
                            id="title" 
                            class="form-control" 
                            value="<?php echo isset($_POST['title']) ? sanitize($_POST['title']) : ''; ?>"
                            placeholder="Ex: Stage Développeur Web, Stage Marketing Digital..."
                            required
                        >
                        <div class="invalid-feedback">
                            Veuillez saisir le titre de l'offre.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">
                            <strong>Description détaillée *</strong>
                        </label>
                        <textarea 
                            name="description" 
                            id="description" 
                            class="form-control" 
                            rows="6"
                            placeholder="Décrivez le poste, les missions, l'environnement de travail, la durée du stage..."
                            required
                        ><?php echo isset($_POST['description']) ? sanitize($_POST['description']) : ''; ?></textarea>
                        <div class="invalid-feedback">
                            Veuillez saisir une description détaillée.
                        </div>
                        <div class="form-text">
                            Soyez précis sur les missions, l'environnement de travail et ce que l'étudiant apprendra.
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="requirements" class="form-label">
                            <strong>Prérequis et compétences</strong>
                        </label>
                        <textarea 
                            name="requirements" 
                            id="requirements" 
                            class="form-control" 
                            rows="4"
                            placeholder="Niveau d'études, compétences techniques, langues, expériences souhaitées..."
                        ><?php echo isset($_POST['requirements']) ? sanitize($_POST['requirements']) : ''; ?></textarea>
                        <div class="form-text">
                            Listez les compétences et prérequis nécessaires ou souhaités.
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <h6 class="alert-heading">Conseils pour une offre attractive</h6>
                        <ul class="mb-0">
                            <li>Utilisez un titre clair et précis</li>
                            <li>Décrivez concrètement les missions du stagiaire</li>
                            <li>Mentionnez les compétences que l'étudiant développera</li>
                            <li>Précisez la durée et les modalités du stage</li>
                            <li>Indiquez si le stage est rémunéré</li>
                        </ul>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="company_dashboard.php" class="btn btn-secondary">
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Publier l'offre
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
