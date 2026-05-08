<?php

namespace App\Controller;

use App\Entity\Chambre;
use App\Repository\ChambreRepository;
use App\Repository\EtageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/chambres', name: 'app_chambre_')]
class ChambreController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(ChambreRepository $chambreRepository, EtageRepository $etageRepository): Response
    {
        return $this->render('chambre/index.html.twig', [
            'chambres' => $chambreRepository->findBy([], ['numeroChambre' => 'ASC']),
            'etages'   => $etageRepository->findBy([], ['numeroEtage' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $em, EtageRepository $etageRepository): Response
    {
        if (!$this->isCsrfTokenValid('chambre-new', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $chambre = new Chambre();
        $chambre->setNumeroChambre($request->request->get('numeroChambre', ''));
        $chambre->setTypeChambre($request->request->get('typeChambre', 'simple'));
        $chambre->setStatut($request->request->get('statut', 'disponible'));
        $chambre->setDescription($request->request->get('description') ?: null);

        $etage = $etageRepository->find((int) $request->request->get('etage'));
        if ($etage) {
            $chambre->setEtage($etage);
        }

        $em->persist($chambre);
        $em->flush();

        $this->addFlash('success_chambre', 'La chambre a été ajoutée avec succès.');

        return $this->redirectToRoute('app_chambre_index');
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['POST'])]
    public function edit(
        Chambre $chambre,
        Request $request,
        EntityManagerInterface $em,
        EtageRepository $etageRepository
    ): Response {
        if (!$this->isCsrfTokenValid('chambre-edit', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $chambre->setNumeroChambre($request->request->get('numeroChambre', ''));
        $chambre->setTypeChambre($request->request->get('typeChambre', 'simple'));
        $chambre->setStatut($request->request->get('statut', 'disponible'));
        $chambre->setDescription($request->request->get('description') ?: null);

        $etage = $etageRepository->find((int) $request->request->get('etage'));
        if ($etage) {
            $chambre->setEtage($etage);
        }

        $em->flush();

        $this->addFlash('success_chambre_edit', 'La chambre a été modifiée avec succès.');

        return $this->redirectToRoute('app_chambre_index');
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Chambre $chambre, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('chambre-delete', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $em->remove($chambre);
        $em->flush();

        $this->addFlash('success_chambre', 'La chambre a été supprimée.');

        return $this->redirectToRoute('app_chambre_index');
    }
}
