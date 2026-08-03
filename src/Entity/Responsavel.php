<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity, ORM\UniqueConstraint(columns:['projeto_id','cpf'])]
class Responsavel {
 use TenantOwnedTrait;
 #[ORM\Id,ORM\GeneratedValue,ORM\Column] public ?int $id=null;
 #[ORM\Column(length:120)] public string $nome='';
 #[ORM\Column(length:20,nullable:true)] public ?string $cpf=null;
 #[ORM\Column(length:30)] public string $telefone='';
 #[ORM\Column(length:30,nullable:true)] public ?string $whatsapp=null;
 #[ORM\Column(length:180,nullable:true)] public ?string $email=null;
 #[ORM\Column(length:30,nullable:true)] public ?string $parentesco=null;
 #[ORM\Column(length:30,nullable:true)] public ?string $rg=null;
 #[ORM\Column(length:120,nullable:true)] public ?string $profissao=null;
 #[ORM\Column(type:'decimal',precision:12,scale:2,nullable:true)] public ?string $rendaFamiliar=null;
 #[ORM\Column(length:255,nullable:true)] public ?string $endereco=null;
 #[ORM\ManyToOne] public ?User $usuario=null;
 #[ORM\Column] public bool $recebeNotificacoes=true;
 #[ORM\Column] public bool $contatoWhatsapp=false;
 #[ORM\Column] public bool $contatoEmail=false;
 #[ORM\Column] public bool $contatoSms=false;
 #[ORM\Column] public bool $contatoTelefone=false;
}
