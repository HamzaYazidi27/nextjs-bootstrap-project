<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

// Require company or admin role
if (!checkRole('company') && !checkRole('admin')) {
    setFlash('Accès non autorisé.', 'error');
    header('Location: index.php');
    exit();
}

$offer_id = isset($_GET['offer_id']) ? (int)$_GET['offer_id'] : 0;

if (!$offer_id) {
    setFlash('Offre non trouvée.', 'error');
    header('Location: ' . (checkRole('admin') ? 'admin_dashboard.php' : 'company_dashboard.php'));
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
    header('Location: ' . (checkRole('admin') ? 'admin_dashboard.php' : 'company_dashboard.php'));
    exit();
}

// Check if company owns this offer (unless admin)
if (checkRole('company') && $offer['company_id'] != $_SESSION['user_id']) {
    setFlash('Accès non autorisé.', 'error');
    header('Location: company_dashboard.php');
    exit();
}

// Get applications for this offer
$stmt = $pdo->prepare("
    SELECT a.*, u.name as student_name, u.email as student_email
    FROM applications a
    JOIN users u ON a.student_id = u.id
    WHERE a.offer_id = ?
    ORDER BY a.applied_date DESC
");
$stmt->execute([$offer_id]);
$applications = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>Candidatures reçues</h1>
                <p class="text-muted">Offre: <?php echo sanitize($offer['title']); ?></p>
            </div>
            <div>
                <a href="<?php echo checkRole('admin') ? 'manage_offers.php' : 'company_dashboard.php'; ?>" 
                   class="btn btn-outline-secondary">
                    Retour
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Offer Summary -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Détails de l'offre</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <h4 class="text-primary"><?php echo sanitize($offer['title']); ?></h4>
                <h6 class="text-muted mb-3"><?php echo sanitize($offer['company_name']); ?></h6>
                
                <div class="mb-3">
                    <strong>Description:</strong>
                    <p><?php echo nl2br(sanitize($offer['description'])); ?></p>
                </div>
                
                <?php if (!empty($offer['requirements'])): ?>
                    <div class="mb-3">
                        <strong>Prérequis:</strong>
                        <p><?php echo nl2br(sanitize($offer['requirements'])); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-4">
                <div class="bg-light p-3 rounded">
                    <div class="text-center">
                        <h3 class="text-primary"><?php echo count($applications); ?></h3>
                        <p class="mb-0">Candidature(s) reçue(s)</p>
                    </div>
                    <hr>
                    <small class="text-muted">
                        <strong>Publié le:</strong><br>
                        <?php echo formatDate($offer['posted_date']); ?>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Applications List -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Liste des candidatures</h5>
    </div>
    <div class="card-body">
        <?php if (empty($applications)): ?>
            <div class="text-center py-5">
                <span class="fs-1">📭</span>
                <h4 class="mt-3">Aucune candidature</h4>
                <p class="text-muted">Cette offre n'a encore reçu aucune candidature.</p>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($applications as $application): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                         style="width: 50px; height: 50px;">
                                        <span class="fs-5">👨‍🎓</span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0"><?php echo sanitize($application['student_name']); ?></h6>
                                        <small class="text-muted"><?php echo sanitize($application['student_email']); ?></small>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <small class="text-muted">
                                        <strong>Candidature envoyée le:</strong><br>
                                        <?php echo formatDate($application['applied_date']); ?>
                                    </small>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <?php if (!empty($application['cv_path'])): ?>
                                        <a href="<?php echo sanitize($application['cv_path']); ?>" 
                                           target="_blank" 
                                           class="btn btn-outline-primary btn-sm">
                                            📄 Télécharger CV
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-outline-secondary btn-sm" disabled>
                                            Aucun CV
                                        </button>
                                    <?php endif; ?>
                                    
                                    <a href="mailto:<?php echo sanitize($application['student_email']); ?>?subject=Candidature - <?php echo urlencode($offer['title']); ?>" 
                                       class="btn btn-outline-success btn-sm">
                                        ✉️ Contacter
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Summary Statistics -->
            <div class="mt-4 pt-4 border-top">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="bg-light p-3 rounded">
                            <h5 class="text-primary"><?php echo count($applications); ?></h5>
                            <small class="text-muted">Total candidatures</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-light p-3 rounded">
                            <h5 class="text-success">
                                <?php echo count(array_filter($applications, function($a) { return !empty($a['cv_path']); })); ?>
                            </h5>
                            <small class="text-muted">Avec CV</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-light p-3 rounded">
                            <h5 class="text-info">
                                <?php 
                                $recentApplications = array_filter($applications, function($a) {
                                    return strtotime($a['applied_date']) > strtotime('-7 days');
                                });
                                echo count($recentApplications);
                                ?>
                            </h5>
                            <small class="text-muted">Cette semaine</small>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
