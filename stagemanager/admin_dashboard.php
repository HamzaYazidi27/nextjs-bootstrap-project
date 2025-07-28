<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

// Require admin role
requireRole('admin');

$currentUser = getCurrentUser();

// Get statistics
$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'student'");
$totalStudents = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'company'");
$totalCompanies = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM offers");
$totalOffers = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM applications");
$totalApplications = $stmt->fetchColumn();

// Get recent users
$stmt = $pdo->prepare("
    SELECT * FROM users 
    WHERE role IN ('student', 'company') 
    ORDER BY created_at DESC 
    LIMIT 10
");
$stmt->execute();
$recentUsers = $stmt->fetchAll();

// Get recent offers
$stmt = $pdo->prepare("
    SELECT o.*, u.name as company_name 
    FROM offers o 
    JOIN users u ON o.company_id = u.id 
    ORDER BY o.posted_date DESC 
    LIMIT 10
");
$stmt->execute();
$recentOffers = $stmt->fetchAll();

// Get recent applications
$stmt = $pdo->prepare("
    SELECT a.*, o.title as offer_title, u1.name as student_name, u2.name as company_name
    FROM applications a
    JOIN offers o ON a.offer_id = o.id
    JOIN users u1 ON a.student_id = u1.id
    JOIN users u2 ON o.company_id = u2.id
    ORDER BY a.applied_date DESC
    LIMIT 10
");
$stmt->execute();
$recentApplications = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>Administration</h1>
                <p class="text-muted">Tableau de bord administrateur - <?php echo sanitize($currentUser['name']); ?></p>
            </div>
            <div>
                <div class="btn-group" role="group">
                    <a href="manage_users.php" class="btn btn-outline-primary">Gérer les utilisateurs</a>
                    <a href="manage_offers_simple.php" class="btn btn-outline-secondary">Gérer les offres</a>
                    <a href="debug_manage_offers.php" class="btn btn-outline-info">Debug Offres</a>
                </div>
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
                        <h4><?php echo $totalStudents; ?></h4>
                        <p class="mb-0">Étudiants inscrits</p>
                    </div>
                    <div class="align-self-center">
                        <span class="fs-1">👨‍🎓</span>
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
                        <h4><?php echo $totalCompanies; ?></h4>
                        <p class="mb-0">Entreprises</p>
                    </div>
                    <div class="align-self-center">
                        <span class="fs-1">🏢</span>
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
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?php echo $totalApplications; ?></h4>
                        <p class="mb-0">Candidatures</p>
                    </div>
                    <div class="align-self-center">
                        <span class="fs-1">📄</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">🚀 Actions Rapides</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="d-grid">
                            <a href="manage_users.php" class="btn btn-outline-primary">
                                👥 Gérer les Utilisateurs
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="d-grid">
                            <a href="manage_offers_simple.php" class="btn btn-outline-secondary">
                                📋 Gérer les Offres
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="d-grid">
                            <a href="debug_manage_offers.php" class="btn btn-outline-info">
                                🔍 Debug Système
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs for different sections -->
<ul class="nav nav-tabs" id="adminTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">
            Utilisateurs récents
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="offers-tab" data-bs-toggle="tab" data-bs-target="#offers" type="button" role="tab">
            Offres récentes
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="applications-tab" data-bs-toggle="tab" data-bs-target="#applications" type="button" role="tab">
            Candidatures récentes
        </button>
    </li>
</ul>

<div class="tab-content" id="adminTabsContent">
    <!-- Users Tab -->
    <div class="tab-pane fade show active" id="users" role="tabpanel">
        <div class="py-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Derniers utilisateurs inscrits</h5>
                <a href="manage_users.php" class="btn btn-outline-primary btn-sm">Voir tous</a>
            </div>
            
            <?php if (empty($recentUsers)): ?>
                <div class="text-center py-4">
                    <p class="text-muted">Aucun utilisateur inscrit</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Type</th>
                                <th>Date d'inscription</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentUsers as $user): ?>
                                <tr>
                                    <td><strong><?php echo sanitize($user['name']); ?></strong></td>
                                    <td><?php echo sanitize($user['email']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $user['role'] === 'student' ? 'bg-primary' : 'bg-success'; ?>">
                                            <?php echo $user['role'] === 'student' ? 'Étudiant' : 'Entreprise'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo formatDate($user['created_at']); ?></td>
                                    <td>
                                        <a href="manage_users.php?user_id=<?php echo $user['id']; ?>" 
                                           class="btn btn-outline-primary btn-sm">
                                            Voir détails
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
    
    <!-- Offers Tab -->
    <div class="tab-pane fade" id="offers" role="tabpanel">
        <div class="py-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Dernières offres publiées</h5>
                <a href="manage_offers_simple.php" class="btn btn-outline-secondary btn-sm">Voir toutes</a>
            </div>
            
            <?php if (empty($recentOffers)): ?>
                <div class="text-center py-4">
                    <p class="text-muted">Aucune offre publiée</p>
                    <div class="mt-3">
                        <p><strong>Pour ajouter des offres de test :</strong></p>
                        <ol class="text-start">
                            <li>Se connecter en tant qu'entreprise : <code>contact@techcorp.com</code> / <code>admin123</code></li>
                            <li>Aller sur "Espace Entreprise"</li>
                            <li>Cliquer sur "Publier une nouvelle offre"</li>
                        </ol>
                    </div>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Entreprise</th>
                                <th>Date de publication</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentOffers as $offer): ?>
                                <tr>
                                    <td><strong><?php echo sanitize($offer['title']); ?></strong></td>
                                    <td><?php echo sanitize($offer['company_name']); ?></td>
                                    <td><?php echo formatDate($offer['posted_date']); ?></td>
                                    <td>
                                        <a href="manage_offers_simple.php?offer_id=<?php echo $offer['id']; ?>" 
                                           class="btn btn-outline-secondary btn-sm">
                                            Voir détails
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
    
    <!-- Applications Tab -->
    <div class="tab-pane fade" id="applications" role="tabpanel">
        <div class="py-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Dernières candidatures</h5>
            </div>
            
            <?php if (empty($recentApplications)): ?>
                <div class="text-center py-4">
                    <p class="text-muted">Aucune candidature</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Étudiant</th>
                                <th>Offre</th>
                                <th>Entreprise</th>
                                <th>Date de candidature</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentApplications as $application): ?>
                                <tr>
                                    <td><strong><?php echo sanitize($application['student_name']); ?></strong></td>
                                    <td><?php echo sanitize($application['offer_title']); ?></td>
                                    <td><?php echo sanitize($application['company_name']); ?></td>
                                    <td><?php echo formatDate($application['applied_date']); ?></td>
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
