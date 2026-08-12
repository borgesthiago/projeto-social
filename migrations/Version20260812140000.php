<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260812140000 extends AbstractMigration
{
 public function getDescription():string{return 'Adiciona construtor de formulários e respostas públicas';}
 public function up(Schema $schema):void
 {
  $this->addSql('CREATE TABLE formulario (id INT AUTO_INCREMENT NOT NULL, projeto_id INT NOT NULL, acao_id INT DEFAULT NULL, aula_id INT DEFAULT NULL, titulo VARCHAR(160) NOT NULL, tipo VARCHAR(30) NOT NULL, token VARCHAR(40) NOT NULL, descricao LONGTEXT DEFAULT NULL, abertura DATETIME DEFAULT NULL, encerramento DATETIME DEFAULT NULL, status VARCHAR(20) NOT NULL, mensagem_confirmacao LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_FORM_TOKEN (token), INDEX IDX_FORM_PROJETO (projeto_id), INDEX IDX_FORM_ACAO (acao_id), INDEX IDX_FORM_AULA (aula_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
  $this->addSql('CREATE TABLE formulario_campo (id INT AUTO_INCREMENT NOT NULL, projeto_id INT NOT NULL, formulario_id INT NOT NULL, rotulo VARCHAR(160) NOT NULL, tipo VARCHAR(30) NOT NULL, ajuda LONGTEXT DEFAULT NULL, opcoes JSON NOT NULL, obrigatorio TINYINT NOT NULL, ordem INT NOT NULL, INDEX IDX_CAMPO_PROJETO (projeto_id), INDEX IDX_CAMPO_FORM (formulario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
  $this->addSql('CREATE TABLE formulario_resposta (id INT AUTO_INCREMENT NOT NULL, projeto_id INT NOT NULL, formulario_id INT NOT NULL, nome VARCHAR(140) DEFAULT NULL, email VARCHAR(180) DEFAULT NULL, telefone VARCHAR(30) DEFAULT NULL, respostas JSON NOT NULL, enviado_em DATETIME NOT NULL, INDEX IDX_RESPOSTA_PROJETO (projeto_id), INDEX IDX_RESPOSTA_FORM (formulario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
  foreach([['formulario','FORM_PROJETO','projeto_social'],['formulario_campo','CAMPO_PROJETO','projeto_social'],['formulario_resposta','RESPOSTA_PROJETO','projeto_social']] as [$table,$key,$target])$this->addSql("ALTER TABLE $table ADD CONSTRAINT FK_$key FOREIGN KEY (projeto_id) REFERENCES $target (id) ON DELETE CASCADE");
  $this->addSql('ALTER TABLE formulario ADD CONSTRAINT FK_FORM_ACAO FOREIGN KEY (acao_id) REFERENCES acao_social (id) ON DELETE SET NULL');$this->addSql('ALTER TABLE formulario ADD CONSTRAINT FK_FORM_AULA FOREIGN KEY (aula_id) REFERENCES aula (id) ON DELETE SET NULL');$this->addSql('ALTER TABLE formulario_campo ADD CONSTRAINT FK_CAMPO_FORM FOREIGN KEY (formulario_id) REFERENCES formulario (id) ON DELETE CASCADE');$this->addSql('ALTER TABLE formulario_resposta ADD CONSTRAINT FK_RESPOSTA_FORM FOREIGN KEY (formulario_id) REFERENCES formulario (id) ON DELETE CASCADE');
 }
 public function down(Schema $schema):void{$this->addSql('DROP TABLE formulario_resposta');$this->addSql('DROP TABLE formulario_campo');$this->addSql('DROP TABLE formulario');}
}
