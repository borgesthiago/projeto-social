<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260811100000 extends AbstractMigration
{
 public function getDescription():string{return 'Adiciona ações e eventos sociais dos projetos';}
 public function up(Schema $schema):void{$this->addSql('CREATE TABLE acao_social (id INT AUTO_INCREMENT NOT NULL, projeto_id INT NOT NULL, nome VARCHAR(150) NOT NULL, tipo VARCHAR(30) NOT NULL, descricao LONGTEXT DEFAULT NULL, data_inicio DATETIME NOT NULL, data_fim DATETIME DEFAULT NULL, local VARCHAR(180) NOT NULL, responsavel VARCHAR(120) DEFAULT NULL, publico_alvo LONGTEXT DEFAULT NULL, capacidade INT DEFAULT NULL, participantes INT NOT NULL, status VARCHAR(20) NOT NULL, publica TINYINT NOT NULL, resultados LONGTEXT DEFAULT NULL, INDEX IDX_ACAO_SOCIAL_PROJETO (projeto_id), INDEX IDX_ACAO_SOCIAL_DATA (data_inicio), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');$this->addSql('ALTER TABLE acao_social ADD CONSTRAINT FK_ACAO_SOCIAL_PROJETO FOREIGN KEY (projeto_id) REFERENCES projeto_social (id) ON DELETE CASCADE');}
 public function down(Schema $schema):void{$this->addSql('DROP TABLE acao_social');}
}
