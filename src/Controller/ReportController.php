<?php
namespace App\Controller;

use App\Entity\{Aluno,Frequencia,MovimentacaoFinanceira,ProjetoSocial,Turma,User};
use App\Service\BillingManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReportController extends AbstractController
{
 public function __construct(private EntityManagerInterface $em,private BillingManager $billing){}

 #[Route('/relatorios',name:'app_reports_index',methods:['GET'])]
 public function index():Response
 {
  $project=$this->project();$this->denyAccessUnlessGranted('ROLE_MASTER');if(!$this->billing->hasFeature($project,'relatorios'))throw $this->createAccessDeniedException('Relatórios não incluídos no plano.');$students=$this->em->getRepository(Aluno::class)->findBy(['projeto'=>$project]);$classes=$this->em->getRepository(Turma::class)->findBy(['projeto'=>$project],['nome'=>'ASC']);$frequencies=$this->em->getRepository(Frequencia::class)->findBy(['projeto'=>$project]);$movements=$this->em->getRepository(MovimentacaoFinanceira::class)->findBy(['projeto'=>$project]);$present=count(array_filter($frequencies,fn(Frequencia $f)=>in_array($f->situacao,['presente','atrasado'],true)));$attendance=count($frequencies)?round($present*100/count($frequencies),1):0;$balance=0.0;foreach($movements as $movement)$balance+=($movement->tipo==='receita'?1:-1)*(float)$movement->valor;
  $statusLabels=['ativo'=>'Ativos','inativo'=>'Inativos','fila'=>'Fila de espera','egresso'=>'Egressos'];$statusCounts=array_fill_keys(array_keys($statusLabels),0);foreach($students as $student)$statusCounts[$student->status]=($statusCounts[$student->status]??0)+1;$studentChart=['labels'=>array_values($statusLabels),'values'=>array_values($statusCounts),'colors'=>['#3b82f6','#94a3b8','#f59e0b','#8b5cf6']];
  $months=[];$monthKeys=[];for($i=5;$i>=0;$i--){$date=(new \DateTimeImmutable('first day of this month'))->modify("-{$i} months");$key=$date->format('Y-m');$monthKeys[]=$key;$months[$key]=['label'=>$this->monthLabel((int)$date->format('n')).'/'.$date->format('y'),'income'=>0.0,'expenses'=>0.0];}foreach($movements as $movement){$key=$movement->data->format('Y-m');if(isset($months[$key]))$months[$key][$movement->tipo==='receita'?'income':'expenses']+=(float)$movement->valor;}$financeChart=['labels'=>array_column(array_values($months),'label'),'income'=>array_column(array_values($months),'income'),'expenses'=>array_column(array_values($months),'expenses')];
  $performance=[];foreach($classes as $class){$classStudents=array_values(array_filter($students,fn(Aluno $a)=>$a->turma===$class&&$a->status==='ativo'));$classRecords=[];foreach($frequencies as $frequency)if($frequency->aula?->turma===$class)$classRecords[]=$frequency;$classPresent=count(array_filter($classRecords,fn(Frequencia $f)=>in_array($f->situacao,['presente','atrasado'],true)));$performance[]=['class'=>$class,'enrolled'=>count($classStudents),'attendance'=>count($classRecords)?round($classPresent*100/count($classRecords),1):0];}
  return $this->render('report/index.html.twig',['metrics'=>['students'=>count($students),'classes'=>count($classes),'attendance'=>$attendance,'balance'=>$balance],'studentChart'=>$studentChart,'financeChart'=>$financeChart,'performance'=>$performance]);
 }

 private function monthLabel(int $month):string{return [1=>'jan',2=>'fev',3=>'mar',4=>'abr',5=>'mai',6=>'jun',7=>'jul',8=>'ago',9=>'set',10=>'out',11=>'nov',12=>'dez'][$month];}
 private function project():ProjetoSocial{$user=$this->getUser();if(!$user instanceof User||!$user->getProjeto())throw $this->createAccessDeniedException();return $user->getProjeto();}
}
