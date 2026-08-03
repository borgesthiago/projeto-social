<?php
namespace App\Service;
final class DocumentoValidator {
 public function normalize(?string $value):string{return preg_replace('/\D+/','',$value??'')??'';}
 public function cpf(?string $value):bool{$cpf=$this->normalize($value);if(strlen($cpf)!==11||preg_match('/^(\d)\1{10}$/',$cpf))return false;for($digit=9;$digit<11;$digit++){$sum=0;for($i=0;$i<$digit;$i++)$sum+=(int)$cpf[$i]*(($digit+1)-$i);$check=(10*($sum%11))%11;if($check===10)$check=0;if((int)$cpf[$digit]!==$check)return false;}return true;}
 public function cnpj(?string $value):bool{$cnpj=$this->normalize($value);if(strlen($cnpj)!==14||preg_match('/^(\d)\1{13}$/',$cnpj))return false;$weights=[[5,4,3,2,9,8,7,6,5,4,3,2],[6,5,4,3,2,9,8,7,6,5,4,3,2]];foreach([12,13] as $round=>$position){$sum=0;foreach($weights[$round] as $i=>$weight)$sum+=(int)$cnpj[$i]*$weight;$rest=$sum%11;$check=$rest<2?0:11-$rest;if((int)$cnpj[$position]!==$check)return false;}return true;}
 public function formatCpf(?string $value):string{$v=$this->normalize($value);return strlen($v)===11?substr($v,0,3).'.'.substr($v,3,3).'.'.substr($v,6,3).'-'.substr($v,9):$v;}
 public function formatCnpj(?string $value):string{$v=$this->normalize($value);return strlen($v)===14?substr($v,0,2).'.'.substr($v,2,3).'.'.substr($v,5,3).'/'.substr($v,8,4).'-'.substr($v,12):$v;}
}
