<?php
namespace App\Tests\Entity;
use App\Entity\{Assinatura,Plano};
use PHPUnit\Framework\TestCase;
final class AssinaturaTest extends TestCase {
 public function testLiberaAssinaturaAtivaDentroDaCarencia():void{$a=new Assinatura();$a->status='ativa';$a->vencimento=new \DateTimeImmutable('-2 days');$a->diasCarencia=5;self::assertTrue($a->acessoLiberado());}
 public function testBloqueiaAssinaturaVencida():void{$a=new Assinatura();$a->status='ativa';$a->vencimento=new \DateTimeImmutable('-10 days');$a->diasCarencia=5;self::assertFalse($a->acessoLiberado());}
 public function testLimitePersonalizadoTemPrioridade():void{$p=new Plano();$p->limiteUsuarios=5;$a=new Assinatura();$a->plano=$p;$a->limiteUsuariosPersonalizado=12;self::assertSame(12,$a->limiteUsuarios());}
}
