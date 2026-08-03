<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260803110000 extends AbstractMigration {
 public function getDescription():string{return 'CNPJ dos projetos e CPF único dos alunos, professores e responsáveis';}
 public function up(Schema $schema):void{$this->addSql('CREATE UNIQUE INDEX UNIQ_67C9710043B584903E3E11F0 ON aluno (projeto_id,cpf)');$this->addSql('ALTER TABLE professor ADD cpf VARCHAR(11) DEFAULT NULL');$this->addSql('CREATE UNIQUE INDEX UNIQ_790DD7E343B584903E3E11F0 ON professor (projeto_id,cpf)');$this->addSql('ALTER TABLE projeto_social ADD cnpj VARCHAR(14) DEFAULT NULL');$this->addSql('CREATE UNIQUE INDEX UNIQ_8FB50808C8C6906B ON projeto_social (cnpj)');$this->addSql('CREATE UNIQUE INDEX UNIQ_E163054643B584903E3E11F0 ON responsavel (projeto_id,cpf)');}
 public function down(Schema $schema):void{$this->addSql('DROP INDEX UNIQ_67C9710043B584903E3E11F0 ON aluno');$this->addSql('DROP INDEX UNIQ_790DD7E343B584903E3E11F0 ON professor');$this->addSql('ALTER TABLE professor DROP cpf');$this->addSql('DROP INDEX UNIQ_8FB50808C8C6906B ON projeto_social');$this->addSql('ALTER TABLE projeto_social DROP cnpj');$this->addSql('DROP INDEX UNIQ_E163054643B584903E3E11F0 ON responsavel');}
}
