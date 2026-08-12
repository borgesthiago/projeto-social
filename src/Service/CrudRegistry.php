<?php
namespace App\Service;

use App\Entity\{AcaoProfissional, AcaoSocial, Aluno, Aula, ConfiguracaoSistema, FilaEspera, Frequencia, Modalidade, MovimentacaoFinanceira, Ods, PrestadorServico, PrioridadeFila, Professor, Responsavel, Turma};

final class CrudRegistry
{
    public function all(): array
    {
        return [
            'alunos' => $this->resource(Aluno::class, 'Alunos', 'Aluno', ['ROLE_MASTER', 'ROLE_ADMINISTRATIVO'], [
                ['nome', 'Nome', 'text', true], ['dataNascimento', 'Data de nascimento', 'date', true],
                ['cpf', 'CPF', 'text', true], ['telefone', 'Telefone', 'text', false], ['sexo', 'Sexo / gênero', 'choice', false, ['feminino'=>'Feminino','masculino'=>'Masculino','nao_binario'=>'Não binário','outro'=>'Outro','nao_informado'=>'Prefere não informar']],
                ['status', 'Status', 'choice', true, ['ativo' => 'Ativo', 'inativo' => 'Inativo', 'fila' => 'Fila de espera', 'egresso' => 'Egresso']],
                ['pontuacao', 'Pontuação calculada', 'readonly', false],
                ['responsavel', 'Responsável', 'relation', false, Responsavel::class, 'nome'], ['turma', 'Turma', 'relation', false, Turma::class, 'nome'],
            ]),
            'responsaveis' => $this->resource(Responsavel::class, 'Responsáveis', 'Responsável', ['ROLE_MASTER', 'ROLE_ADMINISTRATIVO'], [
                ['nome', 'Nome completo', 'text', true], ['parentesco', 'Parentesco', 'choice', false, ['mae'=>'Mãe','pai'=>'Pai','avo'=>'Avô/Avó','tio'=>'Tio/Tia','tutor'=>'Tutor legal','outro'=>'Outro']],
                ['telefone', 'Telefone', 'text', true], ['whatsapp', 'WhatsApp', 'text', false], ['email', 'E-mail', 'email', false], ['cpf', 'CPF', 'text', true],
                ['rg', 'RG', 'text', false], ['profissao', 'Profissão', 'text', false], ['rendaFamiliar', 'Renda familiar (R$)', 'number', false], ['endereco', 'Endereço completo', 'text', false],
                ['contatoWhatsapp', 'Contato por WhatsApp', 'boolean', false], ['contatoEmail', 'Contato por e-mail', 'boolean', false], ['contatoSms', 'Contato por SMS', 'boolean', false], ['contatoTelefone', 'Contato por telefone', 'boolean', false], ['recebeNotificacoes', 'Recebe notificações', 'boolean', false],
            ]),
            'professores' => $this->resource(Professor::class, 'Professores', 'Professor', ['ROLE_MASTER', 'ROLE_ADMINISTRATIVO'], [
                ['nome', 'Nome completo', 'text', true], ['cpf', 'CPF', 'text', true], ['dataNascimento', 'Data de nascimento', 'date', false], ['sexo', 'Sexo / gênero', 'choice', false, ['feminino'=>'Feminino','masculino'=>'Masculino','nao_binario'=>'Não binário','outro'=>'Outro','nao_informado'=>'Prefere não informar']], ['telefone', 'Telefone / WhatsApp', 'text', true],
                ['email', 'E-mail', 'email', true], ['registroProfissional', 'Registro profissional', 'text', false], ['tipoVinculo', 'Tipo de vínculo', 'choice', false, ['clt'=>'CLT','pj'=>'Pessoa jurídica','autonomo'=>'Autônomo','voluntario'=>'Voluntário','estagiario'=>'Estagiário','servidor'=>'Servidor público','outro'=>'Outro']],
                ['cargaHorariaSemanal', 'Carga horária semanal', 'number', false], ['dataAdmissao', 'Data de admissão', 'date', false], ['endereco', 'Endereço completo', 'text', false],
                ['formacao', 'Formação acadêmica', 'textarea', false], ['especialidades', 'Especialidades e áreas de atuação', 'textarea', false], ['observacoes', 'Observações', 'textarea', false], ['ativo', 'Professor ativo', 'boolean', false],
            ]),
            'modalidades' => $this->resource(Modalidade::class, 'Modalidades e Cursos', 'Modalidade', ['ROLE_MASTER', 'ROLE_ADMINISTRATIVO'], [
                ['nome', 'Nome', 'text', true], ['descricao', 'Descrição', 'textarea', false], ['cargaHoraria', 'Carga horária', 'number', false], ['ativa', 'Ativa', 'boolean', false],
            ]),
            'turmas' => $this->resource(Turma::class, 'Turmas', 'Turma', ['ROLE_MASTER', 'ROLE_ADMINISTRATIVO'], [
                ['nome', 'Nome', 'text', true], ['modalidade', 'Modalidade/curso', 'relation', true, Modalidade::class, 'nome'], ['idadeMinima', 'Idade mínima', 'number', true], ['idadeMaxima', 'Idade máxima', 'number', true],
                ['anoLetivo', 'Ano letivo', 'number', true], ['limiteAlunos', 'Limite de alunos', 'number', true], ['horario', 'Horário', 'text', false], ['professor', 'Professor', 'relation', false, Professor::class, 'nome'], ['ativa', 'Ativa', 'boolean', false],
            ]),
            'aulas' => $this->resource(Aula::class, 'Aulas', 'Aula', ['ROLE_MASTER', 'ROLE_ADMINISTRATIVO', 'ROLE_PROFESSOR'], [
                ['titulo', 'Título', 'text', true], ['dataHora', 'Data e hora', 'datetime-local', true], ['turma', 'Turma', 'relation', true, Turma::class, 'nome'],
                ['planejamento', 'Planejamento', 'textarea', false], ['execucao', 'Execução', 'textarea', false], ['materiais', 'Materiais', 'textarea', false],
                ['status', 'Status', 'choice', true, ['planejada' => 'Agendada', 'andamento' => 'Em andamento', 'finalizada' => 'Finalizada', 'cancelada' => 'Cancelada']],
            ]),
            'frequencias' => $this->resource(Frequencia::class, 'Frequência', 'Frequência', ['ROLE_MASTER', 'ROLE_PROFESSOR'], [
                ['aula', 'Aula', 'relation', true, Aula::class, 'titulo'], ['aluno', 'Aluno', 'relation', true, Aluno::class, 'nome'],
                ['situacao', 'Situação', 'choice', true, ['presente' => 'Presente', 'ausente' => 'Ausente', 'justificada' => 'Falta justificada', 'atrasado' => 'Atrasado']],
                ['justificativa', 'Justificativa', 'textarea', false], ['notificacaoEnviada', 'Notificação enviada', 'boolean', false],
            ]),
            'fila-espera' => $this->resource(FilaEspera::class, 'Fila de Espera', 'Aluno na fila', ['ROLE_MASTER', 'ROLE_ADMINISTRATIVO'], [
                ['aluno', 'Aluno cadastrado', 'relation', true, Aluno::class, 'nome'], ['turmaDesejada', 'Turma desejada', 'relation', true, Turma::class, 'nome'],
                ['status', 'Situação', 'choice', true, ['aguardando' => 'Aguardando', 'convocado' => 'Convocado', 'matriculado' => 'Matriculado', 'desistente' => 'Desistente']],
            ]),
            'prioridades' => $this->resource(PrioridadeFila::class, 'Critérios da Fila', 'Critério', ['ROLE_MASTER'], [
                ['nome', 'Critério', 'text', true], ['descricao', 'Descrição e regra de aplicação', 'textarea', false], ['pontos', 'Pontos', 'number', true],
                ['exigeComprovante', 'Exige comprovante', 'boolean', false], ['ativa', 'Ativo', 'boolean', false],
            ]),
            'financeiro' => $this->resource(MovimentacaoFinanceira::class, 'Financeiro', 'Movimentação', ['ROLE_MASTER', 'ROLE_FINANCEIRO'], [
                ['tipo', 'Tipo', 'choice', true, ['receita' => 'Receita', 'despesa' => 'Despesa']], ['descricao', 'Descrição', 'text', true],
                ['valor', 'Valor', 'number', true], ['data', 'Data', 'date', true], ['categoria', 'Categoria', 'text', false],
                ['acao', 'Ação ou evento', 'relation', false, AcaoSocial::class, 'nome'], ['prestador', 'Prestador / profissional', 'relation', false, PrestadorServico::class, 'nome'],
                ['numeroDocumento', 'Nº nota, recibo ou contrato', 'text', false], ['situacao', 'Situação', 'choice', true, ['previsto'=>'Previsto','pago'=>'Pago/recebido','cancelado'=>'Cancelado']],
                ['comprovante', 'Comprovante (URL)', 'text', false], ['publica', 'Exibir na transparência', 'boolean', false],
            ]),
            'configuracoes' => $this->resource(ConfiguracaoSistema::class, 'Configurações', 'Configuração', ['ROLE_MASTER'], [
                ['grupo', 'Grupo', 'choice', true, ['geral' => 'Geral', 'educacional' => 'Educacional', 'notificacao' => 'Notificação', 'evolution' => 'Evolution API', 'mensagem' => 'Mensagem']],
                ['chave', 'Chave', 'text', true], ['valor', 'Valor', 'textarea', false],
                ['tipo', 'Tipo', 'choice', true, ['texto' => 'Texto', 'numero' => 'Número', 'booleano' => 'Sim/Não', 'secreto' => 'Secreto']],
            ]),
            'prestadores' => $this->resource(PrestadorServico::class, 'Profissionais e Prestadores', 'Prestador', ['ROLE_MASTER', 'ROLE_ADMINISTRATIVO', 'ROLE_FINANCEIRO'], [
                ['nome', 'Nome / Razão social', 'text', true], ['tipoPessoa', 'Tipo de pessoa', 'choice', true, ['fisica'=>'Pessoa física','juridica'=>'Pessoa jurídica']], ['documento', 'CPF / CNPJ', 'text', true],
                ['especialidade', 'Profissão / especialidade', 'text', true], ['registroProfissional', 'Registro profissional', 'text', false], ['telefone', 'Telefone / WhatsApp', 'text', true], ['email', 'E-mail', 'email', false],
                ['endereco', 'Endereço', 'text', false], ['dadosBancarios', 'Dados bancários / chave PIX', 'text', false], ['observacoes', 'Observações', 'textarea', false], ['ativo', 'Cadastro ativo', 'boolean', false],
            ]),
            'profissionais-acoes' => $this->resource(AcaoProfissional::class, 'Profissionais por Ação ou Evento', 'Vínculo profissional', ['ROLE_MASTER', 'ROLE_ADMINISTRATIVO', 'ROLE_FINANCEIRO'], [
                ['acao', 'Ação ou evento', 'relation', true, AcaoSocial::class, 'nome'], ['prestador', 'Profissional / prestador', 'relation', true, PrestadorServico::class, 'nome'], ['funcao', 'Função na ação', 'text', true],
                ['inicio', 'Início da atuação', 'datetime-local', false], ['fim', 'Término da atuação', 'datetime-local', false], ['valorContratado', 'Valor contratado (R$)', 'number', true],
                ['situacao', 'Situação', 'choice', true, ['previsto'=>'Previsto','contratado'=>'Contratado','realizado'=>'Serviço realizado','pago'=>'Pago','cancelado'=>'Cancelado']], ['observacoes', 'Escopo e observações', 'textarea', false],
            ]),
            'ods' => $this->resource(Ods::class, 'Objetivos de Desenvolvimento Sustentável', 'ODS', ['ROLE_MASTER', 'ROLE_ADMINISTRATIVO'], [
                ['numero', 'Número', 'number', true], ['nome', 'Objetivo', 'text', true], ['descricao', 'Descrição e metas relacionadas', 'textarea', false], ['cor', 'Cor de identificação', 'text', true], ['ativo', 'ODS ativo', 'boolean', false],
            ]),
            'acoes-sociais' => $this->resource(AcaoSocial::class, 'Ações e Eventos Sociais', 'Ação social', ['ROLE_MASTER', 'ROLE_ADMINISTRATIVO'], [
                ['nome', 'Nome da ação ou evento', 'text', true], ['tipo', 'Tipo', 'choice', true, ['evento'=>'Evento','campanha'=>'Campanha','oficina'=>'Oficina','mutirao'=>'Mutirão','arrecadacao'=>'Arrecadação','atendimento'=>'Atendimento comunitário','outro'=>'Outro']],
                ['dataInicio', 'Início', 'datetime-local', true], ['dataFim', 'Término', 'datetime-local', false], ['local', 'Local', 'text', true], ['responsavel', 'Responsável pela ação', 'text', false],
                ['publicoAlvo', 'Público-alvo', 'textarea', false], ['capacidade', 'Capacidade de participantes', 'number', false], ['participantes', 'Participantes confirmados/atendidos', 'number', true],
                ['status', 'Situação', 'choice', true, ['planejada'=>'Planejada','inscricoes'=>'Inscrições abertas','andamento'=>'Em andamento','concluida'=>'Concluída','cancelada'=>'Cancelada']], ['publica', 'Divulgar publicamente', 'boolean', false],
                ['descricao', 'Descrição e objetivo', 'textarea', false], ['resultados', 'Resultados e impacto alcançado', 'textarea', false],
            ]),
        ];
    }

    public function get(string $name): array
    {
        return $this->all()[$name] ?? throw new \InvalidArgumentException('Módulo inválido.');
    }

    private function resource(string $class, string $title, string $singular, array $roles, array $fields): array
    {
        return compact('class', 'title', 'singular', 'roles', 'fields');
    }
}
