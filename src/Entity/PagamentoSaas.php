<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity]
class PagamentoSaas {
 #[ORM\Id,ORM\GeneratedValue,ORM\Column] public ?int $id=null;
 #[ORM\ManyToOne,ORM\JoinColumn(nullable:false,onDelete:'CASCADE')] public ?Assinatura $assinatura=null;
 #[ORM\Column(type:'decimal',precision:10,scale:2)] public string $valor='0.00';
 #[ORM\Column(type:'date_immutable')] public \DateTimeImmutable $vencimento;
 #[ORM\Column(type:'datetime_immutable',nullable:true)] public ?\DateTimeImmutable $pagoEm=null;
 #[ORM\Column(length:20)] public string $status='pendente';
 #[ORM\Column(length:40,nullable:true)] public ?string $metodo=null;
 #[ORM\Column(length:120,nullable:true)] public ?string $referencia=null;
 #[ORM\Column(type:'text',nullable:true)] public ?string $observacao=null;
 #[ORM\Column(type:'datetime_immutable')] public \DateTimeImmutable $criadoEm;
 public function __construct(){$this->vencimento=new \DateTimeImmutable('+30 days');$this->criadoEm=new \DateTimeImmutable();}
}
