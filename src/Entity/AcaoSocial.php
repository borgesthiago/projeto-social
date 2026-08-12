<?php
namespace App\Entity;
use Doctrine\Common\Collections\{ArrayCollection,Collection};
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity]
class AcaoSocial
{
 use TenantOwnedTrait;
 #[ORM\Id,ORM\GeneratedValue,ORM\Column] public ?int $id=null;
 #[ORM\Column(length:150)] public string $nome='';
 #[ORM\Column(length:30)] public string $tipo='evento';
 #[ORM\Column(type:'text',nullable:true)] public ?string $descricao=null;
 #[ORM\Column(type:'datetime_immutable')] public \DateTimeImmutable $dataInicio;
 #[ORM\Column(type:'datetime_immutable',nullable:true)] public ?\DateTimeImmutable $dataFim=null;
 #[ORM\Column(length:180)] public string $local='';
 #[ORM\Column(length:120,nullable:true)] public ?string $responsavel=null;
 #[ORM\Column(type:'text',nullable:true)] public ?string $publicoAlvo=null;
 #[ORM\Column(nullable:true)] public ?int $capacidade=null;
 #[ORM\Column] public int $participantes=0;
 #[ORM\Column(length:20)] public string $status='planejada';
 #[ORM\Column] public bool $publica=false;
 #[ORM\Column(type:'text',nullable:true)] public ?string $resultados=null;
 #[ORM\ManyToMany(targetEntity:Ods::class)] #[ORM\JoinTable(name:'acao_social_ods')] public Collection $ods;
 public function __construct(){$this->dataInicio=(new \DateTimeImmutable('+7 days'))->setTime(9,0);$this->ods=new ArrayCollection();}
}
