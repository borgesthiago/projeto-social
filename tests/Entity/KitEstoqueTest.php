<?php
namespace App\Tests\Entity;
use App\Entity\{ItemEstoque,KitComponente,KitEstoque};
use PHPUnit\Framework\TestCase;
final class KitEstoqueTest extends TestCase
{
 public function testKitsDiferentesMantemComposicoesIndependentes():void{$snack=new KitEstoque();$snack->nome='Lanche';$crochet=new KitEstoque();$crochet->nome='Crochê';$food=new ItemEstoque();$food->nome='Suco';$thread=new ItemEstoque();$thread->nome='Linha';$a=new KitComponente();$a->kit=$snack;$a->item=$food;$snack->componentes->add($a);$b=new KitComponente();$b->kit=$crochet;$b->item=$thread;$crochet->componentes->add($b);self::assertSame($food,$snack->componentes->first()->item);self::assertSame($thread,$crochet->componentes->first()->item);}
}
