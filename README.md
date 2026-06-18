# Tableau de Bord de Vie (objectifs, habitudes, journal)

## Objectif

Centraliser tes objectifs personnels, suivre tes habitudes quotidiennes avec des séries ("streaks"), et tenir un journal de réflexion.

## Fonctionnalités

**MVP (v1)**
- CRUD Objectifs (titre, description, catégorie, date cible, statut)
- Sous-tâches/checklist par objectif
- CRUD Habitudes (nom, fréquence quotidienne/hebdo, jours concernés)
- Suivi quotidien : cocher une habitude → calcul automatique du streak
- Journal : entrées datées (texte libre + humeur)
- Vue calendrier mensuel (habitudes faites / échéances d'objectifs)

**Évolutions (v2+)**
- Heatmap de complétion façon "GitHub contributions"
- Statistiques de progression hebdo/mensuelle
- Rappels par email (Laravel Notifications)
- Badges/récompenses pour les streaks
- Recherche + tags dans le journal
- Résumé hebdomadaire auto-généré
- Partage d'objectifs avec un ami (accountability → multi-utilisateurs)

## Modèle de données

```
Goal
- id, user_id, title, description, category, target_date, status

GoalTask (sous-tâches)
- id, goal_id, title, done (bool), order
- belongsTo Goal

Habit
- id, user_id, name, frequency (daily | weekly), days_of_week (json), color

HabitLog
- id, habit_id, date, completed (bool)
- belongsTo Habit

JournalEntry
- id, user_id, date, content, mood
```

## Architecture technique

- Toujours Inertia + Vue, mais on monte en complexité front :
  - Calendrier mensuel custom (ou `vue-cal`)
  - `vuedraggable` pour réordonner les sous-tâches
  - Calcul des **streaks côté backend avec Carbon** (manipulation de dates) — bon exercice de logique + tests unitaires
  - Heatmap en SVG/Vue pour visualiser la régularité

## Étapes de développement

1. CRUD Goals + GoalTasks (checklist interactive avec drag & drop)
2. CRUD Habits + interface "check du jour"
3. Calcul et affichage des streaks
4. Journal avec entrées datées
5. Vue calendrier combinant objectifs + habitudes
6. Heatmap de complétion
7. Notifications/rappels par email

## Points de difficulté à anticiper

- Calcul fiable des streaks (gestion des jours manqués, fuseaux horaires)
- Stocker `days_of_week` en JSON et l'interroger côté backend
- Construire une heatmap en SVG depuis Vue (bon exercice de manipulation de données front)
