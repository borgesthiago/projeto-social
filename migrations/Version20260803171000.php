<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260803171000 extends AbstractMigration
{
 public function getDescription():string{return 'Alinha a coluna de comprovante dos critérios ao Doctrine';}
 public function up(Schema $schema):void{$this->addSql('ALTER TABLE prioridade_fila CHANGE exige_comprovante exige_comprovante TINYINT NOT NULL');}
 public function down(Schema $schema):void{$this->addSql('ALTER TABLE prioridade_fila CHANGE exige_comprovante exige_comprovante TINYINT NOT NULL DEFAULT 0');}
}
