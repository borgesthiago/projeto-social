<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260803210500 extends AbstractMigration
{
 public function getDescription():string{return 'Alinha os campos booleanos de preferência de contato';}
 public function up(Schema $schema):void{$this->addSql('ALTER TABLE responsavel CHANGE contato_whatsapp contato_whatsapp TINYINT NOT NULL, CHANGE contato_email contato_email TINYINT NOT NULL, CHANGE contato_sms contato_sms TINYINT NOT NULL, CHANGE contato_telefone contato_telefone TINYINT NOT NULL');}
 public function down(Schema $schema):void{$this->addSql('ALTER TABLE responsavel CHANGE contato_whatsapp contato_whatsapp TINYINT(1) NOT NULL DEFAULT 0, CHANGE contato_email contato_email TINYINT(1) NOT NULL DEFAULT 0, CHANGE contato_sms contato_sms TINYINT(1) NOT NULL DEFAULT 0, CHANGE contato_telefone contato_telefone TINYINT(1) NOT NULL DEFAULT 0');}
}
