# Projeto Social

Plataforma SaaS multi-tenant de gestão educacional e impacto social construída com Symfony 7.3, Doctrine ORM e MySQL 8. Cada organização possui usuários e dados completamente separados.

## Executar localmente

1. Inicie os serviços: `docker compose up -d --build`
2. Instale dependências, se necessário: `docker compose exec app composer install`
3. Aplique o esquema: `docker compose exec app php bin/console doctrine:migrations:migrate -n`
4. Crie o acesso inicial: `docker compose exec app php bin/console app:create-admin admin@projeto.local sua-senha "Administrador"`

O passo 4 é opcional: você também pode abrir http://localhost:8000/cadastro e criar o primeiro projeto pela interface.

> O Docker Desktop precisa estar iniciado antes do primeiro comando. A aplicação abre em http://localhost:8000.

Perfis disponíveis: `ROLE_MASTER`, `ROLE_ADMINISTRATIVO`, `ROLE_PROFESSOR`, `ROLE_FINANCEIRO`, `ROLE_RESPONSAVEL` e `ROLE_ALUNO`.

Também é possível criar uma organização diretamente pela tela pública `/cadastro`. O primeiro usuário se torna o master do projeto e pode cadastrar os demais em **Usuários**.

## Superadministrador

O superadministrador global não pertence a nenhum projeto e acessa `/superadmin`. Para criar outra conta global:

`docker compose exec app php bin/console app:create-super-admin email@dominio.com senha-segura "Nome"`

Essa central permite visualizar, editar, ativar e suspender todas as organizações da plataforma.

## Módulos da primeira versão

Dashboard, usuários, turmas, aulas, alunos, responsáveis, frequência, financeiro, fila de espera, relatórios, áreas de professor/responsável/aluno, transparência, notificações e configurações. O modelo já inclui os campos necessários para a Evolution API; o envio será conectado na etapa de integração.
