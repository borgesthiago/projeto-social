<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/superadmin/perfil')]
final class SuperAdminProfileController extends AbstractController
{
    #[Route('', name: 'app_superadmin_profile', methods: ['GET', 'POST'])]
    public function profile(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $errors = [];
        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('superadmin-profile', (string) $request->request->get('_token'))) {
                throw $this->createAccessDeniedException();
            }

            $name = trim((string) $request->request->get('nome'));
            $email = mb_strtolower(trim((string) $request->request->get('email')));
            $currentPassword = (string) $request->request->get('current_password');
            $newPassword = (string) $request->request->get('new_password');
            $confirmPassword = (string) $request->request->get('confirm_password');
            $emailChanged = $email !== $user->getEmail();
            $passwordChanged = $newPassword !== '' || $confirmPassword !== '';

            if (mb_strlen($name) < 3) {
                $errors[] = 'Informe um nome com pelo menos 3 caracteres.';
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Informe um e-mail válido.';
            } else {
                $duplicate = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
                if ($duplicate && $duplicate !== $user) {
                    $errors[] = 'Este e-mail já está sendo utilizado.';
                }
            }
            if (($emailChanged || $passwordChanged) && !$passwordHasher->isPasswordValid($user, $currentPassword)) {
                $errors[] = 'A senha atual está incorreta.';
            }
            if ($passwordChanged && strlen($newPassword) < 8) {
                $errors[] = 'A nova senha deve ter pelo menos 8 caracteres.';
            }
            if ($passwordChanged && $newPassword !== $confirmPassword) {
                $errors[] = 'A confirmação da nova senha não confere.';
            }

            if (!$errors) {
                $user->setNome($name)->setEmail($email);
                if ($passwordChanged) {
                    $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
                }
                $entityManager->flush();
                $this->addFlash('success', 'Seus dados foram atualizados com segurança.');

                return $this->redirectToRoute('app_superadmin_profile');
            }
        }

        return $this->render('superadmin/profile.html.twig', [
            'profile_user' => $user,
            'errors' => $errors,
        ]);
    }
}
