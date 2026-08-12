<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity, ORM\UniqueConstraint(columns:['projeto_id','cpf'])]
class Professor {
 use TenantOwnedTrait;
 #[ORM\Id,ORM\GeneratedValue,ORM\Column] public ?int $id=null;
 #[ORM\Column(length:120)] public string $nome='';
 #[ORM\Column(length:180,nullable:true)] public ?string $email=null;
 #[ORM\Column(length:11,nullable:true)] public ?string $cpf=null;
 #[ORM\Column(length:30,nullable:true)] public ?string $telefone=null;
 #[ORM\Column(type:'date_immutable',nullable:true)] public ?\DateTimeImmutable $dataNascimento=null;
 #[ORM\Column(length:30,nullable:true)] public ?string $sexo=null;
 #[ORM\Column(length:50,nullable:true)] public ?string $registroProfissional=null;
 #[ORM\Column(length:30,nullable:true)] public ?string $tipoVinculo=null;
 #[ORM\Column(nullable:true)] public ?int $cargaHorariaSemanal=null;
 #[ORM\Column(type:'date_immutable',nullable:true)] public ?\DateTimeImmutable $dataAdmissao=null;
 #[ORM\Column(length:255,nullable:true)] public ?string $endereco=null;
 #[ORM\Column(type:'text',nullable:true)] public ?string $especialidades=null;
 #[ORM\Column(type:'text',nullable:true)] public ?string $formacao=null;
 #[ORM\Column(type:'text',nullable:true)] public ?string $observacoes=null;
 #[ORM\ManyToOne] public ?User $usuario=null;
 #[ORM\Column] public bool $ativo=true;
}
