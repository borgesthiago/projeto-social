<?php
namespace App\Twig;
use App\Entity\User;
use App\Service\BillingManager;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
final class PlanExtension extends AbstractExtension {
 public function __construct(private Security $security,private BillingManager $billing){}
 public function getFunctions():array{return [new TwigFunction('plan_feature',[$this,'hasFeature'])];}
 public function hasFeature(?string $feature):bool{if($feature===null||$this->security->isGranted('ROLE_SUPER_ADMIN'))return true;$user=$this->security->getUser();return $user instanceof User&&$user->getProjeto()&&$this->billing->hasFeature($user->getProjeto(),$feature);}
}
