<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity]
class ItemEstoque
{
 use TenantOwnedTrait;
 #[ORM\Id,ORM\GeneratedValue,ORM\Column] public ?int $id=null;
 #[ORM\Column(length:140)] public string $nome='';
 #[ORM\Column(length:40)] public string $unidade='unidade';
 #[ORM\Column(type:'decimal',precision:12,scale:3)] public string $quantidade='0.000';
 #[ORM\Column(type:'decimal',precision:12,scale:3)] public string $estoqueMinimo='0.000';
 #[ORM\Column(length:80,nullable:true)] public ?string $categoria=null;
 #[ORM\Column(length:80,nullable:true)] public ?string $localizacao=null;
 #[ORM\Column] public bool $ativo=true;
}
