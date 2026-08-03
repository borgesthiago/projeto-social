<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260803170000 extends AbstractMigration
{
 public function getDescription():string{return 'Qualifica os critérios configuráveis da fila de espera';}
 public function up(Schema $schema):void{$this->addSql('ALTER TABLE prioridade_fila ADD descricao LONGTEXT DEFAULT NULL, ADD exige_comprovante TINYINT NOT NULL DEFAULT 0');}
 public function down(Schema $schema):void{$this->addSql('ALTER TABLE prioridade_fila DROP descricao, DROP exige_comprovante');}
}
