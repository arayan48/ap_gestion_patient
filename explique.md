# CSP — Content Security Policy

## C'est quoi la faille ?

Le navigateur ne savait pas quels scripts/images/styles étaient autorisés sur le site.
Un attaquant pouvait injecter du code JavaScript malveillant dans la page (XSS) et le navigateur l'aurait exécuté sans broncher.

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

## Fichiers créés/modifiés

| Fichier | Rôle |
|---|---|
| `src/EventSubscriber/SecurityHeadersSubscriber.php` | Ajoute le header CSP sur chaque réponse |
| `src/Service/CspNonce.php` | Génère le nonce une fois par requête |
| `src/Twig/CspNonceExtension.php` | Rend `csp_nonce()` disponible dans les templates |
| Templates `*.html.twig` | `<script nonce="{{ csp_nonce() }}">` sur chaque script inline |
