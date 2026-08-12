<?php
namespace App\Controller;

use App\Entity\{AcaoProfissional,AcaoSocial,Aluno,Aula,Frequencia,MovimentacaoFinanceira,Ods,Professor,ProjetoSocial,Turma,User};
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
  $teachers=$this->em->getRepository(Professor::class)->findBy(['projeto'=>$project]);$genderLabels=['feminino'=>'Feminino','masculino'=>'Masculino','nao_binario'=>'Não binário','outro'=>'Outro','nao_informado'=>'Não informado'];$gender=['students'=>array_fill_keys(array_keys($genderLabels),0),'teachers'=>array_fill_keys(array_keys($genderLabels),0)];foreach($students as $person)$gender['students'][$person->sexo?:'nao_informado']++;foreach($teachers as $person)$gender['teachers'][$person->sexo?:'nao_informado']++;$ageLabels=['0-5','6-11','12-17','18-29','30-59','60+'];$ages=['students'=>array_fill_keys($ageLabels,0),'teachers'=>array_fill_keys($ageLabels,0)];foreach([['students',$students],['teachers',$teachers]] as [$group,$people])foreach($people as $person){if(!$person->dataNascimento)continue;$age=$person->dataNascimento->diff(new \DateTimeImmutable('today'))->y;$range=$age<=5?'0-5':($age<=11?'6-11':($age<=17?'12-17':($age<=29?'18-29':($age<=59?'30-59':'60+'))));$ages[$group][$range]++;}$goals=$this->em->getRepository(Ods::class)->findBy(['projeto'=>$project,'ativo'=>true],['numero'=>'ASC']);$ods=[];foreach($goals as $goal)$ods[$goal->id]=['goal'=>$goal,'actions'=>0,'lessons'=>0,'professionals'=>0];foreach($this->em->getRepository(AcaoSocial::class)->findBy(['projeto'=>$project]) as $item)foreach($item->ods as $goal)$ods[$goal->id]['actions']++;foreach($this->em->getRepository(Aula::class)->findBy(['projeto'=>$project]) as $item)foreach($item->ods as $goal)$ods[$goal->id]['lessons']++;foreach($this->em->getRepository(AcaoProfissional::class)->findBy(['projeto'=>$project]) as $item)foreach($item->ods as $goal)$ods[$goal->id]['professionals']++;
  foreach($ods as &$indicator)$indicator+=['students'=>0,'teachers'=>0,'classes'=>0,'modalities'=>0,'providers'=>0];unset($indicator);
  foreach($students as $item)foreach($item->ods as $goal)if(isset($ods[$goal->id]))$ods[$goal->id]['students']++;
  foreach($teachers as $item)foreach($item->ods as $goal)if(isset($ods[$goal->id]))$ods[$goal->id]['teachers']++;
  foreach($classes as $item)foreach($item->ods as $goal)if(isset($ods[$goal->id]))$ods[$goal->id]['classes']++;
  foreach($this->em->getRepository(\App\Entity\Modalidade::class)->findBy(['projeto'=>$project]) as $item)foreach($item->ods as $goal)if(isset($ods[$goal->id]))$ods[$goal->id]['modalities']++;
  foreach($this->em->getRepository(\App\Entity\PrestadorServico::class)->findBy(['projeto'=>$project]) as $item)foreach($item->ods as $goal)if(isset($ods[$goal->id]))$ods[$goal->id]['providers']++;
  return $this->render('report/index.html.twig',['metrics'=>['students'=>count($students),'teachers'=>count($teachers),'classes'=>count($classes),'attendance'=>$attendance,'balance'=>$balance],'studentChart'=>$studentChart,'financeChart'=>$financeChart,'performance'=>$performance,'gender'=>['labels'=>$genderLabels,...$gender],'ages'=>['labels'=>$ageLabels,...$ages],'ods'=>$ods]);
 }

 private function monthLabel(int $month):string{return [1=>'jan',2=>'fev',3=>'mar',4=>'abr',5=>'mai',6=>'jun',7=>'jul',8=>'ago',9=>'set',10=>'out',11=>'nov',12=>'dez'][$month];}
 private function project():ProjetoSocial{$user=$this->getUser();if(!$user instanceof User||!$user->getProjeto())throw $this->createAccessDeniedException();return $user->getProjeto();}
}
