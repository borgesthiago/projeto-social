<?php
namespace App\Tests\Service;
use App\Service\DocumentoValidator;
use PHPUnit\Framework\TestCase;
final class DocumentoValidatorTest extends TestCase {
 private DocumentoValidator $validator;
 protected function setUp():void{$this->validator=new DocumentoValidator();}
 public function testValidaCpfComOuSemMascara():void{self::assertTrue($this->validator->cpf('529.982.247-25'));self::assertSame('52998224725',$this->validator->normalize('529.982.247-25'));}
 public function testRejeitaCpfInvalido():void{self::assertFalse($this->validator->cpf('111.111.111-11'));self::assertFalse($this->validator->cpf('52998224724'));}
 public function testValidaCnpjComOuSemMascara():void{self::assertTrue($this->validator->cnpj('11.222.333/0001-81'));self::assertSame('11222333000181',$this->validator->normalize('11.222.333/0001-81'));}
 public function testRejeitaCnpjInvalido():void{self::assertFalse($this->validator->cnpj('00.000.000/0000-00'));self::assertFalse($this->validator->cnpj('11.222.333/0001-82'));}
}
