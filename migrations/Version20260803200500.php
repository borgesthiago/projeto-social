<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260803200500 extends AbstractMigration
{
 public function getDescription():string{return 'Alinha o índice único dos convites de usuário';}
 public function up(Schema $schema):void{$this->addSql('ALTER TABLE `user` RENAME INDEX UNIQ_8D93D649D8EDE356 TO UNIQ_8D93D6497AE76C59');}
 public function down(Schema $schema):void{$this->addSql('ALTER TABLE `user` RENAME INDEX UNIQ_8D93D6497AE76C59 TO UNIQ_8D93D649D8EDE356');}
}
