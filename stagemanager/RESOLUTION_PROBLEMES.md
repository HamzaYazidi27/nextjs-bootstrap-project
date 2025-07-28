# 🚨 Résolution des Problèmes Identifiés

## Problème 1: Impossible d'importer des CV

### ❌ Erreur observée:
```
Warning: move_uploaded_file(uploads/...): Failed to open stream: No such file or directory
Warning: move_uploaded_file(): Unable to move "C:\xampp\tmp\phpFE5.tmp" to "uploads/..."
```

### ✅ Solutions appliquées:

#### 1. Script de correction automatique
```
http://localhost/stagemanager/fix_permissions.php
```
Ce script va:
- Créer le dossier uploads s'il manque
- Corriger les permissions automatiquement
- Tester l'écriture
- Créer le fichier .htaccess de sécurité

#### 2. Correction manuelle (si le script ne suffit pas)

**Sur Windows:**
1. Aller dans le dossier `C:\xampp\htdocs\stagemanager\`
2. Clic droit sur le dossier `uploads` > Propriétés
3. Onglet Sécurité > Modifier
4. Sélectionner "Utilisateurs" > Cocher "Contrôle total"
5. Appliquer > OK

**Sur Linux/Mac:**
```bash
chmod 755 uploads/
chown www-data:www-data uploads/  # Si nécessaire
```

#### 3. Vérification PHP
Vérifier dans `php.ini` (XAMPP > Apache > Config > PHP):
```ini
file_uploads = On
upload_max_filesize = 5M
post_max_size = 8M
upload_tmp_dir = "C:\xampp\tmp"  # Windows
```

---

## Problème 2: Page "Gérer les offres" ne marche pas

### ❌ Problème:
Le fichier `manage_offers.php` était corrompu ou vide

### ✅ Solution appliquée:
- Fichier `manage_offers.php` recréé complètement
- Gestion d'erreurs améliorée
- Interface admin fonctionnelle

### 🧪 Test:
1. Se connecter en tant qu'admin: `admin@stagemanager.com` / `admin123`
2. Aller sur "Administration" > "Gérer les offres"
3. La page doit maintenant s'afficher correctement

---

## 🔍 Vérifications à effectuer

### 1. Test complet du système
```
http://localhost/stagemanager/check_system.php
```

### 2. Test spécifique de la base de données
```
http://localhost/stagemanager/test_db.php
```

### 3. Test des permissions
```
http://localhost/stagemanager/fix_permissions.php
```

---

## 🚀 Tests de fonctionnement

### Test Upload CV:
1. Se connecter comme étudiant: `jean.dupont@email.com` / `admin123`
2. Aller sur "Mon Espace"
3. Cliquer sur "Postuler" sur une offre
4. Essayer d'uploader un fichier PDF
5. ✅ Doit fonctionner sans erreur

### Test Admin - Gérer les offres:
1. Se connecter comme admin: `admin@stagemanager.com` / `admin123`
2. Aller sur "Administration"
3. Cliquer sur "Gérer les offres"
4. ✅ La page doit s'afficher avec la liste des offres

---

## 📋 Checklist de résolution

- [ ] Exécuter `fix_permissions.php`
- [ ] Vérifier que le dossier `uploads/` existe et est accessible
- [ ] Tester l'upload d'un CV
- [ ] Vérifier que `manage_offers.php` fonctionne
- [ ] Tester la connexion admin
- [ ] Exécuter `check_system.php` pour validation finale

---

## 🆘 Si les problèmes persistent

### Diagnostic avancé:
1. **Vérifier les logs Apache** (XAMPP Control Panel > Apache > Logs)
2. **Vérifier les logs PHP** (dans le dossier XAMPP/php/logs/)
3. **Tester avec un autre navigateur** (mode navigation privée)

### Réinstallation propre:
1. Sauvegarder la base de données (Export depuis phpMyAdmin)
2. Supprimer le dossier `stagemanager`
3. Recopier les fichiers
4. Réimporter la base de données
5. Exécuter `install.php`

---

## 📞 Support

Si vous rencontrez encore des problèmes:
1. Consultez `GUIDE_DEPANNAGE.md` pour plus de détails
2. Exécutez tous les scripts de diagnostic
3. Vérifiez que XAMPP (Apache + MySQL) est bien démarré

---

**Note:** Ces corrections ont été testées et devraient résoudre les problèmes identifiés. N'hésitez pas à supprimer les fichiers de diagnostic une fois que tout fonctionne.
