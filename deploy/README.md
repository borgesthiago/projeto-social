# Commit, push e deploy

Copie `.env.deploy.example` para `.env.deploy.local` e configure host, usuário, chave SSH, `DEPLOY_PATH` e `DEPLOY_ENV_FILE`. O servidor deve possuir esse arquivo de ambiente dentro do diretório indicado.

No PowerShell, execute o fluxo completo com:

```powershell
./scripts/release.ps1 "feat: descrição da alteração"
```

O script valida a configuração, os templates Twig e os testes; cria o commit; envia a branch atual; empacota somente os arquivos versionados; e pede confirmação antes de atualizar o servidor, reconstruir os containers e aplicar as migrations. Arquivos persistentes que não fazem parte do Git, como uploads, certificados e o ambiente de produção, são preservados.

Opções disponíveis:

- `-CommitOnly`: cria o commit sem push ou deploy.
- `-PushOnly`: envia os commits existentes e não cria outro commit.
- `-SkipTests`: ignora as validações locais.
- `-ForceDeploy`: remove a confirmação interativa do deploy.
