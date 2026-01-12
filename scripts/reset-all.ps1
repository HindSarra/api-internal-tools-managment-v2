Write-Host "🔄 Resetting all database data..." -ForegroundColor Yellow

$confirm = Read-Host "⚠️ This will destroy ALL database data. Continue? (y/N)"

if ($confirm -eq "y" -or $confirm -eq "Y") {
    Write-Host "🛑 Stopping all containers..." -ForegroundColor Red

    docker compose down -v

    Write-Host "🧹 Cleaning up unused volumes..." -ForegroundColor Yellow
    docker volume prune -f

    Write-Host "✅ All data reset completed!" -ForegroundColor Green
    Write-Host "💡 Restart with: docker compose --profile mysql up -d"
}
else {
    Write-Host "❌ Reset cancelled" -ForegroundColor Cyan
}
