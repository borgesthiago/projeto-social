<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260803150000 extends AbstractMigration {
 public function getDescription():string{return 'Modalidades, faixa etária e vínculo obrigatório por turma';}
 public function up(Schema $schema):void{$this->addSql('CREATE TABLE modalidade (id INT AUTO_INCREMENT NOT NULL, projeto_id INT NOT NULL, nome VARCHAR(120) NOT NULL, descricao LONGTEXT DEFAULT NULL, carga_horaria INT DEFAULT NULL, ativa TINYINT NOT NULL, INDEX IDX_172560F943B58490 (projeto_id), UNIQUE INDEX UNIQ_172560F943B5849054BD530C (projeto_id,nome), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');$this->addSql('ALTER TABLE modalidade ADD CONSTRAINT FK_172560F943B58490 FOREIGN KEY (projeto_id) REFERENCES projeto_social (id) ON DELETE CASCADE');$this->addSql('ALTER TABLE turma ADD idade_minima INT NOT NULL, ADD idade_maxima INT NOT NULL, ADD modalidade_id INT NOT NULL');$this->addSql('ALTER TABLE turma ADD CONSTRAINT FK_2B0219A6F2AD3298 FOREIGN KEY (modalidade_id) REFERENCES modalidade (id)');$this->addSql('CREATE INDEX IDX_2B0219A6F2AD3298 ON turma (modalidade_id)');}
 public function down(Schema $schema):void{$this->addSql('ALTER TABLE turma DROP FOREIGN KEY FK_2B0219A6F2AD3298');$this->addSql('DROP INDEX IDX_2B0219A6F2AD3298 ON turma');$this->addSql('ALTER TABLE turma DROP idade_minima, DROP idade_maxima, DROP modalidade_id');$this->addSql('DROP TABLE modalidade');}
}
