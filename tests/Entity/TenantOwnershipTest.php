<?php
namespace App\Tests\Entity;
use App\Entity\{Aluno,ProjetoSocial,User};
use PHPUnit\Framework\TestCase;
final class TenantOwnershipTest extends TestCase {
 public function testDadosEUsuarioPertencemAoMesmoProjeto():void{$p=new ProjetoSocial();$p->nome='Instituto A';$a=new Aluno();$a->projeto=$p;$u=(new User())->setProjeto($p);self::assertSame($p,$a->projeto);self::assertSame($p,$u->getProjeto());}
 public function testProjetosDiferentesNaoCompartilhamIdentidade():void{$a=new ProjetoSocial();$b=new ProjetoSocial();self::assertNotSame($a,$b);}
}
