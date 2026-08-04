[CmdletBinding()]
param(
    [Parameter(Mandatory = $true, Position = 0)][ValidateNotNullOrEmpty()][string]$Message,
    [switch]$SkipTests, [switch]$CommitOnly, [switch]$PushOnly, [switch]$ForceDeploy
)
$ErrorActionPreference = 'Stop'
$projectRoot = Split-Path -Parent $PSScriptRoot
Set-Location $projectRoot

function Invoke-Step([string]$Title, [scriptblock]$Action) {
    Write-Host "`n==> $Title" -ForegroundColor Cyan
    & $Action
    if ($LASTEXITCODE -ne 0) { throw "Falha em: $Title (código $LASTEXITCODE)." }
}
function Read-DeployEnvironment([string]$Path) {
    $values = @{}
    if (-not (Test-Path -LiteralPath $Path)) { return $values }
    foreach ($line in Get-Content -LiteralPath $Path) {
        $trimmed = $line.Trim()
        if (-not $trimmed -or $trimmed.StartsWith('#') -or -not $trimmed.Contains('=')) { continue }
        $key, $value = $trimmed.Split('=', 2)
        $values[$key.Trim()] = $value.Trim().Trim('"').Trim("'")
    }
    return $values
}

if (-not $PushOnly) {
    if (-not $SkipTests) {
        Invoke-Step 'Validando templates Twig' { php bin/console lint:twig templates }
        Invoke-Step 'Executando testes' { php bin/phpunit }
    }
    if (-not (git status --porcelain)) { throw 'Não há alterações para criar um commit.' }
    Invoke-Step 'Adicionando alterações' { git add --all }
    Invoke-Step 'Criando commit' { git commit -m $Message }
}
if ($CommitOnly) { Write-Host "`nCommit criado; push e deploy não executados." -ForegroundColor Green; exit 0 }

$branch = (git branch --show-current).Trim()
if (-not $branch) { throw 'Não foi possível identificar a branch atual.' }
Invoke-Step "Enviando $branch para origin" { git push origin $branch }
if ($PushOnly) { Write-Host "`nPush concluído; deploy não executado." -ForegroundColor Green; exit 0 }

$deploy = Read-DeployEnvironment (Join-Path $projectRoot '.env.deploy.local')
foreach ($required in @('DEPLOY_HOST', 'DEPLOY_USER', 'DEPLOY_PATH')) {
    if (-not $deploy[$required]) { throw "Configure $required em .env.deploy.local antes do deploy." }
}
$port = if ($deploy.DEPLOY_PORT) { $deploy.DEPLOY_PORT } else { '22' }
$sshArgs = @('-p', $port, '-o', 'BatchMode=yes')
if ($deploy.DEPLOY_IDENTITY_FILE) { $sshArgs += @('-i', $deploy.DEPLOY_IDENTITY_FILE) }
$remote = "$($deploy.DEPLOY_USER)@$($deploy.DEPLOY_HOST)"
if ($deploy.DEPLOY_PATH -notmatch '^[/A-Za-z0-9_.-]+$') { throw 'DEPLOY_PATH contém caracteres não permitidos.' }
$remotePath = $deploy.DEPLOY_PATH
$remoteCommand = "cd '$remotePath' && git fetch origin && git checkout '$branch' && git pull --ff-only origin '$branch' && docker compose -f compose.prod.yaml --env-file .env.prod up -d --build && docker compose -f compose.prod.yaml --env-file .env.prod exec -T app php bin/console doctrine:migrations:migrate --no-interaction"
if (-not $ForceDeploy) {
    $answer = Read-Host "Publicar $branch em $remote ($($deploy.DEPLOY_PATH))? [s/N]"
    if ($answer -notin @('s', 'S', 'sim', 'SIM')) { throw 'Deploy cancelado.' }
}
Invoke-Step 'Executando deploy remoto' { & ssh @sshArgs $remote $remoteCommand }
Write-Host "`nCommit, push e deploy concluídos." -ForegroundColor Green
