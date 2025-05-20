# IUM-SHINE - Système de Vote

IUM-SHINE est une application web moderne permettant de gérer des votes pour différentes catégories de candidats. Le système est conçu pour être facile à utiliser et offre une interface administrateur complète.

## Fonctionnalités

- Interface publique pour consulter les candidats
- Interface d'administration sécurisée
- Gestion des catégories (ajout, modification, suppression)
- Gestion des candidats (ajout, modification, suppression)
- Gestion des votes (ajout/suppression multiple de votes)
- Affichage des statistiques en temps réel
- Design moderne et responsive avec thème personnalisé
- Protection contre les votes multiples
- Système de gestion des photos de candidats

## Prérequis

- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web (Apache, Nginx, etc.)
- Extension PHP PDO
- Extension PHP GD (pour le traitement des images)

## Installation

1. Clonez le dépôt dans votre répertoire web :
```bash
git clone https://github.com/votre-username/ium-shine.git
cd ium-shine
```

2. Créez une base de données MySQL et importez le fichier `database.sql` :
```sql
CREATE DATABASE ium_shine;
USE ium_shine;
source database.sql;
```

3. Configurez les paramètres de connexion à la base de données dans `config/database.php` :
```php
private $host = "localhost";
private $db_name = "ium_shine";
private $username = "votre_username";
private $password = "votre_password";
```

4. Assurez-vous que le dossier `uploads` est accessible en écriture :
```bash
chmod 777 uploads
```

## Structure du Projet

```
ium-shine/
├── config/
│   └── database.php
├── controllers/
│   ├── AdminController.php
│   ├── CandidateController.php
│   ├── CategoryController.php
│   └── VoteController.php
├── views/
│   ├── admin/
│   │   ├── categories/
│   │   ├── candidates/
│   │   ├── votes/
│   │   ├── dashboard.php
│   │   ├── login.php
│   │   └── settings.php
│   ├── category.php
│   └── home.php
├── uploads/
├── admin.php
├── index.php
└── database.sql
```

## Utilisation

### Accès Public
- Accédez à l'application via votre navigateur : `http://votre-domaine/ium-shine`
- Parcourez les différentes catégories
- Consultez les candidats et leurs informations

### Interface Administrateur
- Accédez à l'interface d'administration : `http://votre-domaine/ium-shine/admin.php`
- Identifiants par défaut :
  - Nom d'utilisateur : `admin`
  - Mot de passe : `admin123`

### Fonctionnalités Administrateur
- Gestion des catégories
  - Ajouter une nouvelle catégorie
  - Modifier une catégorie existante
  - Supprimer une catégorie (si aucun candidat n'y est associé)
- Gestion des candidats
  - Ajouter un nouveau candidat avec photo
  - Modifier les informations d'un candidat
  - Supprimer un candidat
- Gestion des votes
  - Ajouter/supprimer plusieurs votes à la fois
  - Consulter les statistiques par catégorie
- Paramètres
  - Modifier les identifiants administrateur

## Sécurité

- Changez le mot de passe administrateur par défaut après la première connexion
- Les photos sont stockées dans un dossier sécurisé
- Protection contre les votes multiples
- Validation des données côté serveur
- Protection XSS avec htmlspecialchars
- Requêtes préparées pour éviter les injections SQL

## Contribution

Les contributions sont les bienvenues ! N'hésitez pas à :
1. Fork le projet
2. Créer une branche pour votre fonctionnalité
3. Commiter vos changements
4. Pousser vers la branche
5. Ouvrir une Pull Request

## Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails. 