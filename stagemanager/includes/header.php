<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StageManager - Gestion des Stages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">StageManager</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if(isLoggedIn()): ?>
                        <?php if(checkRole('student')): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="student_dashboard.php">Mon Espace</a>
                            </li>
                        <?php elseif(checkRole('company')): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="company_dashboard.php">Espace Entreprise</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="post_offer.php">Publier une Offre</a>
                            </li>
                        <?php elseif(checkRole('admin')): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="admin_dashboard.php">Administration</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <?php echo sanitize($_SESSION['user_name'] ?? 'Utilisateur'); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="logout.php">Déconnexion</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Connexion</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="registration_student.php">Inscription Étudiant</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="registration_company.php">Inscription Entreprise</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container mt-4">
        <?php displayFlash(); ?>
