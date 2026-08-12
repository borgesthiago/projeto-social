<?php
namespace App\Controller;
use App\Entity\{Formulario,FormularioResposta};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request,Response};
use Symfony\Component\Routing\Attribute\Route;
final class FormularioPublicoController extends AbstractController
{
 #[Route('/f/{token}',name:'app_public_form',requirements:['token'=>'[a-f0-9]{32}'],methods:['GET','POST'])]
 public function form(string $token,Request $request,EntityManagerInterface $em):Response
 {
  $form=$em->getRepository(Formulario::class)->findOneBy(['token'=>$token]);if(!$form)throw $this->createNotFoundException();$errors=[];$sent=false;if($request->isMethod('POST')){if(!$form->disponivel())$errors[]='Este formulário não está recebendo respostas.';if(!$this->isCsrfTokenValid('public-form-'.$token,(string)$request->request->get('_token')))throw $this->createAccessDeniedException();$values=$request->request->all('campo');$answers=[];foreach($form->campos as $field){$value=$values[$field->id]??null;if($field->obrigatorio&&($value===null||$value===''||$value===[]))$errors[]='Responda: '.$field->rotulo;if($value!==null)$answers[(string)$field->id]=is_array($value)?array_values($value):trim((string)$value);} $email=trim((string)$request->request->get('email'));if($email!==''&&!filter_var($email,FILTER_VALIDATE_EMAIL))$errors[]='Informe um e-mail válido.';if(!$errors){$answer=new FormularioResposta();$answer->projeto=$form->projeto;$answer->formulario=$form;$answer->nome=trim((string)$request->request->get('nome'))?:null;$answer->email=$email?:null;$answer->telefone=trim((string)$request->request->get('telefone'))?:null;$answer->respostas=$answers;$em->persist($answer);$em->flush();$sent=true;}}
  return $this->render('formulario/public.html.twig',['form'=>$form,'errors'=>$errors,'sent'=>$sent]);
 }
}
