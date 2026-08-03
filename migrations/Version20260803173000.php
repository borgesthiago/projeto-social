<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260803173000 extends AbstractMigration
{
 public function getDescription():string{return 'Exige aluno previamente cadastrado na fila de espera';}
 public function up(Schema $schema):void{$this->addSql('ALTER TABLE fila_espera ADD aluno_id INT NOT NULL, DROP nome, DROP data_nascimento, DROP telefone');$this->addSql('ALTER TABLE fila_espera ADD CONSTRAINT FK_694B6EFDB2DDF7F4 FOREIGN KEY (aluno_id) REFERENCES aluno (id)');$this->addSql('CREATE INDEX IDX_694B6EFDB2DDF7F4 ON fila_espera (aluno_id)');$this->addSql('CREATE UNIQUE INDEX UNIQ_694B6EFD6FBBCD3BB2DDF7F4 ON fila_espera (turma_desejada_id, aluno_id)');}
 public function down(Schema $schema):void{$this->addSql('ALTER TABLE fila_espera DROP FOREIGN KEY FK_694B6EFDB2DDF7F4');$this->addSql('DROP INDEX UNIQ_694B6EFD6FBBCD3BB2DDF7F4 ON fila_espera');$this->addSql('DROP INDEX IDX_694B6EFDB2DDF7F4 ON fila_espera');$this->addSql("ALTER TABLE fila_espera ADD nome VARCHAR(120) NOT NULL, ADD data_nascimento DATE NOT NULL, ADD telefone VARCHAR(30) NOT NULL, DROP aluno_id");}
}
