<?php
namespace App\EventSubscriber;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
final class TenantSubscriber implements EventSubscriberInterface {
 public function __construct(private EntityManagerInterface $em,private Security $security){}
 public static function getSubscribedEvents():array{return [KernelEvents::REQUEST=>['enableTenantFilter',-20]];}
 public function enableTenantFilter(RequestEvent $event):void{if(!$event->isMainRequest())return;$user=$this->security->getUser();if(!$user instanceof User||in_array('ROLE_SUPER_ADMIN',$user->getRoles(),true)||!$user->getProjeto())return;$filter=$this->em->getFilters()->enable('tenant');$filter->setParameter('projeto_id',$user->getProjeto()->id);}
}
