<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260802200000 extends AbstractMigration {
 public function getDescription():string{return 'Permite superadministrador global sem projeto';}
 public function up(Schema $schema):void{$this->addSql('ALTER TABLE `user` MODIFY projeto_id INT DEFAULT NULL');}
 public function down(Schema $schema):void{$this->addSql('ALTER TABLE `user` MODIFY projeto_id INT NOT NULL');}
}
