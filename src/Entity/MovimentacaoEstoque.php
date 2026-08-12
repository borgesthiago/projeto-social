<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity]
class MovimentacaoEstoque
{
 use TenantOwnedTrait;
 #[ORM\Id,ORM\GeneratedValue,ORM\Column] public ?int $id=null;
 #[ORM\ManyToOne,ORM\JoinColumn(nullable:false)] public ?ItemEstoque $item=null;
 #[ORM\Column(length:20)] public string $tipo='entrada';
 #[ORM\Column(type:'decimal',precision:12,scale:3)] public string $quantidade='0.000';
 #[ORM\Column(length:160)] public string $motivo='';
 #[ORM\Column(type:'datetime_immutable')] public \DateTimeImmutable $criadaEm;
 public function __construct(){$this->criadaEm=new \DateTimeImmutable();}
}
