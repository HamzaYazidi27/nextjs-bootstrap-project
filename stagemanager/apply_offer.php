<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

// Require student role
requireRole('student');

$currentUser = getCurrentUser();
$offer_id = isset($_GET['offer_id']) ? (int)$_GET['offer_id'] : 0;

if (!$offer_id) {
    setFlash('Offre non trouvée.', 'error');
    header('Location: student_dashboard.php');
    exit();
}

// Get offer details
$stmt = $pdo->prepare("
    SELECT o.*, u.name as company_name 
    FROM offers o 
    JOIN users u ON o.company_id = u.id 
    WHERE o.id = ?
");
$stmt->execute([$offer_id]);
$offer = $stmt->fetch();

if (!$offer) {
    setFlash('Offre non trouvée.', 'error');
    header('Location: student_dashboard.php');
    exit();
}

// Check if already applied
$stmt = $pdo->prepare("SELECT id FROM applications WHERE student_id = ? AND offer_id = ?");
$stmt->execute([$_SESSION['user_id'], $offer_id]);
if ($stmt->rowCount() > 0) {
    setFlash('Vous avez déjà postulé à cette offre.', 'warning');
    header('Location: student_dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    $cv_path = null;
    
    // Handle file upload
    if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
        if (isValidUpload($_FILES['cv'])) {
            $uploadDir = 'uploads/';
            $fileName = generateSecureFilename($_FILES['cv']['name']);
            $uploadPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['cv']['tmp_name'], $uploadPath)) {
                $cv_path = $uploadPath;
            } else {
                $errors[] = 'Erreur lors du téléchargement du CV.';
            }
        } else {
            $errors[] = 'Fichier CV invalide. Formats acceptés: PDF, DOC, DOCX (max 5MB).';
        }
    } else {
        $errors[] = 'Veuillez télécharger votre CV.';
    }
    
    // Insert application if no errors
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO applications (student_id, offer_id, cv_path) VALUES (?, ?, ?)");
            if ($stmt->execute([$_SESSION['user_id'], $offer_id, $cv_path])) {
                setFlash('Candidature envoyée avec succès !', 'success');
                header('Location: student_dashboard.php');
                exit();
            } else {
                $errors[] = 'Erreur lors de l\'envoi de la candidature.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Erreur lors de l\'envoi de la candidature.';
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
                <h3 class="mb-0">Postuler à une offre</h3>
            </div>
            <div class="card-body">
                <!-- Offer Details -->
                <div class="bg-light p-4 rounded mb-4">
                    <h4 class="text-primary"><?php echo sanitize($offer['title']); ?></h4>
                    <h6 class="text-muted mb-3"><?php echo sanitize($offer['company_name']); ?></h6>
                    
                    <div class="mb-3">
                        <h6>Description:</h6>
                        <p><?php echo nl2br(sanitize($offer['description'])); ?></p>
                    </div>
                    
                    <?php if (!empty($offer['requirements'])): ?>
                        <div class="mb-3">
                            <h6>Prérequis:</h6>
                            <p><?php echo nl2br(sanitize($offer['requirements'])); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <small class="text-muted">
                        Offre publiée le <?php echo formatDate($offer['posted_date']); ?>
                    </small>
                </div>
                
                <!-- Application Form -->
                <form method="post" action="apply_offer.php?offer_id=<?php echo $offer_id; ?>" 
                      enctype="multipart/form-data" class="needs-validation" novalidate>
                    
                    <div class="mb-4">
                        <h5>Vos informations</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Nom complet</label>
                                <input type="text" class="form-control" value="<?php echo sanitize($currentUser['name']); ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Adresse e-mail</label>
                                <input type="email" class="form-control" value="<?php echo sanitize($currentUser['email']); ?>" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="cv" class="form-label">
                            <strong>Télécharger votre CV *</strong>
                        </label>
                        <div class="upload-area" onclick="document.getElementById('cv').click()">
                            <div class="text-center">
                                <span class="fs-1">📄</span>
                                <h6 class="mt-2">Cliquez pour sélectionner votre CV</h6>
                                <p class="text-muted mb-0">ou glissez-déposez votre fichier ici</p>
                                <small class="text-muted">Formats acceptés: PDF, DOC, DOCX (max 5MB)</small>
                            </div>
                        </div>
                        <input 
                            type="file" 
                            name="cv" 
                            id="cv" 
                            class="form-control d-none" 
                            accept=".pdf,.doc,.docx"
                            required
                        >
                        <div class="invalid-feedback">
                            Veuillez sélectionner votre CV.
                        </div>
                        <div class="mt-2">
                            <span class="file-label text-muted">Aucun fichier sélectionné</span>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <h6 class="alert-heading">Information importante</h6>
                        <p class="mb-0">
                            En soumettant cette candidature, vous confirmez que les informations fournies sont exactes 
                            et que vous autorisez l'entreprise à consulter votre CV dans le cadre de ce processus de recrutement.
                        </p>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="student_dashboard.php" class="btn btn-secondary">
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Envoyer ma candidature
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('cv');
    const fileLabel = document.querySelector('.file-label');
    
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            const file = this.files[0];
            fileLabel.textContent = file.name + ' (' + formatFileSize(file.size) + ')';
            fileLabel.classList.remove('text-muted');
            fileLabel.classList.add('text-success');
        } else {
            fileLabel.textContent = 'Aucun fichier sélectionné';
            fileLabel.classList.remove('text-success');
            fileLabel.classList.add('text-muted');
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
