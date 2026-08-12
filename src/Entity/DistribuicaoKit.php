<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity]
class DistribuicaoKit
{
 use TenantOwnedTrait;
 #[ORM\Id,ORM\GeneratedValue,ORM\Column] public ?int $id=null;
 #[ORM\ManyToOne,ORM\JoinColumn(nullable:false)] public ?KitEstoque $kit=null;
 #[ORM\ManyToOne] public ?Turma $turma=null;
 #[ORM\ManyToOne] public ?AcaoSocial $acao=null;
 #[ORM\Column] public int $quantidade=1;
 #[ORM\Column(type:'date_immutable')] public \DateTimeImmutable $data;
 #[ORM\Column(type:'text',nullable:true)] public ?string $observacoes=null;
 public function __construct(){$this->data=new \DateTimeImmutable();}
}
