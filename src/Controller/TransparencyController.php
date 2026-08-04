<?php
namespace App\Controller;

use App\Entity\{Aluno,Aula,ConfiguracaoSistema,MovimentacaoFinanceira,ProjetoSocial,Turma,User};
use App\Service\BillingManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request,Response};
use Symfony\Component\Routing\Attribute\Route;

final class TransparencyController extends AbstractController
{
 public function __construct(private EntityManagerInterface $em,private BillingManager $billing){}

 #[Route('/transparencia',name:'app_transparency',methods:['GET'])]
 public function internal(Request $request):Response
 {
  $user=$this->getUser();if(!$user instanceof User||!$user->getProjeto())throw $this->createAccessDeniedException();$project=$user->getProjeto();$this->guard($project);return $this->render('transparency/index.html.twig',$this->data($project,$request)+['publicMode'=>false]);
 }

 #[Route('/transparencia/publica/{slug}',name:'app_transparency_public',methods:['GET'])]
 public function publicPortal(string $slug,Request $request):Response
 {
  $project=$this->em->getRepository(ProjetoSocial::class)->findOneBy(['slug'=>$slug,'ativo'=>true]);if(!$project||!$this->billing->hasFeature($project,'transparencia'))throw $this->createNotFoundException();return $this->render('transparency/public.html.twig',$this->data($project,$request)+['publicMode'=>true]);
 }

 private function data(ProjetoSocial $project,Request $request):array
 {
  $year=$request->query->getInt('ano');$type=(string)$request->query->get('tipo');$category=trim((string)$request->query->get('categoria'));if($year<2000||$year>2100)$year=0;if(!in_array($type,['','receita','despesa'],true))$type='';
  $all=$this->em->getRepository(MovimentacaoFinanceira::class)->findBy(['projeto'=>$project,'publica'=>true],['data'=>'DESC','id'=>'DESC']);$categories=[];$years=[];foreach($all as $entry){if($entry->categoria)$categories[$entry->categoria]=$entry->categoria;$years[(int)$entry->data->format('Y')]=(int)$entry->data->format('Y');}
  $entries=array_values(array_filter($all,fn(MovimentacaoFinanceira $entry)=>(!$year||(int)$entry->data->format('Y')===$year)&&(!$type||$entry->tipo===$type)&&(!$category||$entry->categoria===$category)));
  $income=0.0;$expenses=0.0;$byCategory=[];$monthly=[];foreach($entries as $entry){$value=(float)$entry->valor;$month=$entry->data->format('Y-m');$monthly[$month]??=['receita'=>0.0,'despesa'=>0.0];$monthly[$month][$entry->tipo]+=$value;if($entry->tipo==='receita')$income+=$value;else{$expenses+=$value;$key=$entry->categoria?:'Sem categoria';$byCategory[$key]=($byCategory[$key]??0)+$value;}}
  ksort($monthly);arsort($byCategory);sort($categories);rsort($years);$settings=[];foreach($this->em->getRepository(ConfiguracaoSistema::class)->findBy(['projeto'=>$project]) as $config)$settings[$config->chave]=$config->valor;
  return ['project'=>$project,'entries'=>$entries,'totals'=>['income'=>$income,'expenses'=>$expenses,'balance'=>$income-$expenses],'monthly'=>$monthly,'byCategory'=>$byCategory,'categories'=>$categories,'years'=>$years,'filters'=>['ano'=>$year,'tipo'=>$type,'categoria'=>$category],'impact'=>['students'=>$this->em->getRepository(Aluno::class)->count(['projeto'=>$project,'status'=>'ativo']),'classes'=>$this->em->getRepository(Turma::class)->count(['projeto'=>$project,'ativa'=>true]),'lessons'=>$this->em->getRepository(Aula::class)->count(['projeto'=>$project,'status'=>'finalizada'])],'settings'=>$settings];
 }
 private function guard(ProjetoSocial $project):void{if(!$this->isGranted('ROLE_MASTER')&&!$this->isGranted('ROLE_FINANCEIRO')&&!$this->isGranted('ROLE_RESPONSAVEL'))throw $this->createAccessDeniedException();if(!$this->billing->hasFeature($project,'transparencia'))throw $this->createAccessDeniedException('Portal de transparência não incluído no plano.');}
}
