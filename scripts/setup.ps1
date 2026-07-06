[CmdletBinding()]
param(
    [switch] $ConfigureDatabase,
    [switch] $Seed,
    [switch] $SkipTests,
    [string] $DatabaseName = 'family_finance',
    [string] $DatabaseUser = 'famfinance_app',
    [string] $DatabaseHost = '127.0.0.1',
    [ValidateRange(1, 65535)]
    [int] $DatabasePort = 3306
)

$ErrorActionPreference = 'Stop'

function Write-Step {
    param([string] $Message)
    Write-Host "`n==> $Message" -ForegroundColor Cyan
}

function Assert-Command {
    param(
        [string] $Name,
        [string] $Hint
    )

    if (-not (Get-Command $Name -ErrorAction SilentlyContinue)) {
        throw "Command '$Name' tidak ditemukan. $Hint"
    }
}

function Invoke-Checked {
    param(
        [string] $Command,
        [string[]] $Arguments
    )

    & $Command @Arguments
    if ($LASTEXITCODE -ne 0) {
        throw "'$Command $($Arguments -join ' ')' gagal dengan exit code $LASTEXITCODE."
    }
}

function Assert-MinimumVersion {
    param(
        [string] $Name,
        [string] $Current,
        [version] $Minimum
    )

    try {
        $parsed = [version] $Current
    }
    catch {
        throw "Versi $Name tidak dapat dibaca: '$Current'."
    }

    if ($parsed -lt $Minimum) {
        throw "$Name $Minimum atau lebih baru diperlukan; versi aktif: $parsed."
    }
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

function ConvertFrom-SecureValue {
    param([Security.SecureString] $Value)

    $pointer = [Runtime.InteropServices.Marshal]::SecureStringToBSTR($Value)
    try {
        return [Runtime.InteropServices.Marshal]::PtrToStringBSTR($pointer)
    }
    finally {
        [Runtime.InteropServices.Marshal]::ZeroFreeBSTR($pointer)
    }
}

function New-RandomPassword {
    param([int] $Length = 32)

    $characters = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789'
    $bytes = New-Object byte[] $Length
    $generator = [Security.Cryptography.RandomNumberGenerator]::Create()
    try {
        $generator.GetBytes($bytes)
    }
    finally {
        $generator.Dispose()
    }

    $password = New-Object Text.StringBuilder
    foreach ($byte in $bytes) {
        [void] $password.Append($characters[$byte % $characters.Length])
    }

    return $password.ToString()
}

function ConvertTo-DotEnvValue {
    param([string] $Value)

    if ($Value -match "[`r`n]") {
        throw 'Nilai .env tidak boleh mengandung baris baru.'
    }

    $escaped = $Value.Replace('\', '\\').Replace('"', '\"')
    return '"' + $escaped + '"'
}

function Set-DotEnvValue {
    param(
        [string] $Path,
        [string] $Name,
        [string] $Value
    )

    $content = [IO.File]::ReadAllText($Path)
    $line = "$Name=$Value"
    $pattern = '(?m)^' + [Regex]::Escape($Name) + '=.*$'

    if ([Regex]::IsMatch($content, $pattern)) {
        $content = [Regex]::Replace($content, $pattern, $line)
    }
    else {
        $newLine = if ($content.Contains("`r`n")) { "`r`n" } else { "`n" }
        $content = $content.TrimEnd("`r", "`n") + $newLine + $line + $newLine
    }

    [IO.File]::WriteAllText(
        $Path,
        $content,
        (New-Object Text.UTF8Encoding($false))
    )
}

if ($env:OS -ne 'Windows_NT') {
    throw 'Script ini dibuat untuk PowerShell di Windows.'
}

if ($DatabaseName -notmatch '^[A-Za-z0-9_]+$') {
    throw 'Nama database hanya boleh berisi huruf, angka, dan underscore.'
}

if ($DatabaseUser -notmatch '^[A-Za-z0-9_]+$') {
    throw 'Nama user database hanya boleh berisi huruf, angka, dan underscore.'
}

$projectRoot = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
$envPath = Join-Path $projectRoot '.env'
$envExamplePath = Join-Path $projectRoot '.env.example'

Push-Location $projectRoot
try {
    Write-Step 'Memeriksa runtime'
    Assert-Command 'php' 'Tambahkan PHP FlyEnv ke System PATH.'
    Assert-Command 'composer' 'Pasang Composer 2 dan tambahkan ke System PATH.'
    Assert-Command 'node' 'Pasang Node.js 24 LTS melalui FlyEnv.'
    Assert-Command 'npm' 'npm ikut terpasang bersama Node.js.'

    $phpVersion = (& php -r 'echo PHP_VERSION;').Trim()
    $nodeVersion = (& node --version).Trim().TrimStart('v')
    Assert-MinimumVersion 'PHP' $phpVersion ([version] '8.3.0')
    Assert-MinimumVersion 'Node.js' $nodeVersion ([version] '22.12.0')

    $requiredExtensions = @(
        'ctype', 'curl', 'dom', 'fileinfo', 'filter', 'hash', 'mbstring',
        'openssl', 'pdo', 'pdo_mysql', 'session', 'tokenizer', 'xml'
    )
    $phpModules = @(& php -m) | ForEach-Object { $_.Trim().ToLowerInvariant() }
    $missingExtensions = @($requiredExtensions | Where-Object { $_ -notin $phpModules })
    if ($missingExtensions.Count -gt 0) {
        throw "Extension PHP belum aktif: $($missingExtensions -join ', '). Aktifkan melalui php.ini FlyEnv."
    }

    Write-Host "PHP $phpVersion dan Node.js $nodeVersion siap." -ForegroundColor Green

    Write-Step 'Memasang dependency PHP'
    Invoke-Checked 'composer' @('install', '--prefer-dist', '--no-interaction')
    Invoke-Checked 'composer' @('check-platform-reqs')

    Write-Step 'Menyiapkan file environment'
    if (-not (Test-Path -LiteralPath $envPath)) {
        Copy-Item -LiteralPath $envExamplePath -Destination $envPath
        Write-Host '.env dibuat dari .env.example.'
    }
    else {
        Write-Host '.env sudah ada dan tidak ditimpa.'
    }

    if ($ConfigureDatabase) {
        Assert-Command 'mariadb' 'Pasang MariaDB melalui FlyEnv dan tambahkan client-nya ke System PATH.'

        if (-not (Test-TcpPort -HostName $DatabaseHost -Port $DatabasePort)) {
            throw "MariaDB tidak dapat dihubungi di ${DatabaseHost}:$DatabasePort. Jalankan servicenya dari FlyEnv."
        }

        $adminUser = Read-Host 'User administrator MariaDB [root]'
        if ([string]::IsNullOrWhiteSpace($adminUser)) {
            $adminUser = 'root'
        }
        if ($adminUser -notmatch '^[A-Za-z0-9_]+$') {
            throw 'Nama user administrator hanya boleh berisi huruf, angka, dan underscore.'
        }

        $adminSecurePassword = Read-Host 'Password administrator MariaDB (input disembunyikan)' -AsSecureString
        $appSecurePassword = Read-Host 'Password user aplikasi (kosong = dibuat acak)' -AsSecureString
        $adminPassword = ConvertFrom-SecureValue $adminSecurePassword
        $appPassword = ConvertFrom-SecureValue $appSecurePassword

        if ([string]::IsNullOrEmpty($appPassword)) {
            $appPassword = New-RandomPassword
            Write-Host 'Password acak dibuat dan akan disimpan hanya di .env lokal.'
        }

        $sqlPassword = $appPassword.Replace('\', '\\').Replace("'", "''")
        $sql = @"
CREATE DATABASE IF NOT EXISTS ``$DatabaseName``
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$DatabaseUser'@'127.0.0.1' IDENTIFIED BY '$sqlPassword';
ALTER USER '$DatabaseUser'@'127.0.0.1' IDENTIFIED BY '$sqlPassword';
GRANT ALL PRIVILEGES ON ``$DatabaseName``.* TO '$DatabaseUser'@'127.0.0.1';
CREATE USER IF NOT EXISTS '$DatabaseUser'@'localhost' IDENTIFIED BY '$sqlPassword';
ALTER USER '$DatabaseUser'@'localhost' IDENTIFIED BY '$sqlPassword';
GRANT ALL PRIVILEGES ON ``$DatabaseName``.* TO '$DatabaseUser'@'localhost';
"@

        Write-Step 'Membuat database dan user aplikasi'
        $previousMysqlPassword = $env:MYSQL_PWD
        try {
            $env:MYSQL_PWD = $adminPassword
            $sql | & mariadb `
                '--protocol=tcp' `
                "--host=$DatabaseHost" `
                "--port=$DatabasePort" `
                "--user=$adminUser" `
                '--batch'

            if ($LASTEXITCODE -ne 0) {
                throw "Pembuatan database gagal dengan exit code $LASTEXITCODE."
            }
        }
        finally {
            if ($null -eq $previousMysqlPassword) {
                Remove-Item Env:MYSQL_PWD -ErrorAction SilentlyContinue
            }
            else {
                $env:MYSQL_PWD = $previousMysqlPassword
            }

            $adminPassword = $null
            $adminSecurePassword = $null
            $appSecurePassword = $null
        }

        Set-DotEnvValue $envPath 'DB_CONNECTION' 'mysql'
        Set-DotEnvValue $envPath 'DB_HOST' $DatabaseHost
        Set-DotEnvValue $envPath 'DB_PORT' $DatabasePort.ToString()
        Set-DotEnvValue $envPath 'DB_DATABASE' $DatabaseName
        Set-DotEnvValue $envPath 'DB_USERNAME' $DatabaseUser
        Set-DotEnvValue $envPath 'DB_PASSWORD' (ConvertTo-DotEnvValue $appPassword)
        $appPassword = $null
    }

    $envContent = [IO.File]::ReadAllText($envPath)
    if ($envContent -match '(?m)^APP_KEY=\s*$') {
        Write-Step 'Membuat application key'
        Invoke-Checked 'php' @('artisan', 'key:generate', '--force', '--ansi')
    }

    Write-Step 'Membersihkan cache konfigurasi'
    Invoke-Checked 'php' @('artisan', 'config:clear', '--ansi')

    Write-Step 'Menjalankan migration'
    $migrationArguments = @('artisan', 'migrate', '--force', '--ansi')
    if ($Seed) {
        Write-Warning 'Seeder FamFinance akan mengosongkan tabel aplikasi sebelum membuat data demo.'
        $seedConfirmation = Read-Host 'Ketik SEED untuk melanjutkan'
        if ($seedConfirmation -cne 'SEED') {
            throw 'Seeding dibatalkan agar data yang ada tetap aman.'
        }
        $migrationArguments += '--seed'
    }
    Invoke-Checked 'php' $migrationArguments

    Write-Step 'Memasang dependency frontend'
    Invoke-Checked 'npm' @('ci')

    Write-Step 'Membangun asset frontend'
    Invoke-Checked 'npm' @('run', 'build')

    if (-not $SkipTests) {
        Write-Step 'Menjalankan test'
        Invoke-Checked 'php' @('artisan', 'test', '--ansi')
    }

    Write-Host @'

Setup selesai.
Jalankan aplikasi dengan:
  .\scripts\dev.ps1

Lalu buka:
  http://127.0.0.1:8000
'@ -ForegroundColor Green
}
finally {
    Pop-Location
}
