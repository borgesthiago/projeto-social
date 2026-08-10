<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260810180000 extends AbstractMigration
{
 public function getDescription():string{return 'Amplia os dados cadastrais dos professores';}
 public function up(Schema $schema):void{$this->addSql('ALTER TABLE professor ADD telefone VARCHAR(30) DEFAULT NULL, ADD data_nascimento DATE DEFAULT NULL, ADD registro_profissional VARCHAR(50) DEFAULT NULL, ADD tipo_vinculo VARCHAR(30) DEFAULT NULL, ADD carga_horaria_semanal INT DEFAULT NULL, ADD data_admissao DATE DEFAULT NULL, ADD endereco VARCHAR(255) DEFAULT NULL, ADD observacoes LONGTEXT DEFAULT NULL');}
 public function down(Schema $schema):void{$this->addSql('ALTER TABLE professor DROP telefone, DROP data_nascimento, DROP registro_profissional, DROP tipo_vinculo, DROP carga_horaria_semanal, DROP data_admissao, DROP endereco, DROP observacoes');}
}
