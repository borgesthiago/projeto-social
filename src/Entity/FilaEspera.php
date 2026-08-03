<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity, ORM\UniqueConstraint(columns:['turma_desejada_id','aluno_id'])]
class FilaEspera {
 use TenantOwnedTrait;
 #[ORM\Id,ORM\GeneratedValue,ORM\Column] public ?int $id=null;
 #[ORM\ManyToOne,ORM\JoinColumn(nullable:false)] public ?Aluno $aluno=null;
 #[ORM\Column(length:20)] public string $status='aguardando';
 #[ORM\Column(type:'datetime_immutable')] public \DateTimeImmutable $inscritoEm;
 #[ORM\ManyToOne] public ?Turma $turmaDesejada=null;
 public function __construct(){ $this->inscritoEm=new \DateTimeImmutable(); }
}
