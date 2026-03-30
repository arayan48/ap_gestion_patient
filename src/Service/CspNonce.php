<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class CspNonce
{
    private array $nonces = [];

    public function __construct(private readonly RequestStack $requestStack) {}

    public function get(): string
    {
        $request = $this->requestStack->getCurrentRequest();
        $key = $request ? spl_object_id($request) : 0;

        if (!isset($this->nonces[$key])) {
            $this->nonces[$key] = base64_encode(random_bytes(16));
        }

        return $this->nonces[$key];
    }
}
