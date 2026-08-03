<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity]
class Turma {
 use TenantOwnedTrait;
 #[ORM\Id,ORM\GeneratedValue,ORM\Column] public ?int $id=null;
 #[ORM\Column(length:120)] public string $nome='';
 #[ORM\Column] public int $anoLetivo;
 #[ORM\Column] public int $limiteAlunos=20;
 #[ORM\Column] public int $idadeMinima=0;
 #[ORM\Column] public int $idadeMaxima=99;
 #[ORM\Column(length:120,nullable:true)] public ?string $horario=null;
 #[ORM\ManyToOne] public ?Professor $professor=null;
 #[ORM\ManyToOne,ORM\JoinColumn(nullable:false)] public ?Modalidade $modalidade=null;
 #[ORM\Column] public bool $ativa=true;
 public function __construct(){ $this->anoLetivo=(int)date('Y'); }
}
