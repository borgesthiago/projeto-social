<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity, ORM\UniqueConstraint(columns:['projeto_id','cpf'])]
class Professor {
 use TenantOwnedTrait;
 #[ORM\Id,ORM\GeneratedValue,ORM\Column] public ?int $id=null;
 #[ORM\Column(length:120)] public string $nome='';
 #[ORM\Column(length:180,nullable:true)] public ?string $email=null;
 #[ORM\Column(length:11,nullable:true)] public ?string $cpf=null;
 #[ORM\Column(type:'text',nullable:true)] public ?string $especialidades=null;
 #[ORM\Column(type:'text',nullable:true)] public ?string $formacao=null;
 #[ORM\ManyToOne] public ?User $usuario=null;
 #[ORM\Column] public bool $ativo=true;
}
