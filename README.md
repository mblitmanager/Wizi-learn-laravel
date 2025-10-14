# Wizi Learn - Plateforme d'apprentissage

## Présentation du projet

Wizi Learn est une plateforme d'apprentissage interactive et ludique, conçue pour faciliter la gestion des formations, des quiz, des utilisateurs (stagiaires, formateurs, commerciaux, etc.) et des contenus pédagogiques. Le projet propose un back office complet permettant aux administrateurs de gérer l’ensemble des ressources et des utilisateurs.

## Fonctionnalités principales

- Gestion des utilisateurs (stagiaires, formateurs, commerciaux, PRC)
- Gestion des formations et du catalogue de formations
- Création, importation et exportation de quiz
- Gestion des médias (images, vidéos, PDF)
- Notifications et statistiques
- Import/export de données via fichiers Excel

## Installation

1. **Cloner le dépôt**
   ```sh
   git clone <url-du-repo>
   cd Wizi-learn-laravel
   ```

2. **Installer les dépendances**
   ```sh
   composer install
   npm install
   ```

3. **Configurer l’environnement**
   - Copier `.env.example` en `.env` et adapter les variables (base de données, mail, etc.)
   - Générer la clé d’application :
     ```sh
     php artisan key:generate
     ```

4. **Migrer la base de données**
   ```sh
   php artisan migrate --seed
   ```

5. **Compiler les assets**
   ```sh
   npm run build
   ```

6. **Démarrer le serveur**
   ```sh
   php artisan serve
   ```

## Utilisation du back office

1. **Connexion**
   - Accédez à l’URL du back office (ex : `http://localhost:8000/admin`)
   - Connectez-vous avec un compte administrateur.

2. **Gestion des utilisateurs**
   - Menu « Utilisateurs » : ajouter, modifier, supprimer stagiaires, formateurs, commerciaux, PRC.
   - Import possible via fichiers Excel (modèles téléchargeables depuis chaque section).

3. **Gestion des formations**
   - Menu « Catalogue de formations » : créer, éditer, dupliquer ou supprimer des formations.
   - Ajout de médias (images, vidéos, PDF) et de documents de cursus.

4. **Gestion des quiz**
   - Menu « Quiz » : créer, éditer, importer/exporter des quiz.
   - Import de questions via Excel, export au format JSON.

5. **Statistiques et notifications**
   - Tableau de bord : visualiser les statistiques de participation, filtrer par utilisateur ou formation.
   - Notifications : consulter et marquer comme lues les notifications reçues.

6.