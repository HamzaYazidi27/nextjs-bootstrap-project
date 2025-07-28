<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

// Require company role
requireRole('company');

$currentUser = getCurrentUser();

// Get company's offers with application counts
$stmt = $pdo->prepare("
    SELECT o.*, 
           (SELECT COUNT(*) FROM applications WHERE offer_id = o.id) as application_count
    FROM offers o 
    WHERE o.company_id = ? 
    ORDER BY o.posted_date DESC
");
$stmt->execute([$_SESSION['user_id']]);
$offers = $stmt->fetchAll();

// Get recent applications for company's offers
$stmt = $pdo->prepare("
    SELECT a.*, o.title as offer_title, u.name as student_name, u.email as student_email
    FROM applications a
    JOIN offers o ON a.offer_id = o.id
    JOIN users u ON a.student_id = u.id
    WHERE o.company_id = ?
    ORDER BY a.applied_date DESC
    LIMIT 10
");
$stmt->execute([$_SESSION['user_id']]);
$recentApplications = $stmt->fetchAll();

// Calculate statistics
$totalOffers = count($offers);
$totalApplications = array_sum(array_column($offers, 'application_count'));
$activeOffers = count(array_filter($offers, function($offer) {
    return $offer['application_count'] > 0;
}));

include 'includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>Espace Entreprise</h1>
                <p class="text-muted">Bienvenue, <?php echo sanitize($currentUser['name']); ?></p>
            </div>
            <div>
                <a href="post_offer.php" class="btn btn-primary">
                    Publier une nouvelle offre
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?php echo $totalOffers; ?></h4>
                        <p class="mb-0">Offres publiées</p>
                    </div>
                    <div class="align-self-center">
                        <span class="fs-1">📋</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?php echo $totalApplications; ?></h4>
                        <p class="mb-0">Candidatures reçues</p>
                    </div>
                    <div class="align-self-center">
                        <span class="fs-1">📄</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?php echo $activeOffers; ?></h4>
                        <p class="mb-0">Offres avec candidatures</p>
                    </div>
                    <div class="align-self-center">
                        <span class="fs-1">✨</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?php echo $totalApplications > 0 ? round($totalApplications / max($totalOffers, 1), 1) : 0; ?></h4>
                        <p class="mb-0">Moy. candidatures/offre</p>
                    </div>
                    <div class="align-self-center">
                        <span class="fs-1">📊</span>
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
            Mes Offres (<?php echo $totalOffers; ?>)
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="applications-tab" data-bs-toggle="tab" data-bs-target="#applications" type="button" role="tab">
            Candidatures Reçues (<?php echo $totalApplications; ?>)
        </button>
    </li>
</ul>

<div class="tab-content" id="dashboardTabsContent">
    <!-- Offers Tab -->
    <div class="tab-pane fade show active" id="offers" role="tabpanel">
        <div class="py-4">
            <?php if (empty($offers)): ?>
                <div class="text-center py-5">
                    <span class="fs-1">📝</span>
                    <h4 class="mt-3">Aucune offre publiée</h4>
                    <p class="text-muted">Commencez par publier votre première offre de stage.</p>
                    <a href="post_offer.php" class="btn btn-primary">
                        Publier une offre
                    </a>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($offers as $offer): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card offer-card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title"><?php echo sanitize($offer['title']); ?></h5>
                                        <span class="badge bg-primary"><?php echo $offer['application_count']; ?> candidature(s)</span>
                                    </div>
                                    
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
                                            Publié le <?php echo formatDate($offer['posted_date']); ?>
                                        </small>
                                        
                                        <div class="btn-group" role="group">
                                            <a href="view_applications.php?offer_id=<?php echo $offer['id']; ?>" 
                                               class="btn btn-outline-primary btn-sm">
                                                Candidatures
                                            </a>
                                        </div>
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
            <?php if (empty($recentApplications)): ?>
                <div class="text-center py-5">
                    <span class="fs-1">📭</span>
                    <h4 class="mt-3">Aucune candidature</h4>
                    <p class="text-muted">Vous n'avez encore reçu aucune candidature pour vos offres.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Étudiant</th>
                                <th>Offre</th>
                                <th>Date de candidature</th>
                                <th>CV</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentApplications as $application): ?>
                                <tr>
                                    <td>
                                        <div>
                                            <strong><?php echo sanitize($application['student_name']); ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo sanitize($application['student_email']); ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <strong><?php echo sanitize($application['offer_title']); ?></strong>
                                    </td>
                                    <td><?php echo formatDate($application['applied_date']); ?></td>
                                    <td>
                                        <?php if (!empty($application['cv_path'])): ?>
                                            <a href="<?php echo sanitize($application['cv_path']); ?>" 
                                               target="_blank" class="btn btn-outline-primary btn-sm">
                                                Télécharger CV
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Aucun CV</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="mailto:<?php echo sanitize($application['student_email']); ?>" 
                                           class="btn btn-outline-success btn-sm">
                                            Contacter
                                        </a>
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
