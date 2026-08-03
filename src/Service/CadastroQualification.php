<?php
namespace App\Service;
use App\Entity\{Aluno,ConfiguracaoSistema,ProjetoSocial,Responsavel};
use Doctrine\ORM\EntityManagerInterface;
final class CadastroQualification
{
 public function __construct(private EntityManagerInterface $em){}
 public function evaluate(Aluno $student):array
 {
  $fields=['Nome do aluno'=>$student->nome,'Data de nascimento'=>$student->dataNascimento??null,'CPF do aluno'=>$student->cpf,'Telefone do aluno'=>$student->telefone,'Situação do cadastro'=>$student->status];
  if($student->isMenor()||$student->responsavel){$guardian=$student->responsavel;$fields+=['Responsável vinculado'=>$guardian,'Nome do responsável'=>$guardian?->nome,'CPF do responsável'=>$guardian?->cpf,'Telefone do responsável'=>$guardian?->telefone,'E-mail do responsável'=>$guardian?->email];}
  $completed=count(array_filter($fields,fn($value)=>$value!==null&&$value!==''));$percent=(int)round($completed*100/count($fields));$thresholds=$this->thresholds($student->projeto);$level=$percent>=$thresholds['ouro']?'Ouro':($percent>=$thresholds['prata']?'Prata':($percent>=$thresholds['bronze']?'Bronze':'Incompleto'));
  return ['percent'=>$percent,'level'=>$level,'completed'=>$completed,'total'=>count($fields),'missing'=>array_keys(array_filter($fields,fn($value)=>$value===null||$value===''))];
 }
 public function thresholds(ProjetoSocial $project):array
 {
  $values=['bronze'=>30,'prata'=>60,'ouro'=>90];foreach($this->em->getRepository(ConfiguracaoSistema::class)->findBy(['projeto'=>$project]) as $config)if(isset($values[$config->chave]))$values[$config->chave]=(int)$config->valor;return $values;
 }
 public function evaluateGuardian(Responsavel $guardian):array
 {
  $fields=['Nome'=>$guardian->nome,'Parentesco'=>$guardian->parentesco,'Telefone'=>$guardian->telefone,'WhatsApp'=>$guardian->whatsapp,'E-mail'=>$guardian->email,'CPF'=>$guardian->cpf,'RG'=>$guardian->rg,'Profissão'=>$guardian->profissao,'Renda familiar'=>$guardian->rendaFamiliar,'Endereço'=>$guardian->endereco];$completed=count(array_filter($fields,fn($value)=>$value!==null&&$value!==''));$percent=(int)round($completed*100/count($fields));$thresholds=$this->thresholds($guardian->projeto);$level=$percent>=$thresholds['ouro']?'Ouro':($percent>=$thresholds['prata']?'Prata':($percent>=$thresholds['bronze']?'Bronze':'Incompleto'));return ['percent'=>$percent,'level'=>$level,'completed'=>$completed,'total'=>count($fields),'missing'=>array_keys(array_filter($fields,fn($value)=>$value===null||$value===''))];
 }
}
