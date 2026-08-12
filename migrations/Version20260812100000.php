<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260812100000 extends AbstractMigration
{
 public function getDescription():string{return 'Adiciona prestadores, profissionais por ação e detalhamento financeiro';}
 public function up(Schema $schema):void
 {
  $this->addSql('CREATE TABLE prestador_servico (id INT AUTO_INCREMENT NOT NULL, projeto_id INT NOT NULL, nome VARCHAR(140) NOT NULL, tipo_pessoa VARCHAR(20) NOT NULL, documento VARCHAR(14) NOT NULL, email VARCHAR(180) DEFAULT NULL, telefone VARCHAR(30) NOT NULL, especialidade VARCHAR(120) NOT NULL, registro_profissional VARCHAR(50) DEFAULT NULL, endereco VARCHAR(255) DEFAULT NULL, dados_bancarios VARCHAR(120) DEFAULT NULL, observacoes LONGTEXT DEFAULT NULL, ativo TINYINT NOT NULL, INDEX IDX_PRESTADOR_PROJETO (projeto_id), UNIQUE INDEX UNIQ_PRESTADOR_DOCUMENTO (projeto_id, documento), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
  $this->addSql('CREATE TABLE acao_profissional (id INT AUTO_INCREMENT NOT NULL, projeto_id INT NOT NULL, acao_id INT NOT NULL, prestador_id INT NOT NULL, funcao VARCHAR(120) NOT NULL, inicio DATETIME DEFAULT NULL, fim DATETIME DEFAULT NULL, valor_contratado NUMERIC(12, 2) NOT NULL, situacao VARCHAR(20) NOT NULL, observacoes LONGTEXT DEFAULT NULL, INDEX IDX_ACAO_PROF_PROJETO (projeto_id), INDEX IDX_ACAO_PROF_ACAO (acao_id), INDEX IDX_ACAO_PROF_PRESTADOR (prestador_id), UNIQUE INDEX UNIQ_ACAO_PROF (acao_id, prestador_id, funcao), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
  $this->addSql('ALTER TABLE prestador_servico ADD CONSTRAINT FK_PRESTADOR_PROJETO FOREIGN KEY (projeto_id) REFERENCES projeto_social (id) ON DELETE CASCADE');
  $this->addSql('ALTER TABLE acao_profissional ADD CONSTRAINT FK_ACAO_PROF_PROJETO FOREIGN KEY (projeto_id) REFERENCES projeto_social (id) ON DELETE CASCADE');
  $this->addSql('ALTER TABLE acao_profissional ADD CONSTRAINT FK_ACAO_PROF_ACAO FOREIGN KEY (acao_id) REFERENCES acao_social (id) ON DELETE CASCADE');
  $this->addSql('ALTER TABLE acao_profissional ADD CONSTRAINT FK_ACAO_PROF_PRESTADOR FOREIGN KEY (prestador_id) REFERENCES prestador_servico (id)');
  $this->addSql("ALTER TABLE movimentacao_financeira ADD prestador_id INT DEFAULT NULL, ADD acao_id INT DEFAULT NULL, ADD numero_documento VARCHAR(80) DEFAULT NULL, ADD situacao VARCHAR(20) NOT NULL DEFAULT 'pago', ADD INDEX IDX_FIN_PRESTADOR (prestador_id), ADD INDEX IDX_FIN_ACAO (acao_id)");
  $this->addSql('ALTER TABLE movimentacao_financeira ADD CONSTRAINT FK_FIN_PRESTADOR FOREIGN KEY (prestador_id) REFERENCES prestador_servico (id) ON DELETE SET NULL');
  $this->addSql('ALTER TABLE movimentacao_financeira ADD CONSTRAINT FK_FIN_ACAO FOREIGN KEY (acao_id) REFERENCES acao_social (id) ON DELETE SET NULL');
 }
 public function down(Schema $schema):void{$this->addSql('ALTER TABLE movimentacao_financeira DROP FOREIGN KEY FK_FIN_PRESTADOR, DROP FOREIGN KEY FK_FIN_ACAO, DROP prestador_id, DROP acao_id, DROP numero_documento, DROP situacao');$this->addSql('DROP TABLE acao_profissional');$this->addSql('DROP TABLE prestador_servico');}
}
