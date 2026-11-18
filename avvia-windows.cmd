@echo off
rem Windows: doppio clic per avviare il sito in locale.
rem Controlla e, dopo conferma, installa cio' che serve (Git, PHP, Python), poi avvia WordPress
rem e apre il browser. Il lavoro lo fanno dev\windows\avvia.ps1 e dev\avvia.sh.
rem Opzioni, da prompt: avvia-windows.cmd --rete   (raggiungibile dal telefono)
rem                     avvia-windows.cmd --aggiorna (riscarica i contenuti dal sito)
setlocal
cd /d "%~dp0"
title ITS Rossellini - sito in locale
powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%~dp0dev\windows\avvia.ps1" %*
set "STATUS=%ERRORLEVEL%"
if not "%STATUS%"=="0" if not "%STATUS%"=="130" (
	echo.
	echo   Il sito non e' partito: leggi il messaggio qui sopra.
	pause
)
exit /b %STATUS%
