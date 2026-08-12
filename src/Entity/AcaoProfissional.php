<?php
namespace App\Entity;
use Doctrine\Common\Collections\{ArrayCollection,Collection};
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity,ORM\UniqueConstraint(columns:['acao_id','prestador_id','funcao'])]
class AcaoProfissional
{
 use TenantOwnedTrait;
 #[ORM\Id,ORM\GeneratedValue,ORM\Column] public ?int $id=null;
 #[ORM\ManyToOne,ORM\JoinColumn(nullable:false,onDelete:'CASCADE')] public ?AcaoSocial $acao=null;
 #[ORM\ManyToOne,ORM\JoinColumn(nullable:false)] public ?PrestadorServico $prestador=null;
 #[ORM\Column(length:120)] public string $funcao='';
 #[ORM\Column(type:'datetime_immutable',nullable:true)] public ?\DateTimeImmutable $inicio=null;
 #[ORM\Column(type:'datetime_immutable',nullable:true)] public ?\DateTimeImmutable $fim=null;
 #[ORM\Column(type:'decimal',precision:12,scale:2)] public string $valorContratado='0.00';
 #[ORM\Column(length:20)] public string $situacao='previsto';
 #[ORM\Column(type:'text',nullable:true)] public ?string $observacoes=null;
 #[ORM\ManyToMany(targetEntity:Ods::class)] #[ORM\JoinTable(name:'acao_profissional_ods')] public Collection $ods;
 public function __construct(){$this->ods=new ArrayCollection();}
}
