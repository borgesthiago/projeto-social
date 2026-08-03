<?php
namespace App\Command;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument,InputInterface};
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
#[AsCommand(name:'app:create-super-admin',description:'Cria o superadministrador global da plataforma')]
final class CreateSuperAdminCommand extends Command {
 public function __construct(private EntityManagerInterface $em,private UserPasswordHasherInterface $hasher){parent::__construct();}
 protected function configure():void{$this->addArgument('email',InputArgument::REQUIRED)->addArgument('password',InputArgument::REQUIRED)->addArgument('nome',InputArgument::OPTIONAL,'Nome','Super Admin');}
 protected function execute(InputInterface $input,OutputInterface $output):int{$email=mb_strtolower(trim((string)$input->getArgument('email')));$password=(string)$input->getArgument('password');if(!filter_var($email,FILTER_VALIDATE_EMAIL)){$output->writeln('<error>E-mail inválido.</error>');return Command::INVALID;}if(strlen($password)<8){$output->writeln('<error>A senha deve ter ao menos 8 caracteres.</error>');return Command::INVALID;}if($this->em->getRepository(User::class)->findOneBy(['email'=>$email])){$output->writeln('<error>E-mail já cadastrado.</error>');return Command::FAILURE;}$user=(new User())->setNome((string)$input->getArgument('nome'))->setEmail($email)->setRoles(['ROLE_SUPER_ADMIN'])->setProjeto(null)->setAtivo(true);$user->setPassword($this->hasher->hashPassword($user,$password));$this->em->persist($user);$this->em->flush();$output->writeln('<info>Superadministrador criado com sucesso.</info>');return Command::SUCCESS;}
}
