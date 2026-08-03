<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260803175000 extends AbstractMigration
{
 public function getDescription():string{return 'Move a pontuação da fila para o cadastro do aluno';}
 public function up(Schema $schema):void{$this->addSql('ALTER TABLE aluno ADD pontuacao INT NOT NULL DEFAULT 0');$this->addSql('UPDATE aluno a INNER JOIN (SELECT aluno_id, MAX(pontuacao) pontuacao FROM fila_espera GROUP BY aluno_id) f ON f.aluno_id = a.id SET a.pontuacao = f.pontuacao');$this->addSql('ALTER TABLE aluno CHANGE pontuacao pontuacao INT NOT NULL');$this->addSql('ALTER TABLE fila_espera DROP pontuacao');}
 public function down(Schema $schema):void{$this->addSql('ALTER TABLE fila_espera ADD pontuacao INT NOT NULL DEFAULT 0');$this->addSql('UPDATE fila_espera f INNER JOIN aluno a ON a.id = f.aluno_id SET f.pontuacao = a.pontuacao');$this->addSql('ALTER TABLE fila_espera CHANGE pontuacao pontuacao INT NOT NULL');$this->addSql('ALTER TABLE aluno DROP pontuacao');}
}
