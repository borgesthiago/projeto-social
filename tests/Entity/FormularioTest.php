<?php
namespace App\Tests\Entity;
use App\Entity\Formulario;
use PHPUnit\Framework\TestCase;
final class FormularioTest extends TestCase
{
 public function testSomenteFormularioPublicadoFicaDisponivel():void{$form=new Formulario();self::assertFalse($form->disponivel());$form->status='publicado';self::assertTrue($form->disponivel());}
 public function testRespeitaPeriodoDeDisponibilidade():void{$form=new Formulario();$form->status='publicado';$form->abertura=new \DateTimeImmutable('+1 day');self::assertFalse($form->disponivel());$form->abertura=new \DateTimeImmutable('-1 day');$form->encerramento=new \DateTimeImmutable('-1 minute');self::assertFalse($form->disponivel());}
}
