<?php
namespace App\Service;

final class FeatureCatalog
{
 public const FEATURES = [
  'gestao_alunos' => 'Alunos e responsáveis',
  'gestao_turmas' => 'Turmas e professores',
  'modalidades_cursos' => 'Modalidades e cursos',
  'gestao_aulas' => 'Aulas e planejamento',
  'frequencia' => 'Controle de frequência',
  'fila_espera' => 'Fila de espera e prioridades',
  'financeiro' => 'Financeiro interno da ONG',
  'transparencia' => 'Portal de transparência',
  'relatorios' => 'Relatórios e indicadores',
  'notificacoes' => 'Central de notificações',
  'whatsapp_evolution' => 'Integração Evolution API',
  'identidade_visual' => 'Identidade visual personalizada',
  'exportacao' => 'Exportação de relatórios',
  'api' => 'Acesso à API',
  'suporte_prioritario' => 'Suporte prioritário',
 ];

 public const CHANNELS = ['email' => 'E-mail', 'whatsapp' => 'WhatsApp', 'sms' => 'SMS'];
}
