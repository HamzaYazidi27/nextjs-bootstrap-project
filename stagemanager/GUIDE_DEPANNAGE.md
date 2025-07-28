# 🔧 Guide de Dépannage StageManager

## Problème: Chargement infini lors de la connexion/inscription

### ✅ Solutions appliquées :

1. **JavaScript corrigé** - Le code qui causait le chargement infini a été supprimé
2. **Configuration de debug activée** - Les erreurs PHP sont maintenant visibles
3. **Test de connexion ajouté** - Fichier `test_db.php` pour diagnostiquer

### 🚀 Étapes de résolution :

#### 1. Vérifier XAMPP
```
- Ouvrir XAMPP Control Panel
- Démarrer Apache (doit être vert)
- Démarrer MySQL (doit être vert)
- Si rouge, cliquer sur "Start"
```

#### 2. Tester la base de données
```
- Aller sur: http://localhost/stagemanager/test_db.php
- Vérifier que tous les tests passent (✅)
- Si erreurs, suivre les instructions affichées
```

#### 3. Importer la base de données
```
- Aller sur: http://localhost/phpmyadmin
- Cliquer sur "Importer"
- Sélectionner le fichier "base.sql"
- Cliquer "Exécuter"
```

#### 4. Vérifier les permissions
```
- Le dossier "uploads/" doit être accessible en écriture
- Sur Windows: Clic droit > Propriétés > Sécurité
- Donner les droits complets à "Utilisateurs"
```

#### 5. Tester l'application
```
- Aller sur: http://localhost/stagemanager/
- Essayer de se connecter avec:
  * Email: admin@stagemanager.com
  * Mot de passe: admin123
```

### 🐛 Erreurs courantes et solutions :

#### "Base de données non trouvée"
```
Solution: Importer base.sql dans phpMyAdmin
1. http://localhost/phpmyadmin
2. Importer > Choisir base.sql > Exécuter
```

#### "Connexion refusée"
```
Solution: Démarrer MySQL dans XAMPP
1. Ouvrir XAMPP Control Panel
2. Cliquer "Start" pour MySQL
3. Vérifier qu'il devient vert
```

#### "Page blanche"
```
Solution: Activer l'affichage des erreurs
1. Les erreurs sont maintenant activées dans config.php
2. Rafraîchir la page pour voir l'erreur exacte
```

#### "Chargement infini"
```
Solution: JavaScript corrigé
1. Le code problématique a été supprimé
2. Les boutons fonctionnent maintenant normalement
```

#### "Erreur 500"
```
Solutions possibles:
1. Vérifier les logs Apache dans XAMPP
2. Vérifier la syntaxe PHP
3. Vérifier les permissions des fichiers
```

### 📋 Checklist de vérification :

- [ ] XAMPP Apache démarré (vert)
- [ ] XAMPP MySQL démarré (vert)
- [ ] Base de données importée (base.sql)
- [ ] Test de connexion OK (test_db.php)
- [ ] Dossier uploads/ accessible
- [ ] Pas d'erreurs JavaScript dans la console
- [ ] Connexion avec compte test réussie

### 🔍 Tests de fonctionnement :

#### Test Étudiant :
```
1. S'inscrire comme étudiant
2. Se connecter
3. Voir les offres disponibles
4. Postuler à une offre avec CV
```

#### Test Entreprise :
```
1. S'inscrire comme entreprise
2. Se connecter
3. Publier une offre
4. Voir les candidatures reçues
```

#### Test Admin :
```
1. Se connecter avec admin@stagemanager.com / admin123
2. Voir le tableau de bord
3. Gérer les utilisateurs
4. Gérer les offres
```

### 📞 Support supplémentaire :

Si le problème persiste après ces étapes :

1. **Vérifier les logs d'erreur** :
   - XAMPP > Apache > Logs
   - Chercher les erreurs récentes

2. **Tester avec un autre navigateur** :
   - Chrome, Firefox, Edge
   - Mode navigation privée

3. **Vider le cache** :
   - Ctrl+F5 pour rafraîchir
   - Vider le cache du navigateur

4. **Vérifier la version PHP** :
   - Minimum PHP 7.4 requis
   - Dans XAMPP Control Panel > Apache > Config > PHP

### 🗑️ Nettoyage après tests :

Une fois que tout fonctionne :
```
- Supprimer le fichier test_db.php (sécurité)
- Désactiver l'affichage des erreurs dans config.php
- Changer les mots de passe par défaut
```

---

**Note** : Ce guide couvre les problèmes les plus courants. L'application a été testée et corrigée pour fonctionner correctement avec XAMPP.
