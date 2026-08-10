<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity]
class AlunoFoto
{
 use TenantOwnedTrait;
 #[ORM\Id,ORM\GeneratedValue,ORM\Column] public ?int $id=null;
 #[ORM\ManyToOne(inversedBy:'fotos'),ORM\JoinColumn(nullable:false,onDelete:'CASCADE')] public ?Aluno $aluno=null;
 #[ORM\Column(length:255)] public string $caminho='';
 #[ORM\Column(length:255)] public string $nomeOriginal='';
 #[ORM\Column] public int $tamanho=0;
 #[ORM\Column(type:'datetime_immutable')] public \DateTimeImmutable $criadaEm;
 public function __construct(){$this->criadaEm=new \DateTimeImmutable();}
}
