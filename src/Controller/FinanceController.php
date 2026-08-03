<?php
namespace App\Controller;

use App\Entity\{MovimentacaoFinanceira,ProjetoSocial,User};
use App\Service\BillingManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request,Response};
use Symfony\Component\Routing\Attribute\Route;

#[Route('/financeiro')]
final class FinanceController extends AbstractController
{
 public function __construct(private EntityManagerInterface $em,private BillingManager $billing){}

 #[Route('',name:'app_finance_index',methods:['GET','POST'])]
 public function index(Request $request):Response
 {
  $project=$this->project();$this->guard($project);$errors=[];
  if($request->isMethod('POST')){if(!$this->isCsrfTokenValid('finance-new',(string)$request->request->get('_token')))throw $this->createAccessDeniedException();$entry=new MovimentacaoFinanceira();$entry->projeto=$project;$entry->tipo=(string)$request->request->get('tipo');$entry->categoria=trim((string)$request->request->get('categoria'))?:null;$entry->descricao=trim((string)$request->request->get('descricao'));$entry->valor=(string)$request->request->get('valor');$entry->comprovante=trim((string)$request->request->get('comprovante'))?:null;$entry->publica=$request->request->has('publica');try{$entry->data=new \DateTimeImmutable((string)$request->request->get('data'));}catch(\Throwable){$errors[]='Informe uma data válida.';}if(!in_array($entry->tipo,['receita','despesa'],true))$errors[]='Selecione um tipo válido.';if($entry->descricao==='')$errors[]='Informe a descrição.';if(!is_numeric($entry->valor)||(float)$entry->valor<=0)$errors[]='Informe um valor maior que zero.';if(!$errors){$this->em->persist($entry);$this->em->flush();$this->addFlash('success','Movimentação financeira cadastrada.');return $this->redirectToRoute('app_finance_index');}}
  $entries=$this->em->getRepository(MovimentacaoFinanceira::class)->findBy(['projeto'=>$project],['data'=>'DESC','id'=>'DESC']);$income=0.0;$expenses=0.0;foreach($entries as $entry){if($entry->tipo==='receita')$income+=(float)$entry->valor;else$expenses+=(float)$entry->valor;}return $this->render('finance/index.html.twig',['entries'=>$entries,'totals'=>['income'=>$income,'expenses'=>$expenses,'balance'=>$income-$expenses],'errors'=>$errors]);
 }

 private function guard(ProjetoSocial $project):void{if(!$this->isGranted('ROLE_MASTER')&&!$this->isGranted('ROLE_FINANCEIRO'))throw $this->createAccessDeniedException();if(!$this->billing->hasFeature($project,'financeiro'))throw $this->createAccessDeniedException('Financeiro não incluído no plano.');}
 private function project():ProjetoSocial{$user=$this->getUser();if(!$user instanceof User||!$user->getProjeto())throw $this->createAccessDeniedException();return $user->getProjeto();}
}
