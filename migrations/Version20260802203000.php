<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260802203000 extends AbstractMigration {
 public function getDescription():string{return 'Planos, assinaturas e pagamentos do SaaS';}
 public function up(Schema $schema):void{
  $this->addSql("CREATE TABLE plano (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(100) NOT NULL, valor_mensal NUMERIC(10,2) NOT NULL, limite_usuarios INT NOT NULL, limite_alunos INT DEFAULT NULL, dias_teste INT NOT NULL, recursos JSON NOT NULL, ativo TINYINT NOT NULL, publico TINYINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4");
  $this->addSql("CREATE TABLE assinatura (id INT AUTO_INCREMENT NOT NULL, projeto_id INT NOT NULL, plano_id INT NOT NULL, status VARCHAR(20) NOT NULL, inicio DATE NOT NULL, vencimento DATE NOT NULL, dias_carencia INT NOT NULL, limite_usuarios_personalizado INT DEFAULT NULL, criado_em DATETIME NOT NULL, INDEX IDX_2F0780709A8B86CC (plano_id), UNIQUE INDEX UNIQ_2F07807043B58490 (projeto_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4");
  $this->addSql("CREATE TABLE pagamento_saas (id INT AUTO_INCREMENT NOT NULL, assinatura_id INT NOT NULL, valor NUMERIC(10,2) NOT NULL, vencimento DATE NOT NULL, pago_em DATETIME DEFAULT NULL, status VARCHAR(20) NOT NULL, metodo VARCHAR(40) DEFAULT NULL, referencia VARCHAR(120) DEFAULT NULL, observacao LONGTEXT DEFAULT NULL, criado_em DATETIME NOT NULL, INDEX IDX_C1E392C9757A0A7 (assinatura_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4");
  $this->addSql('ALTER TABLE assinatura ADD CONSTRAINT FK_2F07807043B58490 FOREIGN KEY (projeto_id) REFERENCES projeto_social (id) ON DELETE CASCADE');$this->addSql('ALTER TABLE assinatura ADD CONSTRAINT FK_2F0780709A8B86CC FOREIGN KEY (plano_id) REFERENCES plano (id)');$this->addSql('ALTER TABLE pagamento_saas ADD CONSTRAINT FK_C1E392C9757A0A7 FOREIGN KEY (assinatura_id) REFERENCES assinatura (id) ON DELETE CASCADE');
  $this->addSql("INSERT INTO plano (nome,valor_mensal,limite_usuarios,limite_alunos,dias_teste,recursos,ativo,publico) VALUES ('Essencial',99.90,5,100,14,'[\"gestao\",\"frequencia\"]',1,1),('Profissional',199.90,15,500,14,'[\"gestao\",\"frequencia\",\"financeiro\",\"whatsapp\"]',1,1),('Impacto',399.90,50,NULL,30,'[\"todos\"]',1,1)");
  $this->addSql("INSERT INTO assinatura (projeto_id,plano_id,status,inicio,vencimento,dias_carencia,limite_usuarios_personalizado,criado_em) SELECT p.id,(SELECT id FROM plano ORDER BY id LIMIT 1),'teste',CURRENT_DATE,DATE_ADD(CURRENT_DATE,INTERVAL 14 DAY),5,NULL,NOW() FROM projeto_social p");
 }
 public function down(Schema $schema):void{$this->addSql('DROP TABLE pagamento_saas');$this->addSql('DROP TABLE assinatura');$this->addSql('DROP TABLE plano');}
}
