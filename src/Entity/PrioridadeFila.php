<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity]
class PrioridadeFila {
 use TenantOwnedTrait;
 #[ORM\Id,ORM\GeneratedValue,ORM\Column] public ?int $id=null;
 #[ORM\Column(length:120)] public string $nome='';
 #[ORM\Column(type:'text',nullable:true)] public ?string $descricao=null;
 #[ORM\Column] public int $pontos=0;
 #[ORM\Column] public bool $exigeComprovante=false;
 #[ORM\Column] public bool $ativa=true;
}
