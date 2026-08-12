<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260812170000 extends AbstractMigration
{
 public function getDescription():string{return 'Adiciona estoque, composição e distribuição de kits';}
 public function up(Schema $schema):void
 {
  $this->addSql('CREATE TABLE item_estoque (id INT AUTO_INCREMENT NOT NULL, projeto_id INT NOT NULL, nome VARCHAR(140) NOT NULL, unidade VARCHAR(40) NOT NULL, quantidade NUMERIC(12,3) NOT NULL, estoque_minimo NUMERIC(12,3) NOT NULL, categoria VARCHAR(80) DEFAULT NULL, localizacao VARCHAR(80) DEFAULT NULL, ativo TINYINT NOT NULL, INDEX IDX_ITEM_ESTOQUE_PROJETO (projeto_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
  $this->addSql('CREATE TABLE movimentacao_estoque (id INT AUTO_INCREMENT NOT NULL, projeto_id INT NOT NULL, item_id INT NOT NULL, tipo VARCHAR(20) NOT NULL, quantidade NUMERIC(12,3) NOT NULL, motivo VARCHAR(160) NOT NULL, criada_em DATETIME NOT NULL, INDEX IDX_MOV_ESTOQUE_PROJETO (projeto_id), INDEX IDX_MOV_ESTOQUE_ITEM (item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
  $this->addSql('CREATE TABLE kit_estoque (id INT AUTO_INCREMENT NOT NULL, projeto_id INT NOT NULL, nome VARCHAR(140) NOT NULL, descricao LONGTEXT DEFAULT NULL, ativo TINYINT NOT NULL, INDEX IDX_KIT_ESTOQUE_PROJETO (projeto_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
  $this->addSql('CREATE TABLE kit_componente (id INT AUTO_INCREMENT NOT NULL, projeto_id INT NOT NULL, kit_id INT NOT NULL, item_id INT NOT NULL, quantidade NUMERIC(12,3) NOT NULL, INDEX IDX_KIT_COMP_PROJETO (projeto_id), INDEX IDX_KIT_COMP_KIT (kit_id), INDEX IDX_KIT_COMP_ITEM (item_id), UNIQUE INDEX UNIQ_KIT_ITEM (kit_id,item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
  $this->addSql('CREATE TABLE distribuicao_kit (id INT AUTO_INCREMENT NOT NULL, projeto_id INT NOT NULL, kit_id INT NOT NULL, turma_id INT DEFAULT NULL, acao_id INT DEFAULT NULL, quantidade INT NOT NULL, data DATE NOT NULL, observacoes LONGTEXT DEFAULT NULL, INDEX IDX_DIST_KIT_PROJETO (projeto_id), INDEX IDX_DIST_KIT_KIT (kit_id), INDEX IDX_DIST_KIT_TURMA (turma_id), INDEX IDX_DIST_KIT_ACAO (acao_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
  foreach([['item_estoque','ITEM_EST_PROJ'],['movimentacao_estoque','MOV_EST_PROJ'],['kit_estoque','KIT_EST_PROJ'],['kit_componente','KIT_COMP_PROJ'],['distribuicao_kit','DIST_KIT_PROJ']] as [$table,$key])$this->addSql("ALTER TABLE $table ADD CONSTRAINT FK_$key FOREIGN KEY (projeto_id) REFERENCES projeto_social (id) ON DELETE CASCADE");
  $this->addSql('ALTER TABLE movimentacao_estoque ADD CONSTRAINT FK_MOV_EST_ITEM FOREIGN KEY (item_id) REFERENCES item_estoque (id)');$this->addSql('ALTER TABLE kit_componente ADD CONSTRAINT FK_KIT_COMP_KIT FOREIGN KEY (kit_id) REFERENCES kit_estoque (id) ON DELETE CASCADE');$this->addSql('ALTER TABLE kit_componente ADD CONSTRAINT FK_KIT_COMP_ITEM FOREIGN KEY (item_id) REFERENCES item_estoque (id)');$this->addSql('ALTER TABLE distribuicao_kit ADD CONSTRAINT FK_DIST_KIT_KIT FOREIGN KEY (kit_id) REFERENCES kit_estoque (id)');$this->addSql('ALTER TABLE distribuicao_kit ADD CONSTRAINT FK_DIST_KIT_TURMA FOREIGN KEY (turma_id) REFERENCES turma (id) ON DELETE SET NULL');$this->addSql('ALTER TABLE distribuicao_kit ADD CONSTRAINT FK_DIST_KIT_ACAO FOREIGN KEY (acao_id) REFERENCES acao_social (id) ON DELETE SET NULL');
 }
 public function down(Schema $schema):void{$this->addSql('DROP TABLE distribuicao_kit');$this->addSql('DROP TABLE kit_componente');$this->addSql('DROP TABLE movimentacao_estoque');$this->addSql('DROP TABLE kit_estoque');$this->addSql('DROP TABLE item_estoque');}
}
