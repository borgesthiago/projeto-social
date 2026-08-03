<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260802210000 extends AbstractMigration {
 public function getDescription():string{return 'Funcionalidades, canais e cotas detalhadas dos planos';}
 public function up(Schema $schema):void{
  $this->addSql("ALTER TABLE plano ADD limite_turmas INT NOT NULL DEFAULT 5, ADD limite_notificacoes_mes INT DEFAULT 500, ADD limite_armazenamento_mb INT NOT NULL DEFAULT 1024, ADD canais_notificacao JSON DEFAULT NULL");
  $this->addSql("CREATE TABLE notificacao_log (id INT AUTO_INCREMENT NOT NULL, projeto_id INT NOT NULL, canal VARCHAR(20) NOT NULL, destinatario VARCHAR(180) NOT NULL, mensagem LONGTEXT NOT NULL, status VARCHAR(20) NOT NULL, referencia_externa VARCHAR(120) DEFAULT NULL, criado_em DATETIME NOT NULL, enviado_em DATETIME DEFAULT NULL, INDEX IDX_A8F91AE743B58490 (projeto_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4");
  $this->addSql('ALTER TABLE notificacao_log ADD CONSTRAINT FK_A8F91AE743B58490 FOREIGN KEY (projeto_id) REFERENCES projeto_social (id) ON DELETE CASCADE');
  $this->addSql("UPDATE plano SET recursos='[\"gestao_alunos\",\"gestao_turmas\",\"gestao_aulas\",\"frequencia\",\"relatorios\"]', canais_notificacao='[\"email\"]', limite_turmas=5, limite_notificacoes_mes=500, limite_armazenamento_mb=1024 WHERE nome='Essencial'");
  $this->addSql("UPDATE plano SET recursos='[\"gestao_alunos\",\"gestao_turmas\",\"gestao_aulas\",\"frequencia\",\"fila_espera\",\"financeiro\",\"transparencia\",\"relatorios\",\"notificacoes\",\"whatsapp_evolution\",\"exportacao\"]', canais_notificacao='[\"email\",\"whatsapp\"]', limite_turmas=20, limite_notificacoes_mes=3000, limite_armazenamento_mb=5120 WHERE nome='Profissional'");
  $this->addSql("UPDATE plano SET recursos='[\"todos\"]', canais_notificacao='[\"email\",\"whatsapp\",\"sms\"]', limite_turmas=100, limite_notificacoes_mes=NULL, limite_armazenamento_mb=20480 WHERE nome='Impacto'");
  $this->addSql("UPDATE plano SET canais_notificacao='[]' WHERE canais_notificacao IS NULL");
  $this->addSql('ALTER TABLE plano MODIFY canais_notificacao JSON NOT NULL');
 }
 public function down(Schema $schema):void{$this->addSql('DROP TABLE notificacao_log');$this->addSql('ALTER TABLE plano DROP limite_turmas, DROP limite_notificacoes_mes, DROP limite_armazenamento_mb, DROP canais_notificacao');}
}
