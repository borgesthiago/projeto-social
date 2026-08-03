<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity]
class Plano {
 #[ORM\Id,ORM\GeneratedValue,ORM\Column] public ?int $id=null;
 #[ORM\Column(length:100)] public string $nome='';
 #[ORM\Column(type:'decimal',precision:10,scale:2)] public string $valorMensal='0.00';
 #[ORM\Column] public int $limiteUsuarios=5;
 #[ORM\Column] public int $limiteTurmas=5;
 #[ORM\Column(nullable:true)] public ?int $limiteAlunos=null;
 #[ORM\Column(nullable:true)] public ?int $limiteNotificacoesMes=500;
 #[ORM\Column] public int $limiteArmazenamentoMb=1024;
 #[ORM\Column] public int $diasTeste=0;
 #[ORM\Column(type:'json')] public array $recursos=[];
 #[ORM\Column(type:'json')] public array $canaisNotificacao=[];
 #[ORM\Column] public bool $ativo=true;
 #[ORM\Column] public bool $publico=true;
 public function __toString():string{return $this->nome;}
}
