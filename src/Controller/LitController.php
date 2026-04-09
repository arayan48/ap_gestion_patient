<?php

namespace App\Controller;

use App\Entity\Lit;
use App\Repository\ChambreRepository;
use App\Repository\LitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/lits', name: 'app_lit_')]
class LitController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(LitRepository $litRepository, ChambreRepository $chambreRepository): Response
    {
        return $this->render('lit/index.html.twig', [
            'lits'    => $litRepository->findBy([], ['numeroLit' => 'ASC']),
            'chambres' => $chambreRepository->findBy([], ['numeroChambre' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $em, ChambreRepository $chambreRepository): Response
    {
        $lit = new Lit();
        $lit->setNumeroLit($request->request->get('numeroLit', ''));
        $lit->setStatut($request->request->get('statut', 'disponible'));
        $lit->setDescription($request->request->get('description') ?: null);

        $chambre = $chambreRepository->find((int) $request->request->get('chambre'));
        if ($chambre) {
            $lit->setChambre($chambre);
        }

        $em->persist($lit);
        $em->flush();

        $this->addFlash('success_lit', 'Le lit a été ajouté avec succès.');

        return $this->redirectToRoute('app_lit_index');
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['POST'])]
    public function edit(
        Lit $lit,
        Request $request,
        EntityManagerInterface $em,
        ChambreRepository $chambreRepository
    ): Response {
        $lit->setNumeroLit($request->request->get('numeroLit', ''));
        $lit->setStatut($request->request->get('statut', 'disponible'));
        $lit->setDescription($request->request->get('description') ?: null);

        $chambre = $chambreRepository->find((int) $request->request->get('chambre'));
        if ($chambre) {
            $lit->setChambre($chambre);
        }

        $em->flush();

        $this->addFlash('success_lit_edit', 'Le lit a été modifié avec succès.');

        return $this->redirectToRoute('app_lit_index');
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Lit $lit, EntityManagerInterface $em): Response
    {
        $em->remove($lit);
        $em->flush();

        $this->addFlash('success_lit', 'Le lit a été supprimé.');

        return $this->redirectToRoute('app_lit_index');
    }
}
