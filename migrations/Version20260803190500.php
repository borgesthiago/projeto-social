<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260803190500 extends AbstractMigration
{
 public function getDescription():string{return 'Alinha os índices da galeria de fotos ao Doctrine';}
 public function up(Schema $schema):void{$this->addSql('ALTER TABLE aula_foto RENAME INDEX IDX_2B9F9DD4AD1A1255 TO IDX_B7C45830AD1A1255');$this->addSql('ALTER TABLE aula_foto RENAME INDEX IDX_2B9F9DD643B58490 TO IDX_B7C4583043B58490');}
 public function down(Schema $schema):void{$this->addSql('ALTER TABLE aula_foto RENAME INDEX IDX_B7C45830AD1A1255 TO IDX_2B9F9DD4AD1A1255');$this->addSql('ALTER TABLE aula_foto RENAME INDEX IDX_B7C4583043B58490 TO IDX_2B9F9DD643B58490');}
}
