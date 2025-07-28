<?php
// Version simplifiée de manage_offers.php pour debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once 'config/config.php';
    require_once 'includes/functions.php';
    
    // Require admin role
    requireRole('admin');
    
    // Handle offer deletion
    if (isset($_GET['delete']) && isset($_GET['offer_id'])) {
        $offer_id = (int)$_GET['offer_id'];
        
        try {
            $stmt = $pdo->prepare("DELETE FROM offers WHERE id = ?");
            if ($stmt->execute([$offer_id])) {
                setFlash('Offre supprimée avec succès.', 'success');
            } else {
                setFlash('Erreur lors de la suppression.', 'error');
            }
        } catch (PDOException $e) {
            setFlash('Erreur lors de la suppression: ' . $e->getMessage(), 'error');
        }
        
        header('Location: manage_offers_simple.php');
        exit();
    }
    
    // Get all offers
    $offers = [];
    try {
        $stmt = $pdo->query("
            SELECT o.*, u.name as company_name, u.email as company_email
            FROM offers o 
            JOIN users u ON o.company_id = u.id 
            ORDER BY o.posted_date DESC
        ");
        $offers = $stmt->fetchAll();
        
        // Add application count for each offer
        foreach ($offers as &$offer) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM applications WHERE offer_id = ?");
            $stmt->execute([$offer['id']]);
            $offer['application_count'] = $stmt->fetchColumn();
        }
        
    } catch (PDOException $e) {
        $error_message = "Erreur lors du chargement des offres: " . $e->getMessage();
    }
    
} catch (Exception $e) {
    die("Erreur critique: " . $e->getMessage());
}

include 'includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>Gestion des Offres (Version Simple)</h1>
                <p class="text-muted">Gérez toutes les offres de stage publiées</p>
            </div>
            <div>
                <a href="admin_dashboard.php" class="btn btn-outline-secondary">Retour</a>
                <a href="manage_offers.php" class="btn btn-primary">Version Complète</a>
            </div>
        </div>
    </div>
</div>

<?php if (isset($error_message)): ?>
    <div class="alert alert-danger">
        <?php echo $error_message; ?>
    </div>
<?php endif; ?>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h4><?php echo count($offers); ?></h4>
                <p class="mb-0">Total offres</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h4><?php echo count(array_filter($offers, function($o) { return $o['application_count'] > 0; })); ?></h4>
                <p class="mb-0">Avec candidatures</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h4><?php echo array_sum(array_column($offers, 'application_count')); ?></h4>
                <p class="mb-0">Total candidatures</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h4><?php echo count($offers) > 0 ? round(array_sum(array_column($offers, 'application_count')) / count($offers), 1) : 0; ?></h4>
                <p class="mb-0">Moyenne</p>
            </div>
        </div>
    </div>
</div>

<!-- Offers List -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Liste des offres (<?php echo count($offers); ?>)</h5>
    </div>
    <div class="card-body">
        <?php if (empty($offers)): ?>
            <div class="text-center py-5">
                <span class="fs-1">📋</span>
                <h4 class="mt-3">Aucune offre</h4>
                <p class="text-muted">Aucune offre de stage n'a encore été publiée.</p>
                <div class="mt-3">
                    <p><strong>Pour ajouter des offres de test :</strong></p>
                    <ol>
                        <li>Se connecter en tant qu'entreprise : <code>contact@techcorp.com</code> / <code>admin123</code></li>
                        <li>Aller sur "Espace Entreprise"</li>
                        <li>Cliquer sur "Publier une nouvelle offre"</li>
                        <li>Remplir le formulaire et publier</li>
                    </ol>
                </div>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Entreprise</th>
                            <th>Date</th>
                            <th>Candidatures</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($offers as $offer): ?>
                            <tr>
                                <td><?php echo $offer['id']; ?></td>
                                <td>
                                    <strong><?php echo sanitize($offer['title']); ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        <?php echo sanitize(substr($offer['description'], 0, 80)) . '...'; ?>
                                    </small>
                                </td>
                                <td>
                                    <?php echo sanitize($offer['company_name']); ?>
                                    <br>
                                    <small class="text-muted"><?php echo sanitize($offer['company_email']); ?></small>
                                </td>
                                <td>
                                    <small><?php echo formatDate($offer['posted_date']); ?></small>
                                </td>
                                <td>
                                    <span class="badge <?php echo $offer['application_count'] > 0 ? 'bg-success' : 'bg-secondary'; ?>">
                                        <?php echo $offer['application_count']; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="view_applications.php?offer_id=<?php echo $offer['id']; ?>" 
                                           class="btn btn-outline-primary">
                                            Candidatures
                                        </a>
                                        <a href="manage_offers_simple.php?delete=1&offer_id=<?php echo $offer['id']; ?>" 
                                           class="btn btn-outline-danger"
                                           onclick="return confirm('Supprimer cette offre ?')">
                                            Supprimer
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="mt-4">
    <div class="alert alert-info">
        <h6>🔧 Debug Info:</h6>
        <ul class="mb-0">
            <li>Utilisateur connecté : <?php echo sanitize($_SESSION['user_name'] ?? 'Non défini'); ?></li>
            <li>Rôle : <?php echo sanitize($_SESSION['role'] ?? 'Non défini'); ?></li>
            <li>Nombre d'offres : <?php echo count($offers); ?></li>
            <li><a href="debug_manage_offers.php">Diagnostic complet</a></li>
        </ul>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
