<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260804100000 extends AbstractMigration
{
 public function getDescription():string{return 'Adiciona metadados aos comprovantes financeiros enviados';}
 public function up(Schema $schema):void{$this->addSql('ALTER TABLE movimentacao_financeira ADD comprovante_nome VARCHAR(255) DEFAULT NULL, ADD comprovante_mime VARCHAR(100) DEFAULT NULL, ADD comprovante_tamanho INT DEFAULT NULL');}
 public function down(Schema $schema):void{$this->addSql('ALTER TABLE movimentacao_financeira DROP comprovante_nome, DROP comprovante_mime, DROP comprovante_tamanho');}
}
