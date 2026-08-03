<?php
namespace App\Controller;
use App\Entity\{ProjetoSocial,User};
use App\Service\{BillingManager,DocumentoValidator};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request,Response};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;
final class RegistrationController extends AbstractController {
 #[Route('/cadastro',name:'app_register',methods:['GET','POST'])]
 public function register(Request $request,EntityManagerInterface $em,UserPasswordHasherInterface $hasher,BillingManager $billing,DocumentoValidator $documents):Response {
  if($request->isMethod('POST')){if(!$this->isCsrfTokenValid('register',(string)$request->request->get('_token')))throw $this->createAccessDeniedException();$nomeProjeto=trim((string)$request->request->get('projeto'));$cnpj=$documents->normalize((string)$request->request->get('cnpj'));$nome=trim((string)$request->request->get('nome'));$email=mb_strtolower(trim((string)$request->request->get('email')));$senha=(string)$request->request->get('password');$errors=[];if(mb_strlen($nomeProjeto)<3)$errors[]='Informe o nome do projeto.';if(!$documents->cnpj($cnpj))$errors[]='Informe um CNPJ válido.';elseif($em->getRepository(ProjetoSocial::class)->findOneBy(['cnpj'=>$cnpj]))$errors[]='Este CNPJ já está cadastrado.';if(mb_strlen($nome)<3)$errors[]='Informe seu nome.';if(!filter_var($email,FILTER_VALIDATE_EMAIL))$errors[]='Informe um e-mail válido.';if(strlen($senha)<8)$errors[]='A senha deve ter pelo menos 8 caracteres.';if($em->getRepository(User::class)->findOneBy(['email'=>$email]))$errors[]='Este e-mail já está cadastrado.';if(!$errors){$slug=(new AsciiSlugger())->slug($nomeProjeto)->lower()->toString().'-'.substr(bin2hex(random_bytes(3)),0,6);$p=new ProjetoSocial();$p->nome=$nomeProjeto;$p->cnpj=$cnpj;$p->slug=$slug;$p->email=$email;$u=(new User())->setNome($nome)->setEmail($email)->setRoles(['ROLE_MASTER'])->setProjeto($p);$u->setPassword($hasher->hashPassword($u,$senha));$em->persist($p);$em->persist($u);$billing->createTrial($p);$em->flush();$this->addFlash('success','Projeto criado com período de teste. Entre com seus dados.');return $this->redirectToRoute('app_login');}return $this->render('security/register.html.twig',['errors'=>$errors]);}return $this->render('security/register.html.twig',['errors'=>[]]);
 }
}
