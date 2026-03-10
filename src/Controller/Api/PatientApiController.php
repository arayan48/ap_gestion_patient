<?php

namespace App\Controller\Api;

use App\Entity\Patient;
use App\Repository\PatientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/patients', name: 'api_patient_')]
class PatientApiController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(PatientRepository $repo): JsonResponse
    {
        $patients = $repo->findAll();

        return $this->json(array_map(fn(Patient $p) => $this->serialize($p), $patients));
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Patient $patient): JsonResponse
    {
        return $this->json($this->serialize($patient));
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $patient = new Patient();
        $this->hydrate($patient, $data);

        $em->persist($patient);
        $em->flush();

        return $this->json($this->serialize($patient), Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT', 'PATCH'])]
    public function update(Patient $patient, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $this->hydrate($patient, $data);
        $em->flush();

        return $this->json($this->serialize($patient));
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Patient $patient, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($patient);
        $em->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    private function hydrate(Patient $patient, array $data): void
    {
        if (isset($data['nom']))                  $patient->setNom($data['nom']);
        if (isset($data['prenom']))               $patient->setPrenom($data['prenom']);
        if (isset($data['sexe']))                 $patient->setSexe($data['sexe']);
        if (isset($data['adresse']))              $patient->setAdresse($data['adresse']);
        if (isset($data['telephone']))            $patient->setTelephone($data['telephone']);
        if (isset($data['email']))                $patient->setEmail($data['email']);
        if (isset($data['numeroSecuriteSociale'])) $patient->setNumeroSecuriteSociale($data['numeroSecuriteSociale']);
        if (isset($data['dateNaissance']))        $patient->setDateNaissance(new \DateTime($data['dateNaissance']));
    }

    private function serialize(Patient $p): array
    {
        return [
            'id'                    => $p->getId(),
            'nom'                   => $p->getNom(),
            'prenom'                => $p->getPrenom(),
            'dateNaissance'         => $p->getDateNaissance()?->format('Y-m-d'),
            'sexe'                  => $p->getSexe(),
            'adresse'               => $p->getAdresse(),
            'telephone'             => $p->getTelephone(),
            'email'                 => $p->getEmail(),
            'numeroSecuriteSociale' => $p->getNumeroSecuriteSociale(),
        ];
    }
}
