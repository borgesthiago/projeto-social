<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260812200000 extends AbstractMigration
{
 public function getDescription():string{return 'Adiciona dados demográficos, catálogo e vínculos ODS';}
 public function up(Schema $schema):void
 {
  $this->addSql('ALTER TABLE aluno ADD sexo VARCHAR(30) DEFAULT NULL');$this->addSql('ALTER TABLE professor ADD sexo VARCHAR(30) DEFAULT NULL');
  $this->addSql('CREATE TABLE ods (id INT AUTO_INCREMENT NOT NULL, projeto_id INT NOT NULL, numero INT NOT NULL, nome VARCHAR(160) NOT NULL, descricao LONGTEXT DEFAULT NULL, cor VARCHAR(7) NOT NULL, ativo TINYINT NOT NULL, INDEX IDX_ODS_PROJETO (projeto_id), UNIQUE INDEX UNIQ_ODS_PROJ_NUM (projeto_id,numero), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');$this->addSql('ALTER TABLE ods ADD CONSTRAINT FK_ODS_PROJETO FOREIGN KEY (projeto_id) REFERENCES projeto_social (id) ON DELETE CASCADE');
  foreach([['acao_social_ods','acao_social_id','acao_social','ods_id','ACAO_ODS'],['aula_ods','aula_id','aula','ods_id','AULA_ODS'],['acao_profissional_ods','acao_profissional_id','acao_profissional','ods_id','PROF_ODS']] as [$table,$owner,$target,$ods,$key]){$this->addSql("CREATE TABLE $table ($owner INT NOT NULL, $ods INT NOT NULL, INDEX IDX_{$key}_OWNER ($owner), INDEX IDX_{$key}_ODS ($ods), PRIMARY KEY($owner,$ods)) DEFAULT CHARACTER SET utf8mb4");$this->addSql("ALTER TABLE $table ADD CONSTRAINT FK_{$key}_OWNER FOREIGN KEY ($owner) REFERENCES $target (id) ON DELETE CASCADE");$this->addSql("ALTER TABLE $table ADD CONSTRAINT FK_{$key}_ODS FOREIGN KEY ($ods) REFERENCES ods (id) ON DELETE CASCADE");}
  $names=['Erradicação da pobreza','Fome zero e agricultura sustentável','Saúde e bem-estar','Educação de qualidade','Igualdade de gênero','Água potável e saneamento','Energia limpa e acessível','Trabalho decente e crescimento econômico','Indústria, inovação e infraestrutura','Redução das desigualdades','Cidades e comunidades sustentáveis','Consumo e produção responsáveis','Ação contra a mudança global do clima','Vida na água','Vida terrestre','Paz, justiça e instituições eficazes','Parcerias e meios de implementação'];foreach($names as $i=>$name)$this->addSql('INSERT INTO ods (projeto_id,numero,nome,cor,ativo) SELECT id, ?, ?, ?, 1 FROM projeto_social',[$i+1,$name,'#2563eb']);
 }
 public function down(Schema $schema):void{$this->addSql('DROP TABLE acao_profissional_ods');$this->addSql('DROP TABLE aula_ods');$this->addSql('DROP TABLE acao_social_ods');$this->addSql('DROP TABLE ods');$this->addSql('ALTER TABLE aluno DROP sexo');$this->addSql('ALTER TABLE professor DROP sexo');}
}
