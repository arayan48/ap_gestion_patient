<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/profil', name: 'app_profil')]
class ProfileController extends AbstractController
{
    #[Route('', name: '', methods: ['GET'])]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('profil/index.html.twig');
    }

    #[Route('/edit', name: '_edit', methods: ['POST'])]
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$this->isCsrfTokenValid('profil-edit', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        /** @var \App\Entity\Employe $user */
        $user = $this->getUser();

        $nom    = trim($request->request->get('nom', ''));
        $prenom = trim($request->request->get('prenom', ''));
        $tel    = trim($request->request->get('telephone', '')) ?: null;

        if ($nom)    $user->setNom($nom);
        if ($prenom) $user->setPrenom($prenom);
        $user->setTelephone($tel);

        $em->flush();
        $this->addFlash('success_profil', 'Votre profil a été mis à jour.');

        return $this->redirectToRoute('app_profil');
    }

    #[Route('/password', name: '_password', methods: ['POST'])]
    public function changePassword(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$this->isCsrfTokenValid('profil-password', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        /** @var \App\Entity\Employe $user */
        $user = $this->getUser();

        $current  = $request->request->get('current_password', '');
        $new      = $request->request->get('new_password', '');
        $confirm  = $request->request->get('confirm_password', '');

        if (!$hasher->isPasswordValid($user, $current)) {
            $this->addFlash('error_password', 'Mot de passe actuel incorrect.');
            return $this->redirectToRoute('app_profil');
        }

        if (strlen($new) < 8) {
            $this->addFlash('error_password', 'Le nouveau mot de passe doit faire au moins 8 caractères.');
            return $this->redirectToRoute('app_profil');
        }

        if ($new !== $confirm) {
            $this->addFlash('error_password', 'Les deux mots de passe ne correspondent pas.');
            return $this->redirectToRoute('app_profil');
        }

        $user->setPassword($hasher->hashPassword($user, $new));
        $em->flush();

        $this->addFlash('success_profil', 'Mot de passe modifié avec succès.');

        return $this->redirectToRoute('app_profil');
    }
}
