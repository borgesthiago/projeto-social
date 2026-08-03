<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity]
class MovimentacaoFinanceira {
 use TenantOwnedTrait;
 #[ORM\Id,ORM\GeneratedValue,ORM\Column] public ?int $id=null;
 #[ORM\Column(length:20)] public string $tipo='receita';
 #[ORM\Column(length:160)] public string $descricao='';
 #[ORM\Column(type:'decimal',precision:12,scale:2)] public string $valor='0.00';
 #[ORM\Column(type:'date_immutable')] public \DateTimeImmutable $data;
 #[ORM\Column(length:80,nullable:true)] public ?string $categoria=null;
 #[ORM\Column(length:255,nullable:true)] public ?string $comprovante=null;
 #[ORM\Column] public bool $publica=true;
 public function __construct(){ $this->data=new \DateTimeImmutable(); }
}
