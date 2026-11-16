

@echo off
setlocal
title Instalador NutriSalud

echo ==========================================
echo       INSTALADOR DE NUTRISALUD
echo ==========================================
echo.

set "REPO=https://github.com/UTVAM-Software/2026-III-AdoptCan.git"
set "DEST=C:\wamp64\www\Prueba\2026-III-AdoptCan"

if not exist "C:\wamp64\wampmanager.exe" (
    echo ERROR: WAMP no encontrado.
    pause
    exit /b 1
)

where git >nul 2>&1
if errorlevel 1 (
    echo ERROR: Git no esta instalado.
    pause
    exit /b 1
)

echo Requisitos encontrados.
echo.

echo [2/5] Descargando codigo fuente...

if not exist "%DEST%\.git" (
    if exist "%DEST%" (
        echo ERROR: La carpeta existe pero no es un repositorio Git.
        pause
        exit /b 1
    )

    git clone "%REPO%" "%DEST%"
    if errorlevel 1 (
        echo ERROR al descargar el proyecto.
        pause
        exit /b 1
    )
) else (
    echo Repositorio existente. Se conserva el codigo local.
)

echo.

echo [3/5] Configurando base de datos...

if not exist "%DEST%\database\nutrisalud.sql" (
    echo ERROR: No se encontro el archivo SQL.
    pause
    exit /b 1
)

if not exist "%DEST%\config\conexion.local.php" (
    (
        echo ^<?php
        echo return [
        echo     'host' =^> '127.0.0.1',
        echo     'puerto' =^> '3307',
        echo     'base_datos' =^> 'nutrisalud',
        echo     'usuario' =^> 'root',
        echo     'contrasena' =^> ''
        echo ];
    ) > "%DEST%\config\conexion.local.php"

    echo Configuracion creada.
) else (
    echo Se conserva la configuracion existente.
)

echo.
echo Se conservara la base de datos existente.
echo No se importara nuevamente el SQL.
echo.

echo [4/5] Verificando PHP...

set "PHP="

for /d %%D in ("C:\wamp64\bin\php\php*") do (
    if exist "%%~D\php.exe" set "PHP=%%~D\php.exe"
)

if not defined PHP (
    echo ERROR: No se encontro PHP.
    pause
    exit /b 1
)

"%PHP%" -v
if errorlevel 1 (
    echo ERROR: PHP no funciona.
    pause
    exit /b 1
)

echo PHP funciona correctamente.
echo.

echo [5/5] Activando servicios...

net start wampapache64
net start wampmariadb64

echo.
echo ==========================================
echo       INSTALACION PREPARADA
echo ==========================================
echo.
echo Verifica que WAMP este funcionando.
echo.
echo http://localhost/2026-III-AdoptCan/
echo.

pause
