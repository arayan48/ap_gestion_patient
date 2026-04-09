import './stimulus_bootstrap.js';
import './header-ui.js';
import * as Turbo from '@hotwired/turbo';

// Transmet le nonce CSP à Turbo pour qu'il l'applique aux scripts
// réactivés après navigation XHR (sinon bloqués par script-src)
const nonceMeta = document.querySelector('meta[name="csp-nonce"]');
if (nonceMeta) {
    Turbo.setCSPNonce(nonceMeta.getAttribute('content'));
}

