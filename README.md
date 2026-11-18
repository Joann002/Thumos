# Thumos — Tableau de Bord de Vie

[![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20)](https://laravel.com)
[![Vue](https://img.shields.io/badge/Vue-3-42B883)](https://vuejs.org)
[![Inertia](https://img.shields.io/badge/Inertia.js-3-9553E9)](https://inertiajs.com)
[![Vite](https://img.shields.io/badge/Vite-8-646CFF)](https://vitejs.dev)
[![License](https://img.shields.io/badge/License-MIT-blue)](#licence)

Application web pour centraliser ses objectifs personnels, suivre ses habitudes
quotidiennes avec des séries (« streaks »), et tenir un journal de réflexion.

## À propos

Thumos rassemble en un seul endroit trois piliers du suivi personnel :

- **Objectifs** — définir, découper en sous-tâches et suivre l'avancement de ses buts.
- **Habitudes** — cocher chaque jour les habitudes accomplies et voir sa régularité
  grâce au calcul automatique des streaks.
- **Journal** — consigner des entrées datées avec une humeur pour garder une trace
  de sa réflexion dans le temps.

Le projet est aussi un terrain d'apprentissage : manipulation fine des dates
(Carbon), logique métier testée unitairement, et visualisations front (calendrier,
heatmap SVG).

## Fonctionnalités

### MVP (v1)

| Domaine | Fonctionnalité |
|---|---|
| Comptes | Inscription, connexion, déconnexion (Laravel Fortify) — données isolées par utilisateur |
| Tableau de bord | Écran d'accueil « Aujourd'hui » : mini-stats, habitudes du jour, prochaines échéances |
| Interface | UI dashboard sombre/clair (toggle persisté), layout sidebar, icônes SVG, tokens sémantiques |
| Objectifs | CRUD complet (titre, description, catégorie, date cible, statut) |
| Objectifs | Sous-tâches / checklist réordonnable par objectif |
| Habitudes | CRUD (nom, fréquence quotidienne ou hebdo, jours concernés) |
| Habitudes | Suivi quotidien : cocher une habitude calcule automatiquement le streak |
| Journal | Entrées datées (texte libre + humeur) |
| Calendrier | Vue mensuelle (habitudes faites / échéances d'objectifs) |

### Évolutions (v2+)

- Heatmap de complétion façon « GitHub contributions »
- Statistiques de progression hebdomadaire / mensuelle
- Rappels par email (Laravel Notifications)
- Badges et récompenses pour les streaks
- Recherche dans le journal avec filtre d’humeur et pagination (disponible) ; tags à venir
- Résumé hebdomadaire auto-généré
- Partage d'objectifs avec un ami (accountability, multi-utilisateurs)

## Stack technique

- **Backend** : PHP, [Laravel](https://laravel.com)
- **Authentification** : [Laravel Fortify](https://laravel.com/docs/fortify) (backend headless, vues Inertia maison)
- **Pont front/back** : [Inertia.js](https://inertiajs.com)
- **Frontend** : [Vue 3](https://vuejs.org), bundlé avec [Vite](https://vitejs.dev)
- **Dates & logique métier** : [Carbon](https://carbon.nesbot.com) (calcul des streaks côté backend)
- **Interactions UI** : [vuedraggable](https://github.com/SortableJS/vue.draggable.next) pour réordonner les sous-tâches
- **Visualisation** : calendrier mensuel custom et heatmap en SVG/Vue

## Prérequis

- PHP >= 8.3 et [Composer](https://getcomposer.org)
- [Node.js](https://nodejs.org) >= 22.12 et npm
- Une base de données (SQLite, MySQL ou PostgreSQL)

## Installation

```bash
# 1. Cloner le dépôt
git clone <url-du-depot> thumos
cd thumos

# 2. Installer les dépendances backend
composer install

# 3. Configurer l'environnement
cp .env.example .env
php artisan key:generate

# 4. Préparer la base de données
#    (configurer les variables DB_* dans .env au besoin)
php artisan migrate

# 5. Installer les dépendances frontend
npm install
```

### Lancement en développement

Dans deux terminaux séparés :

```bash
# Serveur applicatif Laravel
php artisan serve

# Build front en watch (Vite)
npm run dev
```

L'application est alors disponible sur `http://localhost:8000`.

### Build de production

```bash
npm run build
```

## Modèle de données

```
Goal
- id, user_id, title, description, category, target_date, status
- hasMany GoalTask

GoalTask (sous-tâches)
- id, goal_id, title, done (bool), order
- belongsTo Goal

Habit
- id, user_id, name, frequency (daily | weekly), days_of_week (json), color
- hasMany HabitLog

HabitLog
- id, habit_id, date, completed (bool)
- belongsTo Habit

JournalEntry
- id, user_id, date, content, mood
```

Toutes les entités principales (`Goal`, `Habit`, `JournalEntry`) sont rattachées
à un utilisateur (`user_id`).

## Architecture et choix techniques

- **Inertia + Vue** comme couche front, avec une montée progressive en complexité.
- **Calendrier mensuel custom** (ou `vue-cal`) pour croiser habitudes et échéances
  d'objectifs sur une même vue.
- **Calcul des streaks côté backend avec Carbon** : la logique de série (jours
  consécutifs, jours manqués) vit dans le backend et est couverte par des tests
  unitaires.
- **Heatmap en SVG/Vue** pour visualiser la régularité dans le temps, alimentée par
  les `HabitLog`.

## Roadmap

- [x] CRUD Goals + GoalTasks (checklist interactive avec drag & drop)
- [x] CRUD Habits + interface « check du jour »
- [x] Calcul et affichage des streaks
- [x] Journal avec entrées datées, recherche par contenu, filtre d’humeur et pagination
- [x] Vue calendrier combinant objectifs + habitudes
- [x] Heatmap de complétion
- [x] Notifications / rappels par email

## Points de vigilance

- **Calcul fiable des streaks** : gestion des jours manqués et des fuseaux horaires.
- **Stockage de `days_of_week` en JSON** et interrogation côté backend.
- **Construction de la heatmap SVG** depuis Vue : bon exercice de manipulation de
  données front.

## Tâches planifiées

Un rappel par email des habitudes non cochées est envoyé chaque jour à 19h
(`app:send-habit-reminders`). En production, activez le scheduler Laravel :

```bash
* * * * * cd /chemin/vers/thumos && php artisan schedule:run >> /dev/null 2>&1
```

Pour tester l'envoi manuellement (le mailer par défaut écrit dans les logs) :

```bash
php artisan app:send-habit-reminders
```

## Tests

```bash
php artisan test
```

La suite exécute 79 tests PHP (312 assertions), dont les anciens tests
réactivés pour PHPUnit 12 : les méthodes doivent commencer par `test_` (les
anciennes annotations `@test` ne suffisent plus). La clé de chiffrement dans
`phpunit.xml` est une valeur non secrète réservée aux tests ; elle ne doit
jamais servir de clé d'application en production.

Les contrôles frontend utilisent le runner intégré à Node, sans dépendance
supplémentaire :

```bash
npm test
npm run build
```

Les 8 tests frontend couvrent les dates civiles dans quatre fuseaux horaires,
les dates relatives futures/passées et le rendu accessible des formulaires et
barres de progression. Les parcours navigateur peuvent être vérifiés avec une
base SQLite temporaire ; ne pas utiliser la base de production pour les tests.

### Règles de données et suivi fiable

- Objectifs : statuts `active`, `paused` et `done`. Une échéance aujourd'hui
  n'est pas en retard ; seuls les objectifs actifs peuvent être en retard.
- Habitudes hebdomadaires : au moins un jour ISO entre 1 (lundi) et 7
  (dimanche), sans doublon. Les jours d'une habitude quotidienne sont remis
  à `null`. La couleur doit être un code hexadécimal `#RRGGBB`.
- Un log `completed=false` ne compte pas comme une réussite. Le basculer le
  marque comme réalisé ; les dates futures et les jours non prévus sont
  refusés. Les séries et la heatmap ignorent les réalisations futures.
- Une période de 30 jours contient exactement 30 dates, aujourd'hui inclus.
  Les statistiques excluent les jours non prévus et restent entre 0 et 100 %.
  Le calcul charge les logs en une seule requête plutôt qu'une requête par jour.
- Réordonner des sous-tâches exige la liste complète et sans doublon des tâches
  du même objectif ; les mises à jour sont appliquées dans une transaction.
- Journal : dates au format `AAAA-MM-JJ`, contenu limité à 5000 caractères,
  humeurs `great`, `good`, `neutral`, `bad` ou `awful`. Les filtres `q` (200
  caractères maximum) et `mood` sont conservés dans les liens de pagination.
  Chaque page contient au maximum 15 entrées de l'utilisateur connecté.
- Les champs de formulaires sont verrouillés pendant l'envoi, les erreurs
  sont reliées aux champs, et les contrôles de sélection annoncent leur état.
  La fenêtre du calendrier s'ouvre au clavier, maintient le focus et le
  restitue à la fermeture. Les recherches et détails restent privés à
  l'utilisateur connecté.

## Contribution

1. Créer une branche à partir de `main` : `git checkout -b feat/ma-fonctionnalite`
2. Committer des changements clairs et atomiques.
3. Vérifier que les tests passent : `php artisan test`
4. Ouvrir une Pull Request décrivant le quoi et le pourquoi du changement.

## Licence

Distribué sous licence [MIT](LICENSE).
