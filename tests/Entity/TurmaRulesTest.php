<?php
namespace App\Tests\Entity;
use App\Entity\{Aluno,Modalidade,Turma};
use PHPUnit\Framework\TestCase;
final class TurmaRulesTest extends TestCase {
 public function testCalculaIdadeDoAluno():void{$student=new Aluno();$student->dataNascimento=(new \DateTimeImmutable('today'))->modify('-12 years');self::assertSame(12,$student->idade());}
 public function testTurmaPodeRepresentarModalidadeEFaixa():void{$modality=new Modalidade();$modality->nome='Jiu-jitsu';$class=new Turma();$class->nome='A1';$class->modalidade=$modality;$class->idadeMinima=8;$class->idadeMaxima=15;$class->limiteAlunos=20;self::assertSame('Jiu-jitsu',$class->modalidade->nome);self::assertSame(20,$class->limiteAlunos);}
}
