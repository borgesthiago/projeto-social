<?php
namespace App\Tests\Entity;
use App\Entity\{AcaoSocial,Aluno,Ods,Professor};
use PHPUnit\Framework\TestCase;
final class OdsTest extends TestCase
{
 public function testAcaoPodeTerMaisDeUmOds():void{$action=new AcaoSocial();$first=new Ods();$first->numero=3;$second=new Ods();$second->numero=4;$action->ods->add($first);$action->ods->add($second);self::assertCount(2,$action->ods);}
 public function testCadastrosPodemSerVinculadosAosOds():void{$goal=new Ods();$student=new Aluno();$teacher=new Professor();$student->ods->add($goal);$teacher->ods->add($goal);self::assertTrue($student->ods->contains($goal));self::assertTrue($teacher->ods->contains($goal));}
}
