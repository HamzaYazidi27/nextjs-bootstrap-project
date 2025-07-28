<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

// Require admin role
requireRole('admin');

// Handle user deletion
if (isset($_GET['delete']) && isset($_GET['user_id'])) {
    $user_id = (int)$_GET['user_id'];
    
    try {
        // Don't allow deletion of admin users
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        
        if ($user && $user['role'] !== 'admin') {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            if ($stmt->execute([$user_id])) {
                setFlash('Utilisateur supprimé avec succès.', 'success');
            } else {
                setFlash('Erreur lors de la suppression.', 'error');
            }
        } else {
            setFlash('Impossible de supprimer cet utilisateur.', 'error');
        }
    } catch (PDOException $e) {
        setFlash('Erreur lors de la suppression.', 'error');
    }
    
    header('Location: manage_users.php');
    exit();
}

// Get all users with statistics
$stmt = $pdo->prepare("
    SELECT u.*, 
           CASE 
               WHEN u.role = 'student' THEN (SELECT COUNT(*) FROM applications WHERE student_id = u.id)
               WHEN u.role = 'company' THEN (SELECT COUNT(*) FROM offers WHERE company_id = u.id)
               ELSE 0
           END as activity_count
    FROM users u 
    WHERE u.role IN ('student', 'company')
    ORDER BY u.created_at DESC
");
$stmt->execute();
$users = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>Gestion des Utilisateurs</h1>
                <p class="text-muted">Gérez les comptes étudiants et entreprises</p>
            </div>
            <div>
                <a href="admin_dashboard.php" class="btn btn-outline-secondary">
                    Retour au tableau de bord
                </a>
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
                        <h4><?php echo count(array_filter($users, function($u) { return $u['role'] === 'student'; })); ?></h4>
                        <p class="mb-0">Étudiants</p>
                    </div>
                    <div class="align-self-center">
                        <span class="fs-1">👨‍🎓</span>
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
                        <h4><?php echo count(array_filter($users, function($u) { return $u['role'] === 'company'; })); ?></h4>
                        <p class="mb-0">Entreprises</p>
                    </div>
                    <div class="align-self-center">
                        <span class="fs-1">🏢</span>
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
                        <h4><?php echo count($users); ?></h4>
                        <p class="mb-0">Total utilisateurs</p>
                    </div>
                    <div class="align-self-center">
                        <span class="fs-1">👥</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <input type="text" id="searchInput" class="form-control" placeholder="Rechercher un utilisateur...">
            </div>
            <div class="col-md-3">
                <select id="roleFilter" class="form-select">
                    <option value="">Tous les types</option>
                    <option value="student">Étudiants</option>
                    <option value="company">Entreprises</option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="sortBy" class="form-select">
                    <option value="created_at">Date d'inscription</option>
                    <option value="name">Nom</option>
                    <option value="activity_count">Activité</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Liste des utilisateurs</h5>
    </div>
    <div class="card-body">
        <?php if (empty($users)): ?>
            <div class="text-center py-4">
                <span class="fs-1">👥</span>
                <h4 class="mt-3">Aucun utilisateur</h4>
                <p class="text-muted">Il n'y a encore aucun utilisateur inscrit.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover" id="usersTable">
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Type</th>
                            <th>Date d'inscription</th>
                            <th>Activité</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr class="user-row" 
                                data-name="<?php echo strtolower(sanitize($user['name'])); ?>"
                                data-email="<?php echo strtolower(sanitize($user['email'])); ?>"
                                data-role="<?php echo $user['role']; ?>"
                                data-created="<?php echo $user['created_at']; ?>"
                                data-activity="<?php echo $user['activity_count']; ?>">
                                <td>
                                    <div>
                                        <strong><?php echo sanitize($user['name']); ?></strong>
                                        <br>
                                        <small class="text-muted"><?php echo sanitize($user['email']); ?></small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge <?php echo $user['role'] === 'student' ? 'bg-primary' : 'bg-success'; ?>">
                                        <?php echo $user['role'] === 'student' ? 'Étudiant' : 'Entreprise'; ?>
                                    </span>
                                </td>
                                <td><?php echo formatDate($user['created_at']); ?></td>
                                <td>
                                    <span class="badge bg-info">
                                        <?php 
                                        if ($user['role'] === 'student') {
                                            echo $user['activity_count'] . ' candidature(s)';
                                        } else {
                                            echo $user['activity_count'] . ' offre(s)';
                                        }
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-outline-info btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#userModal<?php echo $user['id']; ?>">
                                            Détails
                                        </button>
                                        <a href="manage_users.php?delete=1&user_id=<?php echo $user['id']; ?>" 
                                           class="btn btn-outline-danger btn-sm btn-delete"
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
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

<!-- User Detail Modals -->
<?php foreach ($users as $user): ?>
    <div class="modal fade" id="userModal<?php echo $user['id']; ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Détails de l'utilisateur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-4"><strong>Nom:</strong></div>
                        <div class="col-sm-8"><?php echo sanitize($user['name']); ?></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-4"><strong>Email:</strong></div>
                        <div class="col-sm-8"><?php echo sanitize($user['email']); ?></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-4"><strong>Type:</strong></div>
                        <div class="col-sm-8">
                            <span class="badge <?php echo $user['role'] === 'student' ? 'bg-primary' : 'bg-success'; ?>">
                                <?php echo $user['role'] === 'student' ? 'Étudiant' : 'Entreprise'; ?>
                            </span>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-4"><strong>Inscription:</strong></div>
                        <div class="col-sm-8"><?php echo formatDate($user['created_at']); ?></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-4"><strong>Activité:</strong></div>
                        <div class="col-sm-8">
                            <?php 
                            if ($user['role'] === 'student') {
                                echo $user['activity_count'] . ' candidature(s) envoyée(s)';
                            } else {
                                echo $user['activity_count'] . ' offre(s) publiée(s)';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const roleFilter = document.getElementById('roleFilter');
    const sortBy = document.getElementById('sortBy');
    const userRows = document.querySelectorAll('.user-row');
    
    function filterAndSort() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedRole = roleFilter.value;
        const sortField = sortBy.value;
        
        // Filter rows
        const visibleRows = [];
        userRows.forEach(row => {
            const name = row.dataset.name;
            const email = row.dataset.email;
            const role = row.dataset.role;
            
            const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
            const matchesRole = !selectedRole || role === selectedRole;
            
            if (matchesSearch && matchesRole) {
                row.style.display = '';
                visibleRows.push(row);
            } else {
                row.style.display = 'none';
            }
        });
        
        // Sort visible rows
        visibleRows.sort((a, b) => {
            let aValue, bValue;
            
            switch(sortField) {
                case 'name':
                    aValue = a.dataset.name;
                    bValue = b.dataset.name;
                    return aValue.localeCompare(bValue);
                case 'activity_count':
                    aValue = parseInt(a.dataset.activity);
                    bValue = parseInt(b.dataset.activity);
                    return bValue - aValue; // Descending
                case 'created_at':
                default:
                    aValue = new Date(a.dataset.created);
                    bValue = new Date(b.dataset.created);
                    return bValue - aValue; // Most recent first
            }
        });
        
        // Reorder rows in DOM
        const tbody = document.querySelector('#usersTable tbody');
        visibleRows.forEach(row => {
            tbody.appendChild(row);
        });
    }
    
    searchInput.addEventListener('keyup', filterAndSort);
    roleFilter.addEventListener('change', filterAndSort);
    sortBy.addEventListener('change', filterAndSort);
});
</script>

<?php include 'includes/footer.php'; ?>
