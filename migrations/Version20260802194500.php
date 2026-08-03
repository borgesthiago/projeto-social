<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260802194500 extends AbstractMigration {
 public function getDescription():string{return 'Alinha índices multi-tenant aos nomes do Doctrine';}
 public function up(Schema $schema):void{
  foreach([
   ['aluno','idx_aluno_resp','IDX_67C97100BB9AF004'],['aluno','idx_aluno_turma','IDX_67C97100CEBA2CFD'],['aluno','idx_aluno_user','IDX_67C97100DB38439E'],['aluno','idx_aluno_projeto','IDX_67C9710043B58490'],
   ['aula','idx_aula_turma','IDX_31990A4CEBA2CFD'],['aula','idx_aula_projeto','IDX_31990A443B58490'],['configuracao_sistema','uniq_config_projeto_chave','UNIQ_43ABA92043B58490EB8ACFDC'],
   ['fila_espera','idx_fila_turma','IDX_694B6EFD6FBBCD3B'],['fila_espera','idx_fila_projeto','IDX_694B6EFD43B58490'],['frequencia','idx_freq_aluno','IDX_26ED9274B2DDF7F4'],['frequencia','idx_freq_projeto','IDX_26ED927443B58490'],['frequencia','uniq_frequencia','UNIQ_26ED9274AD1A1255B2DDF7F4'],
   ['movimentacao_financeira','idx_fin_projeto','IDX_323184AB43B58490'],['prioridade_fila','idx_prior_projeto','IDX_A869BA7943B58490'],['professor','idx_prof_user','IDX_790DD7E3DB38439E'],['professor','idx_prof_projeto','IDX_790DD7E343B58490'],
   ['projeto_social','uniq_projeto_slug','UNIQ_8FB50808989D9B62'],['responsavel','idx_resp_user','IDX_E1630546DB38439E'],['responsavel','idx_resp_projeto','IDX_E163054643B58490'],['turma','idx_turma_prof','IDX_2B0219A67D2D84D5'],['turma','idx_turma_projeto','IDX_2B0219A643B58490'],
   ['`user`','uniq_user_email','UNIQ_8D93D649E7927C74'],['`user`','idx_user_projeto','IDX_8D93D64943B58490'],
  ] as [$table,$from,$to])$this->addSql("ALTER TABLE $table RENAME INDEX $from TO $to");
 }
 public function down(Schema $schema):void{}
}
