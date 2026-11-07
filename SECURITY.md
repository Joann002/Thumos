# Politique de sécurité

## Versions supportées

Nous maintenons activement les versions suivantes de Thumos :

| Version | Support          |
| ------- | ---------------- |
| main    | ✅ Support actif |

## Signaler une vulnérabilité

Si vous découvrez une vulnérabilité de sécurité dans Thumos, merci de nous le signaler de manière responsable.

### Comment signaler

**Ne créez PAS une issue publique GitHub pour les vulnérabilités de sécurité.**

À la place :

1. Envoyez un email à [votre-email@domain.com] avec :
   - Description de la vulnérabilité
   - Étapes pour reproduire le problème
   - Impact potentiel
   - Suggestions de correctif (si disponibles)

2. Attendez notre confirmation de réception (sous 48h)

3. Nous travaillerons avec vous pour :
   - Comprendre et reproduire le problème
   - Développer un correctif
   - Publier la correction
   - Vous créditer dans le changelog (si souhaité)

### Ce que vous pouvez attendre

- **Accusé de réception** : Sous 48 heures
- **Mise à jour sur le statut** : Sous 1 semaine
- **Correctif** : Selon la gravité (urgent : quelques jours, moyen : 2-4 semaines)

### Scope

Les vulnérabilités de sécurité incluent, sans s'y limiter :

- Injection SQL
- Cross-Site Scripting (XSS)
- Cross-Site Request Forgery (CSRF)
- Authentification / autorisation contournée
- Exposition de données sensibles
- Vulnérabilités des dépendances critiques

### Récompenses

Bien que nous ne puissions offrir de récompenses financières pour ce projet open-source, nous :

- Vous créditerons publiquement (si désiré)
- Vous remercierons dans les release notes
- Apprécions sincèrement votre aide à sécuriser Thumos

## Bonnes pratiques de sécurité

Pour les déployeurs de Thumos :

- ✅ Gardez Laravel et ses dépendances à jour
- ✅ Utilisez HTTPS en production
- ✅ Configurez correctement les variables d'environnement
- ✅ Activez les logs de sécurité
- ✅ Utilisez des mots de passe forts pour les comptes
- ✅ Limitez les permissions de la base de données
- ✅ Configurez un firewall approprié
- ✅ Effectuez des sauvegardes régulières

## Merci

Merci d'aider à garder Thumos et ses utilisateurs en sécurité ! 🙏
