# StageManager - Système de Gestion des Stages

StageManager est une application web complète pour gérer les stages entre étudiants, entreprises et administrateurs. Développée en PHP avec MySQL et Bootstrap.

## 🚀 Fonctionnalités

### Pour les Étudiants
- ✅ Inscription et connexion sécurisée
- ✅ Consultation des offres de stage disponibles
- ✅ Candidature avec téléchargement de CV
- ✅ Suivi des candidatures envoyées
- ✅ Interface responsive et moderne

### Pour les Entreprises
- ✅ Inscription et connexion sécurisée
- ✅ Publication d'offres de stage
- ✅ Gestion des candidatures reçues
- ✅ Téléchargement des CV des candidats
- ✅ Contact direct avec les étudiants

### Pour les Administrateurs
- ✅ Tableau de bord avec statistiques
- ✅ Gestion des utilisateurs (étudiants et entreprises)
- ✅ Gestion des offres de stage
- ✅ Vue d'ensemble des candidatures
- ✅ Outils de modération

## 🛠️ Technologies Utilisées

- **Frontend**: HTML5, CSS3, Bootstrap 5, JavaScript
- **Backend**: PHP 7.4+
- **Base de données**: MySQL 5.7+
- **Serveur local**: XAMPP
- **Sécurité**: Hachage des mots de passe, protection CSRF, validation des données

## 📋 Prérequis

- XAMPP (Apache + MySQL + PHP)
- Navigateur web moderne
- Éditeur de texte (optionnel)

## 🔧 Installation

### 🚀 Installation Automatique (Recommandée)

1. **Installer XAMPP**
   - Téléchargez depuis [https://www.apachefriends.org/](https://www.apachefriends.org/)
   - Installez et démarrez Apache + MySQL

2. **Copier les fichiers**
   ```
   C:\xampp\htdocs\stagemanager\  (Windows)
   /opt/lampp/htdocs/stagemanager/  (Linux)
   /Applications/XAMPP/htdocs/stagemanager/  (macOS)
   ```

3. **Lancer l'installation automatique**
   - Accédez à : [http://localhost/stagemanager/install.php](http://localhost/stagemanager/install.php)
   - Suivez les 3 étapes guidées
   - L'installation se fait automatiquement !

### 🔍 Vérification du système
Après installation, vérifiez que tout fonctionne :
- [http://localhost/stagemanager/check_system.php](http://localhost/stagemanager/check_system.php)

### 🛠️ Installation Manuelle (Alternative)

Si vous préférez l'installation manuelle :

1. **Créer la base de données**
   - Ouvrez phpMyAdmin : [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
   - Importez le fichier `base.sql`

2. **Vérifier la configuration**
   - Fichier `config/config.php` (normalement correct par défaut)

3. **Permissions**
   - Dossier `uploads/` accessible en écriture

## 🌐 Accès à l'application

Ouvrez votre navigateur et accédez à : [http://localhost/stagemanager/](http://localhost/stagemanager/)

## 👤 Comptes de démonstration

L'application inclut des comptes de test prêts à utiliser :

### Administrateur
- **Email**: admin@stagemanager.com
- **Mot de passe**: admin123

### Entreprise
- **Email**: contact@techcorp.com
- **Mot de passe**: admin123

### Étudiant
- **Email**: jean.dupont@email.com
- **Mot de passe**: admin123

## 📁 Structure du projet

```
stagemanager/
├── config/
│   └── config.php              # Configuration base de données
├── includes/
│   ├── header.php              # En-tête commun
│   ├── footer.php              # Pied de page commun
│   └── functions.php           # Fonctions utilitaires
├── css/
│   └── custom.css              # Styles personnalisés
├── js/
│   └── custom.js               # Scripts JavaScript
├── uploads/                    # Dossier pour les CV uploadés
├── base.sql                    # Structure de la base de données
├── index.php                   # Page d'accueil
├── login.php                   # Page de connexion
├── logout.php                  # Déconnexion
├── registration_student.php    # Inscription étudiant
├── registration_company.php    # Inscription entreprise
├── student_dashboard.php       # Tableau de bord étudiant
├── company_dashboard.php       # Tableau de bord entreprise
├── admin_dashboard.php         # Tableau de bord admin
├── apply_offer.php             # Candidature à une offre
├── post_offer.php              # Publication d'offre
├── view_applications.php       # Vue des candidatures
├── manage_users.php            # Gestion utilisateurs (admin)
├── manage_offers.php           # Gestion offres (admin)
└── README.md                   # Ce fichier
```

## 🗄️ Base de données

### Tables principales

#### `users`
- Stockage des utilisateurs (étudiants, entreprises, admin)
- Mots de passe hachés avec `password_hash()`
- Rôles : 'student', 'company', 'admin'

#### `offers`
- Offres de stage publiées par les entreprises
- Liaison avec la table `users` (company_id)

#### `applications`
- Candidatures des étudiants aux offres
- Stockage du chemin vers le CV uploadé
- Contrainte d'unicité (un étudiant ne peut postuler qu'une fois par offre)

## 🔒 Sécurité

- **Authentification**: Sessions PHP sécurisées
- **Mots de passe**: Hachage avec `password_hash()` et `password_verify()`
- **Validation**: Sanitisation de toutes les entrées utilisateur
- **Upload**: Validation stricte des fichiers CV (PDF, DOC, DOCX)
- **Autorisations**: Contrôle d'accès basé sur les rôles
- **Base de données**: Requêtes préparées (PDO) contre l'injection SQL

## 🎨 Interface utilisateur

- **Design**: Interface moderne avec Bootstrap 5
- **Responsive**: Compatible mobile, tablette et desktop
- **UX**: Navigation intuitive et feedback utilisateur
- **Accessibilité**: Respect des standards d'accessibilité web
- **Performance**: Chargement optimisé des ressources

## 🚀 Fonctionnalités avancées

- **Recherche**: Filtrage des offres et utilisateurs
- **Statistiques**: Tableaux de bord avec métriques
- **Notifications**: Messages flash pour les actions utilisateur
- **Validation**: Validation côté client et serveur
- **Gestion des erreurs**: Gestion robuste des erreurs

## 🔧 Personnalisation

### Modifier les couleurs
Éditez le fichier `css/custom.css` et modifiez les variables CSS :
```css
:root {
    --primary-color: #0d6efd;
    --secondary-color: #6c757d;
    /* ... autres couleurs */
}
```

### Ajouter des fonctionnalités
1. Créez de nouveaux fichiers PHP dans le dossier principal
2. Utilisez les fonctions utilitaires dans `includes/functions.php`
3. Respectez la structure existante pour la cohérence

## 🐛 Dépannage

### Erreur de connexion à la base de données
- Vérifiez que MySQL est démarré dans XAMPP
- Contrôlez les paramètres dans `config/config.php`
- Assurez-vous que la base de données `stagemanager_db` existe

### Problème d'upload de CV
- Vérifiez les permissions du dossier `uploads/`
- Contrôlez la taille maximale d'upload dans `php.ini`
- Formats acceptés : PDF, DOC, DOCX (max 5MB)

### Page blanche ou erreur 500
- Activez l'affichage des erreurs PHP
- Vérifiez les logs d'erreur Apache
- Contrôlez la syntaxe PHP des fichiers modifiés

## 📞 Support

Pour toute question ou problème :
1. Vérifiez ce README
2. Consultez les commentaires dans le code
3. Testez avec les comptes de démonstration

## 📄 Licence

Ce projet est développé à des fins éducatives et de démonstration.

---

**StageManager** - Système complet de gestion des stages
Développé avec ❤️ en PHP, MySQL et Bootstrap
