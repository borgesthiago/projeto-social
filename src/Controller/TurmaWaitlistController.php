<?php
namespace App\Controller;
use App\Entity\{FilaEspera,Turma,User};
use App\Service\BillingManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
final class TurmaWaitlistController extends AbstractController {
 #[Route('/turmas/{id}/fila-espera',name:'app_turma_waitlist',requirements:['id'=>'\d+'],methods:['GET'])]
 public function index(int $id,EntityManagerInterface $em,BillingManager $billing):Response{$user=$this->getUser();if(!$user instanceof User||!$user->getProjeto())throw $this->createAccessDeniedException();if(!$billing->hasFeature($user->getProjeto(),'fila_espera'))throw $this->createAccessDeniedException('Fila de espera não incluída no plano.');$turma=$em->getRepository(Turma::class)->findOneBy(['id'=>$id,'projeto'=>$user->getProjeto()]);if(!$turma)throw $this->createNotFoundException();$queue=$em->getRepository(FilaEspera::class)->createQueryBuilder('f')->join('f.aluno','a')->where('f.turmaDesejada = :turma')->setParameter('turma',$turma)->orderBy('a.pontuacao','DESC')->addOrderBy('f.inscritoEm','ASC')->getQuery()->getResult();return $this->render('turma/waitlist.html.twig',['turma'=>$turma,'queue'=>$queue]);}
}
