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

### Phase 1 - Fondation prod-safe
- Stabiliser les interactions critiques (commentaires, suppression, moderation).
- Garder les migrations strictement additives et backward-compatible.
- Renforcer la couverture de tests de regression.

### Phase 2 - Gouvernance contenu et Filament
- Workflow de publication par role (`user => pending`, `author/admin => published`).
- Selection auteur ouverte a tous les utilisateurs selon le besoin editorial.
- Validation des actions de moderation/publication dans l admin.

### Phase 3 - I18n et experience Auth
- Couvrir toutes les traductions auth FR (validation, auth, passwords).
- Auditer les ecrans login/register/forgot/reset/confirm/2FA pour eviter les fallbacks anglais.
- Maintenir la non-regression sur throttling et erreurs metier.

### Phase 4 - Dashboard utilisateur (auth+verified)
- Ouvrir `/dashboard` a tous les comptes verifies.
- Permettre la proposition de contenus internes, externes, community links.
- Exposer le suivi "mes contenus": statuts metier, commentaires, interactions, lectures.

### Phase 5 - Profil public et page About utilisateur
- Enrichir le profil utilisateur (headline, bio, localisation, website, LinkedIn, X, GitHub).
- Permettre la gestion de la visibilite publique et du slug profil.
- Afficher les informations enrichies sur la page publique.

### Phase 6 - OAuth et comptes sociaux
- Authentification OAuth Google + LinkedIn (redirect/callback).
- Liaison des identities externes via `social_accounts`.
- Boutons OAuth sur login/register avec gestion des erreurs callback.

### Phase 7 - Publication sociale applicative
- Confirmer le mode "compte global applicatif" pour la diffusion sociale depuis Filament.
- Renforcer les logs `skipped/success/failed` pour audit et observabilite.
- Conserver X + LinkedIn en diffusion asynchrone post-publication.

### Phase 8 - SEO, newsletter et execution continue
- Maintenir sitemap, RSS, metadonnees, indexation locale.
- Consolider newsletter mensuelle double opt-in.
- Continuer la couverture de tests ciblee a chaque increment.

## Dependances d execution

1. Phase 1 -> prerequisite pour toutes les autres phases.
2. Phase 3 -> prerequisite pour Phase 6 (auth OAuth) afin d eviter des regressions UX.
3. Phase 4 -> prerequisite pour Phase 5 (profil utilisateur complet et usage dashboard).
4. Phase 6 -> prerequisite pour les evolutions futures de federation sociale par utilisateur.
5. Phase 7 -> reste basee sur un compte global applicatif tant que la strategie multi-comptes n est pas validee.
