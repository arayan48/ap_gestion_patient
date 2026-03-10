<?php

namespace App\Controller\Api;

use App\Entity\Reservation;
use App\Repository\EmployeRepository;
use App\Repository\LitRepository;
use App\Repository\PatientRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/reservations', name: 'api_reservation_')]
class ReservationApiController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(ReservationRepository $repo): JsonResponse
    {
        return $this->json(array_map(fn(Reservation $r) => $this->serialize($r), $repo->findAll()));
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Reservation $reservation): JsonResponse
    {
        return $this->json($this->serialize($reservation));
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        PatientRepository $patientRepo,
        LitRepository $litRepo,
        EmployeRepository $employeRepo,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $reservation = new Reservation();

        $patient = $patientRepo->find($data['patientId'] ?? 0);
        $lit     = $litRepo->find($data['litId'] ?? 0);
        $employe = $employeRepo->find($data['employeId'] ?? 0);

        if (!$patient || !$lit || !$employe) {
            return $this->json(['message' => 'Patient, Lit ou Employé introuvable.'], Response::HTTP_BAD_REQUEST);
        }

        $reservation->setPatient($patient);
        $reservation->setLit($lit);
        $reservation->setEmploye($employe);
        $reservation->setDateDebut(new \DateTime($data['dateDebut']));
        $reservation->setDateFin(new \DateTime($data['dateFin']));
        $reservation->setStatut($data['statut'] ?? 'en_cours');
        $reservation->setCommentaire($data['commentaire'] ?? null);

        $em->persist($reservation);
        $em->flush();

        return $this->json($this->serialize($reservation), Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT', 'PATCH'])]
    public function update(
        Reservation $reservation,
        Request $request,
        EntityManagerInterface $em,
        PatientRepository $patientRepo,
        LitRepository $litRepo,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (isset($data['patientId'])) {
            $patient = $patientRepo->find($data['patientId']);
            if ($patient) $reservation->setPatient($patient);
        }
        if (isset($data['litId'])) {
            $lit = $litRepo->find($data['litId']);
            if ($lit) $reservation->setLit($lit);
        }
        if (isset($data['dateDebut']))   $reservation->setDateDebut(new \DateTime($data['dateDebut']));
        if (isset($data['dateFin']))     $reservation->setDateFin(new \DateTime($data['dateFin']));
        if (isset($data['statut']))      $reservation->setStatut($data['statut']);
        if (isset($data['commentaire'])) $reservation->setCommentaire($data['commentaire']);

        $em->flush();

        return $this->json($this->serialize($reservation));
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Reservation $reservation, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($reservation);
        $em->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    private function serialize(Reservation $r): array
    {
        return [
            'id'          => $r->getId(),
            'dateDebut'   => $r->getDateDebut()?->format('Y-m-d H:i:s'),
            'dateFin'     => $r->getDateFin()?->format('Y-m-d H:i:s'),
            'statut'      => $r->getStatut(),
            'commentaire' => $r->getCommentaire(),
            'patient'     => [
                'id'     => $r->getPatient()?->getId(),
                'nom'    => $r->getPatient()?->getNom(),
                'prenom' => $r->getPatient()?->getPrenom(),
            ],
            'lit'     => [
                'id'        => $r->getLit()?->getId(),
                'numeroLit' => $r->getLit()?->getNumeroLit(),
                'chambre'   => $r->getLit()?->getChambre()?->getNumeroChambre(),
            ],
            'employe' => [
                'id'     => $r->getEmploye()?->getId(),
                'nom'    => $r->getEmploye()?->getNom(),
                'prenom' => $r->getEmploye()?->getPrenom(),
            ],
        ];
    }
}
