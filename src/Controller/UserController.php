<?php
namespace App\Controller;

use App\Entity\User;
use App\Service\BillingManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request,Response};
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/usuarios')]
final class UserController extends AbstractController
{
 public const ROLES=['ROLE_MASTER'=>'Master','ROLE_ADMINISTRATIVO'=>'Administrativo','ROLE_PROFESSOR'=>'Professor','ROLE_FINANCEIRO'=>'Financeiro','ROLE_RESPONSAVEL'=>'Responsável','ROLE_ALUNO'=>'Aluno'];

 #[Route('',name:'app_users_index',methods:['GET'])]
 public function index(Request $request,EntityManagerInterface $em):Response
 {
  $this->denyAccessUnlessGranted('ROLE_MASTER');$project=$this->user()->getProjeto();$q=trim((string)$request->query->get('q'));$role=(string)$request->query->get('role');$qb=$em->getRepository(User::class)->createQueryBuilder('u')->where('u.projeto = :project')->setParameter('project',$project)->orderBy('u.nome','ASC');if($q!=='')$qb->andWhere('LOWER(u.nome) LIKE :q OR LOWER(u.email) LIKE :q')->setParameter('q','%'.mb_strtolower($q).'%');if(isset(self::ROLES[$role]))$qb->andWhere('u.roles LIKE :role')->setParameter('role','%"'.$role.'"%');$users=$qb->getQuery()->getResult();$all=$em->getRepository(User::class)->findBy(['projeto'=>$project]);$counts=array_fill_keys(array_keys(self::ROLES),0);foreach($all as $user){$main=$user->getRoles()[0]??'';if(isset($counts[$main]))$counts[$main]++;}return $this->render('users/index.html.twig',['users'=>$users,'roles'=>self::ROLES,'counts'=>$counts,'filters'=>['q'=>$q,'role'=>$role]]);
 }

 #[Route('/convidar',name:'app_users_invite',methods:['POST'])]
 public function invite(Request $request,EntityManagerInterface $em,UserPasswordHasherInterface $hasher,BillingManager $billing,MailerInterface $mailer):Response
 {
  $this->denyAccessUnlessGranted('ROLE_MASTER');$project=$this->user()->getProjeto();if(!$this->isCsrfTokenValid('user-invite',(string)$request->request->get('_token')))throw $this->createAccessDeniedException();if(!$billing->canAddUser($project)){$this->addFlash('error','O limite de usuários do plano foi atingido.');return $this->redirectToRoute('app_users_index');}$email=mb_strtolower(trim((string)$request->request->get('email')));$name=trim((string)$request->request->get('nome'));$role=(string)$request->request->get('role');$message=trim((string)$request->request->get('mensagem'));if(!filter_var($email,FILTER_VALIDATE_EMAIL))$this->addFlash('error','Informe um e-mail válido.');elseif(!isset(self::ROLES[$role]))$this->addFlash('error','Selecione um perfil válido.');elseif($em->getRepository(User::class)->findOneBy(['email'=>$email]))$this->addFlash('error','Este e-mail já está cadastrado.');else{$user=(new User())->setProjeto($project)->setNome($name?:ucfirst(strtok($email,'@')))->setEmail($email)->setRoles([$role])->setAtivo(false)->setConviteToken(bin2hex(random_bytes(32)))->setConviteExpiraEm(new \DateTimeImmutable('+7 days'));$user->setPassword($hasher->hashPassword($user,bin2hex(random_bytes(24))));$em->persist($user);$em->flush();$url=$this->generateUrl('app_user_invite_accept',['token'=>$user->getConviteToken()],UrlGeneratorInterface::ABSOLUTE_URL);try{$mailer->send((new TemplatedEmail())->from('no-reply@projetosocial.local')->to($email)->subject('Convite para '.$project->nome)->htmlTemplate('emails/user_invite.html.twig')->context(['project'=>$project,'user'=>$user,'message'=>$message,'inviteUrl'=>$url]));$this->addFlash('success','Convite enviado para '.$email.'.');}catch(\Throwable){$this->addFlash('error','O usuário foi convidado, mas o e-mail não pôde ser enviado. Link: '.$url);}}return $this->redirectToRoute('app_users_index');
 }

 #[Route('/novo',name:'app_users_new',methods:['GET','POST'])]
 public function new(Request $request,EntityManagerInterface $em,UserPasswordHasherInterface $hasher,BillingManager $billing):Response
 {
  $this->denyAccessUnlessGranted('ROLE_MASTER');$project=$this->user()->getProjeto();if($request->isMethod('POST')&&!$billing->canAddUser($project)){$this->addFlash('error','O limite de usuários do plano foi atingido.');return $this->redirectToRoute('app_users_index');}$user=(new User())->setProjeto($project)->setRoles(['ROLE_ADMINISTRATIVO']);return $this->form($request,$user,$em,$hasher,true);
 }

 #[Route('/{id}/editar',name:'app_users_edit',requirements:['id'=>'\d+'],methods:['GET','POST'])]
 public function edit(int $id,Request $request,EntityManagerInterface $em,UserPasswordHasherInterface $hasher):Response
 {
  $this->denyAccessUnlessGranted('ROLE_MASTER');$user=$em->getRepository(User::class)->findOneBy(['id'=>$id,'projeto'=>$this->user()->getProjeto()]);if(!$user)throw $this->createNotFoundException();return $this->form($request,$user,$em,$hasher,false);
 }

 #[Route('/{id}/excluir',name:'app_users_delete',requirements:['id'=>'\d+'],methods:['POST'])]
 public function delete(int $id,Request $request,EntityManagerInterface $em):Response
 {
  $this->denyAccessUnlessGranted('ROLE_MASTER');$user=$em->getRepository(User::class)->findOneBy(['id'=>$id,'projeto'=>$this->user()->getProjeto()]);if(!$user)throw $this->createNotFoundException();if($user===$this->getUser())$this->addFlash('error','Você não pode excluir sua própria conta.');elseif($this->isCsrfTokenValid('delete-user-'.$id,(string)$request->request->get('_token'))){$em->remove($user);$em->flush();$this->addFlash('success','Usuário excluído.');}return $this->redirectToRoute('app_users_index');
 }

 private function form(Request $request,User $user,EntityManagerInterface $em,UserPasswordHasherInterface $hasher,bool $new):Response
 {
  $errors=[];if($request->isMethod('POST')){if(!$this->isCsrfTokenValid('user-form',(string)$request->request->get('_token')))throw $this->createAccessDeniedException();$email=mb_strtolower(trim((string)$request->request->get('email')));$existing=$em->getRepository(User::class)->findOneBy(['email'=>$email]);if($existing&&$existing!==$user)$errors[]='Este e-mail já está cadastrado.';$password=(string)$request->request->get('password');if($new&&strlen($password)<8)$errors[]='A senha deve ter pelo menos 8 caracteres.';$role=(string)$request->request->get('role');if(!isset(self::ROLES[$role]))$errors[]='Perfil inválido.';if(!$errors){$user->setNome(trim((string)$request->request->get('nome')))->setEmail($email)->setRoles([$role])->setAtivo($request->request->has('ativo'));if($password!=='')$user->setPassword($hasher->hashPassword($user,$password));$em->persist($user);$em->flush();$this->addFlash('success','Usuário salvo.');return $this->redirectToRoute('app_users_index');}}return $this->render('users/form.html.twig',['managed_user'=>$user,'errors'=>$errors,'is_new'=>$new]);
 }

 private function user():User{$user=$this->getUser();if(!$user instanceof User||$this->isGranted('ROLE_SUPER_ADMIN'))throw $this->createAccessDeniedException('A gestão de usuários de uma ONG exige contexto de projeto.');return $user;}
}
