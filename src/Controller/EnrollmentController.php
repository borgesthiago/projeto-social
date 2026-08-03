<?php
namespace App\Controller;

use App\Entity\{Aluno,FilaEspera,ProjetoSocial,Turma,User};
use App\Service\BillingManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request,Response};
use Symfony\Component\Routing\Attribute\Route;

final class EnrollmentController extends AbstractController
{
 #[Route('/turmas/{id}/matricular',name:'app_turma_enroll',requirements:['id'=>'\d+'],methods:['GET','POST'])]
 public function enroll(int $id,Request $request,EntityManagerInterface $em,BillingManager $billing):Response
 {
  $project=$this->project();if(!$this->isGranted('ROLE_MASTER')&&!$this->isGranted('ROLE_ADMINISTRATIVO'))throw $this->createAccessDeniedException();if(!$billing->hasFeature($project,'gestao_turmas')||!$billing->hasFeature($project,'gestao_alunos'))throw $this->createAccessDeniedException('A gestão de turmas e alunos precisa estar incluída no plano.');
  $turma=$em->getRepository(Turma::class)->findOneBy(['id'=>$id,'projeto'=>$project]);if(!$turma)throw $this->createNotFoundException();
  $candidates=$em->getRepository(Aluno::class)->createQueryBuilder('a')->where('a.projeto = :project')->andWhere('a.turma IS NULL')->andWhere('a.status IN (:statuses)')->setParameter('project',$project)->setParameter('statuses',['ativo','fila'])->orderBy('a.pontuacao','DESC')->addOrderBy('a.nome','ASC')->getQuery()->getResult();
  $errors=[];
  if($request->isMethod('POST')){
   if(!$this->isCsrfTokenValid('enroll-'.$turma->id,(string)$request->request->get('_token')))throw $this->createAccessDeniedException();$studentId=$request->request->getInt('aluno');$student=$em->getRepository(Aluno::class)->findOneBy(['id'=>$studentId,'projeto'=>$project]);if(!$student)$errors[]='Selecione um aluno válido.';elseif($student->turma)$errors[]='Este aluno já está matriculado em uma turma.';else{$age=$student->idade();if($age<$turma->idadeMinima||$age>$turma->idadeMaxima)$errors[]='A idade do aluno não está na faixa etária desta turma.';$total=(int)$em->getRepository(Aluno::class)->createQueryBuilder('a')->select('COUNT(a.id)')->where('a.turma = :turma')->andWhere('a.status = :status')->setParameter('turma',$turma)->setParameter('status','ativo')->getQuery()->getSingleScalarResult();if($total>=$turma->limiteAlunos)$errors[]='A turma está lotada. Não é possível realizar a matrícula.';}
   if(!$errors){$student->turma=$turma;$student->status='ativo';$queue=$em->getRepository(FilaEspera::class)->findOneBy(['projeto'=>$project,'aluno'=>$student,'turmaDesejada'=>$turma]);if($queue)$queue->status='matriculado';$em->flush();$this->addFlash('success',$student->nome.' foi matriculado(a) na turma '.$turma->nome.'.');return $this->redirectToRoute('app_crud_index',['resource'=>'turmas']);}
  }
  $enrolled=(int)$em->getRepository(Aluno::class)->count(['turma'=>$turma,'status'=>'ativo']);return $this->render('turma/enroll.html.twig',['turma'=>$turma,'candidates'=>$candidates,'enrolled'=>$enrolled,'errors'=>$errors]);
 }

 private function project():ProjetoSocial
 {
  $user=$this->getUser();if(!$user instanceof User||!$user->getProjeto())throw $this->createAccessDeniedException();return $user->getProjeto();
 }
}
