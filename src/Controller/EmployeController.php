<?php

namespace App\Controller;

use App\Entity\Employe;
use App\Repository\EmployeRepository;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/employes', name: 'app_employe_')]
class EmployeController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(EmployeRepository $employeRepository, RoleRepository $roleRepository): Response
    {
        return $this->render('employe/index.html.twig', [
            'employes' => $employeRepository->findAll(),
            'roles'    => $roleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        RoleRepository $roleRepository,
        UserPasswordHasherInterface $hasher
    ): Response {
        if (!$this->isCsrfTokenValid('employe-new', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $employe = new Employe();
        $employe->setNom($request->request->get('nom', ''));
        $employe->setPrenom($request->request->get('prenom', ''));
        $employe->setEmail($request->request->get('email', ''));
        $employe->setTelephone($request->request->get('telephone') ?: null);
        $employe->setStatut($request->request->get('statut', 'actif'));

        $dateStr = $request->request->get('dateEmbauche');
        $employe->setDateEmbauche($dateStr ? new \DateTime($dateStr) : new \DateTime());

        $employe->setPassword($hasher->hashPassword($employe, $request->request->get('password', '')));

        foreach ($request->request->all('roles') as $roleId) {
            $role = $roleRepository->find((int) $roleId);
            if ($role) {
                $employe->addRole($role);
            }
        }

        $em->persist($employe);
        $em->flush();

        $this->addFlash('success_employe', 'L\'employé a été ajouté avec succès.');

        return $this->redirectToRoute('app_employe_index');
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['POST'])]
    public function edit(
        Employe $employe,
        Request $request,
        EntityManagerInterface $em,
        RoleRepository $roleRepository,
        UserPasswordHasherInterface $hasher
    ): Response {
        if (!$this->isCsrfTokenValid('employe-edit', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $employe->setNom($request->request->get('nom', ''));
        $employe->setPrenom($request->request->get('prenom', ''));
        $employe->setEmail($request->request->get('email', ''));
        $employe->setTelephone($request->request->get('telephone') ?: null);
        $employe->setStatut($request->request->get('statut', 'actif'));

        $dateStr = $request->request->get('dateEmbauche');
        if ($dateStr) {
            $employe->setDateEmbauche(new \DateTime($dateStr));
        }

        $plain = $request->request->get('password', '');
        if ($plain !== '') {
            $employe->setPassword($hasher->hashPassword($employe, $plain));
        }

        foreach ($employe->getRoleEntities()->toArray() as $r) {
            $employe->removeRole($r);
        }
        foreach ($request->request->all('roles') as $roleId) {
            $role = $roleRepository->find((int) $roleId);
            if ($role) {
                $employe->addRole($role);
            }
        }

        $em->flush();

        $this->addFlash('success_employe_edit', 'L\'employé a été modifié avec succès.');

        return $this->redirectToRoute('app_employe_index');
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Employe $employe, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('employe-delete', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $em->remove($employe);
        $em->flush();

        $this->addFlash('success_employe', 'L\'employé a été supprimé.');

        return $this->redirectToRoute('app_employe_index');
    }
}