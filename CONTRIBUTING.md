# Guide de contribution

Merci de contribuer à Thumos ! Ce document décrit les conventions et le processus de contribution.

## 🚀 Démarrage rapide

1. Forkez le repository
2. Créez une branche depuis `main` : `git checkout -b feat/ma-fonctionnalite`
3. Faites vos changements
4. Commitez avec des messages clairs
5. Poussez vers votre fork
6. Ouvrez une Pull Request

## 📝 Convention de commits

Utilisez des messages de commit descriptifs suivant le format :

```
type(scope): description courte

Description détaillée si nécessaire
```

### Types de commits

- `feat`: Nouvelle fonctionnalité
- `fix`: Correction de bug
- `docs`: Documentation uniquement
- `style`: Formatage, points-virgules manquants, etc.
- `refactor`: Refactoring sans changement de fonctionnalité
- `perf`: Amélioration des performances
- `test`: Ajout ou correction de tests
- `chore`: Tâches de maintenance (dépendances, config, etc.)
- `ci`: Modifications CI/CD

### Exemples

```bash
feat(habits): add weekly frequency support
fix(goals): correct progress percentage calculation
docs: update installation instructions in README
refactor(streaks): simplify calculation logic
```

## 🧪 Tests

Avant de soumettre une PR :

```bash
# Vérifiez que tous les tests passent
php artisan test

# Lancez le linter PHP
composer pint

# Lancez le build frontend
npm run build
```

## 💻 Standards de code

### PHP

- Suivez PSR-12
- Utilisez le typage strict des paramètres et retours
- Documentez les méthodes complexes avec des docblocks
- Privilégiez la lisibilité à la concision

### Vue.js / JavaScript

- Utilisez Composition API avec `<script setup>`
- Nommez les composants en PascalCase
- Utilisez les props typées
- Préférez les fonctions fléchées

### CSS

- Utilisez les tokens CSS variables du design system
- Gardez la spécificité basse (pas d'IDs)
- Commentez les sections importantes
- Privilégiez les classes utilitaires réutilisables

## 🏗️ Structure du projet

```
app/
├── Http/
│   ├── Controllers/     # Contrôleurs web
│   ├── Requests/        # Form requests
│   └── Middleware/      # Middleware custom
├── Models/              # Modèles Eloquent
├── Services/            # Logique métier
└── Notifications/       # Notifications email

resources/
├── js/
│   ├── Components/      # Composants Vue réutilisables
│   ├── Composables/     # Hooks Vue
│   └── pages/           # Pages Inertia
└── css/
    └── app.css          # Styles globaux + design system

database/
├── migrations/          # Migrations de base de données
└── seeders/            # Seeders pour données de test
```

## 🔍 Revue de code

Toute PR sera revue pour :

- Respect des conventions de code
- Tests adéquats
- Performance
- Sécurité
- Documentation

## 🐛 Signalement de bugs

Utilisez les GitHub Issues avec :

- Description claire du problème
- Étapes pour reproduire
- Comportement attendu vs actuel
- Version de PHP, Laravel, Node.js
- Captures d'écran si pertinent

## 💡 Suggestions de fonctionnalités

Les suggestions sont les bienvenues ! Ouvrez une Issue pour en discuter avant de commencer le développement.

## 📄 Licence

En contribuant, vous acceptez que vos contributions soient sous licence MIT.

## 🙏 Merci !

Vos contributions font progresser Thumos. Merci de prendre le temps de contribuer !
