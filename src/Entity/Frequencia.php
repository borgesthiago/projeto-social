<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity, ORM\UniqueConstraint(columns:['aula_id','aluno_id'])]
class Frequencia {
 use TenantOwnedTrait;
 #[ORM\Id,ORM\GeneratedValue,ORM\Column] public ?int $id=null;
 #[ORM\ManyToOne,ORM\JoinColumn(nullable:false)] public ?Aula $aula=null;
 #[ORM\ManyToOne,ORM\JoinColumn(nullable:false)] public ?Aluno $aluno=null;
 #[ORM\Column(length:20)] public string $situacao='presente';
 #[ORM\Column(type:'text',nullable:true)] public ?string $justificativa=null;
 #[ORM\Column] public bool $notificacaoEnviada=false;
}
