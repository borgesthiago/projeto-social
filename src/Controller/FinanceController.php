<?php
namespace App\Controller;

use App\Entity\{AulaFoto,MovimentacaoFinanceira,ProjetoSocial,User};
use App\Service\BillingManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{File\UploadedFile,Request,Response};
use Symfony\Component\Routing\Attribute\Route;

#[Route('/financeiro')]
final class FinanceController extends AbstractController
{
 public function __construct(private EntityManagerInterface $em,private BillingManager $billing){}

 #[Route('',name:'app_finance_index',methods:['GET','POST'])]
 public function index(Request $request):Response
 {
  $project=$this->project();$this->guard($project);$errors=[];
  if($request->isMethod('POST')){if(!$this->isCsrfTokenValid('finance-new',(string)$request->request->get('_token')))throw $this->createAccessDeniedException();$entry=new MovimentacaoFinanceira();$entry->projeto=$project;$entry->tipo=(string)$request->request->get('tipo');$entry->categoria=trim((string)$request->request->get('categoria'))?:null;$entry->descricao=trim((string)$request->request->get('descricao'));$entry->valor=(string)$request->request->get('valor');$entry->comprovante=trim((string)$request->request->get('comprovanteUrl'))?:null;$entry->publica=$request->request->has('publica');try{$entry->data=new \DateTimeImmutable((string)$request->request->get('data'));}catch(\Throwable){$errors[]='Informe uma data válida.';}if(!in_array($entry->tipo,['receita','despesa'],true))$errors[]='Selecione um tipo válido.';if($entry->descricao==='')$errors[]='Informe a descrição.';if(!is_numeric($entry->valor)||(float)$entry->valor<=0)$errors[]='Informe um valor maior que zero.';if(!$errors)$this->saveReceipt($request,$project,$entry,$errors);if(!$errors){$this->em->persist($entry);$this->em->flush();$this->addFlash('success','Movimentação financeira cadastrada.');return $this->redirectToRoute('app_finance_index');}}
  $entries=$this->em->getRepository(MovimentacaoFinanceira::class)->findBy(['projeto'=>$project],['data'=>'DESC','id'=>'DESC']);$income=0.0;$expenses=0.0;foreach($entries as $entry){if($entry->tipo==='receita')$income+=(float)$entry->valor;else$expenses+=(float)$entry->valor;}return $this->render('finance/index.html.twig',['entries'=>$entries,'totals'=>['income'=>$income,'expenses'=>$expenses,'balance'=>$income-$expenses],'errors'=>$errors]);
 }

 private function saveReceipt(Request $request,ProjetoSocial $project,MovimentacaoFinanceira $entry,array &$errors):void
 {
  $file=$request->files->get('comprovanteArquivo');if(!$file instanceof UploadedFile)return;$allowed=['application/pdf'=>'pdf','image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];$mime=$file->getMimeType();$size=(int)$file->getSize();if(!$file->isValid()||!isset($allowed[$mime])){$errors[]='O comprovante deve ser PDF, JPG, PNG ou WebP.';return;}if($size>3*1024*1024){$errors[]='O comprovante deve ter no máximo 3 MB.';return;}$subscription=$this->billing->subscription($project);$limit=($subscription?->plano->limiteArmazenamentoMb??0)*1024*1024;$photos=(int)$this->em->getRepository(AulaFoto::class)->createQueryBuilder('f')->select('COALESCE(SUM(f.tamanho),0)')->where('f.projeto = :project')->setParameter('project',$project)->getQuery()->getSingleScalarResult();$receipts=(int)$this->em->getRepository(MovimentacaoFinanceira::class)->createQueryBuilder('m')->select('COALESCE(SUM(m.comprovanteTamanho),0)')->where('m.projeto = :project')->setParameter('project',$project)->getQuery()->getSingleScalarResult();if($limit>0&&$photos+$receipts+$size>$limit){$errors[]='A cota de armazenamento do plano foi atingida.';return;}$directory=$this->getParameter('kernel.project_dir').'/public/uploads/projects/'.$project->id.'/finance';$filename=bin2hex(random_bytes(16)).'.'.$allowed[$mime];try{if(!is_dir($directory)&&!mkdir($directory,0775,true)&&!is_dir($directory))throw new \RuntimeException();$file->move($directory,$filename);}catch(\Throwable){$errors[]='Não foi possível salvar o comprovante.';return;}$entry->comprovante='/uploads/projects/'.$project->id.'/finance/'.$filename;$entry->comprovanteNome=mb_substr(basename($file->getClientOriginalName()),0,255);$entry->comprovanteMime=$mime;$entry->comprovanteTamanho=$size;
 }

 private function guard(ProjetoSocial $project):void{if(!$this->isGranted('ROLE_MASTER')&&!$this->isGranted('ROLE_FINANCEIRO'))throw $this->createAccessDeniedException();if(!$this->billing->hasFeature($project,'financeiro'))throw $this->createAccessDeniedException('Financeiro não incluído no plano.');}
 private function project():ProjetoSocial{$user=$this->getUser();if(!$user instanceof User||!$user->getProjeto())throw $this->createAccessDeniedException();return $user->getProjeto();}
}
