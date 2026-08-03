<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity,ORM\UniqueConstraint(columns:['projeto_id'])]
class Assinatura {
 #[ORM\Id,ORM\GeneratedValue,ORM\Column] public ?int $id=null;
 #[ORM\OneToOne,ORM\JoinColumn(nullable:false,onDelete:'CASCADE')] public ?ProjetoSocial $projeto=null;
 #[ORM\ManyToOne,ORM\JoinColumn(nullable:false)] public ?Plano $plano=null;
 #[ORM\Column(length:20)] public string $status='teste';
 #[ORM\Column(type:'date_immutable')] public \DateTimeImmutable $inicio;
 #[ORM\Column(type:'date_immutable')] public \DateTimeImmutable $vencimento;
 #[ORM\Column] public int $diasCarencia=5;
 #[ORM\Column(nullable:true)] public ?int $limiteUsuariosPersonalizado=null;
 #[ORM\Column(type:'datetime_immutable')] public \DateTimeImmutable $criadoEm;
 public function __construct(){$this->inicio=new \DateTimeImmutable('today');$this->vencimento=new \DateTimeImmutable('+14 days');$this->criadoEm=new \DateTimeImmutable();}
 public function limiteUsuarios():int{return $this->limiteUsuariosPersonalizado??$this->plano?->limiteUsuarios??1;}
 public function acessoLiberado(?\DateTimeImmutable $hoje=null):bool{$hoje??=new \DateTimeImmutable('today');if(!in_array($this->status,['teste','ativa','atrasada'],true))return false;return $hoje<=$this->vencimento->modify('+'.$this->diasCarencia.' days');}
}
