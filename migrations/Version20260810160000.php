<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260810160000 extends AbstractMigration
{
 public function getDescription():string{return 'Adiciona galeria de fotos aos alunos';}
 public function up(Schema $schema):void{$this->addSql('CREATE TABLE aluno_foto (id INT AUTO_INCREMENT NOT NULL, projeto_id INT NOT NULL, aluno_id INT NOT NULL, caminho VARCHAR(255) NOT NULL, nome_original VARCHAR(255) NOT NULL, tamanho INT NOT NULL, criada_em DATETIME NOT NULL, INDEX IDX_ALUNO_FOTO_PROJETO (projeto_id), INDEX IDX_ALUNO_FOTO_ALUNO (aluno_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');$this->addSql('ALTER TABLE aluno_foto ADD CONSTRAINT FK_ALUNO_FOTO_PROJETO FOREIGN KEY (projeto_id) REFERENCES projeto_social (id) ON DELETE CASCADE');$this->addSql('ALTER TABLE aluno_foto ADD CONSTRAINT FK_ALUNO_FOTO_ALUNO FOREIGN KEY (aluno_id) REFERENCES aluno (id) ON DELETE CASCADE');}
 public function down(Schema $schema):void{$this->addSql('DROP TABLE aluno_foto');}
}
