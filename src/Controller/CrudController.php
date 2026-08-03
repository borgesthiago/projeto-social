<?php
namespace App\Controller;

use App\Entity\{Aluno,FilaEspera,PrioridadeFila,ProjetoSocial,Responsavel,Turma,User};
use App\Service\{BillingManager,CadastroQualification,CrudRegistry,DocumentoValidator};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request,Response};
use Symfony\Component\Routing\Attribute\Route;

#[Route('/gestao/{resource}',requirements:['resource'=>'[a-z-]+'])]
final class CrudController extends AbstractController
{
 public function __construct(private EntityManagerInterface $em,private CrudRegistry $registry,private BillingManager $billing,private DocumentoValidator $documents,private CadastroQualification $qualification){}

 #[Route('',name:'app_crud_index',methods:['GET'])]
 public function index(string $resource,Request $request):Response
 {
  $cfg=$this->config($resource);$project=$this->project();$q=trim((string)$request->query->get('q'));$qb=$this->em->getRepository($cfg['class'])->createQueryBuilder('e')->andWhere('e.projeto = :project')->setParameter('project',$project)->orderBy('e.id','DESC');
  if($q!==''&&$this->hasField($cfg,'nome'))$qb->andWhere('LOWER(e.nome) LIKE :q')->setParameter('q','%'.mb_strtolower($q).'%');
  return $this->render('crud/index.html.twig',['config'=>$cfg,'resource'=>$resource,'items'=>$qb->getQuery()->getResult(),'q'=>$q]);
 }

 #[Route('/novo',name:'app_crud_new',methods:['GET','POST'])]
 public function new(string $resource,Request $request):Response
 {
  $cfg=$this->config($resource);$project=$this->project();if(!$this->billing->canCreate($project,$resource)){$this->addFlash('error','O limite deste recurso no plano foi atingido.');return $this->resourceRedirect($resource);}
  $entity=new $cfg['class']();$entity->projeto=$project;if($entity instanceof FilaEspera&&$request->query->getInt('turma'))$entity->turmaDesejada=$this->owned(Turma::class,$request->query->getInt('turma'));return $this->save($request,$resource,$cfg,$entity,true);
 }

 #[Route('/{id}/editar',name:'app_crud_edit',requirements:['id'=>'\d+'],methods:['GET','POST'])]
 public function edit(string $resource,int $id,Request $request):Response{return $this->save($request,$resource,$this->config($resource),$this->owned($this->config($resource)['class'],$id),false);}

 #[Route('/{id}/excluir',name:'app_crud_delete',requirements:['id'=>'\d+'],methods:['POST'])]
 public function delete(string $resource,int $id,Request $request):Response
 {
  $cfg=$this->config($resource);$entity=$this->owned($cfg['class'],$id);if(!$this->isCsrfTokenValid('delete-'.$resource.'-'.$id,(string)$request->request->get('_token')))throw $this->createAccessDeniedException();if($entity instanceof PrioridadeFila)$this->recalculateStudentScores($entity);$this->em->remove($entity);$this->em->flush();$this->addFlash('success',$cfg['singular'].' excluído(a).');return $this->resourceRedirect($resource);
 }

 private function save(Request $request,string $resource,array $cfg,object $entity,bool $new):Response
 {
  $relations=[];foreach($cfg['fields'] as $field)if($field[2]==='relation')$relations[$field[0]]=$this->em->getRepository($field[4])->findBy(['projeto'=>$this->project()],[$field[5]=>'ASC']);
  $criteria=$entity instanceof Aluno?$this->em->getRepository(PrioridadeFila::class)->findBy(['projeto'=>$this->project(),'ativa'=>true],['nome'=>'ASC']):[];$errors=[];$newGuardian=null;
  if($request->isMethod('POST')){
   if(!$this->isCsrfTokenValid('crud-'.$resource,(string)$request->request->get('_token')))throw $this->createAccessDeniedException();
   foreach($cfg['fields'] as $field){[$name,$label,$type,$required]=$field;if($type==='readonly')continue;$raw=$request->request->all()[$name]??null;if($type==='boolean'){$entity->$name=$raw!==null;continue;}if($required&&($raw===null||trim((string)$raw)==='')){$errors[]="O campo {$label} é obrigatório.";continue;}if($raw===null||$raw===''){$entity->$name=null;continue;}try{$entity->$name=match($type){'date'=>new \DateTimeImmutable((string)$raw),'datetime-local'=>new \DateTimeImmutable((string)$raw),'number'=>in_array($name,['valor','rendaFamiliar'],true)?(string)$raw:(int)$raw,'relation'=>$this->owned($field[4],(int)$raw),'email'=>filter_var($raw,FILTER_VALIDATE_EMAIL)?(string)$raw:throw new \InvalidArgumentException('E-mail inválido.'),default=>(string)$raw};}catch(\Throwable $e){$errors[]="{$label}: {$e->getMessage()}";}}
   if(property_exists($entity,'cpf')){$entity->cpf=$this->documents->normalize($entity->cpf);if(!$this->documents->cpf($entity->cpf))$errors[]='Informe um CPF válido.';else{$duplicate=$this->em->getRepository($cfg['class'])->findOneBy(['projeto'=>$this->project(),'cpf'=>$entity->cpf]);if($duplicate&&$duplicate!==$entity)$errors[]='Este CPF já está cadastrado neste projeto.';}}
   if($entity instanceof Turma){if($entity->idadeMinima<0||$entity->idadeMaxima<$entity->idadeMinima)$errors[]='A faixa etária da turma é inválida.';if($entity->limiteAlunos<1)$errors[]='O limite de alunos deve ser maior que zero.';}
   if($entity instanceof Aluno){$newGuardian=$this->newGuardian($request,$errors);if($newGuardian)$entity->responsavel=$newGuardian;$entity->criterios->clear();$entity->pontuacao=0;foreach($request->request->all('criterios') as $id){$criterion=$this->em->getRepository(PrioridadeFila::class)->findOneBy(['id'=>(int)$id,'projeto'=>$this->project(),'ativa'=>true]);if($criterion&&!$entity->criterios->contains($criterion)){$entity->criterios->add($criterion);$entity->pontuacao+=$criterion->pontos;}}if($entity->isMenor()&&!$entity->responsavel)$errors[]='Responsável é obrigatório para aluno menor de 18 anos.';if($entity->turma){$age=$entity->idade();if($age<$entity->turma->idadeMinima||$age>$entity->turma->idadeMaxima)$errors[]='A idade do aluno não está na faixa etária da turma selecionada.';$qb=$this->em->getRepository(Aluno::class)->createQueryBuilder('a')->select('COUNT(a.id)')->where('a.turma = :turma')->andWhere('a.status = :status')->setParameter('turma',$entity->turma)->setParameter('status','ativo');if($entity->id)$qb->andWhere('a.id != :id')->setParameter('id',$entity->id);if((int)$qb->getQuery()->getSingleScalarResult()>=$entity->turma->limiteAlunos)$errors[]='A turma está lotada. Adicione o interessado à fila de espera desta turma.';}}
   if($entity instanceof FilaEspera&&$entity->turmaDesejada&&$entity->aluno){$age=$entity->aluno->idade();if($age<$entity->turmaDesejada->idadeMinima||$age>$entity->turmaDesejada->idadeMaxima)$errors[]='A idade do aluno não está na faixa etária da turma desejada.';if($entity->aluno->turma===$entity->turmaDesejada&&$entity->aluno->status==='ativo')$errors[]='Este aluno já está matriculado nesta turma.';$duplicate=$this->em->getRepository(FilaEspera::class)->findOneBy(['projeto'=>$this->project(),'aluno'=>$entity->aluno,'turmaDesejada'=>$entity->turmaDesejada]);if($duplicate&&$duplicate!==$entity)$errors[]='Este aluno já está na fila de espera desta turma.';}
   if(!$errors){if($newGuardian)$this->em->persist($newGuardian);$this->em->persist($entity);if($entity instanceof PrioridadeFila)$this->recalculateStudentScores();$this->em->flush();$this->addFlash('success',$cfg['singular'].($new?' criado(a).':' atualizado(a).'));return $this->resourceRedirect($resource);}
  }
  $selectedCriteria=$request->isMethod('POST')?array_map('intval',$request->request->all('criterios')):($entity instanceof Aluno?$entity->criterios->map(fn(PrioridadeFila $c)=>$c->id)->toArray():[]);
  return $this->render('crud/form.html.twig',['config'=>$cfg,'resource'=>$resource,'entity'=>$entity,'relations'=>$relations,'errors'=>$errors,'is_new'=>$new,'criteria'=>$criteria,'selectedCriteria'=>$selectedCriteria,'guardianData'=>$request->request->all('novoResponsavel'),'qualification'=>$entity instanceof Aluno?$this->qualification->evaluate($entity):($entity instanceof Responsavel?$this->qualification->evaluateGuardian($entity):null)]);
 }

 private function newGuardian(Request $request,array &$errors):?Responsavel
 {
  $data=$request->request->all('novoResponsavel');$name=trim((string)($data['nome']??''));if($name==='')return null;$cpf=$this->documents->normalize((string)($data['cpf']??''));$phone=trim((string)($data['telefone']??''));$email=trim((string)($data['email']??''));if(!$this->documents->cpf($cpf))$errors[]='Informe um CPF válido para o novo responsável.';if($phone==='')$errors[]='Informe o telefone do novo responsável.';if($email!==''&&!filter_var($email,FILTER_VALIDATE_EMAIL))$errors[]='Informe um e-mail válido para o novo responsável.';$duplicate=$this->em->getRepository(Responsavel::class)->findOneBy(['projeto'=>$this->project(),'cpf'=>$cpf]);if($duplicate)$errors[]='Já existe um responsável com este CPF. Selecione-o no campo Responsável.';$guardian=new Responsavel();$guardian->projeto=$this->project();$guardian->nome=$name;$guardian->cpf=$cpf;$guardian->telefone=$phone;$guardian->email=$email?:null;$guardian->recebeNotificacoes=isset($data['recebeNotificacoes']);return $guardian;
 }

 private function recalculateStudentScores(?PrioridadeFila $excluded=null):void
 {
  foreach($this->em->getRepository(Aluno::class)->findBy(['projeto'=>$this->project()]) as $student){$student->pontuacao=0;foreach($student->criterios as $criterion)if($criterion!==$excluded&&$criterion->ativa)$student->pontuacao+=$criterion->pontos;}
 }

 private function config(string $resource):array
 {
  try{$cfg=$this->registry->get($resource);}catch(\InvalidArgumentException){throw $this->createNotFoundException();}if(!$this->isGrantedAny($cfg['roles']))throw $this->createAccessDeniedException();$features=['alunos'=>'gestao_alunos','responsaveis'=>'gestao_alunos','professores'=>'gestao_turmas','modalidades'=>'modalidades_cursos','turmas'=>'gestao_turmas','aulas'=>'gestao_aulas','frequencias'=>'frequencia','fila-espera'=>'fila_espera','prioridades'=>'fila_espera','financeiro'=>'financeiro'];$feature=$features[$resource]??null;if($feature&&!$this->billing->hasFeature($this->project(),$feature))throw $this->createAccessDeniedException('Funcionalidade não incluída no plano.');return $cfg;
 }

 private function resourceRedirect(string $resource):Response{return match($resource){'turmas'=>$this->redirectToRoute('app_turma_index'),'aulas'=>$this->redirectToRoute('app_aula_index'),'fila-espera'=>$this->redirectToRoute('app_waitlist_index'),'financeiro'=>$this->redirectToRoute('app_finance_index'),'prioridades'=>$this->redirectToRoute('app_settings',['tab'=>'prioridades']),default=>$this->redirectToRoute('app_crud_index',['resource'=>$resource])};}
 private function project():ProjetoSocial{$user=$this->getUser();if(!$user instanceof User||!$user->getProjeto())throw $this->createAccessDeniedException();return $user->getProjeto();}
 private function owned(string $class,int $id):object{$entity=$this->em->getRepository($class)->findOneBy(['id'=>$id,'projeto'=>$this->project()]);if(!$entity)throw $this->createNotFoundException();return $entity;}
 private function isGrantedAny(array $roles):bool{foreach($roles as $role)if($this->isGranted($role))return true;return false;}
 private function hasField(array $cfg,string $name):bool{foreach($cfg['fields'] as $field)if($field[0]===$name)return true;return false;}
}
