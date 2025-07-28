<?php include 'includes/header.php'; ?>

<div class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Bienvenue sur StageManager</h1>
                <p class="lead mb-4">
                    La plateforme complète pour gérer vos stages entre étudiants, entreprises et administrateurs. 
                    Trouvez le stage parfait ou recrutez les meilleurs talents.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <?php if (!isLoggedIn()): ?>
                        <a href="login.php" class="btn btn-light btn-lg">Connexion</a>
                        <a href="registration_student.php" class="btn btn-outline-light btn-lg">Inscription Étudiant</a>
                        <a href="registration_company.php" class="btn btn-outline-light btn-lg">Inscription Entreprise</a>
                    <?php else: ?>
                        <?php if (checkRole('student')): ?>
                            <a href="student_dashboard.php" class="btn btn-light btn-lg">Mon Espace Étudiant</a>
                        <?php elseif (checkRole('company')): ?>
                            <a href="company_dashboard.php" class="btn btn-light btn-lg">Espace Entreprise</a>
                        <?php elseif (checkRole('admin')): ?>
                            <a href="admin_dashboard.php" class="btn btn-light btn-lg">Administration</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="mt-5 mt-lg-0">
                    <div class="row g-4">
                        <div class="col-6">
                            <div class="stats-card bg-white text-dark">
                                <div class="stats-number text-primary">
                                    <?php
                                    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'student'");
                                    echo $stmt->fetchColumn();
                                    ?>
                                </div>
                                <div>Étudiants</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-card bg-white text-dark">
                                <div class="stats-number text-success">
                                    <?php
                                    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'company'");
                                    echo $stmt->fetchColumn();
                                    ?>
                                </div>
                                <div>Entreprises</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-card bg-white text-dark">
                                <div class="stats-number text-warning">
                                    <?php
                                    $stmt = $pdo->query("SELECT COUNT(*) FROM offers");
                                    echo $stmt->fetchColumn();
                                    ?>
                                </div>
                                <div>Offres</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-card bg-white text-dark">
                                <div class="stats-number text-info">
                                    <?php
                                    $stmt = $pdo->query("SELECT COUNT(*) FROM applications");
                                    echo $stmt->fetchColumn();
                                    ?>
                                </div>
                                <div>Candidatures</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <span class="fs-4">👨‍🎓</span>
                        </div>
                    </div>
                    <h5 class="card-title">Pour les Étudiants</h5>
                    <p class="card-text">
                        Trouvez le stage parfait parmi de nombreuses offres d'entreprises. 
                        Postulez facilement en téléchargeant votre CV.
                    </p>
                    <?php if (!isLoggedIn()): ?>
                        <a href="registration_student.php" class="btn btn-primary">S'inscrire</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <span class="fs-4">🏢</span>
                        </div>
                    </div>
                    <h5 class="card-title">Pour les Entreprises</h5>
                    <p class="card-text">
                        Publiez vos offres de stage et trouvez les meilleurs talents. 
                        Gérez facilement vos candidatures.
                    </p>
                    <?php if (!isLoggedIn()): ?>
                        <a href="registration_company.php" class="btn btn-success">S'inscrire</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <span class="fs-4">⚙️</span>
                        </div>
                    </div>
                    <h5 class="card-title">Administration</h5>
                    <p class="card-text">
                        Gérez les utilisateurs, supervisez les offres et consultez les statistiques 
                        de la plateforme.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!isLoggedIn()): ?>
<div class="bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="mb-4">Dernières Offres de Stage</h2>
                <p class="text-muted mb-5">Découvrez quelques-unes des dernières opportunités disponibles</p>
                
                <?php
                $stmt = $pdo->prepare("
                    SELECT o.*, u.name as company_name 
                    FROM offers o 
                    JOIN users u ON o.company_id = u.id 
                    ORDER BY o.posted_date DESC 
                    LIMIT 3
                ");
                $stmt->execute();
                $offers = $stmt->fetchAll();
                ?>
                
                <div class="row">
                    <?php foreach ($offers as $offer): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card offer-card h-100">
                                <div class="card-body">
                                    <h6 class="card-title"><?php echo sanitize($offer['title']); ?></h6>
                                    <p class="text-muted small mb-2"><?php echo sanitize($offer['company_name']); ?></p>
                                    <p class="card-text small">
                                        <?php echo sanitize(substr($offer['description'], 0, 100)) . '...'; ?>
                                    </p>
                                    <small class="text-muted">
                                        Publié le <?php echo formatDate($offer['posted_date']); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="mt-4">
                    <a href="registration_student.php" class="btn btn-primary">
                        Inscrivez-vous pour voir toutes les offres
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
