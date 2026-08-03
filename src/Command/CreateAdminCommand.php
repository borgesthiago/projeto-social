<?php
namespace App\Command;
use App\Entity\{ProjetoSocial,User};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument,InputInterface};
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
#[AsCommand(name:'app:create-admin',description:'Cria o usuário master inicial')]
class CreateAdminCommand extends Command {
 public function __construct(private EntityManagerInterface $em,private UserPasswordHasherInterface $hasher){parent::__construct();}
 protected function configure():void{$this->addArgument('email',InputArgument::REQUIRED)->addArgument('password',InputArgument::REQUIRED)->addArgument('nome',InputArgument::OPTIONAL,'Nome','Administrador');}
 protected function execute(InputInterface $in,OutputInterface $out):int{$p=new ProjetoSocial();$p->nome='Projeto Social';$p->slug='projeto-social-'.substr(bin2hex(random_bytes(3)),0,6);$p->email=$in->getArgument('email');$u=(new User())->setEmail($in->getArgument('email'))->setNome($in->getArgument('nome'))->setRoles(['ROLE_MASTER'])->setProjeto($p);$u->setPassword($this->hasher->hashPassword($u,$in->getArgument('password')));$this->em->persist($p);$this->em->persist($u);$this->em->flush();$out->writeln('<info>Projeto e usuário master criados.</info>');return Command::SUCCESS;}
}
