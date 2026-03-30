<?php

namespace App\EventSubscriber;

use App\Service\CspNonce;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SecurityHeadersSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly CspNonce $cspNonce) {}

    public static function getSubscribedEvents(): array
    {
        // Priorité -256 : s'exécute après ErrorListener::removeCspHeader() (-128)
        // pour que le CSP soit bien présent même sur les réponses d'erreur
        return [
            KernelEvents::RESPONSE => ['onKernelResponse', -256],
        ];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $response = $event->getResponse();
        $headers = $response->headers;

        $nonce = $this->cspNonce->get();

        // CSP avec nonce : supprime 'unsafe-inline' pour script-src et style-src
        // ui-avatars.com autorisé uniquement en img-src pour les avatars employés
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'nonce-{$nonce}'",
            "style-src 'self'",
            "img-src 'self' data: https://ui-avatars.com",
            "font-src 'self'",
            "connect-src 'self'",
            "frame-src 'none'",
            "frame-ancestors 'none'",
            "form-action 'self'",
            "base-uri 'self'",
        ]);

        $headers->set('Content-Security-Policy', $csp);
        $headers->set('X-Content-Type-Options', 'nosniff');
        $headers->set('X-Frame-Options', 'DENY');
        $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
    }
}
