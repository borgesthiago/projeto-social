<?php
namespace App\Controller;

use App\Entity\{Aluno,Aula,Frequencia,ProjetoSocial,Turma,User};
use App\Service\BillingManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request,Response};
use Symfony\Component\Routing\Attribute\Route;

#[Route('/turmas')]
final class TurmaController extends AbstractController
{
 public function __construct(private EntityManagerInterface $em,private BillingManager $billing){}

 #[Route('',name:'app_turma_index',methods:['GET'])]
 public function index(Request $request):Response
 {
  $project=$this->project();$this->guard($project);$q=trim((string)$request->query->get('q'));$qb=$this->em->getRepository(Turma::class)->createQueryBuilder('t')->where('t.projeto = :project')->setParameter('project',$project)->orderBy('t.nome','ASC');if($q!=='')$qb->andWhere('LOWER(t.nome) LIKE :q OR LOWER(t.horario) LIKE :q')->setParameter('q','%'.mb_strtolower($q).'%');$classes=$qb->getQuery()->getResult();$occupancy=[];foreach($classes as $class)$occupancy[$class->id]=(int)$this->em->getRepository(Aluno::class)->count(['turma'=>$class,'status'=>'ativo']);return $this->render('turma/index.html.twig',['classes'=>$classes,'occupancy'=>$occupancy,'q'=>$q]);
 }

 #[Route('/{id}',name:'app_turma_show',requirements:['id'=>'\d+'],methods:['GET'])]
 public function show(int $id):Response
 {
  $project=$this->project();$this->guard($project);$class=$this->em->getRepository(Turma::class)->findOneBy(['id'=>$id,'projeto'=>$project]);if(!$class)throw $this->createNotFoundException();$lessons=$this->em->getRepository(Aula::class)->findBy(['projeto'=>$project,'turma'=>$class],['dataHora'=>'ASC']);$students=$this->em->getRepository(Aluno::class)->findBy(['projeto'=>$project,'turma'=>$class,'status'=>'ativo'],['nome'=>'ASC']);$lessonAttendance=[];$rates=[];$attended=0;$totalRecords=0;
  foreach($lessons as $lesson){$lessonAttendance[$lesson->id]=(int)$this->em->getRepository(Frequencia::class)->count(['projeto'=>$project,'aula'=>$lesson]);}
  foreach($students as $student){$records=$this->em->getRepository(Frequencia::class)->createQueryBuilder('f')->join('f.aula','a')->where('f.projeto = :project')->andWhere('f.aluno = :student')->andWhere('a.turma = :class')->setParameter('project',$project)->setParameter('student',$student)->setParameter('class',$class)->getQuery()->getResult();$present=0;foreach($records as $record)if(in_array($record->situacao,['presente','atrasado'],true))$present++;$count=count($records);$rates[$student->id]=$count?round($present*100/$count,1):0;$attended+=$present;$totalRecords+=$count;}
  return $this->render('turma/show.html.twig',['turma'=>$class,'lessons'=>$lessons,'students'=>$students,'lessonAttendance'=>$lessonAttendance,'rates'=>$rates,'stats'=>['lessons'=>count($lessons),'completed'=>count(array_filter($lessons,fn(Aula $a)=>$a->status==='finalizada')),'attendance'=>$totalRecords?round($attended*100/$totalRecords,1):0,'students'=>count($students)]]);
 }

 private function guard(ProjetoSocial $project):void
 {
  if(!$this->isGranted('ROLE_MASTER')&&!$this->isGranted('ROLE_ADMINISTRATIVO')&&!$this->isGranted('ROLE_PROFESSOR'))throw $this->createAccessDeniedException();if(!$this->billing->hasFeature($project,'gestao_turmas'))throw $this->createAccessDeniedException('Gestão de turmas não incluída no plano.');
 }
 private function project():ProjetoSocial{$user=$this->getUser();if(!$user instanceof User||!$user->getProjeto())throw $this->createAccessDeniedException();return $user->getProjeto();}
}
