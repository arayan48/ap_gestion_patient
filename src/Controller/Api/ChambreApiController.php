<?php

namespace App\Controller\Api;

use App\Entity\Chambre;
use App\Entity\Lit;
use App\Repository\ChambreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/chambres', name: 'api_chambre_')]
class ChambreApiController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(ChambreRepository $repo): JsonResponse
    {
        return $this->json(array_map(fn(Chambre $c) => $this->serialize($c), $repo->findAll()));
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Chambre $chambre): JsonResponse
    {
        return $this->json($this->serialize($chambre));
    }

    #[Route('/disponibles', name: 'disponibles', methods: ['GET'])]
    public function disponibles(ChambreRepository $repo): JsonResponse
    {
        $chambres = $repo->findBy(['statut' => 'disponible']);

        return $this->json(array_map(fn(Chambre $c) => $this->serialize($c), $chambres));
    }

    private function serialize(Chambre $c): array
    {
        return [
            'id'           => $c->getId(),
            'numeroChambre' => $c->getNumeroChambre(),
            'typeChambre'   => $c->getTypeChambre(),
            'statut'        => $c->getStatut(),
            'description'   => $c->getDescription(),
            'etage'         => $c->getEtage()?->getId(),
            'lits'          => $c->getLits()->map(fn(Lit $l) => [
                'id'        => $l->getId(),
                'numeroLit' => $l->getNumeroLit(),
                'statut'    => $l->getStatut(),
            ])->toArray(),
        ];
    }
}
