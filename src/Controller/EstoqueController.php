<?php
namespace App\Controller;
use App\Entity\{AcaoSocial,Aluno,DistribuicaoKit,ItemEstoque,KitComponente,KitEstoque,MovimentacaoEstoque,ProjetoSocial,Turma,User};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request,Response};
use Symfony\Component\Routing\Attribute\Route;

#[Route('/estoque')]
final class EstoqueController extends AbstractController
{
 public function __construct(private EntityManagerInterface $em){}
 #[Route('',name:'app_stock_index',methods:['GET','POST'])]
 public function index(Request $request):Response
 {
  $project=$this->project();$this->guard();$errors=[];if($request->isMethod('POST')){if(!$this->isCsrfTokenValid('stock-item',(string)$request->request->get('_token')))throw $this->createAccessDeniedException();$item=new ItemEstoque();$item->projeto=$project;$item->nome=trim((string)$request->request->get('nome'));$item->unidade=trim((string)$request->request->get('unidade'));$item->categoria=trim((string)$request->request->get('categoria'))?:null;$item->localizacao=trim((string)$request->request->get('localizacao'))?:null;$item->estoqueMinimo=(string)$request->request->get('estoqueMinimo','0');if($item->nome==='')$errors[]='Informe o item.';if($item->unidade==='')$errors[]='Informe a unidade de medida.';if(!is_numeric($item->estoqueMinimo)||(float)$item->estoqueMinimo<0)$errors[]='Informe um estoque mínimo válido.';if(!$errors){$this->em->persist($item);$this->em->flush();$this->addFlash('success','Item cadastrado.');return $this->redirectToRoute('app_stock_index');}}
  return $this->render('estoque/index.html.twig',['items'=>$this->em->getRepository(ItemEstoque::class)->findBy(['projeto'=>$project],['nome'=>'ASC']),'movements'=>$this->em->getRepository(MovimentacaoEstoque::class)->findBy(['projeto'=>$project],['criadaEm'=>'DESC'],20),'errors'=>$errors]);
 }
 #[Route('/item/{id}/movimentar',name:'app_stock_move',requirements:['id'=>'\d+'],methods:['POST'])]
 public function move(int $id,Request $request):Response
 {
  $this->guard();$item=$this->owned(ItemEstoque::class,$id);if(!$this->isCsrfTokenValid('stock-move-'.$id,(string)$request->request->get('_token')))throw $this->createAccessDeniedException();$type=(string)$request->request->get('tipo');$quantity=(float)$request->request->get('quantidade');$reason=trim((string)$request->request->get('motivo'));if(!in_array($type,['entrada','saida','ajuste'],true)||$quantity<=0||$reason===''){$this->addFlash('error','Informe tipo, quantidade e motivo válidos.');return $this->redirectToRoute('app_stock_index');}$current=(float)$item->quantidade;$next=$type==='entrada'?$current+$quantity:($type==='saida'?$current-$quantity:$quantity);if($next<0){$this->addFlash('error','A saída é maior que o saldo disponível.');return $this->redirectToRoute('app_stock_index');}$movement=new MovimentacaoEstoque();$movement->projeto=$this->project();$movement->item=$item;$movement->tipo=$type;$movement->quantidade=number_format($quantity,3,'.','');$movement->motivo=$reason;$item->quantidade=number_format($next,3,'.','');$this->em->persist($movement);$this->em->flush();return $this->redirectToRoute('app_stock_index');
 }
 #[Route('/kits',name:'app_kits_index',methods:['GET','POST'])]
 public function kits(Request $request):Response
 {
  $project=$this->project();$this->guard();$errors=[];if($request->isMethod('POST')){if(!$this->isCsrfTokenValid('kit-new',(string)$request->request->get('_token')))throw $this->createAccessDeniedException();$kit=new KitEstoque();$kit->projeto=$project;$kit->nome=trim((string)$request->request->get('nome'));$kit->descricao=trim((string)$request->request->get('descricao'))?:null;if($kit->nome==='')$errors[]='Informe o nome do kit.';if(!$errors){$this->em->persist($kit);$this->em->flush();return $this->redirectToRoute('app_kit_builder',['id'=>$kit->id]);}}$kits=$this->em->getRepository(KitEstoque::class)->findBy(['projeto'=>$project],['nome'=>'ASC']);$capacity=[];foreach($kits as $kit)$capacity[$kit->id]=$this->capacity($kit);return $this->render('estoque/kits.html.twig',['kits'=>$kits,'capacity'=>$capacity,'errors'=>$errors]);
 }
 #[Route('/kit/{id}',name:'app_kit_builder',requirements:['id'=>'\d+'],methods:['GET','POST'])]
 public function builder(int $id,Request $request):Response
 {
  $this->guard();$kit=$this->owned(KitEstoque::class,$id);$errors=[];if($request->isMethod('POST')){if(!$this->isCsrfTokenValid('kit-component-'.$id,(string)$request->request->get('_token')))throw $this->createAccessDeniedException();$item=$this->owned(ItemEstoque::class,$request->request->getInt('item'));$quantity=(float)$request->request->get('quantidade');$duplicate=$this->em->getRepository(KitComponente::class)->findOneBy(['projeto'=>$this->project(),'kit'=>$kit,'item'=>$item]);if($quantity<=0)$errors[]='Informe uma quantidade maior que zero.';if($duplicate)$errors[]='Este item já faz parte do kit.';if(!$errors){$component=new KitComponente();$component->projeto=$this->project();$component->kit=$kit;$component->item=$item;$component->quantidade=number_format($quantity,3,'.','');$this->em->persist($component);$this->em->flush();return $this->redirectToRoute('app_kit_builder',['id'=>$id]);}}return $this->render('estoque/builder.html.twig',['kit'=>$kit,'items'=>$this->em->getRepository(ItemEstoque::class)->findBy(['projeto'=>$this->project(),'ativo'=>true],['nome'=>'ASC']),'capacity'=>$this->capacity($kit),'errors'=>$errors]);
 }
 #[Route('/kit/{kitId}/componente/{id}/excluir',name:'app_kit_component_delete',requirements:['kitId'=>'\d+','id'=>'\d+'],methods:['POST'])]
 public function deleteComponent(int $kitId,int $id,Request $request):Response
 {
  $this->guard();$kit=$this->owned(KitEstoque::class,$kitId);$component=$this->owned(KitComponente::class,$id);if($component->kit!==$kit)throw $this->createNotFoundException();if(!$this->isCsrfTokenValid('kit-component-delete-'.$id,(string)$request->request->get('_token')))throw $this->createAccessDeniedException();$this->em->remove($component);$this->em->flush();$this->addFlash('success','Componente removido do kit.');return $this->redirectToRoute('app_kit_builder',['id'=>$kitId]);
 }
 #[Route('/distribuir',name:'app_kit_distribute',methods:['GET','POST'])]
 public function distribute(Request $request):Response
 {
  $project=$this->project();$this->guard();$errors=[];if($request->isMethod('POST')){if(!$this->isCsrfTokenValid('kit-distribute',(string)$request->request->get('_token')))throw $this->createAccessDeniedException();$kit=$this->owned(KitEstoque::class,$request->request->getInt('kit'));$class=$request->request->getInt('turma')?$this->owned(Turma::class,$request->request->getInt('turma')):null;$action=$request->request->getInt('acao')?$this->owned(AcaoSocial::class,$request->request->getInt('acao')):null;$quantity=$request->request->getInt('quantidade');if(($class?1:0)+($action?1:0)!==1)$errors[]='Selecione uma turma ou uma ação/evento.';$audience=$class?$this->em->getRepository(Aluno::class)->count(['projeto'=>$project,'turma'=>$class,'status'=>'ativo']):($action?max(0,$action->participantes):0);if($quantity<1)$errors[]='Informe a quantidade de kits.';if($quantity>$audience)$errors[]="A distribuição de {$quantity} kits excede o público do destino ({$audience}).";if($quantity>$this->capacity($kit))$errors[]='Não há componentes suficientes no estoque para esta quantidade.';if(!$errors){foreach($kit->componentes as $component){$used=(float)$component->quantidade*$quantity;$component->item->quantidade=number_format((float)$component->item->quantidade-$used,3,'.','');$movement=new MovimentacaoEstoque();$movement->projeto=$project;$movement->item=$component->item;$movement->tipo='saida';$movement->quantidade=number_format($used,3,'.','');$movement->motivo="Distribuição de {$quantity} kit(s): {$kit->nome}";$this->em->persist($movement);}$distribution=new DistribuicaoKit();$distribution->projeto=$project;$distribution->kit=$kit;$distribution->turma=$class;$distribution->acao=$action;$distribution->quantidade=$quantity;$distribution->observacoes=trim((string)$request->request->get('observacoes'))?:null;$this->em->persist($distribution);$this->em->flush();$this->addFlash('success','Distribuição registrada e estoque atualizado.');return $this->redirectToRoute('app_kit_distribute');}}
  return $this->render('estoque/distribute.html.twig',['kits'=>$this->em->getRepository(KitEstoque::class)->findBy(['projeto'=>$project,'ativo'=>true],['nome'=>'ASC']),'classes'=>$this->em->getRepository(Turma::class)->findBy(['projeto'=>$project,'ativa'=>true],['nome'=>'ASC']),'actions'=>$this->em->getRepository(AcaoSocial::class)->findBy(['projeto'=>$project],['dataInicio'=>'DESC']),'distributions'=>$this->em->getRepository(DistribuicaoKit::class)->findBy(['projeto'=>$project],['data'=>'DESC','id'=>'DESC']),'errors'=>$errors]);
 }
 private function capacity(KitEstoque $kit):int{if($kit->componentes->isEmpty())return 0;$capacity=PHP_INT_MAX;foreach($kit->componentes as $component)$capacity=min($capacity,(int)floor((float)$component->item->quantidade/(float)$component->quantidade));return max(0,$capacity);}
 private function guard():void{if(!$this->isGranted('ROLE_MASTER')&&!$this->isGranted('ROLE_ADMINISTRATIVO'))throw $this->createAccessDeniedException();}
 private function project():ProjetoSocial{$user=$this->getUser();if(!$user instanceof User||!$user->getProjeto())throw $this->createAccessDeniedException();return $user->getProjeto();}
 private function owned(string $class,int $id):object{$entity=$this->em->getRepository($class)->findOneBy(['id'=>$id,'projeto'=>$this->project()]);if(!$entity)throw $this->createNotFoundException();return $entity;}
}
