<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260803181000 extends AbstractMigration
{
 public function getDescription():string{return 'Vincula critérios ao aluno e remove o nível manual';}
 public function up(Schema $schema):void{$this->addSql('CREATE TABLE aluno_criterio (aluno_id INT NOT NULL, prioridade_fila_id INT NOT NULL, INDEX IDX_61E9E7DBB2DDF7F4 (aluno_id), INDEX IDX_61E9E7DBF50F586C (prioridade_fila_id), PRIMARY KEY(aluno_id, prioridade_fila_id)) DEFAULT CHARACTER SET utf8mb4');$this->addSql('ALTER TABLE aluno_criterio ADD CONSTRAINT FK_61E9E7DBB2DDF7F4 FOREIGN KEY (aluno_id) REFERENCES aluno (id) ON DELETE CASCADE');$this->addSql('ALTER TABLE aluno_criterio ADD CONSTRAINT FK_61E9E7DBF50F586C FOREIGN KEY (prioridade_fila_id) REFERENCES prioridade_fila (id) ON DELETE CASCADE');$this->addSql('ALTER TABLE aluno DROP qualificacao');}
 public function down(Schema $schema):void{$this->addSql("ALTER TABLE aluno ADD qualificacao VARCHAR(20) NOT NULL DEFAULT 'bronze'");$this->addSql('DROP TABLE aluno_criterio');}
}
