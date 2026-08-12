<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity,ORM\UniqueConstraint(columns:['kit_id','item_id'])]
class KitComponente
{
 use TenantOwnedTrait;
 #[ORM\Id,ORM\GeneratedValue,ORM\Column] public ?int $id=null;
 #[ORM\ManyToOne(inversedBy:'componentes'),ORM\JoinColumn(nullable:false,onDelete:'CASCADE')] public ?KitEstoque $kit=null;
 #[ORM\ManyToOne,ORM\JoinColumn(nullable:false)] public ?ItemEstoque $item=null;
 #[ORM\Column(type:'decimal',precision:12,scale:3)] public string $quantidade='1.000';
}
