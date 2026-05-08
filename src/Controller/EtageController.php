<?php

namespace App\Controller;

use App\Entity\Etage;
use App\Repository\EtageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/etages', name: 'app_etage_')]
class EtageController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(EtageRepository $etageRepository): Response
    {
        return $this->render('etage/index.html.twig', [
            'etages' => $etageRepository->findBy([], ['numeroEtage' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('etage-new', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $etage = new Etage();
        $etage->setNumeroEtage((int) $request->request->get('numeroEtage', 0));
        $etage->setNomEtage($request->request->get('nomEtage') ?: null);
        $etage->setDescription($request->request->get('description') ?: null);

        $em->persist($etage);
        $em->flush();

        $this->addFlash('success_etage', 'L\'étage a été ajouté avec succès.');

        return $this->redirectToRoute('app_etage_index');
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['POST'])]
    public function edit(Etage $etage, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('etage-edit', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $etage->setNumeroEtage((int) $request->request->get('numeroEtage', 0));
        $etage->setNomEtage($request->request->get('nomEtage') ?: null);
        $etage->setDescription($request->request->get('description') ?: null);

        $em->flush();

        $this->addFlash('success_etage_edit', 'L\'étage a été modifié avec succès.');

        return $this->redirectToRoute('app_etage_index');
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Etage $etage, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('etage-delete', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $em->remove($etage);
        $em->flush();

        $this->addFlash('success_etage', 'L\'étage a été supprimé.');

        return $this->redirectToRoute('app_etage_index');
    }
}
