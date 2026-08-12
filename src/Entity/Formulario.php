<?php
namespace App\Entity;
use Doctrine\Common\Collections\{ArrayCollection,Collection};
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity]
class Formulario
{
 use TenantOwnedTrait;
 #[ORM\Id,ORM\GeneratedValue,ORM\Column] public ?int $id=null;
 #[ORM\Column(length:160)] public string $titulo='';
 #[ORM\Column(length:30)] public string $tipo='inscricao';
 #[ORM\Column(length:40,unique:true)] public string $token;
 #[ORM\Column(type:'text',nullable:true)] public ?string $descricao=null;
 #[ORM\ManyToOne] public ?AcaoSocial $acao=null;
 #[ORM\ManyToOne] public ?Aula $aula=null;
 #[ORM\Column(type:'datetime_immutable',nullable:true)] public ?\DateTimeImmutable $abertura=null;
 #[ORM\Column(type:'datetime_immutable',nullable:true)] public ?\DateTimeImmutable $encerramento=null;
 #[ORM\Column(length:20)] public string $status='rascunho';
 #[ORM\Column(type:'text',nullable:true)] public ?string $mensagemConfirmacao=null;
 #[ORM\OneToMany(mappedBy:'formulario',targetEntity:FormularioCampo::class,cascade:['persist','remove'],orphanRemoval:true)]
 #[ORM\OrderBy(['ordem'=>'ASC'])] public Collection $campos;
 public function __construct(){$this->token=bin2hex(random_bytes(16));$this->campos=new ArrayCollection();}
 public function disponivel():bool{$now=new \DateTimeImmutable();return $this->status==='publicado'&&(!$this->abertura||$this->abertura<=$now)&&(!$this->encerramento||$this->encerramento>=$now);}
}
