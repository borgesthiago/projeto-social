<?php
namespace App\Entity;
use Doctrine\Common\Collections\{ArrayCollection,Collection};
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity]
class KitEstoque
{
 use TenantOwnedTrait;
 #[ORM\Id,ORM\GeneratedValue,ORM\Column] public ?int $id=null;
 #[ORM\Column(length:140)] public string $nome='';
 #[ORM\Column(type:'text',nullable:true)] public ?string $descricao=null;
 #[ORM\Column] public bool $ativo=true;
 #[ORM\OneToMany(mappedBy:'kit',targetEntity:KitComponente::class,cascade:['persist','remove'],orphanRemoval:true)] public Collection $componentes;
 public function __construct(){$this->componentes=new ArrayCollection();}
}
