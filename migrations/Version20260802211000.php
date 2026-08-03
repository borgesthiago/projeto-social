<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260802211000 extends AbstractMigration {
 public function getDescription():string{return 'Alinha cotas e índice de notificações ao Doctrine';}
 public function up(Schema $schema):void{$this->addSql('ALTER TABLE notificacao_log RENAME INDEX IDX_A8F91AE743B58490 TO IDX_D98F9843B58490');$this->addSql('ALTER TABLE plano CHANGE limite_turmas limite_turmas INT NOT NULL, CHANGE limite_notificacoes_mes limite_notificacoes_mes INT DEFAULT NULL, CHANGE limite_armazenamento_mb limite_armazenamento_mb INT NOT NULL');}
 public function down(Schema $schema):void{}
}
