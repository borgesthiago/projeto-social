# Commit, push e deploy

Copie `.env.deploy.example` para `.env.deploy.local` e configure host, usuário, chave SSH e `DEPLOY_PATH`. Esse caminho deve apontar para o clone do projeto no servidor. O servidor também deve possuir um `.env.prod` com as variáveis usadas por `compose.prod.yaml`.

No PowerShell, execute o fluxo completo com:

```powershell
./scripts/release.ps1 "feat: descrição da alteração"
```

O script valida os templates Twig, executa os testes, cria o commit, envia a branch atual e pede confirmação antes de atualizar o servidor, reconstruir os containers e aplicar as migrations.

Opções disponíveis:

- `-CommitOnly`: cria o commit sem push ou deploy.
- `-PushOnly`: envia os commits existentes e não cria outro commit.
- `-SkipTests`: ignora as validações locais.
- `-ForceDeploy`: remove a confirmação interativa do deploy.
