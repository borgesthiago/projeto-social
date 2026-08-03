<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity, ORM\UniqueConstraint(columns:['projeto_id','chave'])]
class ConfiguracaoSistema {
 use TenantOwnedTrait;
 #[ORM\Id,ORM\GeneratedValue,ORM\Column] public ?int $id=null;
 #[ORM\Column(length:120)] public string $chave='';
 #[ORM\Column(type:'text',nullable:true)] public ?string $valor=null;
 #[ORM\Column(length:40)] public string $grupo='geral';
 #[ORM\Column(length:20)] public string $tipo='texto';
}
