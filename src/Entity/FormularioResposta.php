<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity]
class FormularioResposta
{
 use TenantOwnedTrait;
 #[ORM\Id,ORM\GeneratedValue,ORM\Column] public ?int $id=null;
 #[ORM\ManyToOne,ORM\JoinColumn(nullable:false,onDelete:'CASCADE')] public ?Formulario $formulario=null;
 #[ORM\Column(length:140,nullable:true)] public ?string $nome=null;
 #[ORM\Column(length:180,nullable:true)] public ?string $email=null;
 #[ORM\Column(length:30,nullable:true)] public ?string $telefone=null;
 #[ORM\Column(type:'json')] public array $respostas=[];
 #[ORM\Column(type:'datetime_immutable')] public \DateTimeImmutable $enviadoEm;
 public function __construct(){$this->enviadoEm=new \DateTimeImmutable();}
}
