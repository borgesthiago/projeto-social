<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260803200000 extends AbstractMigration
{
 public function getDescription():string{return 'Adiciona convites temporários para usuários';}
 public function up(Schema $schema):void{$this->addSql('ALTER TABLE `user` ADD convite_token VARCHAR(64) DEFAULT NULL, ADD convite_expira_em DATETIME DEFAULT NULL');$this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649D8EDE356 ON `user` (convite_token)');}
 public function down(Schema $schema):void{$this->addSql('DROP INDEX UNIQ_8D93D649D8EDE356 ON `user`');$this->addSql('ALTER TABLE `user` DROP convite_token, DROP convite_expira_em');}
}
