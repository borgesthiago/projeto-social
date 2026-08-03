<?php
namespace App\Controller;

use App\Entity\{Aluno,FilaEspera,ProjetoSocial,Turma,User};
use App\Service\BillingManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request,Response};
use Symfony\Component\Routing\Attribute\Route;

#[Route('/fila-espera')]
final class WaitlistController extends AbstractController
{
 public function __construct(private EntityManagerInterface $em,private BillingManager $billing){}

 #[Route('',name:'app_waitlist_index',methods:['GET'])]
 public function index(Request $request):Response
 {
  $project=$this->project();$this->guard($project);$name=trim((string)$request->query->get('nome'));$classId=$request->query->getInt('turma');$status=trim((string)$request->query->get('status'));if(!in_array($status,['','aguardando','convocado','matriculado','desistente'],true))$status='';$classes=$this->em->getRepository(Turma::class)->findBy(['projeto'=>$project],['nome'=>'ASC']);$counts=[];foreach($classes as $class)$counts[$class->id]=(int)$this->em->getRepository(FilaEspera::class)->count(['projeto'=>$project,'turmaDesejada'=>$class,'status'=>'aguardando']);$qb=$this->em->getRepository(FilaEspera::class)->createQueryBuilder('queue')->join('queue.aluno','student')->join('queue.turmaDesejada','class')->leftJoin('student.responsavel','guardian')->where('queue.projeto = :project')->setParameter('project',$project)->orderBy('class.nome','ASC')->addOrderBy('student.pontuacao','DESC')->addOrderBy('queue.inscritoEm','ASC');if($name!=='')$qb->andWhere('LOWER(student.nome) LIKE :name')->setParameter('name','%'.mb_strtolower($name).'%');if($classId)$qb->andWhere('class.id = :classId')->setParameter('classId',$classId);if($status!=='')$qb->andWhere('queue.status = :status')->setParameter('status',$status);$entries=$qb->getQuery()->getResult();$positions=[];$classPositions=[];foreach($entries as $entry)if($entry->status==='aguardando'){$key=$entry->turmaDesejada->id;$classPositions[$key]=($classPositions[$key]??0)+1;$positions[$entry->id]=$classPositions[$key];}return $this->render('waitlist/index.html.twig',['entries'=>$entries,'classes'=>$classes,'counts'=>$counts,'positions'=>$positions,'filters'=>['nome'=>$name,'turma'=>$classId,'status'=>$status]]);
 }

 #[Route('/{id}/matricular',name:'app_waitlist_enroll',requirements:['id'=>'\d+'],methods:['POST'])]
 public function enroll(int $id,Request $request):Response
 {
  $project=$this->project();$this->guard($project);$entry=$this->em->getRepository(FilaEspera::class)->findOneBy(['id'=>$id,'projeto'=>$project]);if(!$entry)throw $this->createNotFoundException();if(!$this->isCsrfTokenValid('waitlist-enroll-'.$entry->id,(string)$request->request->get('_token')))throw $this->createAccessDeniedException();$student=$entry->aluno;$class=$entry->turmaDesejada;if($entry->status!=='aguardando'&&$entry->status!=='convocado')$this->addFlash('error','Esta inscrição não está disponível para matrícula.');elseif($student->turma)$this->addFlash('error','O aluno já está matriculado em uma turma.');elseif($student->idade()<$class->idadeMinima||$student->idade()>$class->idadeMaxima)$this->addFlash('error','O aluno não está na faixa etária da turma.');elseif($this->em->getRepository(Aluno::class)->count(['turma'=>$class,'status'=>'ativo'])>=$class->limiteAlunos)$this->addFlash('error','A turma está lotada.');else{$student->turma=$class;$student->status='ativo';$entry->status='matriculado';$this->em->flush();$this->addFlash('success',$student->nome.' foi matriculado(a) na turma '.$class->nome.'.');}return $this->redirectToRoute('app_waitlist_index');
 }

 private function guard(ProjetoSocial $project):void{if(!$this->isGranted('ROLE_MASTER')&&!$this->isGranted('ROLE_ADMINISTRATIVO'))throw $this->createAccessDeniedException();if(!$this->billing->hasFeature($project,'fila_espera'))throw $this->createAccessDeniedException('Fila de espera não incluída no plano.');}
 private function project():ProjetoSocial{$user=$this->getUser();if(!$user instanceof User||!$user->getProjeto())throw $this->createAccessDeniedException();return $user->getProjeto();}
}
