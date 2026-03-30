# CSP — Content Security Policy

## C'est quoi la faille ?

Le navigateur ne savait pas quels scripts/images/styles étaient autorisés sur le site.
Un attaquant pouvait injecter du code JavaScript malveillant dans la page (XSS) et le navigateur l'aurait exécuté sans broncher.

Références : **CWE-693** / OWASP 2021 A05 / OWASP 2017 A06 (Security Misconfiguration)

## Comment on était censé la corriger ?

Ajouter un header HTTP `Content-Security-Policy` sur chaque réponse du serveur.
Ce header dit au navigateur : **"n'exécute que ce qui vient de moi"**.

```
Content-Security-Policy: default-src 'self'; script-src 'self' 'nonce-xxxx'; ...
```

## Ce qui a été fait concrètement

1. **Un `EventSubscriber` Symfony** écoute chaque réponse HTTP et ajoute le header CSP automatiquement.

2. **Un nonce** (nombre aléatoire unique par requête) est généré côté serveur et glissé dans le header ET dans chaque balise `<script>` du HTML.
   Le navigateur n'exécute que les scripts qui ont le bon nonce — les scripts injectés par un attaquant n'ont pas le nonce, donc ils sont bloqués.

3. **`unsafe-inline` supprimé** : avant, n'importe quel script inline était autorisé. Maintenant seuls ceux avec le bon nonce passent.

4. **Priorité -256 sur le subscriber** : Symfony a un listener interne (`ErrorListener::removeCspHeader`) qui supprime le CSP sur les pages d'erreur (404, 500). En mettant le subscriber à priorité -256 (plus bas que -128), il s'exécute en dernier et réajoute le header même sur les erreurs.

5. **Cache prod recompilé** : en `APP_ENV=prod`, Symfony compile les services une seule fois. Après ajout du subscriber, il a fallu relancer `php bin/console cache:clear && cache:warmup` pour que le nouveau code soit pris en compte.

## Fichiers créés/modifiés

| Fichier | Rôle |
|---|---|
| `src/EventSubscriber/SecurityHeadersSubscriber.php` | Ajoute le header CSP sur chaque réponse (priorité -256) |
| `src/Service/CspNonce.php` | Génère le nonce une fois par requête |
| `src/Twig/CspNonceExtension.php` | Rend `csp_nonce()` disponible dans les templates |
| Templates `*.html.twig` | `<script nonce="{{ csp_nonce() }}">` sur chaque script inline |

## Headers ajoutés sur chaque réponse

| Header | Valeur | Protection |
|---|---|---|
| `Content-Security-Policy` | `default-src 'self'; script-src 'self' 'nonce-...'` | Bloque XSS et injections |
| `X-Frame-Options` | `DENY` | Anti-clickjacking |
| `X-Content-Type-Options` | `nosniff` | Empêche le MIME sniffing |
| `Referrer-Policy` | `strict-origin-when-cross-origin` | Limite les infos dans Referer |

## À retenir pour la production

Après tout changement de code, toujours relancer :
```bash
php bin/console cache:clear
php bin/console cache:warmup
# puis redémarrer le serveur
symfony server:start
```
