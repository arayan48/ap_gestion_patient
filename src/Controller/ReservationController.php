<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\EmployeRepository;
use App\Repository\LitRepository;
use App\Repository\PatientRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reservations', name: 'app_reservation_')]
class ReservationController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(
        ReservationRepository $reservationRepository,
        PatientRepository $patientRepository,
        LitRepository $litRepository,
        EmployeRepository $employeRepository
    ): Response {
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findBy([], ['dateDebut' => 'DESC']),
            'patients'     => $patientRepository->findBy([], ['nom' => 'ASC']),
            'lits'         => $litRepository->findAll(),
            'employes'     => $employeRepository->findBy([], ['nom' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        PatientRepository $patientRepository,
        LitRepository $litRepository,
        EmployeRepository $employeRepository
    ): Response {
        if (!$this->isCsrfTokenValid('reservation-new', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $reservation = new Reservation();

        $patient = $patientRepository->find((int) $request->request->get('patient'));
        $lit     = $litRepository->find((int) $request->request->get('lit'));
        $employe = $employeRepository->find((int) $request->request->get('employe'));

        if ($patient) $reservation->setPatient($patient);
        if ($lit)     $reservation->setLit($lit);
        if ($employe) $reservation->setEmploye($employe);

        $reservation->setStatut($request->request->get('statut', 'en_cours'));
        $reservation->setCommentaire($request->request->get('commentaire') ?: null);

        $dateDebut = $request->request->get('dateDebut');
        $dateFin   = $request->request->get('dateFin');
        $reservation->setDateDebut($dateDebut ? new \DateTime($dateDebut) : new \DateTime());
        $reservation->setDateFin($dateFin ? new \DateTime($dateFin) : new \DateTime('+1 day'));

        $em->persist($reservation);
        $em->flush();

        $this->addFlash('success_reservation', 'La réservation a été créée avec succès.');

        return $this->redirectToRoute('app_reservation_index');
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['POST'])]
    public function edit(
        Reservation $reservation,
        Request $request,
        EntityManagerInterface $em,
        PatientRepository $patientRepository,
        LitRepository $litRepository,
        EmployeRepository $employeRepository
    ): Response {
        if (!$this->isCsrfTokenValid('reservation-edit', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $patient = $patientRepository->find((int) $request->request->get('patient'));
        $lit     = $litRepository->find((int) $request->request->get('lit'));
        $employe = $employeRepository->find((int) $request->request->get('employe'));

        if ($patient) $reservation->setPatient($patient);
        if ($lit)     $reservation->setLit($lit);
        if ($employe) $reservation->setEmploye($employe);

        $reservation->setStatut($request->request->get('statut', 'en_cours'));
        $reservation->setCommentaire($request->request->get('commentaire') ?: null);

        $dateDebut = $request->request->get('dateDebut');
        $dateFin   = $request->request->get('dateFin');
        if ($dateDebut) $reservation->setDateDebut(new \DateTime($dateDebut));
        if ($dateFin)   $reservation->setDateFin(new \DateTime($dateFin));

        $em->flush();

        $this->addFlash('success_reservation_edit', 'La réservation a été modifiée avec succès.');

        return $this->redirectToRoute('app_reservation_index');
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Reservation $reservation, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('reservation-delete', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $em->remove($reservation);
        $em->flush();

        $this->addFlash('success_reservation', 'La réservation a été supprimée.');

        return $this->redirectToRoute('app_reservation_index');
    }
}
