<?php
namespace App\Controller;

use App\Entity\{Aula,Frequencia,ProjetoSocial,User};
use App\Service\BillingManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request,Response};
use Symfony\Component\Routing\Attribute\Route;

#[Route('/aulas')]
final class AulaController extends AbstractController
{
 public function __construct(private EntityManagerInterface $em,private BillingManager $billing){}

 #[Route('',name:'app_aula_index',methods:['GET'])]
 public function index(Request $request):Response
 {
  $project=$this->project();$this->guard($project);$q=trim((string)$request->query->get('q'));$qb=$this->em->getRepository(Aula::class)->createQueryBuilder('a')->leftJoin('a.turma','t')->where('a.projeto = :project')->setParameter('project',$project)->orderBy('a.dataHora','DESC');if($q!=='')$qb->andWhere('LOWER(a.titulo) LIKE :q OR LOWER(t.nome) LIKE :q')->setParameter('q','%'.mb_strtolower($q).'%');$lessons=$qb->getQuery()->getResult();$attendance=[];$stats=['planejada'=>0,'andamento'=>0,'finalizada'=>0,'cancelada'=>0];foreach($lessons as $lesson){$stats[$lesson->status]=($stats[$lesson->status]??0)+1;$attendance[$lesson->id]=(int)$this->em->getRepository(Frequencia::class)->count(['projeto'=>$project,'aula'=>$lesson]);}return $this->render('aula/index.html.twig',['lessons'=>$lessons,'attendance'=>$attendance,'stats'=>$stats,'q'=>$q]);
 }

 private function guard(ProjetoSocial $project):void{if(!$this->isGranted('ROLE_MASTER')&&!$this->isGranted('ROLE_ADMINISTRATIVO')&&!$this->isGranted('ROLE_PROFESSOR'))throw $this->createAccessDeniedException();if(!$this->billing->hasFeature($project,'gestao_aulas'))throw $this->createAccessDeniedException('Gestão de aulas não incluída no plano.');}
 private function project():ProjetoSocial{$user=$this->getUser();if(!$user instanceof User||!$user->getProjeto())throw $this->createAccessDeniedException();return $user->getProjeto();}
}
