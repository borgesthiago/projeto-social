<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260812223000 extends AbstractMigration
{
 public function getDescription():string{return 'Adiciona tamanho máximo configurável aos campos dos formulários';}
 public function up(Schema $schema):void{$this->addSql('ALTER TABLE formulario_campo ADD tamanho INT DEFAULT NULL');}
 public function down(Schema $schema):void{$this->addSql('ALTER TABLE formulario_campo DROP tamanho');}
}
