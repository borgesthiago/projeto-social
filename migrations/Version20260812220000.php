<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260812220000 extends AbstractMigration
{
 public function getDescription():string{return 'Vincula cadastros de alunos, professores, turmas, modalidades e prestadores aos ODS';}
 public function up(Schema $schema):void
 {
  foreach([['aluno_ods','aluno_id','aluno','ALUNO_ODS'],['professor_ods','professor_id','professor','PROFESSOR_ODS'],['turma_ods','turma_id','turma','TURMA_ODS'],['modalidade_ods','modalidade_id','modalidade','MODALIDADE_ODS'],['prestador_servico_ods','prestador_servico_id','prestador_servico','PRESTADOR_ODS']] as [$table,$owner,$target,$key]){$this->addSql("CREATE TABLE $table ($owner INT NOT NULL, ods_id INT NOT NULL, INDEX IDX_{$key}_OWNER ($owner), INDEX IDX_{$key}_ODS (ods_id), PRIMARY KEY($owner,ods_id)) DEFAULT CHARACTER SET utf8mb4");$this->addSql("ALTER TABLE $table ADD CONSTRAINT FK_{$key}_OWNER FOREIGN KEY ($owner) REFERENCES $target (id) ON DELETE CASCADE");$this->addSql("ALTER TABLE $table ADD CONSTRAINT FK_{$key}_ODS FOREIGN KEY (ods_id) REFERENCES ods (id) ON DELETE CASCADE");}
 }
 public function down(Schema $schema):void{foreach(['prestador_servico_ods','modalidade_ods','turma_ods','professor_ods','aluno_ods'] as $table)$this->addSql("DROP TABLE $table");}
}
