[CmdletBinding()]
param()

$ErrorActionPreference = 'Stop'

function Get-DotEnvValue {
    param(
        [string] $Path,
        [string] $Name,
        [string] $Default
    )

    if (-not (Test-Path -LiteralPath $Path)) {
        return $Default
    }

    $match = [Regex]::Match(
        [IO.File]::ReadAllText($Path),
        '(?m)^' + [Regex]::Escape($Name) + '=(.*)$'
    )
    if (-not $match.Success) {
        return $Default
    }

    return $match.Groups[1].Value.Trim().Trim('"').Trim("'")
}

function Test-TcpPort {
    param(
        [string] $HostName,
        [int] $Port,
        [int] $TimeoutMilliseconds = 1500
    )

    $client = New-Object System.Net.Sockets.TcpClient
    try {
        $result = $client.BeginConnect($HostName, $Port, $null, $null)
        if (-not $result.AsyncWaitHandle.WaitOne($TimeoutMilliseconds, $false)) {
            return $false
        }

        $client.EndConnect($result)
        return $true
    }
    catch {
        return $false
    }
    finally {
        $client.Close()
    }
}

$projectRoot = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
$envPath = Join-Path $projectRoot '.env'

if (-not (Get-Command composer -ErrorAction SilentlyContinue)) {
    throw "Command 'composer' tidak ditemukan. Aktifkan Composer di System PATH FlyEnv."
}

if (-not (Test-Path -LiteralPath $envPath)) {
    throw '.env belum ada. Jalankan .\scripts\setup.ps1 terlebih dahulu.'
}

$databaseConnection = Get-DotEnvValue $envPath 'DB_CONNECTION' 'mysql'
if ($databaseConnection -eq 'mysql') {
    $databaseHost = Get-DotEnvValue $envPath 'DB_HOST' '127.0.0.1'
    $databasePort = [int] (Get-DotEnvValue $envPath 'DB_PORT' '3306')

    if (-not (Test-TcpPort -HostName $databaseHost -Port $databasePort)) {
        throw "MariaDB tidak dapat dihubungi di ${databaseHost}:$databasePort. Jalankan MariaDB dari FlyEnv."
    }
}

Push-Location $projectRoot
try {
    Write-Host 'Menjalankan FamFinance di http://127.0.0.1:8000' -ForegroundColor Green
    Write-Host 'Tekan Ctrl+C untuk menghentikan seluruh development stack.' -ForegroundColor DarkGray
    & composer run dev
    if ($LASTEXITCODE -ne 0) {
        throw "Development stack berhenti dengan exit code $LASTEXITCODE."
    }
}
finally {
    Pop-Location
}
