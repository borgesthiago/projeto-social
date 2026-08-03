<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260803210000 extends AbstractMigration
{
 public function getDescription():string{return 'Qualifica o cadastro e as preferências de contato dos responsáveis';}
 public function up(Schema $schema):void{$this->addSql("ALTER TABLE responsavel ADD whatsapp VARCHAR(30) DEFAULT NULL, ADD parentesco VARCHAR(30) DEFAULT NULL, ADD rg VARCHAR(30) DEFAULT NULL, ADD profissao VARCHAR(120) DEFAULT NULL, ADD renda_familiar NUMERIC(12, 2) DEFAULT NULL, ADD endereco VARCHAR(255) DEFAULT NULL, ADD contato_whatsapp TINYINT(1) NOT NULL DEFAULT 0, ADD contato_email TINYINT(1) NOT NULL DEFAULT 0, ADD contato_sms TINYINT(1) NOT NULL DEFAULT 0, ADD contato_telefone TINYINT(1) NOT NULL DEFAULT 0");}
 public function down(Schema $schema):void{$this->addSql('ALTER TABLE responsavel DROP whatsapp, DROP parentesco, DROP rg, DROP profissao, DROP renda_familiar, DROP endereco, DROP contato_whatsapp, DROP contato_email, DROP contato_sms, DROP contato_telefone');}
}
