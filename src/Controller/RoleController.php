<?php

namespace App\Controller;

use App\Entity\Role;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/roles', name: 'app_role_')]
class RoleController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(RoleRepository $roleRepository): Response
    {
        return $this->render('role/index.html.twig', [
            'roles' => $roleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $role = new Role();
        $role->setNomRole($request->request->get('nomRole', ''));
        $role->setDescription($request->request->get('description') ?: null);

        $em->persist($role);
        $em->flush();

        $this->addFlash('success_role', 'Le rôle a été créé avec succès.');

        return $this->redirectToRoute('app_role_index');
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['POST'])]
    public function edit(Role $role, Request $request, EntityManagerInterface $em): Response
    {
        $role->setNomRole($request->request->get('nomRole', ''));
        $role->setDescription($request->request->get('description') ?: null);

        $em->flush();

        $this->addFlash('success_role_edit', 'Le rôle a été modifié avec succès.');

        return $this->redirectToRoute('app_role_index');
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Role $role, EntityManagerInterface $em): Response
    {
        $em->remove($role);
        $em->flush();

        $this->addFlash('success_role', 'Le rôle a été supprimé.');

        return $this->redirectToRoute('app_role_index');
    }
}
