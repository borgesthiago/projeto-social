<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity]
class NotificacaoLog {
 use TenantOwnedTrait;
 #[ORM\Id,ORM\GeneratedValue,ORM\Column] public ?int $id=null;
 #[ORM\Column(length:20)] public string $canal='email';
 #[ORM\Column(length:180)] public string $destinatario='';
 #[ORM\Column(type:'text')] public string $mensagem='';
 #[ORM\Column(length:20)] public string $status='pendente';
 #[ORM\Column(length:120,nullable:true)] public ?string $referenciaExterna=null;
 #[ORM\Column(type:'datetime_immutable')] public \DateTimeImmutable $criadoEm;
 #[ORM\Column(type:'datetime_immutable',nullable:true)] public ?\DateTimeImmutable $enviadoEm=null;
 public function __construct(){$this->criadoEm=new \DateTimeImmutable();}
}
