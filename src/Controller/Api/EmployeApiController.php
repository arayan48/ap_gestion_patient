<?php

namespace App\Controller\Api;

use App\Entity\Employe;
use App\Repository\EmployeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/employes', name: 'api_employe_')]
class EmployeApiController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(EmployeRepository $repo): JsonResponse
    {
        return $this->json(array_map(fn(Employe $e) => $this->serialize($e), $repo->findAll()));
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Employe $employe): JsonResponse
    {
        return $this->json($this->serialize($employe));
    }

    #[Route('/{id}', name: 'update', methods: ['PUT', 'PATCH'])]
    public function update(
        Employe $employe,
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (isset($data['nom']))          $employe->setNom($data['nom']);
        if (isset($data['prenom']))       $employe->setPrenom($data['prenom']);
        if (isset($data['telephone']))    $employe->setTelephone($data['telephone']);
        if (isset($data['statut']))       $employe->setStatut($data['statut']);
        if (isset($data['dateEmbauche'])) $employe->setDateEmbauche(new \DateTime($data['dateEmbauche']));
        if (!empty($data['password'])) {
            $employe->setPassword($passwordHasher->hashPassword($employe, $data['password']));
        }

        $em->flush();

        return $this->json($this->serialize($employe));
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Employe $employe, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($employe);
        $em->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    private function serialize(Employe $e): array
    {
        return [
            'id'           => $e->getId(),
            'nom'          => $e->getNom(),
            'prenom'       => $e->getPrenom(),
            'email'        => $e->getEmail(),
            'telephone'    => $e->getTelephone(),
            'statut'       => $e->getStatut(),
            'dateEmbauche' => $e->getDateEmbauche()?->format('Y-m-d'),
            'roles'        => $e->getRoles(),
        ];
    }
}
