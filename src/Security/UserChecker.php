<?php
namespace App\Security;
use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Service\BillingManager;
final class UserChecker implements UserCheckerInterface {
 public function __construct(private BillingManager $billing){}
 public function checkPreAuth(UserInterface $user): void { if (!$user instanceof User)return;if(!$user->isAtivo())throw new CustomUserMessageAccountStatusException('Esta conta está inativa.');if(in_array('ROLE_SUPER_ADMIN',$user->getRoles(),true))return;if(!$user->getProjeto()||!$user->getProjeto()->ativo)throw new CustomUserMessageAccountStatusException('O projeto está inativo.');$subscription=$this->billing->subscription($user->getProjeto());if(!$subscription||!$subscription->acessoLiberado())throw new CustomUserMessageAccountStatusException('A assinatura do projeto está vencida ou suspensa. Procure o administrador da plataforma.'); }
 public function checkPostAuth(UserInterface $user): void {}
}
