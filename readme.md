# NYUNDA.DEV

Blog technique personnel orienté PHP, Laravel, IA et engineering pragmatique.

## Objectif

Construire un blog maintenable long terme avec:
- articles internes (lecture complète sur le site),
- articles externes (badge + redirection),
- community links soumis par les utilisateurs,
- newsletter mensuelle avec double opt-in,
- page About (bio, projets, outils),
- partage social asynchrone (X + LinkedIn),
- SEO propre dès le MVP.

## Stack

- PHP 8.5
- Laravel 12
- Livewire 4
- Flux UI 2 (Free)
- Filament (admin)
- MySQL
- Queue jobs (actions externes async)
- Déploiement: Ploi + VPS

## Principes d’architecture

- Les controllers REST ne contiennent que CRUD (`index/show/create/store/edit/update/destroy`).
- Toute action additionnelle passe par un controller dédié (ex: `PublishContentController`).
- La logique métier vit dans des Class Actions / Use Cases.
- Validation via Form Requests.
- Autorisation via Policies.
- Actions externes en Jobs queueés (ne jamais bloquer une requête HTTP).
- Sécurité systématique: validation stricte, rate limiting, sanitization Markdown.

## Modèle de contenu (i18n-first)

- Entité canonique: `content_items`
- Traductions: `content_translations` (`fr`, `en`)
- Fallback prévu: locale utilisateur -> `fr` -> première traduction disponible.

## Rôles

- `admin`: gestion complète
- `author`: publication directe
- `user`: soumission avec workflow `pending`

## Fondations déjà posées

- Enum rôles utilisateur et statut de publication.
- Base de workflow de publication (`published` vs `pending`) via action métier.
- Rate limiters nommés pour soumissions, votes, commentaires.
- Profil public partageable (privé par défaut).
- Locale par défaut en français.

## Roadmap d’implémentation

- A. Fondations
- B. Modèle i18n
- C. Admin Filament
- D. Front blog
- E. Interactions
- F. Community links
- G. About
- H. Newsletter
- I. Partage social
- J. SEO
