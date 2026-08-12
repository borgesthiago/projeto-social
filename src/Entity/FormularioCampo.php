<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity]
class FormularioCampo
{
 use TenantOwnedTrait;
 #[ORM\Id,ORM\GeneratedValue,ORM\Column] public ?int $id=null;
 #[ORM\ManyToOne(inversedBy:'campos'),ORM\JoinColumn(nullable:false,onDelete:'CASCADE')] public ?Formulario $formulario=null;
 #[ORM\Column(length:160)] public string $rotulo='';
 #[ORM\Column(length:30)] public string $tipo='texto';
 #[ORM\Column(type:'text',nullable:true)] public ?string $ajuda=null;
 #[ORM\Column(type:'json')] public array $opcoes=[];
 #[ORM\Column(nullable:true)] public ?int $tamanho=null;
 #[ORM\Column] public bool $obrigatorio=false;
 #[ORM\Column] public int $ordem=0;
}
