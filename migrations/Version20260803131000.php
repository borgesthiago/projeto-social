<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260803131000 extends AbstractMigration {
 public function getDescription():string{return 'Alinha colunas de cores ao Doctrine';}
 public function up(Schema $schema):void{$this->addSql('ALTER TABLE projeto_social CHANGE cor_primaria cor_primaria VARCHAR(7) NOT NULL, CHANGE cor_secundaria cor_secundaria VARCHAR(7) NOT NULL, CHANGE cor_sidebar cor_sidebar VARCHAR(7) NOT NULL, CHANGE cor_texto_botao cor_texto_botao VARCHAR(7) NOT NULL');}
 public function down(Schema $schema):void{}
}
