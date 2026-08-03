<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity,ORM\UniqueConstraint(columns:['projeto_id','nome'])]
class Modalidade {
 use TenantOwnedTrait;
 #[ORM\Id,ORM\GeneratedValue,ORM\Column] public ?int $id=null;
 #[ORM\Column(length:120)] public string $nome='';
 #[ORM\Column(type:'text',nullable:true)] public ?string $descricao=null;
 #[ORM\Column(nullable:true)] public ?int $cargaHoraria=null;
 #[ORM\Column] public bool $ativa=true;
 public function __toString():string{return $this->nome;}
}
