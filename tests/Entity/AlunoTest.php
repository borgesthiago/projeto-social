<?php
namespace App\Tests\Entity;
use App\Entity\Aluno;
use PHPUnit\Framework\TestCase;
final class AlunoTest extends TestCase {
 public function testIdentificaAlunoMenorDeIdade(): void { $a=new Aluno(); $a->dataNascimento=new \DateTimeImmutable('-12 years'); self::assertTrue($a->isMenor()); }
 public function testIdentificaAlunoAdulto(): void { $a=new Aluno(); $a->dataNascimento=new \DateTimeImmutable('-20 years'); self::assertFalse($a->isMenor()); }
}
