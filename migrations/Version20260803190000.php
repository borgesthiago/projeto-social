<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260803190000 extends AbstractMigration
{
 public function getDescription():string{return 'Adiciona galeria de fotos à execução das aulas';}
 public function up(Schema $schema):void{$this->addSql('CREATE TABLE aula_foto (id INT AUTO_INCREMENT NOT NULL, projeto_id INT NOT NULL, aula_id INT NOT NULL, caminho VARCHAR(255) NOT NULL, nome_original VARCHAR(255) NOT NULL, tamanho INT NOT NULL, criada_em DATETIME NOT NULL, INDEX IDX_2B9F9DD643B58490 (projeto_id), INDEX IDX_2B9F9DD4AD1A1255 (aula_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');$this->addSql('ALTER TABLE aula_foto ADD CONSTRAINT FK_2B9F9DD643B58490 FOREIGN KEY (projeto_id) REFERENCES projeto_social (id) ON DELETE CASCADE');$this->addSql('ALTER TABLE aula_foto ADD CONSTRAINT FK_2B9F9DD4AD1A1255 FOREIGN KEY (aula_id) REFERENCES aula (id) ON DELETE CASCADE');}
 public function down(Schema $schema):void{$this->addSql('DROP TABLE aula_foto');}
}
