<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity]
class Aula {
 use TenantOwnedTrait;
 #[ORM\Id,ORM\GeneratedValue,ORM\Column] public ?int $id=null;
 #[ORM\Column(length:160)] public string $titulo='';
 #[ORM\Column(type:'datetime_immutable')] public \DateTimeImmutable $dataHora;
 #[ORM\Column(type:'text',nullable:true)] public ?string $planejamento=null;
 #[ORM\Column(type:'text',nullable:true)] public ?string $execucao=null;
 #[ORM\Column(type:'text',nullable:true)] public ?string $materiais=null;
 #[ORM\Column(length:20)] public string $status='planejada';
 #[ORM\ManyToOne] public ?Turma $turma=null;
 public function __construct(){ $this->dataHora=new \DateTimeImmutable(); }
}
