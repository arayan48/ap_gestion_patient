<?php

namespace App\Controller;

use App\Entity\Patient;
use App\Repository\PatientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/patients', name: 'app_patient_')]
class PatientController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(PatientRepository $patientRepository): Response
    {
        return $this->render('patient/index.html.twig', [
            'patients' => $patientRepository->findBy([], ['nom' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('patient-new', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $patient = new Patient();
        $patient->setNom($request->request->get('nom', ''));
        $patient->setPrenom($request->request->get('prenom', ''));
        $patient->setSexe($request->request->get('sexe', 'M'));
        $patient->setNumeroSecuriteSociale($request->request->get('numeroSecuriteSociale', ''));
        $patient->setAdresse($request->request->get('adresse') ?: null);
        $patient->setTelephone($request->request->get('telephone') ?: null);
        $patient->setEmail($request->request->get('email') ?: null);

        $dateStr = $request->request->get('dateNaissance');
        $patient->setDateNaissance($dateStr ? new \DateTime($dateStr) : new \DateTime());

        $em->persist($patient);
        $em->flush();

        $this->addFlash('success_patient', 'Le patient a été ajouté avec succès.');

        return $this->redirectToRoute('app_patient_index');
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['POST'])]
    public function edit(Patient $patient, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('patient-edit', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $patient->setNom($request->request->get('nom', ''));
        $patient->setPrenom($request->request->get('prenom', ''));
        $patient->setSexe($request->request->get('sexe', 'M'));
        $patient->setNumeroSecuriteSociale($request->request->get('numeroSecuriteSociale', ''));
        $patient->setAdresse($request->request->get('adresse') ?: null);
        $patient->setTelephone($request->request->get('telephone') ?: null);
        $patient->setEmail($request->request->get('email') ?: null);

        $dateStr = $request->request->get('dateNaissance');
        if ($dateStr) {
            $patient->setDateNaissance(new \DateTime($dateStr));
        }

        $em->flush();

        $this->addFlash('success_patient_edit', 'Le patient a été modifié avec succès.');

        return $this->redirectToRoute('app_patient_index');
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Patient $patient, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('patient-delete', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $em->remove($patient);
        $em->flush();

        $this->addFlash('success_patient', 'Le patient a été supprimé.');

        return $this->redirectToRoute('app_patient_index');
    }
}
