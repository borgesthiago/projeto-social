<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260803181500 extends AbstractMigration
{
 public function getDescription():string{return 'Alinha os índices da relação entre alunos e critérios';}
 public function up(Schema $schema):void{$this->addSql('ALTER TABLE aluno_criterio RENAME INDEX IDX_61E9E7DBB2DDF7F4 TO IDX_53E98752B2DDF7F4');$this->addSql('ALTER TABLE aluno_criterio RENAME INDEX IDX_61E9E7DBF50F586C TO IDX_53E9875217250989');}
 public function down(Schema $schema):void{$this->addSql('ALTER TABLE aluno_criterio RENAME INDEX IDX_53E98752B2DDF7F4 TO IDX_61E9E7DBB2DDF7F4');$this->addSql('ALTER TABLE aluno_criterio RENAME INDEX IDX_53E9875217250989 TO IDX_61E9E7DBF50F586C');}
}
