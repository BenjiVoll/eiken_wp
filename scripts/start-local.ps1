param(
    [switch]$SeedOnly,
    [switch]$SetupOnly
)

$ErrorActionPreference = "Stop"
$RootDir = Split-Path -Parent $PSScriptRoot
$RuntimeDir = Join-Path $RootDir ".runtime"
$PhpDir = Join-Path $RuntimeDir "php"
$WpDir = Join-Path $RuntimeDir "wordpress"
$WpCliPhar = Join-Path $RuntimeDir "wp-cli.phar"
$PhpExe = Join-Path $PhpDir "php.exe"

New-Item -ItemType Directory -Force -Path $RuntimeDir | Out-Null

# 1. Descargar PHP 8.3 portable si no existe
if (-not (Test-Path $PhpExe)) {
    Write-Host "=> Descargando PHP 8.3 portable para Windows..." -ForegroundColor Cyan
    $PhpZip = Join-Path $RuntimeDir "php.zip"
    curl.exe -L -s "https://windows.php.net/downloads/releases/php-8.3.36-1-nts-Win32-vs16-x64.zip" -o $PhpZip
    New-Item -ItemType Directory -Force -Path $PhpDir | Out-Null
    Expand-Archive -Path $PhpZip -DestinationPath $PhpDir -Force
    Remove-Item $PhpZip -Force

    # Configurar php.ini con extensiones necesarias para WordPress y WooCommerce
    $PhpIni = Join-Path $PhpDir "php.ini"
    Copy-Item (Join-Path $PhpDir "php.ini-development") $PhpIni -Force
    $iniContent = Get-Content $PhpIni -Raw
    $ExtDir = (Join-Path $PhpDir "ext") -replace "\\", "/"
    $iniContent = $iniContent -replace ';?\s*extension_dir\s*=\s*"ext"', "extension_dir = `"$ExtDir`""
    foreach ($ext in @("curl", "gd", "intl", "mbstring", "mysqli", "openssl", "pdo_mysql", "pdo_sqlite", "sqlite3", "zip", "fileinfo", "exif")) {
        $iniContent = $iniContent -replace ";extension=$ext", "extension=$ext"
    }
    $iniContent += "`nmemory_limit = 512M`nmax_execution_time = 300`nupload_max_filesize = 64M`npost_max_size = 64M`n"
    Set-Content -Path $PhpIni -Value $iniContent -Encoding ASCII
}

# 2. Descargar WP-CLI si no existe
if (-not (Test-Path $WpCliPhar)) {
    Write-Host "=> Descargando WP-CLI..." -ForegroundColor Cyan
    curl.exe -L -s "https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar" -o $WpCliPhar
}

# 3. Descargar WordPress + SQLite integration si no existe
if (-not (Test-Path (Join-Path $WpDir "wp-load.php"))) {
    Write-Host "=> Descargando WordPress oficial..." -ForegroundColor Cyan
    $WpZip = Join-Path $RuntimeDir "wordpress.zip"
    curl.exe -L -s "https://wordpress.org/latest.zip" -o $WpZip
    Expand-Archive -Path $WpZip -DestinationPath $RuntimeDir -Force
    Remove-Item $WpZip -Force

    # Configurar SQLite Database Integration (permite correr WP sin servidor MySQL externo)
    Write-Host "=> Configurando base de datos local SQLite para WordPress..." -ForegroundColor Cyan
    $SqliteZip = Join-Path $RuntimeDir "sqlite.zip"
    curl.exe -L -s "https://downloads.wordpress.org/plugin/sqlite-database-integration.latest-stable.zip" -o $SqliteZip
    $PluginsDir = Join-Path $WpDir "wp-content\plugins"
    Expand-Archive -Path $SqliteZip -DestinationPath $PluginsDir -Force
    Remove-Item $SqliteZip -Force

    # Crear wp-config.php
    $WpConfig = Join-Path $WpDir "wp-config.php"
    Copy-Item (Join-Path $WpDir "wp-config-sample.php") $WpConfig -Force
    $dbCopy = Get-Content (Join-Path $PluginsDir "sqlite-database-integration\db.copy") -Raw
    $dbCopy = $dbCopy -replace "\{SQLITE_IMPLEMENTATION_FOLDER_PATH\}", ((Join-Path $PluginsDir "sqlite-database-integration") -replace "\\", "/")
    $dbCopy = $dbCopy -replace "\{SQLITE_PLUGIN\}", "sqlite-database-integration/load.php"
    Set-Content -Path (Join-Path $WpDir "wp-content\db.php") -Value $dbCopy -Encoding UTF8
}

# 4. Copiar tema personalizado Eiken Design
$DestTheme = Join-Path $WpDir "wp-content\themes\eiken-store"
New-Item -ItemType Directory -Force -Path $DestTheme | Out-Null
Copy-Item -Path (Join-Path $RootDir "theme\eiken-store\*") -Destination $DestTheme -Recurse -Force

# 5. Instalar WordPress, WooCommerce, Transbank Webpay REST y cargar datos de Eiken Design
$ErrorActionPreference = "Continue"
$env:WP_CLI_PHP = $PhpExe
$Utf8NoBom = New-Object System.Text.UTF8Encoding $false

# Asegurar wp-config.php sin BOM y con HTTP_HOST para ejecuciones CLI
$WpConfig = Join-Path $WpDir "wp-config.php"
Copy-Item (Join-Path $WpDir "wp-config-sample.php") $WpConfig -Force
$cfgText = Get-Content $WpConfig -Raw
$cfgText = "<?php`nif (!isset(`$_SERVER['HTTP_HOST'])) { `$_SERVER['HTTP_HOST'] = 'localhost:8080'; }`n" + ($cfgText -replace '^<\?php\s*', '')
[System.IO.File]::WriteAllText($WpConfig, $cfgText, $Utf8NoBom)

# Asegurar db.php sin BOM
$PluginsDir = Join-Path $WpDir "wp-content\plugins"
$dbCopy = Get-Content (Join-Path $PluginsDir "sqlite-database-integration\db.copy") -Raw
$dbCopy = $dbCopy -replace "\{SQLITE_IMPLEMENTATION_FOLDER_PATH\}", ((Join-Path $PluginsDir "sqlite-database-integration") -replace "\\", "/")
$dbCopy = $dbCopy -replace "\{SQLITE_PLUGIN\}", "sqlite-database-integration/load.php"
[System.IO.File]::WriteAllText((Join-Path $WpDir "wp-content\db.php"), $dbCopy, $Utf8NoBom)

function Invoke-Wp {
    param([Parameter(ValueFromRemainingArguments = $true)]$ArgsList)
    & $PhpExe $WpCliPhar --path=$WpDir @ArgsList
}

& $PhpExe $WpCliPhar --path=$WpDir core is-installed *> $null
if ($LASTEXITCODE -ne 0) {
    Write-Host "=> Instalando WordPress + WooCommerce + Transbank Webpay..." -ForegroundColor Cyan
    Invoke-Wp core install --url="http://localhost:8080" --title="Eiken Design Store" --admin_user="admin" --admin_password="eiken2026" --admin_email="admin@eikendesign.cl" --skip-email
    Invoke-Wp plugin install woocommerce --activate
    Invoke-Wp plugin install transbank-webpay-plus-rest --activate
    Invoke-Wp theme activate eiken-store
    Invoke-Wp eval-file (Join-Path $RootDir "scripts\seed-eiken.php")
} elseif ($SeedOnly) {
    Invoke-Wp theme activate eiken-store
    Invoke-Wp eval-file (Join-Path $RootDir "scripts\seed-eiken.php")
    exit 0
}

if ($SetupOnly) {
    Write-Host "=> Entorno WordPress + WooCommerce preparado en $WpDir" -ForegroundColor Green
    exit 0
}

Write-Host "`n============================================================" -ForegroundColor Green
Write-Host " EIKEN DESIGN (WORDPRESS + WOOCOMMERCE) CORRIENDO EN:" -ForegroundColor Green
Write-Host "   Tienda Pública: http://localhost:8080" -ForegroundColor Yellow
Write-Host "   Panel Admin:    http://localhost:8080/wp-admin" -ForegroundColor Yellow
Write-Host "   Usuario Admin:  admin" -ForegroundColor Cyan
Write-Host "   Clave Admin:    eiken2026" -ForegroundColor Cyan
Write-Host "============================================================`n" -ForegroundColor Green

& $PhpExe -S localhost:8080 -t $WpDir
