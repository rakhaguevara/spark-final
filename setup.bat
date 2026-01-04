@echo off
REM SPARK Quick Setup Script for Windows
REM Run this after cloning: setup.bat

echo ================================
echo   SPARK - Quick Setup Script
echo ================================
echo.

REM Check if database.php exists
if not exist "config\database.php" (
    echo [ERROR] config\database.php not found
    echo         Please copy config\database.php.example to config\database.php
    pause
    exit /b 1
)

REM Check if spark (2).sql exists
if not exist "spark (2).sql" (
    echo [WARNING] spark (2).sql not found
    echo           Please import your database manually
) else (
    echo [OK] Database file found: spark (2).sql
    echo.
    
    set /p IMPORT="Import database? (y/n): "
    if /i "%IMPORT%"=="y" (
        set /p DB_USER="MySQL username [root]: "
        if "%DB_USER%"=="" set DB_USER=root
        
        set /p DB_NAME="Database name [spark]: "
        if "%DB_NAME%"=="" set DB_NAME=spark
        
        echo.
        echo Importing database...
        mysql -u %DB_USER% -p %DB_NAME% < "spark (2).sql"
        
        if %ERRORLEVEL% EQU 0 (
            echo [SUCCESS] Database imported
        ) else (
            echo [ERROR] Database import failed
            pause
            exit /b 1
        )
    )
)

echo.
echo Running database setup script...
php database\setup.php

if %ERRORLEVEL% EQU 0 (
    echo [SUCCESS] Database setup complete
) else (
    echo [ERROR] Database setup failed
    echo         You can run it manually: php database\setup.php
)

echo.
echo Setting up directories...
if not exist "uploads\profile" mkdir uploads\profile
if not exist "uploads\tickets" mkdir uploads\tickets

echo.
echo ================================
echo   Setup Complete!
echo ================================
echo.
echo Next Steps:
echo   1. Update config\database.php with your credentials
echo   2. Visit: http://localhost/spark
echo   3. Admin panel: http://localhost/spark/admin/login.php
echo.
echo For more info, read SETUP_GUIDE.md
echo.
pause
