<#
  Windows: prepares what the local site needs, then runs dev/avvia.sh in Git Bash.
  Started by avvia-windows.cmd (double click); options are passed on (--rete, --aggiorna).

  - Git for Windows (git and Git Bash): installed with winget, after a yes.
  - PHP 8.4: downloaded into .wp\php (portable, nothing is installed in the system), checked against the
    SHA-256 that php.net publishes, with a php.ini that enables the extensions WordPress needs.
  - Python 3: installed with winget, after a yes; needed only the first time, to download the content.

  Written for Windows PowerShell 5.1 (the one every Windows 10 and 11 has) and PowerShell 7.
#>

$ErrorActionPreference = 'Continue'
$ProgressPreference = 'SilentlyContinue'
try { [Console]::OutputEncoding = [System.Text.Encoding]::UTF8 } catch { }
[Net.ServicePointManager]::SecurityProtocol = [Net.ServicePointManager]::SecurityProtocol -bor [Net.SecurityProtocolType]::Tls12

$Root = (Resolve-Path (Join-Path $PSScriptRoot '..\..')).Path
$Wp = Join-Path $Root '.wp'
$WingetNoUpdate = -1978335189 # "already installed, no newer version": fine for us

function Write-Step([string] $Text) {
	Write-Host ''
	Write-Host "  $Text" -ForegroundColor Cyan
}

function Write-Done([string] $Text) {
	Write-Host "  OK  $Text" -ForegroundColor Green
}

function Stop-Here([string] $Text) {
	Write-Host ''
	Write-Host "  $Text" -ForegroundColor Red
	exit 1
}

function Update-SessionPath {
	$machine = [Environment]::GetEnvironmentVariable('Path', 'Machine')
	$user = [Environment]::GetEnvironmentVariable('Path', 'User')
	$env:Path = "$machine;$user"
}

function Confirm-Step([string] $Question) {
	$answer = Read-Host "  $Question [S/n]"
	return ($answer -eq '' -or $answer -match '^\s*(s|si|sì|y|yes)\s*$')
}

function Install-Package([string] $Id, [string] $Name, [string] $Page) {
	if (-not (Get-Command winget.exe -ErrorAction SilentlyContinue)) {
		Stop-Here "Manca $Name e winget non è disponibile. Scaricalo da $Page, installalo e rilancia avvia-windows.cmd."
	}
	if (-not (Confirm-Step "Manca $Name. Lo installo adesso con winget?")) {
		Stop-Here "Senza $Name il sito locale non parte. Si scarica da $Page."
	}
	& winget.exe install --id $Id --exact --source winget --accept-package-agreements --accept-source-agreements
	if ($LASTEXITCODE -ne 0 -and $LASTEXITCODE -ne $WingetNoUpdate) {
		Stop-Here "Installazione di $Name non riuscita (codice $LASTEXITCODE). Scaricalo da $Page e rilancia."
	}
	Update-SessionPath
}

# ---------- Git Bash ----------

function Find-GitBash {
	$candidates = @()
	$git = Get-Command git.exe -ErrorAction SilentlyContinue
	if ($git) {
		# ...\Git\cmd\git.exe -> ...\Git\bin\bash.exe (never C:\Windows\System32\bash.exe, which is WSL)
		$candidates += Join-Path (Split-Path (Split-Path $git.Source -Parent) -Parent) 'bin\bash.exe'
	}
	foreach ($dir in @($env:ProgramFiles, ${env:ProgramFiles(x86)}, (Join-Path $env:LOCALAPPDATA 'Programs'))) {
		if ($dir) { $candidates += Join-Path $dir 'Git\bin\bash.exe' }
	}
	foreach ($path in $candidates) {
		if (Test-Path -LiteralPath $path) { return $path }
	}
	return $null
}

# ---------- PHP ----------

function Test-Php([string] $Exe) {
	if (-not $Exe -or -not (Test-Path -LiteralPath $Exe)) { return $false }
	& $Exe -r 'exit(PHP_VERSION_ID >= 80100 && extension_loaded(''pdo_sqlite'') && extension_loaded(''mbstring'') ? 0 : 1);' *> $null
	return ($LASTEXITCODE -eq 0)
}

function Get-PortablePhp {
	$dir = Join-Path $Wp 'php'
	$exe = Join-Path $dir 'php.exe'
	if (Test-Php $exe) { return $exe }

	Write-Step 'Scarico PHP 8.4 nella cartella del progetto (.wp\php, circa 35 MB). Non viene installato nel sistema.'
	try {
		$index = Invoke-RestMethod -Uri 'https://downloads.php.net/~windows/releases/releases.json' -UseBasicParsing -ErrorAction Stop
	} catch {
		Stop-Here "Non riesco a leggere l'elenco delle versioni di PHP ($($_.Exception.Message)). Controlla la connessione e rilancia."
	}
	$zip = $index.'8.4'.'nts-vs17-x64'.zip
	if (-not $zip -or -not $zip.path -or -not $zip.sha256) {
		Stop-Here 'Su php.net non trovo PHP 8.4 per Windows x64: installalo a mano (README, «Windows (Git Bash)»).'
	}
	$file = Join-Path ([IO.Path]::GetTempPath()) $zip.path
	try {
		Invoke-WebRequest -Uri "https://downloads.php.net/~windows/releases/$($zip.path)" -OutFile $file -UseBasicParsing -ErrorAction Stop
	} catch {
		Stop-Here "Download di PHP non riuscito ($($_.Exception.Message)). Rilancia tra poco."
	}
	$hash = (Get-FileHash -LiteralPath $file -Algorithm SHA256).Hash
	if ($hash -ne $zip.sha256) {
		Remove-Item -LiteralPath $file -Force
		Stop-Here 'Il file di PHP scaricato non corrisponde all''impronta pubblicata da php.net: non lo uso. Rilancia tra poco.'
	}
	if (Test-Path -LiteralPath $dir) { Remove-Item -LiteralPath $dir -Recurse -Force }
	try {
		Expand-Archive -LiteralPath $file -DestinationPath $dir -ErrorAction Stop
	} catch {
		Stop-Here "Non riesco a estrarre PHP in $dir ($($_.Exception.Message))."
	}
	Remove-Item -LiteralPath $file -Force

	# php.ini: the development defaults, the extension folder and the extensions WordPress and the setup use.
	$ini = [IO.File]::ReadAllText((Join-Path $dir 'php.ini-development'))
	$extDir = (Join-Path $dir 'ext').Replace('\', '/')
	$ini = [regex]::Replace($ini, '(?m)^;[ \t]*extension_dir[ \t]*=[ \t]*"ext"[ \t]*(?=\r?$)', "extension_dir = `"$extDir`"")
	foreach ($ext in @('curl', 'fileinfo', 'gd', 'mbstring', 'openssl', 'pdo_sqlite', 'sqlite3', 'zip')) {
		$ini = [regex]::Replace($ini, '(?m)^;(extension=' + $ext + ')(?=[ \t]*\r?$)', '$1')
	}
	$ini += "`r`n; ITS Rossellini (avvia-windows.cmd)`r`nmemory_limit = 512M`r`nupload_max_filesize = 64M`r`npost_max_size = 64M`r`n"
	[IO.File]::WriteAllText((Join-Path $dir 'php.ini'), $ini, (New-Object System.Text.UTF8Encoding $false))

	# PHP for Windows needs the Visual C++ runtime: 0xC0000135 means a DLL is missing.
	& $exe -v *> $null
	if ($LASTEXITCODE -eq -1073741515) {
		Install-Package 'Microsoft.VCRedist.2015+.x64' 'il Visual C++ Redistributable (serve a PHP)' 'https://aka.ms/vs/17/release/vc_redist.x64.exe'
	}
	if (-not (Test-Php $exe)) {
		Stop-Here 'PHP è stato scaricato ma non parte. Installalo a mano seguendo il README, «Windows (Git Bash)».'
	}
	return $exe
}

# ---------- Python ----------

function Find-Python {
	$candidates = @()
	foreach ($name in @('py.exe', 'python.exe', 'python3.exe')) {
		$cmd = Get-Command $name -ErrorAction SilentlyContinue
		if ($cmd) { $candidates += $cmd.Source }
	}
	$installed = Get-ChildItem -Path (Join-Path $env:LOCALAPPDATA 'Programs\Python\Python3*\python.exe') -ErrorAction SilentlyContinue
	$candidates += @($installed | Sort-Object FullName -Descending | ForEach-Object { $_.FullName })
	foreach ($exe in $candidates) {
		# The Microsoft Store stub ("python.exe" in WindowsApps) fails here and is skipped.
		& $exe -c 'import sys; sys.exit(0 if sys.version_info >= (3, 8) else 1)' *> $null
		if ($LASTEXITCODE -eq 0) { return $exe }
	}
	return $null
}

# ---------- Run ----------

Write-Host ''
Write-Host '  ITS Rossellini - sito in locale' -ForegroundColor White

$bash = Find-GitBash
if (-not $bash) {
	Install-Package 'Git.Git' 'Git per Windows' 'https://git-scm.com/download/win'
	$bash = Find-GitBash
	if (-not $bash) { Stop-Here 'Git per Windows risulta installato ma non trovo Git Bash: chiudi questa finestra e rilancia avvia-windows.cmd.' }
}
Write-Done "Git Bash: $bash"

$php = $null
$systemPhp = Get-Command php.exe -ErrorAction SilentlyContinue
if ($systemPhp -and (Test-Php $systemPhp.Source)) {
	$php = $systemPhp.Source
} else {
	$php = Get-PortablePhp
}
Write-Done "PHP: $php"

$paths = @(Split-Path $php -Parent)
$firstRun = -not (Test-Path -LiteralPath (Join-Path $Wp 'db\its.sqlite')) -or ($args -contains '--aggiorna')
if ($firstRun) {
	$python = Find-Python
	if (-not $python) {
		Install-Package 'Python.Python.3.12' 'Python 3' 'https://www.python.org/downloads/windows/'
		$python = Find-Python
		if (-not $python) { Stop-Here 'Python risulta installato ma non lo trovo: chiudi questa finestra e rilancia avvia-windows.cmd.' }
	}
	Write-Done "Python: $python"
	$paths += Split-Path $python -Parent
}

$env:Path = ($paths -join ';') + ';' + $env:Path
Set-Location -LiteralPath $Root
& $bash ((Join-Path $Root 'dev\avvia.sh').Replace('\', '/')) @args
exit $LASTEXITCODE
