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

#[Route('/api', name: 'api_auth_')]
class AuthController extends AbstractController
{
    /**
     * Inscription d'un nouvel employé.
     * POST /api/register
     * Body JSON: { email, password, nom, prenom, telephone, dateEmbauche, statut }
     */
    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em,
        EmployeRepository $employeRepository,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (empty($data['email']) || empty($data['password'])) {
            return $this->json(['message' => 'email et password sont requis.'], Response::HTTP_BAD_REQUEST);
        }

        if ($employeRepository->findOneBy(['email' => $data['email']])) {
            return $this->json(['message' => 'Cet email est déjà utilisé.'], Response::HTTP_CONFLICT);
        }

        $employe = new Employe();
        $employe->setEmail($data['email']);
        $employe->setNom($data['nom'] ?? '');
        $employe->setPrenom($data['prenom'] ?? '');
        $employe->setTelephone($data['telephone'] ?? null);
        $employe->setStatut($data['statut'] ?? 'actif');

        $dateEmbauche = isset($data['dateEmbauche'])
            ? new \DateTime($data['dateEmbauche'])
            : new \DateTime();
        $employe->setDateEmbauche($dateEmbauche);

        $hashed = $passwordHasher->hashPassword($employe, $data['password']);
        $employe->setPassword($hashed);

        $em->persist($employe);
        $em->flush();

        return $this->json([
            'message' => 'Compte créé avec succès.',
            'id'      => $employe->getId(),
            'email'   => $employe->getEmail(),
        ], Response::HTTP_CREATED);
    }

    /**
     * Retourne le profil de l'employé connecté.
     * GET /api/me
     */
    #[Route('/me', name: 'me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        /** @var Employe $employe */
        $employe = $this->getUser();

        return $this->json([
            'id'           => $employe->getId(),
            'email'        => $employe->getEmail(),
            'nom'          => $employe->getNom(),
            'prenom'       => $employe->getPrenom(),
            'telephone'    => $employe->getTelephone(),
            'statut'       => $employe->getStatut(),
            'dateEmbauche' => $employe->getDateEmbauche()?->format('Y-m-d'),
            'roles'        => $employe->getRoles(),
        ]);
    }
}
