# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

### CSS / Assets

```bash
# Dev — recompiler le CSS à chaque modification de template
php bin/console tailwind:build --watch

# Prod — compiler CSS + exporter tous les assets dans public/
php bin/console tailwind:build
php bin/console asset-map:compile
```

> **Important** : le projet tourne en `APP_ENV=prod` (vérifier `.env.local`). En prod, `asset-map:compile` est obligatoire après chaque changement de CSS, sinon les styles n'apparaissent pas.

### Cache

```bash
php bin/console cache:clear
```

### Base de données

```bash
# Appliquer les migrations
php bin/console doctrine:migrations:migrate

# Créer une nouvelle migration après modification d'entité
php bin/console doctrine:migrations:diff

# Exécuter du SQL direct
php bin/console dbal:run-sql "SELECT ..."
```

### Serveur de développement

```bash
symfony server:start
# ou
php -S localhost:8000 -t public/
```

### Docker

```bash
# Démarrer les services (MySQL, phpMyAdmin, Mailpit)
docker compose up -d

# Arrêter
docker compose down
```

| Service    | URL                       |
|------------|---------------------------|
| phpMyAdmin | http://localhost:8080      |
| Mailpit    | http://localhost:8025      |
| MySQL      | localhost:3306             |

> Les variables `MYSQL_USER`, `MYSQL_PASSWORD`, `MYSQL_ROOT_PASSWORD` doivent être définies dans `.env.local` (non commité).

## Architecture

### Double interface : Web + API REST

Le projet expose **deux couches parallèles** pour les mêmes données :

- **Web** (`src/Controller/*.php`) → rendu Twig, sessions, form_login
- **API REST** (`src/Controller/Api/*.php`) → JSON stateless, authentification JWT via LexikJWTAuthenticationBundle

Les routes web sont sous `/patients`, `/chambres`, etc. Les routes API sont sous `/api/...` et nécessitent un Bearer token JWT obtenu via `POST /api/login`.

### Entités et relations clés

```
Employe (UserInterface) ──< employe_role >── Role
Reservation ──> Patient
Reservation ──> Lit ──> Chambre ──> Etage
Reservation ──> Employe
Log (audit trail : employe, action, tableConcernee, ancienEtat, nouvelEtat)
```

`Employe` implémente `UserInterface` et `PasswordAuthenticatedUserInterface`. Les rôles Symfony (`ROLE_XXX`) sont dérivés dynamiquement depuis les entités `Role` (table `role`) via `getRoles()`.

### Assets — Tailwind v4 + AssetMapper

- Source CSS : `assets/styles/app.css` (utilise `@import "tailwindcss"` et `@theme` pour les couleurs custom)
- Couleur `primary` = bleu (#2563eb pour 600) définie dans `assets/styles/app.css` via variables CSS `--color-primary-*`
- AssetMapper sert les assets depuis `assets/` ; en prod ils sont copiés dans `public/assets/` avec hash
- Pas de `node_modules`, pas de `npm run` — tout passe par `php bin/console tailwind:build`

### Turbo (Hotwire)

`@hotwired/turbo` est actif. Conséquences importantes :
- Utiliser `turbo:load` au lieu de `DOMContentLoaded` pour tout JS dans les templates body
- Les listeners ajoutés sur `document` s'accumulent entre navigations → toujours ajouter un guard en début de callback (ex: `if (!document.getElementById('mon-element')) return;`)
- Les pages sans header (ex: login) déclenchent quand même les callbacks `turbo:load` attachés sur les pages précédentes

### Templates

- `base.html.twig` : système de modals global (`window.Modal.open/close`, attributs `data-open-modal`, `data-close-modal`)
- `inc/header.html.twig` : contient tout son JS inline (dark mode, dropdowns, hamburger, recherche). Doit être inclus manuellement dans chaque page via `{% include %}`
- `inc/modal_success.html.twig` et `inc/modal_confirm_delete.html.twig` : composants réutilisables avec paramètres Twig
- Pages d'erreur custom : `templates/bundles/TwigBundle/Exception/error404.html.twig` et `error500.html.twig` — **standalone** (pas de `{% extends %}`), utilisent `{{ asset('styles/app.css') }}`

### Sécurité

- Web : form_login sur `app_login`, CSRF activé
- API : JWT stateless, tous les endpoints `/api/*` sauf `/api/login` nécessitent `IS_AUTHENTICATED_FULLY`
- La route `/` est publique (affiche landing ou dashboard selon `app.user`)
