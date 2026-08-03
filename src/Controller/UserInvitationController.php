<?php
namespace App\Controller;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request,Response};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class UserInvitationController extends AbstractController
{
 #[Route('/convite/{token}',name:'app_user_invite_accept',requirements:['token'=>'[a-f0-9]{64}'],methods:['GET','POST'])]
 public function accept(string $token,Request $request,EntityManagerInterface $em,UserPasswordHasherInterface $hasher):Response
 {
  $user=$em->getRepository(User::class)->findOneBy(['conviteToken'=>$token]);if(!$user||!$user->getConviteExpiraEm()||$user->getConviteExpiraEm()<new \DateTimeImmutable())throw $this->createNotFoundException('Convite inválido ou expirado.');$errors=[];if($request->isMethod('POST')){if(!$this->isCsrfTokenValid('accept-invite-'.$token,(string)$request->request->get('_token')))throw $this->createAccessDeniedException();$password=(string)$request->request->get('password');if(strlen($password)<8)$errors[]='A senha deve ter pelo menos 8 caracteres.';if($password!==(string)$request->request->get('confirmPassword'))$errors[]='As senhas não coincidem.';if(!$errors){$user->setPassword($hasher->hashPassword($user,$password))->setAtivo(true)->setConviteToken(null)->setConviteExpiraEm(null);$em->flush();$this->addFlash('success','Conta ativada. Entre com seu e-mail e senha.');return $this->redirectToRoute('app_login');}}return $this->render('users/accept_invite.html.twig',['invited_user'=>$user,'errors'=>$errors,'token'=>$token]);
 }
}
