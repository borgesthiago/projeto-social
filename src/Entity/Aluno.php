<?php
namespace App\Entity;
use Doctrine\Common\Collections\{ArrayCollection,Collection};
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity, ORM\UniqueConstraint(columns:['projeto_id','cpf'])]
class Aluno {
 use TenantOwnedTrait;
 #[ORM\Id,ORM\GeneratedValue,ORM\Column] public ?int $id=null;
 #[ORM\Column(length:120)] public string $nome='';
 #[ORM\Column(type:'date_immutable')] public \DateTimeImmutable $dataNascimento;
 #[ORM\Column(length:20,nullable:true)] public ?string $cpf=null;
 #[ORM\Column(length:30,nullable:true)] public ?string $telefone=null;
 #[ORM\Column(length:20)] public string $status='ativo';
 #[ORM\Column] public int $pontuacao=0;
 #[ORM\ManyToMany(targetEntity:PrioridadeFila::class)]
 #[ORM\JoinTable(name:'aluno_criterio')]
 public Collection $criterios;
 #[ORM\ManyToOne] public ?Responsavel $responsavel=null;
 #[ORM\ManyToOne] public ?Turma $turma=null;
 #[ORM\ManyToOne] public ?User $usuario=null;
 public function __construct(){ $this->dataNascimento=new \DateTimeImmutable('-10 years');$this->criterios=new ArrayCollection(); }
 public function isMenor(): bool { return $this->dataNascimento->diff(new \DateTimeImmutable())->y < 18; }
 public function idade(): int { return $this->dataNascimento->diff(new \DateTimeImmutable('today'))->y; }
}
