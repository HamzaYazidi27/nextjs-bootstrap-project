<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

// Require student role
requireRole('student');

$currentUser = getCurrentUser();

// Get available offers
$stmt = $pdo->prepare("
    SELECT o.*, u.name as company_name,
           (SELECT COUNT(*) FROM applications WHERE offer_id = o.id AND student_id = ?) as has_applied
    FROM offers o 
    JOIN users u ON o.company_id = u.id 
    ORDER BY o.posted_date DESC
");
$stmt->execute([$_SESSION['user_id']]);
$offers = $stmt->fetchAll();

// Get user's applications
$stmt = $pdo->prepare("
    SELECT a.*, o.title as offer_title, u.name as company_name, o.posted_date
    FROM applications a
    JOIN offers o ON a.offer_id = o.id
    JOIN users u ON o.company_id = u.id
    WHERE a.student_id = ?
    ORDER BY a.applied_date DESC
");
$stmt->execute([$_SESSION['user_id']]);
$applications = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>Bonjour, <?php echo sanitize($currentUser['name']); ?> !</h1>
                <p class="text-muted">Bienvenue dans votre espace étudiant</p>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?php echo count($offers); ?></h4>
                        <p class="mb-0">Offres disponibles</p>
                    </div>
                    <div class="align-self-center">
                        <span class="fs-1">📋</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?php echo count($applications); ?></h4>
                        <p class="mb-0">Mes candidatures</p>
                    </div>
                    <div class="align-self-center">
                        <span class="fs-1">📄</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?php echo count(array_filter($offers, function($o) { return $o['has_applied'] == 0; })); ?></h4>
                        <p class="mb-0">Nouvelles opportunités</p>
                    </div>
                    <div class="align-self-center">
                        <span class="fs-1">✨</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs for Offers and Applications -->
<ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="offers-tab" data-bs-toggle="tab" data-bs-target="#offers" type="button" role="tab">
            Offres de Stage
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="applications-tab" data-bs-toggle="tab" data-bs-target="#applications" type="button" role="tab">
            Mes Candidatures
        </button>
    </li>
</ul>

<div class="tab-content" id="dashboardTabsContent">
    <!-- Offers Tab -->
    <div class="tab-pane fade show active" id="offers" role="tabpanel">
        <div class="py-4">
            <?php if (empty($offers)): ?>
                <div class="text-center py-5">
                    <span class="fs-1">📭</span>
                    <h4 class="mt-3">Aucune offre disponible</h4>
                    <p class="text-muted">Il n'y a actuellement aucune offre de stage disponible.</p>
                </div>
            <?php else: ?>
                <!-- Search bar -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <input type="text" id="searchInput" class="form-control" placeholder="Rechercher une offre...">
                    </div>
                </div>
                
                <div class="row">
                    <?php foreach ($offers as $offer): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card offer-card searchable-card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title"><?php echo sanitize($offer['title']); ?></h5>
                                        <?php if ($offer['has_applied'] > 0): ?>
                                            <span class="badge bg-success">Candidature envoyée</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <h6 class="text-primary mb-2"><?php echo sanitize($offer['company_name']); ?></h6>
                                    
                                    <p class="card-text">
                                        <?php echo sanitize(substr($offer['description'], 0, 150)) . '...'; ?>
                                    </p>
                                    
                                    <?php if (!empty($offer['requirements'])): ?>
                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <strong>Prérequis:</strong> 
                                                <?php echo sanitize(substr($offer['requirements'], 0, 100)) . '...'; ?>
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <?php echo formatDate($offer['posted_date']); ?>
                                        </small>
                                        
                                        <?php if ($offer['has_applied'] == 0): ?>
                                            <a href="apply_offer.php?offer_id=<?php echo $offer['id']; ?>" 
                                               class="btn btn-primary btn-sm">
                                                Postuler
                                            </a>
                                        <?php else: ?>
                                            <span class="text-success small">✓ Candidature envoyée</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Applications Tab -->
    <div class="tab-pane fade" id="applications" role="tabpanel">
        <div class="py-4">
            <?php if (empty($applications)): ?>
                <div class="text-center py-5">
                    <span class="fs-1">📝</span>
                    <h4 class="mt-3">Aucune candidature</h4>
                    <p class="text-muted">Vous n'avez encore postulé à aucune offre de stage.</p>
                    <button class="btn btn-primary" onclick="document.getElementById('offers-tab').click()">
                        Voir les offres disponibles
                    </button>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Offre</th>
                                <th>Entreprise</th>
                                <th>Date de candidature</th>
                                <th>CV</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $application): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo sanitize($application['offer_title']); ?></strong>
                                    </td>
                                    <td><?php echo sanitize($application['company_name']); ?></td>
                                    <td><?php echo formatDate($application['applied_date']); ?></td>
                                    <td>
                                        <?php if (!empty($application['cv_path'])): ?>
                                            <a href="<?php echo sanitize($application['cv_path']); ?>" 
                                               target="_blank" class="btn btn-outline-primary btn-sm">
                                                Voir CV
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Aucun CV</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">En attente</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
