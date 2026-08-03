<?php
namespace App\Controller;

use App\Entity\{Aluno,Aula,AulaFoto,Frequencia,NotificacaoLog,ProjetoSocial,User};
use App\Service\BillingManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{File\UploadedFile,Request,Response};
use Symfony\Component\Routing\Attribute\Route;

#[Route('/frequencia')]
final class AttendanceController extends AbstractController
{
 public function __construct(private EntityManagerInterface $em,private BillingManager $billing){}

 #[Route('',name:'app_attendance_index',methods:['GET'])]
 public function index(Request $request):Response
 {
  $project=$this->project();$this->guard($project);$name=trim((string)$request->query->get('aluno'));$classId=$request->query->getInt('turma');$from=$this->filterDate((string)$request->query->get('inicio'));$to=$this->filterDate((string)$request->query->get('fim'),true);$qb=$this->em->getRepository(Frequencia::class)->createQueryBuilder('f')->join('f.aluno','student')->join('f.aula','lesson')->join('lesson.turma','class')->leftJoin('class.professor','teacher')->where('f.projeto = :project')->setParameter('project',$project)->orderBy('lesson.dataHora','DESC')->addOrderBy('student.nome','ASC');if($name!=='')$qb->andWhere('LOWER(student.nome) LIKE :name')->setParameter('name','%'.mb_strtolower($name).'%');if($classId)$qb->andWhere('class.id = :classId')->setParameter('classId',$classId);if($from)$qb->andWhere('lesson.dataHora >= :from')->setParameter('from',$from);if($to)$qb->andWhere('lesson.dataHora <= :to')->setParameter('to',$to);$classes=$this->em->getRepository(\App\Entity\Turma::class)->findBy(['projeto'=>$project],['nome'=>'ASC']);return $this->render('attendance/index.html.twig',['records'=>$qb->getQuery()->getResult(),'classes'=>$classes,'filters'=>['aluno'=>$name,'turma'=>$classId,'inicio'=>$request->query->get('inicio'),'fim'=>$request->query->get('fim')]]);
 }

 #[Route('/aula/{id}',name:'app_attendance_call',requirements:['id'=>'\d+'],methods:['GET','POST'])]
 public function call(int $id,Request $request):Response
 {
  $project=$this->project();$this->guard($project);$aula=$this->em->getRepository(Aula::class)->findOneBy(['id'=>$id,'projeto'=>$project]);if(!$aula)throw $this->createNotFoundException();if(!$aula->turma)throw $this->createNotFoundException('A aula não possui turma.');$alunos=$this->em->getRepository(Aluno::class)->findBy(['projeto'=>$project,'turma'=>$aula->turma,'status'=>'ativo'],['nome'=>'ASC']);$records=[];foreach($this->em->getRepository(Frequencia::class)->findBy(['projeto'=>$project,'aula'=>$aula]) as $frequency)$records[$frequency->aluno->id]=$frequency;$channels=$this->availableChannels($project);
  if($request->isMethod('POST')){
   if(!$this->isCsrfTokenValid('attendance-'.$aula->id,(string)$request->request->get('_token')))throw $this->createAccessDeniedException();$aula->execucao=trim((string)$request->request->get('execucao'))?:null;$aula->materiais=trim((string)$request->request->get('materiais'))?:null;$situations=$request->request->all('situacao');$justifications=$request->request->all('justificativa');$notify=$request->request->has('notificarAusencias');$selected=array_intersect($request->request->all('canais'),$channels);$notifications=0;
   foreach($alunos as $aluno){$situation=$situations[$aluno->id]??'presente';if(!in_array($situation,['presente','ausente','justificada','atrasado'],true))$situation='presente';$frequency=$records[$aluno->id]??new Frequencia();$frequency->projeto=$project;$frequency->aula=$aula;$frequency->aluno=$aluno;$frequency->situacao=$situation;$frequency->justificativa=trim((string)($justifications[$aluno->id]??''))?:null;$frequency->notificacaoEnviada=false;$this->em->persist($frequency);if($notify&&$situation==='ausente'&&$aluno->responsavel&&$aluno->responsavel->recebeNotificacoes){foreach($selected as $channel){$destination=$channel==='email'?$aluno->responsavel->email:$aluno->responsavel->telefone;if(!$destination||!$this->billing->canNotify($project,$channel))continue;$log=new NotificacaoLog();$log->projeto=$project;$log->canal=$channel;$log->destinatario=$destination;$log->mensagem="Olá, {$aluno->responsavel->nome}. Registramos a ausência de {$aluno->nome} na aula {$aula->titulo}, em ".$aula->dataHora->format('d/m/Y H:i').'.';$log->status='pendente';$this->em->persist($log);$this->em->flush();$frequency->notificacaoEnviada=true;$notifications++;}}}
   $uploaded=$this->savePhotos($request,$project,$aula);$aula->status='finalizada';$this->em->flush();$message='Execução e chamada salvas para '.count($alunos).' aluno(s).';if($uploaded)$message.=" {$uploaded} foto(s) adicionada(s).";if($notifications)$message.=" {$notifications} notificação(ões) adicionada(s) à fila de envio.";$this->addFlash('success',$message);return $this->redirectToRoute('app_attendance_call',['id'=>$aula->id]);
  }
  $photos=$this->em->getRepository(AulaFoto::class)->findBy(['projeto'=>$project,'aula'=>$aula],['criadaEm'=>'DESC']);return $this->render('attendance/call.html.twig',['aula'=>$aula,'alunos'=>$alunos,'records'=>$records,'channels'=>$channels,'photos'=>$photos]);
 }

 #[Route('/aula/{aulaId}/foto/{id}/excluir',name:'app_lesson_photo_delete',requirements:['aulaId'=>'\d+','id'=>'\d+'],methods:['POST'])]
 public function deletePhoto(int $aulaId,int $id,Request $request):Response
 {
  $project=$this->project();$this->guard($project);$photo=$this->em->getRepository(AulaFoto::class)->findOneBy(['id'=>$id,'projeto'=>$project]);if(!$photo||$photo->aula?->id!==$aulaId)throw $this->createNotFoundException();if(!$this->isCsrfTokenValid('delete-lesson-photo-'.$photo->id,(string)$request->request->get('_token')))throw $this->createAccessDeniedException();$absolute=$this->getParameter('kernel.project_dir').'/public'.$photo->caminho;$this->em->remove($photo);$this->em->flush();if(is_file($absolute))@unlink($absolute);$this->addFlash('success','Foto removida da execução.');return $this->redirectToRoute('app_attendance_call',['id'=>$aulaId]);
 }

 private function savePhotos(Request $request,ProjetoSocial $project,Aula $lesson):int
 {
  $files=$request->files->get('fotos',[]);if(!is_array($files))$files=[$files];$files=array_values(array_filter($files,fn($file)=>$file instanceof UploadedFile));if(!$files)return 0;if(count($files)>8){$this->addFlash('error','Envie no máximo 8 fotos por vez.');$files=array_slice($files,0,8);}$subscription=$this->billing->subscription($project);$limitBytes=($subscription?->plano->limiteArmazenamentoMb??0)*1024*1024;$used=(int)$this->em->getRepository(AulaFoto::class)->createQueryBuilder('f')->select('COALESCE(SUM(f.tamanho),0)')->where('f.projeto = :project')->setParameter('project',$project)->getQuery()->getSingleScalarResult();$allowed=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];$directory=$this->getParameter('kernel.project_dir').'/public/uploads/projects/'.$project->id.'/lessons/'.$lesson->id;$saved=0;
  foreach($files as $file){$mime=$file->getMimeType();$size=(int)$file->getSize();if(!$file->isValid()||!isset($allowed[$mime])){$this->addFlash('error','Uma foto foi ignorada. Use apenas JPG, PNG ou WebP.');continue;}if($size>5*1024*1024){$this->addFlash('error','Uma foto foi ignorada por exceder 5 MB.');continue;}if($limitBytes>0&&$used+$size>$limitBytes){$this->addFlash('error','A cota de armazenamento do plano foi atingida.');break;}$filename=bin2hex(random_bytes(12)).'.'.$allowed[$mime];try{if(!is_dir($directory)&&!mkdir($directory,0775,true)&&!is_dir($directory))throw new \RuntimeException();$file->move($directory,$filename);}catch(\Throwable){$this->addFlash('error','Não foi possível salvar uma das fotos.');continue;}$photo=new AulaFoto();$photo->projeto=$project;$photo->aula=$lesson;$photo->caminho='/uploads/projects/'.$project->id.'/lessons/'.$lesson->id.'/'.$filename;$photo->nomeOriginal=mb_substr(basename($file->getClientOriginalName()),0,255);$photo->tamanho=$size;$this->em->persist($photo);$used+=$size;$saved++;}
  return $saved;
 }

 private function guard(ProjetoSocial $project):void{if(!$this->isGranted('ROLE_MASTER')&&!$this->isGranted('ROLE_PROFESSOR'))throw $this->createAccessDeniedException();if(!$this->billing->hasFeature($project,'frequencia'))throw $this->createAccessDeniedException('Funcionalidade não incluída no plano.');}
 private function availableChannels(ProjetoSocial $project):array{$subscription=$this->billing->subscription($project);if(!$subscription||!$this->billing->hasFeature($project,'notificacoes'))return [];return array_values(array_intersect(['email','sms'],$subscription->plano->canaisNotificacao));}
 private function filterDate(string $value,bool $endOfDay=false):?\DateTimeImmutable{if($value==='')return null;try{$date=new \DateTimeImmutable($value);return $endOfDay?$date->setTime(23,59,59):$date->setTime(0,0);}catch(\Throwable){return null;}}
 private function project():ProjetoSocial{$user=$this->getUser();if(!$user instanceof User||!$user->getProjeto())throw $this->createAccessDeniedException();return $user->getProjeto();}
}
