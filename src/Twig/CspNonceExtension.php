<?php

namespace App\Twig;

use App\Service\CspNonce;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CspNonceExtension extends AbstractExtension
{
    public function __construct(private readonly CspNonce $cspNonce) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('csp_nonce', $this->cspNonce->get(...)),
        ];
    }
}
