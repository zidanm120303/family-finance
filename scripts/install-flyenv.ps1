[CmdletBinding()]
param(
    [switch] $NoLaunch
)

$ErrorActionPreference = 'Stop'

function Write-Step {
    param([string] $Message)
    Write-Host "`n==> $Message" -ForegroundColor Cyan
}

if ($env:OS -ne 'Windows_NT') {
    throw 'Script ini hanya mendukung Windows. Ikuti panduan manual pada README untuk sistem lain.'
}

if ($PSVersionTable.PSVersion -lt [version] '5.1') {
    throw 'PowerShell 5.1 atau lebih baru diperlukan.'
}

$flyEnvCandidates = @(
    (Join-Path $env:ProgramFiles 'FlyEnv\FlyEnv.exe'),
    (Join-Path $env:LOCALAPPDATA 'Programs\FlyEnv\FlyEnv.exe')
)

$flyEnvExecutable = $flyEnvCandidates |
    Where-Object { Test-Path -LiteralPath $_ } |
    Select-Object -First 1

if (-not $flyEnvExecutable) {
    Write-Step 'Memasang FlyEnv melalui winget'

    if (-not (Get-Command winget -ErrorAction SilentlyContinue)) {
        throw @'
winget tidak ditemukan. Pasang "App Installer" dari Microsoft Store,
atau unduh FlyEnv secara manual dari:
https://github.com/xpf0000/FlyEnv/releases/latest
'@
    }

    & winget install `
        --id xpf0000.FlyEnv `
        --exact `
        --source winget `
        --accept-package-agreements `
        --accept-source-agreements

    if ($LASTEXITCODE -ne 0) {
        throw "Instalasi FlyEnv gagal dengan exit code $LASTEXITCODE."
    }

    $flyEnvExecutable = $flyEnvCandidates |
        Where-Object { Test-Path -LiteralPath $_ } |
        Select-Object -First 1
}
else {
    Write-Step 'FlyEnv sudah terpasang'
    Write-Host $flyEnvExecutable
}

if (-not $flyEnvExecutable) {
    throw 'FlyEnv selesai dipasang, tetapi FlyEnv.exe tidak ditemukan. Buka FlyEnv dari Start Menu.'
}

if (-not $NoLaunch) {
    Write-Step 'Membuka FlyEnv'
    Start-Process -FilePath $flyEnvExecutable
}

Write-Host @'

Lanjutkan di antarmuka FlyEnv:
  1. PHP      : pasang PHP 8.5 x64.
  2. Extension: aktifkan curl, dom, fileinfo, mbstring, openssl,
                pdo, pdo_mysql, tokenizer, dan xml.
  3. Composer : pasang Composer 2 stabil.
  4. MariaDB  : pasang MariaDB 11.8 LTS dan klik Start.
  5. Node.js  : pasang Node.js 24 LTS (npm ikut terpasang).
  6. Tambahkan PHP, Composer, MariaDB, dan Node.js ke System PATH.
  7. Tutup terminal, buka kembali, lalu jalankan:
       php --version
       composer --version
       mariadb --version
       node --version
       npm --version

Setelah semuanya terdeteksi:
  .\scripts\setup.ps1 -ConfigureDatabase -Seed
  .\scripts\dev.ps1
'@ -ForegroundColor Green
