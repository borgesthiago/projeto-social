# Commit, push e deploy

Copie `.env.deploy.example` para `.env.deploy.local` e configure host, usuário, chave SSH, `DEPLOY_PATH`, `DEPLOY_REPOSITORY` e `DEPLOY_ENV_FILE`. O servidor deve possuir esse arquivo de ambiente dentro do diretório indicado.

No PowerShell, execute o fluxo completo com:

```powershell
./scripts/release.ps1 "feat: descrição da alteração"
```

O script valida a configuração, os templates Twig e os testes; cria o commit; envia a branch atual; acessa a VPS por SSH; executa `git pull --ff-only`; reconstrói os containers; e aplica as migrations. Na primeira execução, se o diretório remoto ainda não for um repositório Git, ele é vinculado automaticamente ao repositório configurado. Arquivos persistentes fora do Git, como uploads, certificados e o ambiente de produção, são preservados.

Opções disponíveis:

- `-CommitOnly`: cria o commit sem push ou deploy.
- `-PushOnly`: envia os commits existentes e não cria outro commit.
- `-SkipTests`: ignora as validações locais.
- `-ForceDeploy`: remove a confirmação interativa do deploy.
