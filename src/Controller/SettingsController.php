<?php
namespace App\Controller;

use App\Entity\{Aluno,ConfiguracaoSistema,PrioridadeFila,ProjetoSocial,User};
use App\Service\{BillingManager,CadastroQualification};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{File\UploadedFile,Request,Response};
use Symfony\Component\Routing\Attribute\Route;

final class SettingsController extends AbstractController
{
 private const TABS=['gerais','educacionais','qualificacao','notificacoes','evolution','mensagens','prioridades'];
 public function __construct(private EntityManagerInterface $em,private BillingManager $billing,private CadastroQualification $qualification){}

 #[Route('/configuracoes/{tab}',name:'app_settings',defaults:['tab'=>'gerais'],requirements:['tab'=>'gerais|educacionais|qualificacao|notificacoes|evolution|mensagens|prioridades'],methods:['GET','POST'])]
 public function index(string $tab,Request $request):Response
 {
  $this->denyAccessUnlessGranted('ROLE_MASTER');$project=$this->project();if(!in_array($tab,self::TABS,true))throw $this->createNotFoundException();if($tab==='evolution'&&!$this->billing->hasFeature($project,'whatsapp_evolution'))throw $this->createAccessDeniedException('Evolution API não incluída no plano.');$errors=[];
  if($request->isMethod('POST')){if(!$this->isCsrfTokenValid('settings-'.$tab,(string)$request->request->get('_token')))throw $this->createAccessDeniedException();if($tab==='gerais')$this->saveGeneral($request,$project,$errors);else $this->saveValues($tab,$request);if(!$errors){$this->em->flush();$this->addFlash('success','Configurações salvas.');return $this->redirectToRoute('app_settings',['tab'=>$tab]);}}
  $values=$this->values($project);$priorities=$this->em->getRepository(PrioridadeFila::class)->findBy(['projeto'=>$project],['pontos'=>'DESC','nome'=>'ASC']);$students=$this->em->getRepository(Aluno::class)->findBy(['projeto'=>$project]);$levels=['Incompleto'=>0,'Bronze'=>0,'Prata'=>0,'Ouro'=>0];foreach($students as $student)$levels[$this->qualification->evaluate($student)['level']]++;return $this->render('settings/index.html.twig',['tab'=>$tab,'project'=>$project,'values'=>$values,'priorities'=>$priorities,'students'=>$students,'levels'=>$levels,'thresholds'=>$this->qualification->thresholds($project),'errors'=>$errors,'evolutionEnabled'=>$this->billing->hasFeature($project,'whatsapp_evolution'),'identityEnabled'=>$this->billing->hasFeature($project,'identidade_visual')]);
 }

 private function saveGeneral(Request $request,ProjetoSocial $project,array &$errors):void
 {
  $name=trim((string)$request->request->get('nome'));$email=trim((string)$request->request->get('email'));if(mb_strlen($name)<3)$errors[]='Informe o nome do projeto.';else$project->nome=$name;if($email!==''&&!filter_var($email,FILTER_VALIDATE_EMAIL))$errors[]='Informe um e-mail válido.';else$project->email=$email?:null;$project->telefone=trim((string)$request->request->get('telefone'))?:null;$project->slogan=mb_substr(trim((string)$request->request->get('slogan')),0,160)?:null;$this->setValue($project,'geral','endereco',(string)$request->request->get('endereco'));$this->setValue($project,'geral','descricao',(string)$request->request->get('descricao'));if(!$this->billing->hasFeature($project,'identidade_visual'))return;if($request->request->has('remove_logo'))$project->logoPath=null;if($request->request->has('remove_banner'))$project->bannerPath=null;foreach(['logo'=>['logoPath',1024*1024],'banner'=>['bannerPath',2*1024*1024]] as $name=>[$property,$limit]){$file=$request->files->get($name);if($file instanceof UploadedFile){$path=$this->upload($file,$project,$name,$limit,$errors);if($path)$project->$property=$path;}}
 }

 private function saveValues(string $tab,Request $request):void
 {
  $fields=match($tab){'educacionais'=>['frequencia_minima','aulas_minimas_mes','tolerancia_atraso'],'qualificacao'=>['bronze','prata','ouro'],'notificacoes'=>['notificar_faltas','notificar_pagamento','notificar_vaga','antecedencia_dias'],'evolution'=>['evolution_url','evolution_token','evolution_instancia','evolution_dev','evolution_celular_teste'],'mensagens'=>['mensagem_falta','mensagem_presenca','mensagem_vaga','mensagem_matricula','mensagem_pagamento'],default=>[]};foreach($fields as $field){$value=in_array($field,['notificar_faltas','notificar_pagamento','notificar_vaga','evolution_dev'],true)?($request->request->has($field)?'1':'0'):(string)$request->request->get($field);$this->setValue($this->project(),$tab,$field,$value);}
 }

 private function values(ProjetoSocial $project):array{$values=[];foreach($this->em->getRepository(ConfiguracaoSistema::class)->findBy(['projeto'=>$project]) as $config)$values[$config->chave]=$config->valor;return $values;}
 private function setValue(ProjetoSocial $project,string $group,string $key,string $value):void{$config=$this->em->getRepository(ConfiguracaoSistema::class)->findOneBy(['projeto'=>$project,'chave'=>$key])??new ConfiguracaoSistema();$config->projeto=$project;$config->grupo=$group;$config->chave=$key;$config->valor=trim($value);$config->tipo='texto';$this->em->persist($config);}
 private function upload(UploadedFile $file,ProjetoSocial $project,string $kind,int $limit,array &$errors):?string{if(!$file->isValid()||$file->getSize()>$limit){$errors[]='A imagem de '.$kind.' excede o tamanho permitido.';return null;}$allowed=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];$mime=$file->getMimeType();if(!isset($allowed[$mime])){$errors[]='Use imagens JPG, PNG ou WebP.';return null;}$directory=$this->getParameter('kernel.project_dir').'/public/uploads/projects/'.$project->id;$filename=$kind.'-'.bin2hex(random_bytes(8)).'.'.$allowed[$mime];try{if(!is_dir($directory)&&!mkdir($directory,0775,true)&&!is_dir($directory))throw new \RuntimeException();$file->move($directory,$filename);}catch(\Throwable){$errors[]='Não foi possível salvar a imagem.';return null;}return '/uploads/projects/'.$project->id.'/'.$filename;}
 private function project():ProjetoSocial{$user=$this->getUser();if(!$user instanceof User||!$user->getProjeto())throw $this->createAccessDeniedException();return $user->getProjeto();}
}
