Write-Host "🐬 Starting MySQL Stack (MySQL + phpMyAdmin)..." -ForegroundColor Cyan

# Lance le profil mysql (commande moderne : docker compose)
docker compose --profile mysql up -d

Write-Host "⏳ Waiting for services to be healthy..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

# Charge les variables depuis le .env à la racine (si présent)
$envFile = Join-Path (Resolve-Path "..") ".env"
if (Test-Path $envFile) {
    Get-Content $envFile | ForEach-Object {
        if ($_ -match '^\s*#' -or $_ -match '^\s*$') { return }
        $parts = $_ -split '=', 2
        if ($parts.Count -eq 2) {
            $key = $parts[0].Trim()
            $val = $parts[1].Trim().Trim('"')
            if ($key) { Set-Item -Path "Env:$key" -Value $val }
        }
    }
}

# Valeurs par défaut si non définies
$MYSQL_PORT = if ($env:MYSQL_PORT) { $env:MYSQL_PORT } else { "3306" }
$PHPMYADMIN_PORT = if ($env:PHPMYADMIN_PORT) { $env:PHPMYADMIN_PORT } else { "8080" }
$MYSQL_DATABASE = if ($env:MYSQL_DATABASE) { $env:MYSQL_DATABASE } else { "internal_tools" }
$MYSQL_USER = if ($env:MYSQL_USER) { $env:MYSQL_USER } else { "dev" }
$MYSQL_PASSWORD = if ($env:MYSQL_PASSWORD) { $env:MYSQL_PASSWORD } else { "dev123" }

Write-Host "✅ MySQL Stack Ready!" -ForegroundColor Green
Write-Host ""
Write-Host "🔗 Access Information:"
Write-Host "   MySQL: localhost:$MYSQL_PORT"
Write-Host "   phpMyAdmin: http://localhost:$PHPMYADMIN_PORT"
Write-Host "   Database: $MYSQL_DATABASE"
Write-Host "   User: $MYSQL_USER"
Write-Host ""
Write-Host "📊 Connection String:"
Write-Host "   mysql://$MYSQL_USER:$MYSQL_PASSWORD@localhost:$MYSQL_PORT/$MYSQL_DATABASE"
