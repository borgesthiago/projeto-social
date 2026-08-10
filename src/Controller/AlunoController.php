<?php
namespace App\Controller;
use App\Entity\{Aluno,AlunoFoto,AulaFoto,MovimentacaoFinanceira,ProjetoSocial,User};
use App\Service\BillingManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{File\UploadedFile,Request,Response};
use Symfony\Component\Routing\Attribute\Route;

#[Route('/gestao/alunos')]
final class AlunoController extends AbstractController
{
 public function __construct(private EntityManagerInterface $em,private BillingManager $billing){}
 #[Route('/{id}',name:'app_aluno_show',requirements:['id'=>'\d+'],methods:['GET','POST'],priority:10)]
 public function show(int $id,Request $request):Response
 {
  $project=$this->project();$this->guard($project);$aluno=$this->em->getRepository(Aluno::class)->findOneBy(['id'=>$id,'projeto'=>$project]);if(!$aluno)throw $this->createNotFoundException();
  if($request->isMethod('POST')){if(!$this->isCsrfTokenValid('student-photos-'.$aluno->id,(string)$request->request->get('_token')))throw $this->createAccessDeniedException();$saved=$this->savePhotos($request,$project,$aluno);$this->em->flush();if($saved)$this->addFlash('success',"{$saved} foto(s) adicionada(s) ao aluno.");return $this->redirectToRoute('app_aluno_show',['id'=>$aluno->id]);}
  return $this->render('aluno/show.html.twig',['aluno'=>$aluno,'photos'=>$this->em->getRepository(AlunoFoto::class)->findBy(['projeto'=>$project,'aluno'=>$aluno],['criadaEm'=>'DESC'])]);
 }
 #[Route('/{alunoId}/foto/{id}/excluir',name:'app_aluno_photo_delete',requirements:['alunoId'=>'\d+','id'=>'\d+'],methods:['POST'],priority:10)]
 public function deletePhoto(int $alunoId,int $id,Request $request):Response
 {
  $project=$this->project();$this->guard($project);$photo=$this->em->getRepository(AlunoFoto::class)->findOneBy(['id'=>$id,'projeto'=>$project]);if(!$photo||$photo->aluno?->id!==$alunoId)throw $this->createNotFoundException();if(!$this->isCsrfTokenValid('delete-student-photo-'.$photo->id,(string)$request->request->get('_token')))throw $this->createAccessDeniedException();$absolute=$this->getParameter('kernel.project_dir').'/public'.$photo->caminho;$this->em->remove($photo);$this->em->flush();if(is_file($absolute))@unlink($absolute);$this->addFlash('success','Foto removida do aluno.');return $this->redirectToRoute('app_aluno_show',['id'=>$alunoId]);
 }
 private function savePhotos(Request $request,ProjetoSocial $project,Aluno $aluno):int
 {
  $files=$request->files->get('fotos',[]);if(!is_array($files))$files=[$files];$files=array_values(array_filter($files,fn($file)=>$file instanceof UploadedFile));if(!$files)return 0;if(count($files)>8){$this->addFlash('error','Envie no máximo 8 fotos por vez.');$files=array_slice($files,0,8);}$subscription=$this->billing->subscription($project);$limit=($subscription?->plano->limiteArmazenamentoMb??0)*1024*1024;$used=$this->usedStorage($project);$allowed=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];$directory=$this->getParameter('kernel.project_dir').'/public/uploads/projects/'.$project->id.'/students/'.$aluno->id;$saved=0;
  foreach($files as $file){$mime=$file->getMimeType();$size=(int)$file->getSize();if(!$file->isValid()||!isset($allowed[$mime])){$this->addFlash('error','Uma foto foi ignorada. Use apenas JPG, PNG ou WebP.');continue;}if($size>5*1024*1024){$this->addFlash('error','Uma foto foi ignorada por exceder 5 MB.');continue;}if($limit>0&&$used+$size>$limit){$this->addFlash('error','A cota de armazenamento do plano foi atingida.');break;}$filename=bin2hex(random_bytes(16)).'.'.$allowed[$mime];try{if(!is_dir($directory)&&!mkdir($directory,0775,true)&&!is_dir($directory))throw new \RuntimeException();$file->move($directory,$filename);}catch(\Throwable){$this->addFlash('error','Não foi possível salvar uma das fotos.');continue;}$photo=new AlunoFoto();$photo->projeto=$project;$photo->aluno=$aluno;$photo->caminho='/uploads/projects/'.$project->id.'/students/'.$aluno->id.'/'.$filename;$photo->nomeOriginal=mb_substr(basename($file->getClientOriginalName()),0,255);$photo->tamanho=$size;$this->em->persist($photo);$used+=$size;$saved++;}return $saved;
 }
 private function usedStorage(ProjetoSocial $project):int{$sum=fn(string $class,string $field)=>(int)$this->em->getRepository($class)->createQueryBuilder('e')->select("COALESCE(SUM(e.{$field}),0)")->where('e.projeto = :project')->setParameter('project',$project)->getQuery()->getSingleScalarResult();return $sum(AlunoFoto::class,'tamanho')+$sum(AulaFoto::class,'tamanho')+$sum(MovimentacaoFinanceira::class,'comprovanteTamanho');}
 private function guard(ProjetoSocial $project):void{if(!$this->isGranted('ROLE_MASTER')&&!$this->isGranted('ROLE_ADMINISTRATIVO'))throw $this->createAccessDeniedException();if(!$this->billing->hasFeature($project,'gestao_alunos'))throw $this->createAccessDeniedException('Gestão de alunos não incluída no plano.');}
 private function project():ProjetoSocial{$user=$this->getUser();if(!$user instanceof User||!$user->getProjeto())throw $this->createAccessDeniedException();return $user->getProjeto();}
}
