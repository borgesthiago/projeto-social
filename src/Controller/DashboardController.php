<?php
namespace App\Controller;
use App\Entity\{Aluno,Aula,FilaEspera,MovimentacaoFinanceira,Responsavel,Turma,User};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\BillingManager;
final class DashboardController extends AbstractController {
 #[Route('/',name:'app_dashboard')]
 public function index(EntityManagerInterface $em): Response {
  if($this->isGranted('ROLE_SUPER_ADMIN'))return $this->redirectToRoute('app_superadmin_index');
  $project=$this->getUser() instanceof User ? $this->getUser()->getProjeto() : null;
  $count=fn(string $c)=>$em->getRepository($c)->count(['projeto'=>$project]);
  return $this->render('dashboard/index.html.twig',['stats'=>['alunos'=>$count(Aluno::class),'turmas'=>$count(Turma::class),'aulas'=>$count(Aula::class),'fila'=>$count(FilaEspera::class)]]);
 }
 #[Route('/modulo/{slug}',name:'app_module')]
 public function module(string $slug,BillingManager $billing): Response {
  $modules=['usuarios'=>'Usuários','aulas'=>'Aulas','alunos'=>'Alunos','responsaveis'=>'Responsáveis','frequencia'=>'Frequência','financeiro'=>'Financeiro','fila-espera'=>'Fila de espera','relatorios'=>'Relatórios','minhas-aulas'=>'Minhas aulas','meus-alunos'=>'Meus alunos','relatorios-financeiros'=>'Relatórios financeiros','meus-filhos'=>'Meus filhos','aulas-responsavel'=>'Aulas','transparencia'=>'Transparência','minhas-aulas-aluno'=>'Minhas aulas','minha-frequencia'=>'Minha frequência','notificacoes'=>'Notificações','configuracoes'=>'Configurações','turmas'=>'Turmas'];
  if(!isset($modules[$slug])) throw $this->createNotFoundException();
  $features=['relatorios'=>'relatorios','relatorios-financeiros'=>'relatorios','transparencia'=>'transparencia','notificacoes'=>'notificacoes'];$user=$this->getUser();if(isset($features[$slug])&&$user instanceof User&&$user->getProjeto()&&!$billing->hasFeature($user->getProjeto(),$features[$slug]))throw $this->createAccessDeniedException('Funcionalidade não incluída no plano.');
  return $this->render('module/index.html.twig',['title'=>$modules[$slug],'slug'=>$slug]);
 }
}
