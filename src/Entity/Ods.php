<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity,ORM\UniqueConstraint(columns:['projeto_id','numero'])]
class Ods
{
 use TenantOwnedTrait;
 #[ORM\Id,ORM\GeneratedValue,ORM\Column] public ?int $id=null;
 #[ORM\Column] public int $numero=1;
 #[ORM\Column(length:160)] public string $nome='';
 #[ORM\Column(type:'text',nullable:true)] public ?string $descricao=null;
 #[ORM\Column(length:7)] public string $cor='#2563eb';
 #[ORM\Column] public bool $ativo=true;
}
