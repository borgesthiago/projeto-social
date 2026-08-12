<?php
namespace App\Entity;
use Doctrine\Common\Collections\{ArrayCollection,Collection};
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity,ORM\UniqueConstraint(columns:['projeto_id','documento'])]
class PrestadorServico
{
 use TenantOwnedTrait;
 #[ORM\Id,ORM\GeneratedValue,ORM\Column] public ?int $id=null;
 #[ORM\Column(length:140)] public string $nome='';
 #[ORM\Column(length:20)] public string $tipoPessoa='fisica';
 #[ORM\Column(length:14)] public string $documento='';
 #[ORM\Column(length:180,nullable:true)] public ?string $email=null;
 #[ORM\Column(length:30)] public string $telefone='';
 #[ORM\Column(length:120)] public string $especialidade='';
 #[ORM\Column(length:50,nullable:true)] public ?string $registroProfissional=null;
 #[ORM\Column(length:255,nullable:true)] public ?string $endereco=null;
 #[ORM\Column(length:120,nullable:true)] public ?string $dadosBancarios=null;
 #[ORM\Column(type:'text',nullable:true)] public ?string $observacoes=null;
 #[ORM\ManyToMany(targetEntity:Ods::class)] #[ORM\JoinTable(name:'prestador_servico_ods')] public Collection $ods;
 #[ORM\Column] public bool $ativo=true;
 public function __construct(){$this->ods=new ArrayCollection();}
}
